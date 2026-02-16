import React from 'react';
import { Navigate } from 'react-router-dom';
import { getAppProps } from '../lib/appProps.js';

export default function AdminRoute({ children }) {
    const { auth } = getAppProps();

    if (!auth?.check) {
        return <Navigate to="/login" replace />;
    }

    if (!auth?.user?.is_admin) {
        return <Navigate to="/dashboard" replace />;
    }

    return children;
}
