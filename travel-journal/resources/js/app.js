import './bootstrap';
import { initGlobeDashboard } from './modules/globe-dashboard';
import { initJourneyDashboard } from './modules/journey-dashboard';
import { initGlobalStatus } from './modules/global-status';
import { initThemeToggle } from './modules/theme-toggle';

initThemeToggle();
initGlobeDashboard();
initJourneyDashboard();
initGlobalStatus();
