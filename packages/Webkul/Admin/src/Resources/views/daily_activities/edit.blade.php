<x-admin::layouts>
    <x-slot:title>Edit Submission #{{ $activity->id }}</x-slot>

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">
            Edit Submission #{{ $activity->id }}
        </h1>

        <a href="{{ route('admin.daily_activities.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </div>

    <form action="{{ route('admin.daily_activities.update', $activity->id) }}" method="POST" class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        @csrf
        @method('PUT')

        {{-- Include your existing form fields here, but prefill with $activity --}}
        {{-- Example: --}}
        {{-- <x-admin::form.control type="number" name="tasks_completed" :value="$activity->tally['tasks_completed'] ?? 0" label="Tasks Completed" /> --}}
        {{-- Repeat for each tally field… --}}

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</x-admin::layouts>
