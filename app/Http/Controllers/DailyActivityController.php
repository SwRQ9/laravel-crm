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
    // Ownership check (keep your current rule)
    if ($dailyActivity->user_id !== Auth::id() && !request()->boolean('all')) {
        abort(403);
    }

    // If AJAX/JSON requested → return JSON
    if (request()->wantsJson() || request()->ajax()) {
        return response()->json($dailyActivity->load('user'));
    }

    // Otherwise → return Blade view (normal browser navigation)
    return view('admin::daily_activities.show', [
        'activity' => $dailyActivity->load('user'),
    ]);
}

public function edit(DailyActivity $dailyActivity)
{
    // later we’ll enforce same-day restriction
    return view('admin::daily_activities.edit', [
        'activity' => $dailyActivity,
    ]);
}

public function update(Request $request, DailyActivity $dailyActivity)
{
    // validate like in store()
    $data = $request->validate([
        // add your tally fields here
    ]);

    // compute total points (same logic as in store)
    $dailyActivity->update([
        // …map $data here…
        'total_points' => $this->computeTotalPoints($data),
    ]);

    return redirect()->route('admin.daily_activities.index')
                     ->with('success', 'Submission updated.');
}

public function destroy(DailyActivity $dailyActivity)
{
    $dailyActivity->delete();

    return redirect()->route('admin.daily_activities.index')
                     ->with('success', 'Submission deleted.');
}

}
