import React, { useEffect, useMemo, useState } from 'react';
import Loader from '../components/Loader.jsx';
import { getAppProps, getCsrfToken } from '../lib/appProps.js';

export default function Admin() {
    const { auth } = getAppProps();

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [overview, setOverview] = useState(null);
    const [users, setUsers] = useState([]);
    const [q, setQ] = useState('');
    const [selectedUser, setSelectedUser] = useState(null);
    const [selectedDiagnostics, setSelectedDiagnostics] = useState([]);
    const [detailsLoading, setDetailsLoading] = useState(false);

    const csrf = useMemo(() => getCsrfToken(), []);

    useEffect(() => {
        let mounted = true;

        const run = async () => {
            setLoading(true);
            setError(null);

            const [oResp, uResp] = await Promise.all([
                fetch('/admin/api/overview', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                }).catch(() => null),
                fetch('/admin/api/users', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                }).catch(() => null),
            ]);

            if (!mounted) return;

            if (!oResp || !oResp.ok) {
                setError("Impossible de charger l'aperçu admin.");
                setLoading(false);
                return;
            }

            const oPayload = await oResp.json().catch(() => null);
            setOverview(oPayload);

            if (uResp && uResp.ok) {
                const uPayload = await uResp.json().catch(() => null);
                setUsers(uPayload?.users || []);
            }

            setLoading(false);
        };

        run();
        return () => {
            mounted = false;
        };
    }, []);

    const loadUsers = async (query) => {
        const url = query ? `/admin/api/users?q=${encodeURIComponent(query)}` : '/admin/api/users';
        const resp = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).catch(() => null);
        if (!resp || !resp.ok) return;
        const payload = await resp.json().catch(() => null);
        setUsers(payload?.users || []);
    };

    const loadUserDetails = async (userId) => {
        setDetailsLoading(true);
        const resp = await fetch(`/admin/api/users/${userId}/diagnostics`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).catch(() => null);

        if (!resp || !resp.ok) {
            setDetailsLoading(false);
            return;
        }

        const payload = await resp.json().catch(() => null);
        setSelectedUser(payload?.user || null);
        setSelectedDiagnostics(payload?.diagnostics || []);
        setDetailsLoading(false);
    };

    const toggleBan = async (userId) => {
        const formData = new FormData();
        formData.append('_token', csrf);

        const resp = await fetch(`/admin/api/users/${userId}/toggle-ban`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) return;

        const payload = await resp.json().catch(() => null);
        if (!resp.ok) {
            alert(payload?.message || 'Action impossible');
            return;
        }

        await loadUsers(q);
        if (selectedUser?.id === userId) {
            await loadUserDetails(userId);
        }
    };

    const totals = overview?.totals;
    const latest = (overview?.latest_diagnostics || []).slice(0, 3);
    const series = overview?.scans_per_day || [];

    const chartPoints = useMemo(() => {
        if (!series.length) return '';
        const w = 520;
        const h = 120;
        const padding = 8;
        const max = Math.max(...series.map((x) => Number(x.count || 0)), 1);

        return series
            .map((p, idx) => {
                const x = padding + (idx * (w - padding * 2)) / Math.max(series.length - 1, 1);
                const y = h - padding - ((Number(p.count || 0) / max) * (h - padding * 2));
                return `${x},${y}`;
            })
            .join(' ');
    }, [series]);

    const chartMeta = useMemo(() => {
        const w = 520;
        const h = 120;
        const padding = 8;
        const max = Math.max(...series.map((x) => Number(x.count || 0)), 0);

        const yTicks = [0, Math.round(max / 2), max].filter((v, idx, arr) => arr.indexOf(v) === idx);

        const xTickIndexes = (() => {
            if (!series.length) return [];
            const indexes = new Set([0, Math.floor((series.length - 1) / 2), series.length - 1]);
            for (let i = 5; i < series.length - 1; i += 7) indexes.add(i);
            return Array.from(indexes).sort((a, b) => a - b);
        })();

        const xTicks = xTickIndexes.map((idx) => {
            const x = padding + (idx * (w - padding * 2)) / Math.max(series.length - 1, 1);
            const label = String(series[idx]?.day || '').slice(5);
            return { idx, x, label };
        });

        const yTicksMeta = yTicks.map((v) => {
            const y = h - padding - ((Number(v) / Math.max(max, 1)) * (h - padding * 2));
            return { v, y };
        });

        return { w, h, padding, max, xTicks, yTicks: yTicksMeta };
    }, [series]);

    if (loading) return <Loader fullscreen label="Chargement admin…" />;
    if (error) return <div className="container py-5"><div className="alert alert-danger">{error}</div></div>;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Espace admin</h1>
                    <div className="text-muted">Connecté en tant que {auth?.user?.name || 'Admin'}.</div>
                </div>
            </div>

            <div className="row g-3 mt-2">
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body">
                            <div className="text-muted small">Utilisateurs</div>
                            <div className="fs-3 fw-semibold">{totals?.users ?? 0}</div>
                            <div className="text-muted small mt-2">Total inscrits</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body">
                            <div className="text-muted small">Actifs (15 min)</div>
                            <div className="fs-3 fw-semibold">{totals?.active_users ?? 0}</div>
                            <div className="text-muted small mt-2">Basé sur la table sessions</div>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body">
                            <div className="text-muted small">Scans</div>
                            <div className="fs-3 fw-semibold">{totals?.scans ?? 0}</div>
                            <div className="text-muted small mt-2">Total diagnostics</div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row g-3 mt-1">
                <div className="col-lg-7">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold">Tendance scans (30 jours)</div>
                            <div className="text-muted small mt-1">Courbe de volume journalier.</div>
                            <div className="mt-3">
                                <svg width="100%" viewBox="0 0 520 150" preserveAspectRatio="none" role="img">
                                    <line x1={chartMeta.padding} y1={chartMeta.padding} x2={chartMeta.padding} y2={chartMeta.h - chartMeta.padding} stroke="rgba(0,0,0,0.2)" />
                                    <line x1={chartMeta.padding} y1={chartMeta.h - chartMeta.padding} x2={chartMeta.w - chartMeta.padding} y2={chartMeta.h - chartMeta.padding} stroke="rgba(0,0,0,0.2)" />

                                    {chartMeta.yTicks.map((t) => (
                                        <g key={`y-${t.v}`}>
                                            <line x1={chartMeta.padding} y1={t.y} x2={chartMeta.w - chartMeta.padding} y2={t.y} stroke="rgba(0,0,0,0.08)" />
                                            <text x={chartMeta.padding + 2} y={t.y - 2} fontSize="10" fill="rgba(0,0,0,0.55)">{t.v}</text>
                                        </g>
                                    ))}

                                    {chartMeta.xTicks.map((t) => (
                                        <g key={`x-${t.idx}`}>
                                            <line x1={t.x} y1={chartMeta.h - chartMeta.padding} x2={t.x} y2={chartMeta.h - chartMeta.padding + 4} stroke="rgba(0,0,0,0.2)" />
                                            <text x={t.x - 10} y={chartMeta.h - chartMeta.padding + 16} fontSize="10" fill="rgba(0,0,0,0.55)">{t.label}</text>
                                        </g>
                                    ))}

                                    <polyline
                                        fill="none"
                                        stroke="rgba(25,135,84,1)"
                                        strokeWidth="2"
                                        points={chartPoints}
                                    />

                                    <text x={chartMeta.w - chartMeta.padding - 60} y={chartMeta.h + 24} fontSize="10" fill="rgba(0,0,0,0.55)">Date (MM-JJ)</text>
                                    <text x={chartMeta.padding} y={chartMeta.padding - 2} fontSize="10" fill="rgba(0,0,0,0.55)">Scans/jour</text>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div className="fw-semibold">Derniers scans</div>
                                <a className="btn btn-sm btn-outline-success" href="/admin/diagnostics">Voir tout</a>
                            </div>
                            <div className="table-responsive mt-2">
                                <table className="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Utilisateur</th>
                                            <th>Maladie</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {latest.map((d) => (
                                            <tr key={d.id}>
                                                <td className="text-muted">#{d.id}</td>
                                                <td>
                                                    <div className="fw-semibold small">{d.user?.name || '—'}</div>
                                                    <div className="text-muted small">{d.user?.email || ''}</div>
                                                </td>
                                                <td className="small">{d.maladie || '—'}</td>
                                                <td className="text-end">
                                                    <a className="btn btn-sm btn-outline-secondary" href={`/result/${d.id}`}>Voir</a>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row g-3 mt-1">
                <div className="col-lg-7">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                <div>
                                    <div className="fw-semibold">Utilisateurs</div>
                                    <div className="text-muted small">Recherche, historique et actions.</div>
                                </div>
                                <form
                                    className="d-flex gap-2"
                                    onSubmit={(e) => {
                                        e.preventDefault();
                                        loadUsers(q);
                                    }}
                                >
                                    <input
                                        className="form-control form-control-sm"
                                        placeholder="Rechercher (nom/email)"
                                        value={q}
                                        onChange={(e) => setQ(e.target.value)}
                                    />
                                    <button className="btn btn-sm btn-outline-success" type="submit">Chercher</button>
                                </form>
                            </div>

                            <div className="table-responsive mt-3">
                                <table className="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Scans</th>
                                            <th>Statut</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {users.map((u) => (
                                            <tr key={u.id}>
                                                <td className="fw-semibold small">{u.name}</td>
                                                <td className="text-muted small">{u.email}</td>
                                                <td className="small">{u.diagnostics_count ?? 0}</td>
                                                <td>
                                                    {u.is_admin ? <span className="badge text-bg-success">admin</span> : null}
                                                    {u.is_banned ? <span className="badge text-bg-danger ms-1">banni</span> : <span className="badge text-bg-secondary ms-1">actif</span>}
                                                </td>
                                                <td className="text-end">
                                                    <button
                                                        className="btn btn-sm btn-outline-secondary me-2"
                                                        onClick={() => loadUserDetails(u.id)}
                                                    >
                                                        Historique
                                                    </button>
                                                    <button
                                                        className={u.is_banned ? 'btn btn-sm btn-success' : 'btn btn-sm btn-danger'}
                                                        onClick={() => toggleBan(u.id)}
                                                    >
                                                        {u.is_banned ? 'Débannir' : 'Bannir'}
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold">Historique utilisateur</div>
                            {detailsLoading ? (
                                <div className="text-muted small mt-2">Chargement…</div>
                            ) : selectedUser ? (
                                <>
                                    <div className="text-muted small mt-1">
                                        {selectedUser.name} • {selectedUser.email}
                                    </div>
                                    <div className="table-responsive mt-3">
                                        <table className="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Maladie</th>
                                                    <th>Risque</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {selectedDiagnostics.map((d) => (
                                                    <tr key={d.id}>
                                                        <td className="text-muted">#{d.id}</td>
                                                        <td className="small">{d.maladie}</td>
                                                        <td className="small">{d.niveau_risque}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </>
                            ) : (
                                <div className="text-muted small mt-2">Sélectionne un utilisateur.</div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
