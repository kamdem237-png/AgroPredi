import React, { useEffect, useMemo, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { getAppProps, getCsrfToken } from '../lib/appProps.js';

export default function Login() {
    const navigate = useNavigate();
    const location = useLocation();
    const appProps = getAppProps();

    const from = useMemo(() => location.state?.from?.pathname || '/dashboard', [location.state]);

    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [remember, setRemember] = useState(true);
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
        formData.append('email', email);
        formData.append('password', password);
        if (remember) formData.append('remember', 'on');

        const resp = await fetch('/login', {
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
            window.location.href = from;
            return;
        }

        let payload = null;
        try {
            payload = await resp.json();
        } catch {
            payload = null;
        }

        const message = payload?.message || 'Connexion échouée. Vérifie tes identifiants.';
        setError(message);
        setLoading(false);
    };

    return (
        <div className="container py-5">
            <div className="row justify-content-center">
                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body p-4">
                            <div className="fw-bold fs-4 mb-1">Connexion</div>
                            <div className="text-muted small mb-4">Accède à ton dashboard et ton historique.</div>

                            {error ? <div className="alert alert-danger">{error}</div> : null}

                            <form onSubmit={onSubmit} className="vstack gap-3">
                                <div>
                                    <label className="form-label">Email</label>
                                    <input className="form-control" value={email} onChange={(e) => setEmail(e.target.value)} type="email" required />
                                </div>
                                <div>
                                    <label className="form-label">Mot de passe</label>
                                    <input className="form-control" value={password} onChange={(e) => setPassword(e.target.value)} type="password" required />
                                </div>
                                <div className="form-check">
                                    <input className="form-check-input" id="remember" type="checkbox" checked={remember} onChange={(e) => setRemember(e.target.checked)} />
                                    <label className="form-check-label" htmlFor="remember">Se souvenir de moi</label>
                                </div>

                                <button className="btn btn-success" disabled={loading}>
                                    {loading ? 'Connexion…' : 'Se connecter'}
                                </button>

                                <div className="d-flex justify-content-between small">
                                    <Link to="/register" className="text-decoration-none">Créer un compte</Link>
                                    <a href="/forgot-password" className="text-decoration-none">Mot de passe oublié</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
