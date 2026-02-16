import React from 'react';

export default function StatCard({ title, value, hint, icon }) {
    return (
        <div className="card shadow-sm border-0 h-100">
            <div className="card-body">
                <div className="d-flex align-items-center justify-content-between">
                    <div>
                        <div className="text-muted small">{title}</div>
                        <div className="fs-3 fw-semibold">{value}</div>
                    </div>
                    <div className="text-success opacity-75 fs-2">{icon}</div>
                </div>
                {hint ? <div className="text-muted small mt-2">{hint}</div> : null}
            </div>
        </div>
    );
}
