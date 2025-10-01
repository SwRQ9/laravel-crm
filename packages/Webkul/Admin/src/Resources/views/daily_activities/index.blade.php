<x-admin::layouts>
    <x-slot:title>
        Daily Activities
        </x-slot>

        <div class="flex flex-col gap-4">
            <!-- Header Card -->
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-bold dark:text-white">
                        Daily Activities
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    <a href="{{ route('admin.daily_activities.form') }}" class="primary-button">
                        New Submission
                    </a>
                </div>
            </div>

<form method="GET" action="{{ route('admin.daily_activities.index') }}" class="flex items-center gap-4">
    <!-- Start Date -->
    <div class="w-50">
        <x-admin::flat-picker.datetime class="!w-40">
            <input
            type="text"
            name="start_date"
            value="{{ request('start_date') }}"
            autocomplete="off"  
            data-enable-time="true"
            data-allow-input="true"
            class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 
            dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
            placeholder="Start Date"
            />
        </x-admin::flat-picker.datetime>
    </div>

    <!-- End Date -->
    <div class="w-50">
        <x-admin::flat-picker.datetime class="!w-40">
            <input
            type="text"
            name="end_date"
            value="{{ request('end_date') }}"
            autocomplete="off"  
            data-enable-time="true"
            data-allow-input="true"
            class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 
            dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
            placeholder="End Date"
            />
        </x-admin::flat-picker.datetime>
    </div>

    <!-- Filter Button -->
    <button type="submit" 
        class="primary-button px-3 py-1 text-sm">
        Filter
    </button>

    <!-- Reset Button -->
     @if(request('start_date') || request('end_date'))
    <a href="{{ route('admin.daily_activities.index') }}" 
       class="ml-2 secondary-button px-3 py-1 text-sm">
       Reset
    </a>
    @endif
</form>



            <!-- Table Card - Full Width -->
            <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    ID
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    User
                                </th>
                                {{-- Removed original "Date" column --}}
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Submission Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Total Points
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Actions
                                </th>
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

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                        <div class="inline-flex items-center gap-2" onclick="event.stopPropagation();">
                                            {{-- View --}}
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

                                            {{-- Edit: only same-day + owner --}}
                                            @if($isOwner && $isToday)
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

                                            {{-- Delete: only same-day + owner --}}
                                            @if($isOwner && $isToday)
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
                                                                d="M9 3h6l1 1h4v2H4V4h4l1-1zm1 6h2v8h-2V9zm4 0h2v8h-2V9zM7 9h2v8H7V9z" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
        </div>
</x-admin::layouts>