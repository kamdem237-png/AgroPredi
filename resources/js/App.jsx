import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import MainLayout from './layouts/MainLayout.jsx';
import AppRoutes from './router.jsx';

export default function App() {
    return (
        <BrowserRouter>
            <MainLayout>
                <AppRoutes />
            </MainLayout>
        </BrowserRouter>
    );
}
