import React, { Suspense, lazy } from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import ProtectedRoute from './components/ProtectedRoute.jsx';
import AdminRoute from './components/AdminRoute.jsx';
import Loader from './components/Loader.jsx';
import LegacyScanResultRedirect from './components/LegacyScanResultRedirect.jsx';

const Home = lazy(() => import('./pages/Home.jsx'));
const Scan = lazy(() => import('./pages/Scan.jsx'));
const Result = lazy(() => import('./pages/Result.jsx'));
const Dashboard = lazy(() => import('./pages/Dashboard.jsx'));
const History = lazy(() => import('./pages/History.jsx'));
const Profile = lazy(() => import('./pages/Profile.jsx'));
const Admin = lazy(() => import('./pages/Admin.jsx'));
const AdminDiagnostics = lazy(() => import('./pages/AdminDiagnostics.jsx'));
const Login = lazy(() => import('./pages/Login.jsx'));
const Register = lazy(() => import('./pages/Register.jsx'));

export default function AppRoutes() {
    return (
        <Suspense fallback={<Loader fullscreen />}>
            <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/scan" element={<Scan />} />

                <Route path="/result/:id" element={<Result />} />
                <Route path="/scan/result/:id" element={<LegacyScanResultRedirect />} />

                <Route
                    path="/dashboard"
                    element={
                        <ProtectedRoute>
                            <Dashboard />
                        </ProtectedRoute>
                    }
                />
                <Route
                    path="/history"
                    element={
                        <ProtectedRoute>
                            <History />
                        </ProtectedRoute>
                    }
                />

                <Route
                    path="/profile"
                    element={
                        <ProtectedRoute>
                            <Profile />
                        </ProtectedRoute>
                    }
                />

                <Route
                    path="/admin"
                    element={
                        <AdminRoute>
                            <Admin />
                        </AdminRoute>
                    }
                />

                <Route
                    path="/admin/diagnostics"
                    element={
                        <AdminRoute>
                            <AdminDiagnostics />
                        </AdminRoute>
                    }
                />

                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />

                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </Suspense>
    );
}
