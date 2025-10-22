<!-- Table Card - Full Width -->
<div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Submission Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Total Points
                    </th>
                    @if(bouncer()->hasPermission('daily_activities.view') || 
    bouncer()->hasPermission('daily_activities.edit') || 
    bouncer()->hasPermission('daily_activities.delete'))
<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
    Actions
</th>
@endif
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @php
                    $tz = config('app.timezone', 'UTC');
                    $today = \Carbon\Carbon::now($tz)->toDateString();
                @endphp

                @forelse ($activities as $activity)
                    @php
                        $isOwner = $activity->user_id === auth()->id();
                        $isToday = optional($activity->created_at)->timezone($tz)->toDateString() === $today;
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 ">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $activity->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $activity->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ optional($activity->created_at)->timezone($tz)->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $activity->total_points }}
                        </td>

                        @if(bouncer()->hasPermission('daily_activities.view') || 
    bouncer()->hasPermission('daily_activities.edit') || 
    bouncer()->hasPermission('daily_activities.delete'))
<td class="px-6 py-4 whitespace-nowrap text-sm text-right">
    <div class="inline-flex items-center gap-2" onclick="event.stopPropagation();">
        {{-- View --}}
        @if(bouncer()->hasPermission('daily_activities.view'))
        <a href="{{ route('admin.daily_activities.show', $activity->id) }}"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
            title="View">
            <!-- eye icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 .001-10.001A5 5 0 0 1 12 17z" />
                <circle cx="12" cy="12" r="2.5" />
            </svg>
        </a>
        @endif

        {{-- Edit: only same-day + owner + permission --}}
        @if($isOwner && $isToday && bouncer()->hasPermission('daily_activities.edit'))
            <a href="{{ route('admin.daily_activities.edit', $activity->id) }}"
                class="text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                title="Edit">
                <!-- pencil icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                </svg>
            </a>
        @endif

        {{-- Delete: only same-day + owner + permission --}}
        @if($isOwner && $isToday && bouncer()->hasPermission('daily_activities.delete'))
            <form action="{{ route('admin.daily_activities.destroy', $activity->id) }}"
                method="POST"
                onsubmit="return confirm('Delete this submission? This cannot be undone.');">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                    title="Delete">
                    <!-- trash icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M9 3h6l1 1h4v2H4V4h4l1-1zm1 6h2v8h-2V9zm4 0h2v8h-2V9zm-6 0h2v8H7V9z" />
                    </svg>
                </button>
            </form>
        @endif
    </div>
</td>
@endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (bouncer()->hasPermission('daily_activities.view') || bouncer()->hasPermission('daily_activities.edit') || bouncer()->hasPermission('daily_activities.delete')) ? 5 : 4 }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    {{ $activities->appends(request()->query())->links() }}
</div>