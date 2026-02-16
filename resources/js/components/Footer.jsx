import React from 'react';
import { Link } from 'react-router-dom';

export default function Footer() {
    return (
        <footer className="bg-white border-top mt-5">
            <div className="container py-4">
                <div className="row g-4">
                    <div className="col-md-5">
                        <div className="fw-semibold text-success mb-2">AgroPredi</div>
                        <div className="text-muted small">
                            Plateforme de diagnostic des maladies des plantes assistée par IA.
                            Analyse rapide, recommandations utiles, et suivi de vos scans.
                        </div>
                    </div>

                    <div className="col-6 col-md-3">
                        <div className="fw-semibold mb-2">Navigation</div>
                        <div className="d-flex flex-column gap-1 small">
                            <Link to="/" className="text-decoration-none text-muted">Accueil</Link>
                            <Link to="/scan" className="text-decoration-none text-muted">Scanner</Link>
                            <Link to="/dashboard" className="text-decoration-none text-muted">Dashboard</Link>
                            <Link to="/history" className="text-decoration-none text-muted">Historique</Link>
                        </div>
                    </div>

                    <div className="col-6 col-md-4">
                        <div className="fw-semibold mb-2">Bonnes pratiques</div>
                        <div className="text-muted small">
                            Prenez une photo nette en lumière naturelle, ciblez la zone atteinte, et évitez le flou.
                        </div>
                    </div>
                </div>

                <div className="text-muted small mt-4 d-flex justify-content-between flex-wrap gap-2">
                    <div>© {new Date().getFullYear()} AgroPredi</div>
                    <div>Laravel + React + Bootstrap</div>
                </div>
            </div>
        </footer>
    );
}
