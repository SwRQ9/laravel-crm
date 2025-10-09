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
                ← Back
            </a>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('admin.daily_activities.update', $activity->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div
                class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Update Tally</h2>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Task
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Value
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($activityTypes as $type)
                                @php
                                    $existing = $activity->entries->firstWhere('activity_type_id', $type->id);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        {{ $type->name }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <input
                                            type="number"
                                            name="tally[{{ $type->id }}]"
                                            value="{{ old('tally.' . $type->id, $existing->value ?? 0) }}"
                                            min="0"
                                            class="w-24 text-center rounded-md border border-gray-300 dark:border-gray-700
                                                   bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100
                                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                        />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="primary-button px-5 py-2 text-sm font-medium">
                    Update Submission
                </button>
            </div>
        </form>
    </div>
</x-admin::layouts>
