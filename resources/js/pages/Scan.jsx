import React, { useMemo, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Loader from '../components/Loader.jsx';
import { getCsrfToken } from '../lib/appProps.js';

export default function Scan() {
    const navigate = useNavigate();

    const videoRef = useRef(null);
    const canvasRef = useRef(null);
    const [activeTab, setActiveTab] = useState('upload');

    const [file, setFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState(null);

    const [stream, setStream] = useState(null);

    const canUseCamera = useMemo(() => {
        return typeof navigator !== 'undefined' && !!navigator.mediaDevices?.getUserMedia;
    }, []);

    const stopCamera = () => {
        if (stream) {
            stream.getTracks().forEach((t) => t.stop());
            setStream(null);
        }
    };

    const startCamera = async () => {
        setError(null);
        try {
            const s = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
            setStream(s);
            if (videoRef.current) {
                videoRef.current.srcObject = s;
                await videoRef.current.play();
            }
        } catch (e) {
            setError("Accès caméra refusé ou indisponible. Essayez l'import.");
        }
    };

    const onPickFile = (f) => {
        setFile(f);
        setPreviewUrl(f ? URL.createObjectURL(f) : null);
    };

    const sendImage = async (blobOrFile) => {
        setBusy(true);
        setError(null);

        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('image', blobOrFile, blobOrFile?.name || 'capture.jpg');

        const resp = await fetch('/scan/analyze', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        }).catch(() => null);

        if (!resp) {
            setError('Impossible de contacter le serveur.');
            setBusy(false);
            return;
        }

        let payload = null;
        try {
            payload = await resp.json();
        } catch {
            payload = null;
        }

        if (!resp.ok || !payload?.success) {
            setError(payload?.message || payload?.error || 'Analyse échouée.');
            setBusy(false);
            return;
        }

        const id = payload.diagnostic_id;
        setBusy(false);
        navigate(`/result/${id}`);
    };

    const captureAndSend = async () => {
        if (!videoRef.current || !canvasRef.current) return;
        const video = videoRef.current;
        const canvas = canvasRef.current;
        const w = video.videoWidth || 640;
        const h = video.videoHeight || 480;
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, w, h);

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.9));
        if (!blob) {
            setError('Capture impossible.');
            return;
        }
        await sendImage(blob);
    };

    return (
        <div className="container py-4">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h1 className="h3 fw-bold mb-1">Scanner une plante</h1>
                            <div className="text-muted">Importe une image ou utilise la caméra.</div>
                        </div>
                        <div className="text-muted small">Endpoint: <code>/scan/analyze</code></div>
                    </div>

                    {error ? <div className="alert alert-danger mt-3">{error}</div> : null}

                    <div className="card border-0 shadow-sm mt-3">
                        <div className="card-body">
                            <ul className="nav nav-pills gap-2">
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${activeTab === 'upload' ? 'active' : ''}`}
                                        onClick={() => {
                                            stopCamera();
                                            setActiveTab('upload');
                                        }}
                                        type="button"
                                    >
                                        Import
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${activeTab === 'camera' ? 'active' : ''}`}
                                        onClick={() => {
                                            setActiveTab('camera');
                                        }}
                                        type="button"
                                        disabled={!canUseCamera}
                                    >
                                        Caméra
                                    </button>
                                </li>
                            </ul>

                            {busy ? <Loader label="Analyse en cours…" /> : null}

                            {activeTab === 'upload' ? (
                                <div className="mt-3">
                                    <input
                                        className="form-control"
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => onPickFile(e.target.files?.[0] || null)}
                                    />

                                    {previewUrl ? (
                                        <div className="mt-3">
                                            <div className="text-muted small mb-2">Aperçu</div>
                                            <img src={previewUrl} alt="preview" className="img-fluid rounded border" />
                                        </div>
                                    ) : null}

                                    <button
                                        className="btn btn-success mt-3"
                                        disabled={!file || busy}
                                        onClick={() => sendImage(file)}
                                        type="button"
                                    >
                                        Lancer l'analyse
                                    </button>
                                </div>
                            ) : (
                                <div className="mt-3">
                                    <div className="d-flex gap-2 flex-wrap mb-3">
                                        <button className="btn btn-outline-success" type="button" onClick={startCamera} disabled={!!stream || busy}>
                                            Démarrer
                                        </button>
                                        <button className="btn btn-outline-secondary" type="button" onClick={stopCamera} disabled={!stream || busy}>
                                            Stop
                                        </button>
                                        <button className="btn btn-success" type="button" onClick={captureAndSend} disabled={!stream || busy}>
                                            Capturer & analyser
                                        </button>
                                    </div>

                                    <div className="ratio ratio-16x9 bg-dark rounded overflow-hidden">
                                        <video ref={videoRef} className="w-100" playsInline muted />
                                    </div>
                                    <canvas ref={canvasRef} className="d-none" />

                                    {!canUseCamera ? (
                                        <div className="alert alert-warning mt-3">Caméra non supportée par ce navigateur.</div>
                                    ) : null}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    <div className="card border-0 shadow-sm">
                        <div className="card-body">
                            <div className="fw-semibold">Conseils pour une bonne photo</div>
                            <ul className="text-muted small mt-2 mb-0">
                                <li>Lumière naturelle, sans flash agressif.</li>
                                <li>Feuille nette, sans flou de mouvement.</li>
                                <li>Cadre serré sur la zone touchée.</li>
                                <li>Évite les arrière-plans trop chargés.</li>
                            </ul>
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm mt-3">
                        <div className="card-body">
                            <div className="fw-semibold">PDF</div>
                            <div className="text-muted small mt-1">
                                Le bouton PDF est disponible sur la page de résultat.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
