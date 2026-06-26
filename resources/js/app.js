import Alpine from 'alpinejs';
import { initDashboardCharts } from './dashboard-charts';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', initDashboardCharts);
