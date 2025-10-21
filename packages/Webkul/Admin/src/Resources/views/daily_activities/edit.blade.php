<x-admin::layouts>
    <x-slot:title>
        Edit Submission #{{ $activity->id }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header -->
        <div
            class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2
                   text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="text-xl font-bold dark:text-white">
                Edit Daily Activity
            </div>

            <a href="{{ route('admin.daily_activities.index') }}"
               class="primary-button !bg-gray-200 hover:!bg-gray-300 !text-gray-800 dark:!bg-gray-800 dark:hover:!bg-gray-700 dark:!text-gray-200">
                 Back
            </a>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('admin.daily_activities.update', $activity->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div
                class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Tally</h2>

                <!-- Activities grid - Same layout as form.blade.php -->
                <div class="grid grid-cols-2 gap-x-5 gap-y-4">
                    @foreach ($activityTypes as $activityType)
                        @php
                            $existing = $activity->entries->firstWhere('activity_type_id', $activityType->id);
                        @endphp
                        <div class="flex items-center justify-between p-2 border border-gray-700 rounded-md mb-2">
                            <label for="activity_{{ $activityType->slug }}"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $activityType->name }}
                            </label>

                            <input type="number"
                                   name="tally[{{ $activityType->id }}]"
                                   id="activity_{{ $activityType->slug }}"
                                   value="{{ old('tally.' . $activityType->id, $existing->value ?? 0) }}"
                                   class="w-20 rounded-md border border-gray-300 bg-white text-gray-900
                                      focus:border-brandColor focus:ring-brandColor sm:text-sm
                                      dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:placeholder-gray-500"
                                   min="0">
                        </div>
                    @endforeach
                </div>
            

            <div class="flex justify-end mt-4">
                <button type="submit"
                        class="primary-button px-5 py-2 text-sm font-medium">
                    Update Submission
                </button>
            </div>
        </form>
    </div>
</div>    
</x-admin::layouts>