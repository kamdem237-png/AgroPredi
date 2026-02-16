import React, { useEffect, useState } from 'react';
import Loader from '../components/Loader.jsx';

export default function AdminDiagnostics() {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [rows, setRows] = useState([]);
    const [pagination, setPagination] = useState(null);
    const [page, setPage] = useState(1);

    useEffect(() => {
        let mounted = true;

        const run = async () => {
            setLoading(true);
            setError(null);

            const resp = await fetch(`/admin/api/diagnostics?page=${page}&per_page=25`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            }).catch(() => null);

            if (!mounted) return;

            if (!resp || !resp.ok) {
                setError('Impossible de charger la liste des diagnostics.');
                setLoading(false);
                return;
            }

            const payload = await resp.json().catch(() => null);
            setRows(payload?.diagnostics || []);
            setPagination(payload?.pagination || null);
            setLoading(false);
        };

        run();
        return () => {
            mounted = false;
        };
    }, [page]);

    if (loading) return <Loader fullscreen label="Chargement diagnostics…" />;
    if (error) return <div className="container py-5"><div className="alert alert-danger">{error}</div></div>;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Tous les diagnostics</h1>
                    <div className="text-muted">Liste complète des scans.</div>
                </div>
                <div className="text-muted small">
                    {pagination ? `Page ${pagination.current_page} / ${pagination.last_page} • Total ${pagination.total}` : ''}
                </div>
            </div>

            <div className="card border-0 shadow-sm mt-3">
                <div className="card-body">
                    <div className="table-responsive">
                        <table className="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Utilisateur</th>
                                    <th>Maladie</th>
                                    <th>État</th>
                                    <th>Risque</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {rows.map((d) => (
                                    <tr key={d.id}>
                                        <td className="text-muted">#{d.id}</td>
                                        <td className="small text-muted">{String(d.created_at || '').replace('T', ' ').slice(0, 16)}</td>
                                        <td>
                                            <div className="fw-semibold small">{d.user?.name || '—'}</div>
                                            <div className="text-muted small">{d.user?.email || ''}</div>
                                        </td>
                                        <td className="small">{d.maladie || '—'}</td>
                                        <td className="small">{d.etat || '—'}</td>
                                        <td className="small">{d.niveau_risque || '—'}</td>
                                        <td className="text-end">
                                            <a className="btn btn-sm btn-outline-secondary" href={`/result/${d.id}`}>Voir</a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {pagination ? (
                        <div className="d-flex justify-content-between align-items-center mt-3">
                            <button className="btn btn-sm btn-outline-secondary" disabled={page <= 1} onClick={() => setPage((p) => Math.max(1, p - 1))}>
                                Précédent
                            </button>
                            <button className="btn btn-sm btn-outline-secondary" disabled={page >= pagination.last_page} onClick={() => setPage((p) => p + 1)}>
                                Suivant
                            </button>
                        </div>
                    ) : null}
                </div>
            </div>
        </div>
    );
}
