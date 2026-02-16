import React, { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { getAppProps, getCsrfToken } from '../lib/appProps.js';

export default function Register() {
    const navigate = useNavigate();
    const appProps = getAppProps();

    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');

    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (appProps?.auth?.check) {
            navigate('/dashboard', { replace: true });
        }
    }, [appProps?.auth?.check, navigate]);

    const onSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('password_confirmation', passwordConfirmation);

        const resp = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) {
            setError('Impossible de contacter le serveur.');
            setLoading(false);
            return;
        }

        if (resp.ok) {
            window.location.href = '/dashboard';
            return;
        }

        let payload = null;
        try {
            payload = await resp.json();
        } catch {
            payload = null;
        }

        const message = payload?.message || 'Inscription échouée. Vérifie les champs.';
        setError(message);
        setLoading(false);
    };

    return (
        <div className="container py-5">
            <div className="row justify-content-center">
                <div className="col-lg-6">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body p-4">
                            <div className="fw-bold fs-4 mb-1">Inscription</div>
                            <div className="text-muted small mb-4">Crée ton compte pour sauvegarder ton historique.</div>

                            {error ? <div className="alert alert-danger">{error}</div> : null}

                            <form onSubmit={onSubmit} className="row g-3">
                                <div className="col-md-6">
                                    <label className="form-label">Nom</label>
                                    <input className="form-control" value={name} onChange={(e) => setName(e.target.value)} required />
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Email</label>
                                    <input className="form-control" value={email} onChange={(e) => setEmail(e.target.value)} type="email" required />
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Mot de passe</label>
                                    <input className="form-control" value={password} onChange={(e) => setPassword(e.target.value)} type="password" required />
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Confirmer</label>
                                    <input className="form-control" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} type="password" required />
                                </div>

                                <div className="col-12">
                                    <button className="btn btn-success" disabled={loading}>
                                        {loading ? 'Création…' : 'Créer le compte'}
                                    </button>
                                </div>

                                <div className="col-12 small">
                                    Déjà un compte ? <Link to="/login" className="text-decoration-none">Connexion</Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
