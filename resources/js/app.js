import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Tambahkan kode ini untuk registrasi PWA
import { registerSW } from 'virtual:pwa-register';
registerSW({ immediate: true });