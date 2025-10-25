<x-admin::layouts>
    <x-slot:title>
        Employes Analytics
    </x-slot>

    <div class="flex flex-col gap-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="text-xl font-bold dark:text-white">
                    Employes Analytics Dashboard
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Track Employes performance and submission metrics
                </div>
            </div>
        </div>

        <!-- Vue.js Filters Component -->
        <v-analytics-filters
            :has-view-all-permission="{{ bouncer()->hasPermission('daily_activities.analytics.view_all') ? 'true' : 'false' }}">
        </v-analytics-filters>

        <!-- Analytics Content that will be updated via Vue -->
        <div id="analytics-content">
            @include('admin::daily_activities.analytics_content', [
                'hasData' => $hasData,
                'totalSubmissions' => $totalSubmissions,
                'totalPoints' => $totalPoints,
                'averagePoints' => $averagePoints,
                'mostActiveCategory' => $mostActiveCategory,
                'chartData' => $chartData,
                'timeSeriesData' => $timeSeriesData
            ])
        </div>
    </div>

    @if($hasData)
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif

    @push('scripts')
    <script
        type="text/x-template"
        id="v-analytics-filters-template"
    >
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-end gap-4">
                <!-- User Filter - Only show if user has view_all permission -->
@if(bouncer()->hasPermission('daily_activities.analytics.view_all'))
<div class="w-64">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        Select Employes
    </label>
    <select
        v-model="filters.user_id"
        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
    >
        <option value="">All Employes</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}">
                {{ $user->name }} 
            </option>
        @endforeach
    </select>
</div>
@endif

                <!-- Start Date -->
                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Start Date
                    </label>
                    <x-admin::flat-picker.datetime
                        class="!w-full"
                        ::allow-input="false"
                    >
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
                    <x-admin::flat-picker.datetime
                        class="!w-full"
                        ::allow-input="false"
                    >
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
                        v-if="hasViewAllPermission ? hasActiveFilters : (filters.start_date || filters.end_date)"
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
    // Store chart instances globally
    let activityChart = null;
    let timeSeriesChart = null;

    function initializeCharts(chartData, timeSeriesData) {
        // Destroy existing charts if they exist
        if (activityChart) {
            activityChart.destroy();
        }
        if (timeSeriesChart) {
            timeSeriesChart.destroy();
        }

        // Activity Type Chart
        const activityCtx = document.getElementById('activityTypeChart');
        if (activityCtx && chartData && chartData.labels && chartData.labels.length > 0) {
            activityChart = new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Points',
                        data: chartData.data,
                        backgroundColor: [
                            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                            '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'
                        ],
                        borderColor: [
                            '#4f46e5', '#10b981', '#f59e0b', '#ef4444', 
                            '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });
        }

        // Time Series Chart
        const timeCtx = document.getElementById('timeSeriesChart');
        if (timeCtx && timeSeriesData && timeSeriesData.labels && timeSeriesData.labels.length > 0) {
            timeSeriesChart = new Chart(timeCtx, {
                type: 'line',
                data: {
                    labels: timeSeriesData.labels,
                    datasets: [{
                        label: 'Daily Points',
                        data: timeSeriesData.data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#6b7280'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize charts on first load
    document.addEventListener('DOMContentLoaded', function() {
        @if($hasData)
        setTimeout(() => {
            initializeCharts(
                @json($chartData),
                @json($timeSeriesData)
            );
        }, 100);
        @endif
    });

    app.component('v-analytics-filters', {
        template: '#v-analytics-filters-template',

        props: {
        hasViewAllPermission: {
            type: Boolean,
            required: true,
            default: false
        }
    },

        data() {
            return {
                filters: {
                    user_id: @json($userId ?? ''),
                    start_date: @json($from ? $from->format('Y-m-d H:i') : ''),
                    end_date: @json($to ? $to->format('Y-m-d H:i') : ''),
                },
                loading: false
            }
        },

        methods: {
            async applyFilters() {
                this.loading = true;
                
                // Show skeleton loading state instead of simple text
                document.getElementById('analytics-content').innerHTML = this.getSkeletonHTML();

                try {
                    // Build query string
                    const params = new URLSearchParams();
                    if (this.filters.user_id) params.append('user_id', this.filters.user_id);
                    if (this.filters.start_date) params.append('start_date', this.filters.start_date);
                    if (this.filters.end_date) params.append('end_date', this.filters.end_date);
                    params.append('ajax', '1');

                    const response = await fetch(`{{ route('admin.daily_activities.analytics') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();

                    // Update the content without page reload
                    document.getElementById('analytics-content').innerHTML = data.content;

                    // Reinitialize charts with new data
                    if (data.chartData && data.timeSeriesData) {
                        setTimeout(() => {
                            initializeCharts(data.chartData, data.timeSeriesData);
                        }, 100);
                    }

                } catch (error) {
                    console.error('Error fetching analytics data:', error);
                    document.getElementById('analytics-content').innerHTML = `
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
                    user_id: '',
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

                    <!-- Compact Metrics Cards Skeleton -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <!-- First Row -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Total Submissions Skeleton -->
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded shimmer w-16 mb-1"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded shimmer w-20"></div>
                                </div>
                            </div>

                            <!-- Total Points Skeleton -->
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded shimmer w-16 mb-1"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded shimmer w-20"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row -->
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Average Points Skeleton -->
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded shimmer w-16 mb-1"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded shimmer w-20"></div>
                                </div>
                            </div>

                            <!-- Most Active Category Skeleton -->
                            <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
                                <div class="flex flex-col items-center text-center">
                                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded shimmer w-24 mb-1"></div>
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded shimmer w-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section Skeleton -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Activity Type Distribution Skeleton -->
                        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded shimmer w-48 mb-4"></div>
                            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded shimmer"></div>
                        </div>

                        <!-- Points Over Time Skeleton -->
                        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                            <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded shimmer w-32 mb-4"></div>
                            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded shimmer"></div>
                        </div>
                    </div>
                `;
            }
        },

        computed: {
            hasActiveFilters() {
                return this.filters.user_id || this.filters.start_date || this.filters.end_date;
            }
        },

        mounted() {
            console.log('Analytics filters component mounted');
            
            // Auto-apply filters when dropdown changes
            this.$watch('filters.user_id', (newVal, oldVal) => {
                    // Small delay to avoid too many requests
                    setTimeout(() => {
                        this.applyFilters();
                    }, 300);               
            });

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