<x-admin::layouts>
    <x-slot:title>
        Daily Activities
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="text-xl font-bold dark:text-white">
                    Daily Activities
                </div>
            </div>

            <div class="flex items-center gap-x-2.5">
                @if (bouncer()->hasPermission('daily_activities.create'))
            <a href="{{ route('admin.daily_activities.form') }}" class="primary-button">
                New Submission
            </a>
        @endif

                @if (bouncer()->hasPermission('daily_activities.analytics'))
        <a href="{{ route('admin.daily_activities.analytics') }}" 
           class="primary-button !bg-indigo-500 hover:!bg-indigo-600">
            📊 Analytics
        </a>
    @endif
    @if (!bouncer()->hasPermission('daily_activities.create') && !bouncer()->hasPermission('daily_activities.analytics'))
        <span class="text-sm text-gray-500 dark:text-gray-400">
            No actions available
        </span>
    @endif
                
            </div>
        </div>

        <!-- Vue.js Filters Component -->
        <v-daily-activities-filters></v-daily-activities-filters>

        <!-- Content that will be updated via Vue -->
        <div id="daily-activities-content">
            @include('admin::daily_activities.content', ['activities' => $activities])
        </div>
    </div>

    @push('scripts')
<script type="text/x-template" id="v-daily-activities-filters-template">
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-end gap-4">
            <!-- Start Date -->
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Start Date
                </label>
                <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                    <input
                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="filters.start_date"
                        placeholder="Start Date"
                    />
                </x-admin::flat-picker.datetime>
            </div>

            <!-- End Date -->
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    End Date
                </label>
                <x-admin::flat-picker.datetime class="!w-full" ::allow-input="false">
                    <input
                        class="flex w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="filters.end_date"
                        placeholder="End Date"
                    />
                </x-admin::flat-picker.datetime>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button 
                    v-if="hasActiveFilters"
                    type="button" 
                    @click="resetFilters" 
                    class="secondary-button px-4 py-2"
                >
                    Reset
                </button>
            </div>
        </div>
    </div>
</script>

<script type="module">
    app.component('v-daily-activities-filters', {
        template: '#v-daily-activities-filters-template',

        data() {
            return {
                filters: {
                    start_date: @json(request('start_date', '')),
                    end_date: @json(request('end_date', '')),
                },
                loading: false
            }
        },

        methods: {
            async applyFilters() {
                this.loading = true;
                
                // Show skeleton loading state instead of simple text
                document.getElementById('daily-activities-content').innerHTML = this.getSkeletonHTML();

                try {
                    // Build query string
                    const params = new URLSearchParams();
                    if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                    if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                    params.append('ajax', '1');

                    const response = await fetch(`{{ route('admin.daily_activities.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();

                    // Update the content without page reload
                    document.getElementById('daily-activities-content').innerHTML = data.content;

                } catch (error) {
                    console.error('Error fetching activities:', error);
                    document.getElementById('daily-activities-content').innerHTML = `
                        <div class="rounded-lg border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
                            <div class="text-red-500">Error loading data. Please try again.</div>
                        </div>
                    `;
                } finally {
                    this.loading = false;
                }
            },

            resetFilters() {
                this.filters = {
                    start_date: '',
                    end_date: '',
                };
                this.applyFilters();
            },

            getSkeletonHTML() {
    return `
        <style>
            @keyframes shimmer {
                0% {
                    background-position: -468px 0;
                }
                100% {
                    background-position: 468px 0;
                }
            }
            .shimmer {
                animation: shimmer 2s infinite linear;
                background: #f6f7f8;
                background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
                background-size: 800px 104px;
                position: relative;
            }
            .dark .shimmer {
                background: #2d3748;
                background: linear-gradient(to right, #4a5568 8%, #718096 18%, #4a5568 33%);
                background-size: 800px 104px;
            }
        </style>
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
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        ${Array.from({length: 5}, () => `
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 rounded shimmer w-8"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 rounded shimmer w-24"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 rounded shimmer w-32"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-4 rounded shimmer w-16"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <div class="h-5 w-5 rounded shimmer"></div>
                                        <div class="h-5 w-5 rounded shimmer"></div>
                                        <div class="h-5 w-5 rounded shimmer"></div>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            <!-- Skeleton Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="h-4 rounded shimmer w-32"></div>
                    <div class="flex space-x-2">
                        ${Array.from({length: 5}, () => `
                            <div class="h-8 w-8 rounded shimmer"></div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
}
        },

        computed: {
            hasActiveFilters() {
                return this.filters.start_date || this.filters.end_date;
            }
        },

        mounted() {
            console.log('Daily activities filters component mounted');
            
            // Auto-apply filters when dates change
            this.$watch('filters.start_date', () => {
                if (this.filters.start_date) {
                    setTimeout(() => {
                        this.applyFilters();
                    }, 500);
                    }
                });

            this.$watch('filters.end_date', () => {
                if (this.filters.end_date) {
                    setTimeout(() => {
                        this.applyFilters();
                    }, 500);
                }
            });
        }
    });
</script>
@endpush
</x-admin::layouts>