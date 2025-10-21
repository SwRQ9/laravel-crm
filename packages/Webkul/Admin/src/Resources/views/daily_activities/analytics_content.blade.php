@if($hasData)
<!-- Compact Metrics Cards - Two Rows -->
<div class="grid grid-cols-2 gap-3 mb-4">
    <!-- First Row -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Total Submissions -->
        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col items-center text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalSubmissions }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Submissions</div>
            </div>
        </div>

        <!-- Total Points -->
        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col items-center text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $totalPoints }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Total Points</div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Average Points -->
        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col items-center text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $averagePoints }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Avg Points</div>
            </div>
        </div>

        <!-- Most Active Category -->
        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col items-center text-center">
                <div class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate w-full">{{ $mostActiveCategory }}</div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wide">Top Category</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Activity Type Distribution -->
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Type Distribution</h3>
        <div class="h-64">
            <canvas id="activityTypeChart" width="400" height="400"></canvas>
        </div>
    </div>

    <!-- Points Over Time -->
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Points Over Time</h3>
        <div class="h-64">
            <canvas id="timeSeriesChart" width="400" height="400"></canvas>
        </div>
    </div>
</div>
@else
<!-- Empty State -->
<div class="rounded-lg border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No data available</h3>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        {{ request()->anyFilled(['user_id', 'start_date', 'end_date']) ? 'Try adjusting your filters' : 'Start by selecting a worker or date range' }}
    </p>
</div>
@endif