import React, { useEffect, useMemo, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import Loader from '../components/Loader.jsx';
import { getAppProps } from '../lib/appProps.js';

export default function Result() {
    const { id } = useParams();
    const appProps = getAppProps();

    const initial = useMemo(() => {
        if (appProps?.data?.diagnostic && String(appProps?.data?.diagnostic?.id) === String(id)) {
            return appProps.data;
        }
        return null;
    }, [appProps?.data, id]);

    const [data, setData] = useState(initial);
    const [loading, setLoading] = useState(!initial);
    const [error, setError] = useState(null);
    const [imgFailed, setImgFailed] = useState(false);

    const diagnostic = data?.diagnostic;
    const doc = data?.doc;
    const imageSrc = diagnostic?.image_url || (diagnostic?.image_path ? `/storage/${diagnostic.image_path}` : null);

    const severityMeta = useMemo(() => {
        const raw = String(doc?.severity || '').toLowerCase();
        if (raw === 'high') return { label: 'Élevé', className: 'badge text-bg-danger-subtle text-danger' };
        if (raw === 'low') return { label: 'Faible', className: 'badge text-bg-success-subtle text-success' };
        if (raw === 'moderate') return { label: 'Modéré', className: 'badge text-bg-warning-subtle text-warning' };
        return null;
    }, [doc?.severity]);

    useEffect(() => {
        setImgFailed(false);
    }, [imageSrc]);

    useEffect(() => {
        if (initial) return;
        let mounted = true;

        const run = async () => {
            setLoading(true);
            const resp = await fetch(`/scan/result/${id}`, {
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
                setError('Accès refusé ou résultat introuvable.');
                setLoading(false);
                return;
            }

            let payload = null;
            try {
                payload = await resp.json();
            } catch {
                payload = null;
            }

            setData(payload);
            setLoading(false);
        };

        run();
        return () => {
            mounted = false;
        };
    }, [id, initial]);

    if (loading) return <Loader fullscreen label="Chargement du résultat…" />;
    if (error) return <div className="container py-5"><div className="alert alert-danger">{error}</div></div>;

    return (
        <div className="container py-4">
            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h1 className="h3 fw-bold mb-1">Résultat</h1>
                    <div className="text-muted">Diagnostic #{diagnostic?.id}</div>
                </div>
                <div className="d-flex gap-2 flex-wrap">
                    <a className="btn btn-outline-secondary" href={`/scan/result/${id}/pdf`}>Télécharger PDF</a>
                    <Link className="btn btn-success" to="/scan">Nouveau scan</Link>
                </div>
            </div>

            <div className="row g-4 mt-1">
                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold mb-2">Image analysée</div>
                            {imageSrc && !imgFailed ? (
                                <img
                                    src={imageSrc}
                                    alt="scan"
                                    className="img-fluid rounded border"
                                    onError={() => setImgFailed(true)}
                                />
                            ) : (
                                <div className="text-muted small">Image indisponible.</div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-lg-7">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-6">
                                    <div className="p-3 bg-light rounded">
                                        <div className="text-muted small">Plante</div>
                                        <div className="fw-semibold">{diagnostic?.plante || '—'}</div>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="p-3 bg-light rounded">
                                        <div className="text-muted small">Maladie</div>
                                        <div className="fw-semibold">{diagnostic?.maladie || '—'}</div>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="p-3 bg-light rounded">
                                        <div className="text-muted small">État</div>
                                        <div className="fw-semibold">{diagnostic?.etat || '—'}</div>
                                    </div>
                                </div>
                                <div className="col-md-6">
                                    <div className="p-3 bg-light rounded">
                                        <div className="text-muted small">Risque</div>
                                        <div className="fw-semibold">{diagnostic?.niveau_risque || '—'}</div>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div className="fw-semibold">Recommandations</div>
                            {Array.isArray(diagnostic?.conseils) && diagnostic.conseils.length ? (
                                <ul className="mt-2 mb-0">
                                    {diagnostic.conseils.slice(0, 10).map((c, idx) => (
                                        <li key={idx} className="text-muted">{c}</li>
                                    ))}
                                </ul>
                            ) : (
                                <div className="text-muted small mt-2">Aucune recommandation.</div>
                            )}
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mt-3">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <div className="fw-semibold">Documentation</div>
                                    <div className="text-muted small">{doc?.scientific_name || ''}</div>
                                </div>
                                {severityMeta ? (
                                    <span className={severityMeta.className}>{severityMeta.label}</span>
                                ) : null}
                            </div>

                            {doc?.description ? <p className="text-muted mt-3 mb-2">{doc.description}</p> : null}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
