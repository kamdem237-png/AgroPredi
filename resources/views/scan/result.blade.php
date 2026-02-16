@extends('layouts.app')

@section('content')
@php
    $confidence = (float) ($diagnostic->confiance ?? 0);
    $confidenceBadge = $confidence > 80 ? 'agro-badge-success' : ($confidence >= 60 ? 'agro-badge-warning' : 'agro-badge-danger');

    $etatLabel = 'Malade';
    if (($diagnostic->etat ?? '') === 'Sain') {
        $etatLabel = 'Sain';
    } else {
        $risk = $diagnostic->niveau_risque ?? '';
        if ($risk === 'Moyen') {
            $etatLabel = 'Malade léger';
        } elseif ($risk === 'Élevé') {
            $etatLabel = 'Malade grave';
        } else {
            $etatLabel = 'Malade';
        }
    }
@endphp

<div class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Résultat du diagnostic</p>
                <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900">
                    {{ $diagnostic->maladie }}
                </h1>
                @if(!empty($doc['scientific_name']))
                    <p class="mt-2 text-gray-600">
                        Nom scientifique : <span class="font-semibold text-gray-800">{{ $doc['scientific_name'] }}</span>
                    </p>
                @endif
                <p class="mt-2 text-gray-600">
                    Plante : <span class="font-semibold text-gray-800">{{ $diagnostic->plante }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    Date : <span class="font-semibold text-gray-800">{{ $diagnostic->created_at?->format('d/m/Y H:i') }}</span>
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('scan.result.pdf', $diagnostic->id) }}" class="agro-btn-primary">
                    Télécharger le diagnostic en PDF
                </a>
                <a href="{{ route('scan.upload') }}" class="agro-btn-outline">
                    Nouveau scan
                </a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="agro-card overflow-hidden">
                <div class="p-6 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Image analysée</h2>
                    <span class="text-sm text-gray-500">#{{ $diagnostic->id }}</span>
                </div>
                <div class="bg-[--color-agro-gray]">
                    <img src="{{ asset('storage/' . $diagnostic->image_path) }}" alt="Image analysée" class="w-full h-80 object-cover">
                </div>
            </div>

            <div class="space-y-6">
                <div class="agro-card p-6">
                    <h2 class="text-lg font-bold text-gray-900">Synthèse</h2>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="agro-card p-4 bg-white">
                            <p class="text-xs font-semibold text-gray-500">Confiance</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-2xl font-extrabold text-gray-900">{{ number_format($confidence, 2) }}%</span>
                                <span class="{{ $confidenceBadge }}">{{ $confidence > 80 ? 'Élevée' : ($confidence >= 60 ? 'Moyenne' : 'Faible') }}</span>
                            </div>
                        </div>

                        <div class="agro-card p-4 bg-white">
                            <p class="text-xs font-semibold text-gray-500">État de la plante</p>
                            <p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $etatLabel }}</p>
                            <p class="mt-1 text-sm text-gray-600">Risque : <span class="font-semibold">{{ $diagnostic->niveau_risque }}</span></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-xs font-semibold text-gray-500">Niveau de gravité</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            @php
                                $severity = strtolower((string)($doc['severity'] ?? ''));
                                $severityLabel = $severity === 'high' ? 'Élevé' : ($severity === 'low' ? 'Faible' : 'Modéré');
                            @endphp
                            {{ $severityLabel }}
                        </p>
                    </div>
                </div>

                <div class="agro-card p-6">
                    <h2 class="text-lg font-bold text-gray-900">Recommandations IA</h2>

                    @if(is_array($diagnostic->conseils) && count($diagnostic->conseils) > 0)
                        <ul class="mt-4 space-y-3">
                            @foreach($diagnostic->conseils as $conseil)
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-6 w-6 rounded-full bg-[--color-agro-gray] flex items-center justify-center text-[--color-agro-green] font-bold">✓</span>
                                    <span class="text-gray-700">{{ $conseil }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-gray-600">Aucune recommandation disponible.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-8 agro-card p-6">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <h2 class="text-xl font-extrabold text-gray-900">Documentation complète de la maladie</h2>
                <span class="text-sm text-gray-500">Référence locale</span>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <h3 class="font-bold text-gray-900">Description détaillée</h3>
                        <p class="mt-2 text-gray-700">{{ $doc['description'] ?? 'Documentation en cours de mise à jour.' }}</p>
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900">Causes</h3>
                        @if(!empty($doc['causes']))
                            <ul class="mt-2 space-y-2">
                                @foreach($doc['causes'] as $item)
                                    <li class="text-gray-700">- {{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900">Symptômes</h3>
                        @if(!empty($doc['symptoms']))
                            <ul class="mt-2 space-y-2">
                                @foreach($doc['symptoms'] as $item)
                                    <li class="text-gray-700">- {{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <h3 class="font-bold text-gray-900">Méthodes de prévention</h3>
                        @if(!empty($doc['prevention']))
                            <ul class="mt-2 space-y-2">
                                @foreach($doc['prevention'] as $item)
                                    <li class="text-gray-700">- {{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900">Traitements recommandés</h3>
                        @if(!empty($doc['treatment']))
                            <ul class="mt-2 space-y-2">
                                @foreach($doc['treatment'] as $item)
                                    <li class="text-gray-700">- {{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900">Impact sur le rendement</h3>
                        @if(!empty($doc['impact']))
                            <p class="mt-2 text-gray-700">{{ $doc['impact'] }}</p>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="font-bold text-gray-900">Bonnes pratiques agricoles</h3>
                        @if(!empty($doc['best_practices']))
                            <ul class="mt-2 space-y-2">
                                @foreach($doc['best_practices'] as $item)
                                    <li class="text-gray-700">- {{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 text-gray-600">Documentation en cours de mise à jour.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="{{ route('scan.upload') }}" class="agro-btn-outline">Retour vers /scan</a>
        </div>
    </div>
</div>
@endsection
