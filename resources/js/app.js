import './bootstrap';

import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App.jsx';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const el = document.getElementById('app');
if (el) {
    createRoot(el).render(
        React.createElement(
            React.StrictMode,
            null,
            React.createElement(App)
        )
    );
}
