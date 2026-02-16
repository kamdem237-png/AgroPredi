import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { getAppProps } from '../lib/appProps.js';

export default function ProtectedRoute({ children }) {
    const location = useLocation();
    const { auth } = getAppProps();

    if (!auth?.check) {
        return <Navigate to="/login" replace state={{ from: location }} />;
    }

    return children;
}
