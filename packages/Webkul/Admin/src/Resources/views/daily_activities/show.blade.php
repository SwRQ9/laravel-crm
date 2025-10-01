<x-admin::layouts>
    <x-slot:title>Submission #{{ $activity->id }}</x-slot>

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">
            Submission #{{ $activity->id }}
        </h1>

        <a href="{{ route('admin.daily_activities.index') }}" class="btn btn-secondary">
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
    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">Detailed Tally</div>

        @php
            // adjust if you store fields differently (e.g., JSON column `tally`)
            // $tally = $activity->tally ?? [];
            $tally = []; // TODO: replace with your actual data source
        @endphp

        @if(!empty($tally))
            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                @foreach($tally as $label => $value)
                    <div class="flex items-center justify-between rounded border border-gray-200 px-3 py-2 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-300">{{ ucwords(str_replace('_',' ',$label)) }}</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $value }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-sm text-gray-500 dark:text-gray-400">
                No detailed tally saved for this submission.
            </div>
        @endif
    </div>
</x-admin::layouts>
