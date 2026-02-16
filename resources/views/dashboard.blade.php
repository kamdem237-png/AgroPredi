@extends('layouts.app')

@section('content')
<div class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Espace utilisateur</p>
                <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900">Dashboard</h1>
                <p class="mt-2 text-gray-600">Suivi de tes diagnostics et statistiques personnelles.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('scan.upload') }}" class="agro-btn-primary">Nouveau scan</a>
                <a href="{{ route('dashboard.history') }}" class="agro-btn-secondary">Voir historique</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="agro-card p-5">
                <p class="text-xs font-semibold text-gray-500">Total scans</p>
                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $totalScans }}</p>
            </div>
            <div class="agro-card p-5">
                <p class="text-xs font-semibold text-gray-500">Plantes saines</p>
                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $healthyScans }}</p>
            </div>
            <div class="agro-card p-5">
                <p class="text-xs font-semibold text-gray-500">Maladies graves</p>
                <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $severeScans }}</p>
            </div>
            <div class="agro-card p-5">
                <p class="text-xs font-semibold text-gray-500">Dernier scan</p>
                <p class="mt-2 text-sm font-semibold text-gray-900">
                    {{ $latestScan?->created_at?->format('d/m/Y H:i') ?? '—' }}
                </p>
                @if($latestScan)
                    <a href="{{ route('scan.result', $latestScan->id) }}" class="mt-3 inline-block text-sm font-semibold text-[--color-agro-green]">Voir résultat →</a>
                @endif
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="agro-card p-6 lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-900">Répartition des maladies détectées</h2>
                <p class="mt-1 text-sm text-gray-600">Basé sur ton historique personnel.</p>

                @if($diseaseDistribution->count() > 0)
                    <div class="mt-4 space-y-3">
                        @foreach($diseaseDistribution as $row)
                            @php
                                $pct = $totalScans > 0 ? round(($row->count / $totalScans) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ $row->maladie }}</p>
                                    <p class="text-sm text-gray-600">{{ $row->count }} ({{ $pct }}%)</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-gray-600">Aucune donnée disponible pour le moment. Lance un scan pour démarrer ton historique.</p>
                @endif
            </div>

            <div class="agro-card p-6">
                <h2 class="text-lg font-bold text-gray-900">Actions rapides</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('scan.upload') }}" class="agro-btn-primary w-full text-center">Nouveau scan</a>
                    <a href="{{ route('dashboard.history') }}" class="agro-btn-secondary w-full text-center">Voir historique</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
