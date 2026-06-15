/**
 * Dashboard Manager - Alpine.js Component
 * Mengelola filter dan interaksi pada dashboard management
 */

export function dashboardManager() {
    return {
        // State untuk filter
        activeSection: 'menu',
        filters: {
            menu: {
                search: '',
                category: 'all',
                status: 'all',
                sorting: 'newest'
            },
            tables: {
                search: '',
                location: 'all',
                status: 'all',
                sorting: 'number'
            },
            orders: {
                search: '',
                status: 'all',
                dateRange: 'today',
                customer: ''
            },
            reservations: {
                search: '',
                status: 'all',
                dateRange: 'upcoming',
                guests: 'all'
            }
        },

        // Statistics yang akan di-update real-time
        stats: {
            menu: { total: 0, available: 0, unavailable: 0 },
            tables: { total: 0, available: 0, occupied: 0, reserved: 0 },
            orders: { today: 0, pending: 0, processing: 0, ready: 0, completed: 0 },
            reservations: { upcoming: 0, pending: 0, confirmed: 0, rejected: 0 }
        },

        /**
         * Initialize component
         */
        init() {
            this.loadStats();
            this.setupWatchers();
            this.setupRealtimeListeners();
        },

        /**
         * Load statistics data
         */
        loadStats() {
            // Stats akan dimuat dari server via Alpine fetch atau Laravel Livewire
            console.log('📊 Loading dashboard statistics...');
        },

        /**
         * Setup Alpine watchers untuk filter changes
         */
        setupWatchers() {
            // Watch untuk perubahan filter di setiap section
            Object.keys(this.filters).forEach(section => {
                this.$watch(`filters.${section}`, () => {
                    this.applyFilterToSection(section);
                }, { deep: true });
            });
        },

        /**
         * Apply filter ke section tertentu
         */
        applyFilterToSection(section) {
            const filters = this.filters[section];
            console.log(`🔍 Filtering ${section}:`, filters);
            
            // Construct query string
            const params = new URLSearchParams();
            Object.entries(filters).forEach(([key, value]) => {
                if (value && value !== 'all') {
                    params.append(key, value);
                }
            });

            // Update URL tanpa reload (menggunakan History API)
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.replaceState({}, '', newUrl);

            // Trigger filter application (bisa via AJAX atau server-side)
            this.triggerFilterUpdate(section);
        },

        /**
         * Trigger filter update via AJAX
         */
        triggerFilterUpdate(section) {
            const filters = this.filters[section];
            const params = new URLSearchParams();
            
            Object.entries(filters).forEach(([key, value]) => {
                if (value && value !== 'all') {
                    params.append(key, value);
                }
            });

            // Tentukan URL endpoint berdasarkan section
            let url = '';
            if (section === 'orders') url = '/admin/orders/feed';
            
            if (url) {
                fetch(`${url}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    // Asumsi container ID di blade adalah section + '-list-container'
                    const container = document.getElementById(`${section}-list-container`);
                    if (container && data.html) {
                        container.innerHTML = data.html;
                    }
                    if (data.status_counts) this.stats.orders = data.status_counts;
                })
                .catch(err => console.error('Error fetching data:', err));
            }
        },

        /**
         * Setup real-time listeners menggunakan Laravel Echo
         */
        setupRealtimeListeners() {
            if (window.Echo) {
                // Listen untuk updates di different channels
                ['menu-updates', 'tables-updates', 'orders-updates', 'reservations-updates'].forEach(channel => {
                    window.Echo.channel(channel).listen('DataUpdated', (data) => {
                        console.log(`✨ Real-time update dari ${channel}:`, data);
                        this.refreshStats();
                    });
                });
            }
        },

        /**
         * Refresh statistics
         */
        refreshStats() {
            console.log('🔄 Refreshing dashboard statistics...');
            // Bisa fetch stats dari server
        },

        /**
         * Reset semua filter ke default
         */
        resetAllFilters() {
            Object.keys(this.filters).forEach(section => {
                this.resetSectionFilters(section);
            });
            this.$dispatch('toast', {
                message: 'Semua filter telah direset',
                type: 'info'
            });
        },

        /**
         * Reset filter untuk section tertentu
         */
        resetSectionFilters(section) {
            const defaults = {
                menu: { search: '', category: 'all', status: 'all', sorting: 'newest' },
                tables: { search: '', location: 'all', status: 'all', sorting: 'number' },
                orders: { search: '', status: 'all', dateRange: 'today', customer: '' },
                reservations: { search: '', status: 'all', dateRange: 'upcoming', guests: 'all' }
            };
            this.filters[section] = defaults[section];
        },

        /**
         * Change active section
         */
        changeSection(section) {
            this.activeSection = section;
            console.log(`📑 Switched to ${section} section`);
        },

        /**
         * Export filter state
         */
        exportFilters() {
            return JSON.stringify(this.filters, null, 2);
        },

        /**
         * Import filter state
         */
        importFilters(filterJSON) {
            try {
                this.filters = JSON.parse(filterJSON);
                this.$dispatch('toast', {
                    message: 'Filter berhasil diimport',
                    type: 'success'
                });
            } catch (error) {
                this.$dispatch('toast', {
                    message: 'Error mengimport filter',
                    type: 'error'
                });
            }
        }
    };
}
