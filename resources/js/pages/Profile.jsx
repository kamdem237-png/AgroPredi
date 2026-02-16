import React, { useEffect, useMemo, useState } from 'react';
import Loader from '../components/Loader.jsx';
import { getAppProps, getCsrfToken } from '../lib/appProps.js';

export default function Profile() {
    const appProps = getAppProps();

    const initialUser = useMemo(() => {
        return appProps?.data?.user || appProps?.auth?.user || null;
    }, [appProps?.data?.user, appProps?.auth?.user]);

    const [user, setUser] = useState(initialUser);
    const [loading, setLoading] = useState(!initialUser);

    const [name, setName] = useState(initialUser?.name || '');
    const [email, setEmail] = useState(initialUser?.email || '');

    const [currentPassword, setCurrentPassword] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');

    const [deletePassword, setDeletePassword] = useState('');

    const [success, setSuccess] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (initialUser) return;
        let mounted = true;

        const run = async () => {
            setLoading(true);
            const resp = await fetch('/profile', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            }).catch(() => null);

            if (!mounted) return;

            if (!resp || !resp.ok) {
                setError('Impossible de charger le profil.');
                setLoading(false);
                return;
            }

            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }

            const u = payload?.user || payload?.data?.user || null;
            setUser(u);
            setName(u?.name || '');
            setEmail(u?.email || '');
            setLoading(false);
        };

        run();
        return () => {
            mounted = false;
        };
    }, [initialUser]);

    const onUpdateProfile = async (e) => {
        e.preventDefault();
        setSuccess(null);
        setError(null);

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('_method', 'PATCH');
        formData.append('name', name);
        formData.append('email', email);

        const resp = await fetch('/profile', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) {
            setError('Impossible de contacter le serveur.');
            return;
        }

        if (!resp.ok) {
            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }
            const msg = payload?.message || payload?.error || 'Mise à jour impossible.';
            setError(msg);
            return;
        }

        const payload = await resp.json().catch(() => null);
        setUser(payload?.user || user);
        setSuccess('Profil mis à jour.');
    };

    const onUpdatePassword = async (e) => {
        e.preventDefault();
        setSuccess(null);
        setError(null);

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('_method', 'PUT');
        formData.append('current_password', currentPassword);
        formData.append('password', password);
        formData.append('password_confirmation', passwordConfirmation);

        const resp = await fetch('/password', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) {
            setError('Impossible de contacter le serveur.');
            return;
        }

        if (!resp.ok) {
            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }
            const msg = payload?.message || payload?.error || 'Changement de mot de passe impossible.';
            setError(msg);
            return;
        }

        setCurrentPassword('');
        setPassword('');
        setPasswordConfirmation('');
        setSuccess('Mot de passe mis à jour.');
    };

    const onDeleteAccount = async (e) => {
        e.preventDefault();
        setSuccess(null);
        setError(null);

        const ok = window.confirm('Confirmer la suppression du compte ? Cette action est définitive.');
        if (!ok) return;

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('_method', 'DELETE');
        formData.append('password', deletePassword);

        const resp = await fetch('/profile', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) {
            setError('Impossible de contacter le serveur.');
            return;
        }

        if (!resp.ok) {
            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }
            const msg = payload?.message || payload?.error || 'Suppression du compte impossible.';
            setError(msg);
            return;
        }

        const payload = await resp.json().catch(() => null);
        window.location.href = payload?.redirect || '/';
    };

    if (loading) return <Loader fullscreen label="Chargement du profil…" />;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Profil</h1>
                    <div className="text-muted">Gère tes informations de compte.</div>
                </div>
            </div>

            {success ? <div className="alert alert-success mt-3">{success}</div> : null}
            {error ? <div className="alert alert-danger mt-3">{error}</div> : null}

            <div className="row g-3 mt-1">
                <div className="col-lg-6">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold mb-2">Informations</div>
                            <form onSubmit={onUpdateProfile}>
                                <div className="mb-3">
                                    <label className="form-label">Nom</label>
                                    <input className="form-control" value={name} onChange={(e) => setName(e.target.value)} required />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Email</label>
                                    <input type="email" className="form-control" value={email} onChange={(e) => setEmail(e.target.value)} required />
                                </div>
                                <button className="btn btn-success" type="submit">Enregistrer</button>
                                {user?.email_verified_at === null ? (
                                    <div className="text-muted small mt-2">Email non vérifié.</div>
                                ) : null}
                            </form>
                        </div>
                    </div>
                </div>

                <div className="col-lg-6">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold mb-2">Sécurité</div>
                            <form onSubmit={onUpdatePassword}>
                                <div className="mb-3">
                                    <label className="form-label">Mot de passe actuel</label>
                                    <input type="password" className="form-control" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} required />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Nouveau mot de passe</label>
                                    <input type="password" className="form-control" value={password} onChange={(e) => setPassword(e.target.value)} required />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label">Confirmation</label>
                                    <input type="password" className="form-control" value={passwordConfirmation} onChange={(e) => setPasswordConfirmation(e.target.value)} required />
                                </div>
                                <button className="btn btn-outline-success" type="submit">Mettre à jour</button>
                            </form>
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mt-3">
                        <div className="card-body">
                            <div className="fw-semibold mb-2 text-danger">Supprimer le compte</div>
                            <form onSubmit={onDeleteAccount}>
                                <div className="mb-3">
                                    <label className="form-label">Mot de passe</label>
                                    <input type="password" className="form-control" value={deletePassword} onChange={(e) => setDeletePassword(e.target.value)} required />
                                </div>
                                <button className="btn btn-danger" type="submit">Supprimer définitivement</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
