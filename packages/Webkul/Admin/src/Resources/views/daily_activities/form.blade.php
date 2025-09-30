<x-admin::layouts>
    <x-slot:title>
        Daily Activity Form
    </x-slot:title>

    <div class="content full-page">
        <h2 class="p-6 text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Daily Activity Form</h2>
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

            <!-- Submit button -->
            <div class="mt-4">
                <button type="submit"
                        class="px-4 py-2 bg-brandColor text-white rounded-lg shadow 
                               hover:bg-brandColor-dark focus:outline-none
                               dark:bg-brandColor dark:hover:bg-brandColor-dark">
                    Submit
                </button>
            </div>
        </form>
    </div>
</x-admin::layouts>
