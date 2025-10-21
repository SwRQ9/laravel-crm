<x-admin::layouts>
    <x-slot:title>
        Daily Activity Form
    </x-slot:title>

    <div class="flex flex-col gap-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="text-xl font-bold dark:text-white">
                Daily Activity Form
            </div>

            <a href="{{ route('admin.daily_activities.index') }}"
               class="primary-button !bg-gray-200 hover:!bg-gray-300 !text-gray-800 dark:!bg-gray-800 dark:hover:!bg-gray-700 dark:!text-gray-200">
                Back
            </a>
        </div>

        <!-- Form Card -->
        <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 shadow-sm">
            <p class="text-gray-600 dark:text-gray-400 mb-6">Fill in your tallies for today. Totals will update automatically.</p>

            <form method="POST" action="{{ route('daily-activities.store') }}">
                @csrf

                <!-- Activities grid -->
                <div class="grid grid-cols-2 gap-x-5 gap-y-4">
                    @foreach ($activityTypes as $activityType)
                        <div class="flex items-center justify-between p-2 border border-gray-700 rounded-md mb-2">
                            <label for="activity_{{ $activityType->slug }}"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $activityType->name }}
                            </label>

                            <input type="number"
                                   name="activities[{{ $activityType->slug }}]"
                                   id="activity_{{ $activityType->slug }}"
                                   class="w-20 rounded-md border border-gray-300 bg-white text-gray-900
                                      focus:border-brandColor focus:ring-brandColor sm:text-sm
                                      dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100 dark:placeholder-gray-500"
                                   min="0">
                        </div>
                    @endforeach
                </div>

                <!-- Submit button - Right aligned -->
                <div class="flex justify-end mt-4">
                    <button type="submit"
                            class="primary-button px-4 py-2 text-sm font-medium">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts>