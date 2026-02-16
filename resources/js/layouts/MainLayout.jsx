import React from 'react';
import Navbar from '../components/Navbar.jsx';
import Footer from '../components/Footer.jsx';

export default function MainLayout({ children }) {
    return (
        <div className="min-vh-100 d-flex flex-column bg-light">
            <Navbar />
            <main className="flex-grow-1">
                {children}
            </main>
            <Footer />
        </div>
    );
}
