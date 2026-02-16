import React from 'react';
import { Link } from 'react-router-dom';

export default function Home() {
    return (
        <div>
            <section className="bg-white">
                <div className="container py-5">
                    <div className="row align-items-center g-4">
                        <div className="col-lg-6">
                            <div className="badge text-bg-success-subtle text-success mb-3">IA • Diagnostic • Conseils</div>
                            <h1 className="display-5 fw-bold mb-3">
                                Diagnostique tes plantes en quelques secondes
                            </h1>
                            <p className="lead text-muted mb-4">
                                AgroPredi analyse une photo, identifie l’état de santé et propose des recommandations claires.
                                Une interface moderne, un backend Laravel sécurisé.
                            </p>
                            <div className="d-flex gap-2 flex-wrap">
                                <Link to="/scan" className="btn btn-success btn-lg">Lancer un scan</Link>
                                <Link to="/dashboard" className="btn btn-outline-success btn-lg">Voir mon dashboard</Link>
                            </div>
                            <div className="text-muted small mt-3">
                                Astuce : privilégie une photo nette en lumière naturelle.
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="card border-0 shadow-sm">
                                <div className="card-body">
                                    <div className="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div className="fw-semibold">Exemple de résultat</div>
                                            <div className="text-muted small">Score, risque, recommandations</div>
                                        </div>
                                        <div className="badge text-bg-success">Prêt</div>
                                    </div>
                                    <hr />
                                    <div className="row g-3">
                                        <div className="col-6">
                                            <div className="p-3 bg-light rounded">
                                                <div className="text-muted small">État</div>
                                                <div className="fw-semibold">Sain</div>
                                            </div>
                                        </div>
                                        <div className="col-6">
                                            <div className="p-3 bg-light rounded">
                                                <div className="text-muted small">Confiance</div>
                                                <div className="fw-semibold">92%</div>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <div className="p-3 bg-light rounded">
                                                <div className="text-muted small">Conseil</div>
                                                <div className="fw-semibold">Surveille l’arrosage et l’aération</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="container py-5">
                <div className="row g-4">
                    <div className="col-12">
                        <h2 className="fw-bold">Comment ça marche</h2>
                        <div className="text-muted">Un flux simple et efficace, optimisé pour mobile.</div>
                    </div>
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm">
                            <div className="card-body">
                                <div className="fs-2">1</div>
                                <div className="fw-semibold">Prendre une photo</div>
                                <div className="text-muted small mt-1">Caméra ou import depuis la galerie.</div>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm">
                            <div className="card-body">
                                <div className="fs-2">2</div>
                                <div className="fw-semibold">Analyse IA</div>
                                <div className="text-muted small mt-1">Modèle PyTorch via API locale.</div>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="card h-100 border-0 shadow-sm">
                            <div className="card-body">
                                <div className="fs-2">3</div>
                                <div className="fw-semibold">Recommandations</div>
                                <div className="text-muted small mt-1">Conseils pratiques et export PDF.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="bg-white border-top">
                <div className="container py-5">
                    <div className="row g-4">
                        <div className="col-12">
                            <h2 className="fw-bold">FAQ</h2>
                        </div>
                        <div className="col-md-6">
                            <div className="card border-0 shadow-sm">
                                <div className="card-body">
                                    <div className="fw-semibold">Mes données sont-elles privées ?</div>
                                    <div className="text-muted small mt-1">Oui. Les diagnostics sont liés à ton compte (`user_id`) et protégés.</div>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6">
                            <div className="card border-0 shadow-sm">
                                <div className="card-body">
                                    <div className="fw-semibold">Et si l’API IA n’est pas démarrée ?</div>
                                    <div className="text-muted small mt-1">Le scan renverra une erreur claire. Démarre l’API Flask sur `127.0.0.1:5001`.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="container py-5">
                <div className="card border-0 shadow-sm bg-success text-white">
                    <div className="card-body p-4 p-md-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div className="h3 fw-bold mb-1">Prêt à diagnostiquer ?</div>
                            <div className="opacity-75">Lance un scan et obtiens un rapport détaillé.</div>
                        </div>
                        <Link to="/scan" className="btn btn-light btn-lg">Scanner maintenant</Link>
                    </div>
                </div>
            </section>
        </div>
    );
}
