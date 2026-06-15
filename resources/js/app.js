import './bootstrap';
import { dashboardManager } from './dashboard-manager.js';

// Register Alpine.js components globally
document.addEventListener('alpine:init', () => {
    // Dashboard Manager akan tersedia sebagai x-data="dashboardManager()"
    window.dashboardManager = dashboardManager;
});

