import React from 'react';
import { Link } from 'react-router-dom';
import { getAppProps } from '../lib/appProps.js';
import StatCard from '../components/StatCard.jsx';

export default function Dashboard() {
    const { data, auth } = getAppProps();

    const totalScans = data?.totalScans ?? 0;
    const healthyScans = data?.healthyScans ?? 0;
    const severeScans = data?.severeScans ?? 0;
    const latestScan = data?.latestScan ?? null;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Dashboard</h1>
                    <div className="text-muted">Bienvenue, {auth?.user?.name || 'Utilisateur'}.</div>
                </div>
                <div className="d-flex gap-2 flex-wrap">
                    <Link className="btn btn-success" to="/scan">Nouveau scan</Link>
                    <Link className="btn btn-outline-success" to="/history">Historique</Link>
                </div>
            </div>

            <div className="row g-3 mt-1">
                <div className="col-md-4">
                    <StatCard title="Scans" value={totalScans} hint="Total de tes analyses" icon={<i className="bi bi-camera" />} />
                </div>
                <div className="col-md-4">
                    <StatCard title="Sains" value={healthyScans} hint="Diagnostics rassurants" icon={<i className="bi bi-check-circle" />} />
                </div>
                <div className="col-md-4">
                    <StatCard title="Risque élevé" value={severeScans} hint="À traiter rapidement" icon={<i className="bi bi-exclamation-triangle" />} />
                </div>
            </div>

            <div className="row g-3 mt-3">
                <div className="col-lg-7">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold">Résumé</div>
                            <div className="text-muted small mt-1">
                                Tes données sont protégées par l’auth Laravel et liées à ton compte.
                            </div>

                            <div className="mt-3 p-3 bg-light rounded">
                                <div className="text-muted small">Prochaine action recommandée</div>
                                <div className="fw-semibold">Scanner une nouvelle feuille suspecte</div>
                                <div className="text-muted small">Surveille l’évolution et conserve un historique.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold">Dernier diagnostic</div>
                            {latestScan ? (
                                <div className="mt-3">
                                    <div className="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div className="fw-semibold">{latestScan.maladie || '—'}</div>
                                            <div className="text-muted small">{latestScan.etat || ''} • {latestScan.niveau_risque || ''}</div>
                                        </div>
                                        <Link className="btn btn-sm btn-outline-secondary" to={`/result/${latestScan.id}`}>Voir</Link>
                                    </div>
                                </div>
                            ) : (
                                <div className="text-muted small mt-2">Aucun scan pour le moment.</div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
