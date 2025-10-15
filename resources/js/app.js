import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// Register the collapse plugin
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();
