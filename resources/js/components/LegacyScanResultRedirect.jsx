import React from 'react';
import { Navigate, useParams } from 'react-router-dom';

export default function LegacyScanResultRedirect() {
    const { id } = useParams();
    return <Navigate to={`/result/${id}`} replace />;
}
