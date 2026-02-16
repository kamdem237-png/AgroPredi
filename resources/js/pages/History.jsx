import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Loader from '../components/Loader.jsx';

export default function History() {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [items, setItems] = useState([]);

    useEffect(() => {
        let mounted = true;

        const run = async () => {
            setLoading(true);
            const resp = await fetch('/scan/history', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            }).catch(() => null);

            if (!mounted) return;

            if (!resp) {
                setError('Impossible de contacter le serveur.');
                setLoading(false);
                return;
            }

            if (!resp.ok) {
                setError('Accès refusé.');
                setLoading(false);
                return;
            }

            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }

            setItems(payload?.diagnostics || []);
            setLoading(false);
        };

        run();
        return () => {
            mounted = false;
        };
    }, []);

    if (loading) return <Loader fullscreen label="Chargement de l’historique…" />;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Historique</h1>
                    <div className="text-muted">Tes derniers diagnostics (protégés).</div>
                </div>
                <Link className="btn btn-success" to="/scan">Nouveau scan</Link>
            </div>

            {error ? <div className="alert alert-danger mt-3">{error}</div> : null}

            <div className="card border-0 shadow-sm mt-3">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Maladie</th>
                                <th>État</th>
                                <th>Risque</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.length ? items.map((d) => (
                                <tr key={d.id}>
                                    <td style={{ width: 110 }}>
                                        {d.image_path ? (
                                            <img src={`/storage/${d.image_path}`} alt="" className="img-fluid rounded border" style={{ maxHeight: 64 }} />
                                        ) : null}
                                    </td>
                                    <td className="fw-semibold">{d.maladie || '—'}</td>
                                    <td>{d.etat || '—'}</td>
                                    <td>{d.niveau_risque || '—'}</td>
                                    <td className="text-muted small">{d.created_at || '—'}</td>
                                    <td className="text-end">
                                        <Link className="btn btn-sm btn-outline-secondary" to={`/result/${d.id}`}>Voir</Link>
                                    </td>
                                </tr>
                            )) : (
                                <tr>
                                    <td colSpan={6} className="text-center text-muted py-4">Aucun diagnostic.</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <div className="text-muted small mt-3">Pagination UI à venir (endpoint JSON dédié recommandé).</div>
        </div>
    );
}
