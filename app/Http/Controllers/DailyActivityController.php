<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use App\Models\DailyActivity;
use App\Models\DailyActivityEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyActivityController extends Controller
{
    /**
     * List daily activities.
     * - If ?all=1 is passed, show all users (for admin view).
     * - Otherwise show only the current user's submissions.
     * Optional filters: ?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
   public function index(Request $request)
{
    $q = DailyActivity::with('user')->orderByDesc('date');

    if (!$request->boolean('all')) {
        $q->where('user_id', Auth::id());
    }

    // Use start_date and end_date (same as input names)
    if ($from = $request->input('start_date')) {
        $q->where('created_at', '>=', $from);
    }

    if ($to = $request->input('end_date')) {
        $q->where('created_at', '<=', $to);
    }

    $activities = $q->paginate(10);

    // If it's an AJAX request, return JSON with the content
    if ($request->ajax() || $request->has('ajax')) {
        $content = view('admin::daily_activities.content', compact('activities'))->render();

        return response()->json([
            'content' => $content
        ]);
    }

    return view('admin::daily_activities.index', compact('activities'));
}


public function create()
{
    return view('admin::daily_activities.form');
}


    /**
     * Show a single submission (drilldown).
     */
    

    /**
     * Store or update today's submission for current user.
     * Expects payload like:
     *  activities[calls_email_socmed]=5
     *  activities[cases_closed]=1
     * Optionally: date=YYYY-MM-DD (defaults to today)
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'date' => ['nullable', 'date'],
        'activities' => ['required', 'array'],
        'activities.*' => ['nullable', 'integer', 'min:0'],
    ]);

    // Create the parent daily_activity
    $daily = DailyActivity::create([
        'user_id' => auth()->id(),
        'date'    => $validated['date'] ?? now()->toDateString(),
    ]);

    // Insert all entries (lookup activity type ID by slug)
    foreach (ActivityType::all() as $type) {
    $value = $request->input("activities.{$type->slug}", 0);

    DailyActivityEntry::create([
        'daily_activity_id' => $daily->id,
        'activity_type_id'  => $type->id,
        'value'             => $value,
    ]);
}


    // Calculate total points
    $totalPoints = $daily->entries()
        ->with('activityType')
        ->get()
        ->sum(fn($e) => $e->value * $e->activityType->point_value);

    // Update parent record
    $daily->update(['total_points' => $totalPoints]);

    return redirect()
        ->route('admin.daily_activities.index')
        ->with('success', 'Daily activities submitted successfully!');
}




    /**
     * Normalize the submitted tallies against active Activity Types
     * and calculate the Total Champion Points.
     *
     * @return array [$normalizedActivities, $totalPoints]
     */
    private function normalizeAndCalculate(array $input): array
    {
        // Load active activity types keyed by slug
        $types = ActivityType::where('is_active', true)
            ->orderBy('weight')
            ->get(['slug', 'point_value'])
            ->keyBy('slug');

        $normalized = [];
        $total = 0;

        foreach ($input as $slug => $rawTally) {
            if (!isset($types[$slug])) {
                // ignore unknown slugs
                continue;
            }
            $tally = (int) $rawTally;
            if ($tally <= 0) {
                continue;
            }

            $normalized[$slug] = $tally;
            $total += $tally * (int) $types[$slug]->point_value;
        }

        return [$normalized, $total];
    }

    public function form()
{
    // Fetch all activity types from DB
    $activityTypes = \App\Models\ActivityType::all();

    // Pass them to the Blade view
    return view('daily_activities.form', compact('activityTypes'));
}

public function show(DailyActivity $dailyActivity)
{
    if ($dailyActivity->user_id !== Auth::id() && !request()->boolean('all')) {
        abort(403);
    }

    $dailyActivity->load(['user', 'entries.activityType']);

    return view('admin::daily_activities.show', [
        'activity' => $dailyActivity,
    ]);
}





public function edit(DailyActivity $dailyActivity)
{
    // Load existing tally entries and activity types
    $dailyActivity->load(['entries.activityType']);

    $activityTypes = \App\Models\ActivityType::where('is_active', true)
        ->orderBy('weight')
        ->get();

    return view('admin::daily_activities.edit', [
        'activity' => $dailyActivity,
        'activityTypes' => $activityTypes,
    ]);
}


public function update(Request $request, DailyActivity $dailyActivity)
{
    $data = $request->validate([
        'tally' => 'required|array',
        'tally.*' => 'nullable|integer|min:0',
    ]);

    // Delete old entries and insert new ones
    $dailyActivity->entries()->delete();

    foreach ($data['tally'] as $typeId => $value) {
        if ($value > 0) {
            $dailyActivity->entries()->create([
                'activity_type_id' => $typeId,
                'value' => $value,
            ]);
        }
    }

    // Recalculate total points
    $total = $dailyActivity->entries()
        ->join('activity_types', 'activity_types.id', '=', 'daily_activity_entries.activity_type_id')
        ->sum(\DB::raw('daily_activity_entries.value * activity_types.point_value'));

    $dailyActivity->update(['total_points' => $total]);

    return redirect()->route('admin.daily_activities.index')
        ->with('success', 'Submission updated successfully.');
}


public function destroy(DailyActivity $dailyActivity)
{
    $dailyActivity->delete();

    return redirect()->route('admin.daily_activities.index')
                     ->with('success', 'Submission deleted.');
}


public function analytics(Request $request)
{
    // ✅ Admin-only access (user_id = 1)
    if (auth()->id() !== 1) {
        abort(403, 'Access denied.');
    }

    $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

    $userId = $request->get('user_id');
    $from = $request->date('start_date');
    $to = $request->date('end_date');

    $query = \App\Models\DailyActivity::query()
        ->with(['user', 'entries.activityType']);

    if ($userId) {
        $query->where('user_id', $userId);
    }

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }

    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }

    $activities = $query->get();

    // ✅ Compute KPIs
    $totalSubmissions = $activities->count();
    $totalPoints = $activities->sum('total_points');
    $averagePoints = $totalSubmissions > 0 ? round($totalPoints / $totalSubmissions, 1) : 0;

    // Find most active category
    $categoryCounts = [];
    foreach ($activities as $activity) {
        foreach ($activity->entries as $entry) {
            if ($entry->activityType && $entry->activityType->category) {
                $categoryCounts[$entry->activityType->category] =
                    ($categoryCounts[$entry->activityType->category] ?? 0) + $entry->value;
            }
        }
    }

    $mostActiveCategory = count($categoryCounts)
        ? collect($categoryCounts)->sortDesc()->keys()->first()
        : 'N/A';

    // ✅ Chart Data: Activity Type Points
    $activityTypePoints = [];
    foreach ($activities as $activity) {
        foreach ($activity->entries as $entry) {
            $name = $entry->activityType->name ?? 'Unknown';
            $activityTypePoints[$name] =
                ($activityTypePoints[$name] ?? 0) + $entry->value;
        }
    }

    $chartData = [
        'labels' => array_keys($activityTypePoints),
        'data' => array_values($activityTypePoints),
        'colors' => ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
    ];

    // ✅ Chart Data: Points over time
    $dailyTotals = $activities->groupBy(fn($a) => $a->created_at->format('Y-m-d'))
        ->map(fn($group) => $group->sum('total_points'));

    $timeSeriesData = [
        'labels' => $dailyTotals->keys(),
        'data' => $dailyTotals->values(),
    ];

    // ✅ Check if we need to show export buttons
    $hasData = $totalSubmissions > 0;

    // If it's an AJAX request, return JSON with the content and chart data
    if ($request->ajax() || $request->has('ajax')) {
        $content = view('admin::daily_activities.analytics_content', compact(
            'hasData', 'totalSubmissions', 'totalPoints', 'averagePoints',
            'mostActiveCategory', 'chartData', 'timeSeriesData'
        ))->render();

        return response()->json([
            'content' => $content,
            'chartData' => $chartData,
            'timeSeriesData' => $timeSeriesData
        ]);
    }

    return view('admin::daily_activities.analytics', compact(
        'users', 'activities', 'userId', 'from', 'to',
        'totalSubmissions', 'totalPoints', 'averagePoints',
        'mostActiveCategory', 'chartData', 'timeSeriesData',
        'hasData'
    ));
}

}
