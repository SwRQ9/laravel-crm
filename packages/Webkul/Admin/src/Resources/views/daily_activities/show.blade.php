<x-admin::layouts>
    <x-slot:title>Submission #{{ $activity->id }}</x-slot>

    <div
            class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 mb-2
                   text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="text-xl font-bold dark:text-white">
                Submission #{{ $activity->id }}
            </div>

            <a href="{{ route('admin.daily_activities.index') }}"
               class="primary-button !bg-gray-200 hover:!bg-gray-300 !text-gray-800 dark:!bg-gray-800 dark:hover:!bg-gray-700 dark:!text-gray-200">
                 Back
            </a>
        </div>
    

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-300">User</div>
            <div class="text-base font-medium text-gray-900 dark:text-white">
                {{ $activity->user->name ?? 'N/A' }}
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-300">Submission Date</div>
            <div class="text-base font-medium text-gray-900 dark:text-white">
                {{ optional($activity->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-300">Total Points</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $activity->total_points }}
            </div>
        </div>
    </div>

    {{-- Detailed tally section --}}
    <div class="mt-6 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 ">
    <div class="border-b border-gray-200 px-6 py-3 text-lg font-semibold dark:border-gray-800 dark:text-white">
        Detailed Tally
    </div>

    @if($activity->entries->count())
        <table class="w-full border-collapse divide-y divide-gray-200 dark:divide-gray-700 ">
    <thead class="bg-gray-100 dark:bg-gray-800">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300 w-1/2">
                Task
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300 w-1/4">
                Value
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300 w-1/4">
                Points
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-700 ">
        @forelse ($activity->entries as $entry)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 "> 
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                    {{ $entry->activityType->name ?? '-' }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                    {{ $entry->value ?? 0 }}
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-300">
                    {{ $entry->activityType->point_value ?? 0 }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    No detailed tally saved for this submission.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

    @else
        <div class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
            No detailed tally saved for this submission.
        </div>
    @endif
</div>

</x-admin::layouts>
