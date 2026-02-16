@extends('layouts.app')

@section('content')
<div class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Espace utilisateur</p>
                <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900">Historique</h1>
                <p class="mt-2 text-gray-600">{{ $diagnostics->total() }} diagnostic(s) enregistré(s).</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('scan.upload') }}" class="agro-btn-primary">Nouveau scan</a>
                <a href="{{ route('dashboard') }}" class="agro-btn-secondary">Retour dashboard</a>
            </div>
        </div>

        @if($diagnostics->count() > 0)
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($diagnostics as $diag)
                    <div class="agro-card overflow-hidden">
                        @if($diag->image_path)
                            <img src="{{ asset('storage/' . $diag->image_path) }}" alt="Scan" class="w-full h-44 object-cover">
                        @endif

                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-500">Maladie</p>
                                    <p class="mt-1 text-lg font-extrabold text-gray-900">{{ $diag->maladie }}</p>
                                    <p class="mt-1 text-sm text-gray-600">Plante : <span class="font-semibold text-gray-800">{{ $diag->plante }}</span></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $diag->etat === 'Sain' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $diag->etat }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-600">Score</p>
                                    <p class="font-semibold text-gray-900">{{ $diag->confiance }}%</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Date</p>
                                    <p class="font-semibold text-gray-900">{{ $diag->created_at?->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between">
                                <a href="{{ route('scan.result', $diag->id) }}" class="text-sm font-semibold text-[--color-agro-green]">Voir résultat →</a>
                                <span class="text-xs text-gray-500">#{{ $diag->id }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $diagnostics->links() }}
            </div>
        @else
            <div class="mt-10 agro-card p-8 text-center">
                <p class="text-gray-700 font-semibold">Aucun diagnostic enregistré pour le moment.</p>
                <p class="mt-2 text-gray-600">Fais un scan pour construire ton historique.</p>
                <div class="mt-6">
                    <a href="{{ route('scan.upload') }}" class="agro-btn-primary">Nouveau scan</a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
