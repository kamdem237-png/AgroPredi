import React from 'react';

export default function Loader({ fullscreen = false, label = 'Chargement…' }) {
    const content = (
        <div className="d-flex flex-column align-items-center justify-content-center gap-3 py-5">
            <div className="spinner-border text-success" role="status" aria-hidden="true" />
            <div className="text-muted">{label}</div>
        </div>
    );

    if (!fullscreen) return content;

    return (
        <div className="d-flex align-items-center justify-content-center" style={{ minHeight: '70vh' }}>
            {content}
        </div>
    );
}
