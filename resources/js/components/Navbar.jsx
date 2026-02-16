import React from 'react';
import { Link, NavLink, useNavigate } from 'react-router-dom';
import { getAppProps, getCsrfToken } from '../lib/appProps.js';

export default function Navbar() {
    const navigate = useNavigate();
    const { auth } = getAppProps();

    const onLogout = async () => {
        const formData = new FormData();
        formData.append('_token', getCsrfToken());

        await fetch('/logout', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => { });

        window.location.href = '/';
    };

    return (
        <nav className="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
            <div className="container">
                <Link className="navbar-brand fw-semibold text-success" to="/">
                    AgroPredi
                </Link>

                <button
                    className="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span className="navbar-toggler-icon" />
                </button>

                <div className="collapse navbar-collapse" id="mainNavbar">
                    <ul className="navbar-nav me-auto mb-2 mb-lg-0">
                        <li className="nav-item">
                            <NavLink className="nav-link" to="/scan">Scanner</NavLink>
                        </li>
                        {auth?.check ? (
                            <>
                                <li className="nav-item">
                                    <NavLink className="nav-link" to="/dashboard">Dashboard</NavLink>
                                </li>
                                <li className="nav-item">
                                    <NavLink className="nav-link" to="/history">Historique</NavLink>
                                </li>
                                <li className="nav-item">
                                    <NavLink className="nav-link" to="/profile">Profil</NavLink>
                                </li>
                                {auth?.user?.is_admin ? (
                                    <li className="nav-item">
                                        <NavLink className="nav-link" to="/admin">Admin</NavLink>
                                    </li>
                                ) : null}
                            </>
                        ) : null}
                    </ul>

                    <div className="d-flex gap-2 align-items-center">
                        {auth?.check ? (
                            <>
                                <div className="text-muted small d-none d-lg-block">
                                    {auth?.user?.name || 'Utilisateur'}
                                </div>
                                <button className="btn btn-outline-success btn-sm" onClick={() => navigate('/dashboard')}>
                                    Mon espace
                                </button>
                                <button className="btn btn-success btn-sm" onClick={onLogout}>
                                    Déconnexion
                                </button>
                            </>
                        ) : (
                            <>
                                <Link className="btn btn-outline-success btn-sm" to="/login">Connexion</Link>
                                <Link className="btn btn-success btn-sm" to="/register">Inscription</Link>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </nav>
    );
}
