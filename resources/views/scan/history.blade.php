@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📋 Historique des diagnostics</h1>
            <p class="text-gray-600">{{ $diagnostics->total() }} analyse(s) effectuée(s)</p>
        </div>

        @if($diagnostics->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($diagnostics as $diag)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                    <!-- Image -->
                    @if($diag->image_path)
                    <img src="{{ asset('storage/' . $diag->image_path) }}" 
                         alt="Scan" class="w-full h-48 object-cover rounded-t-lg">
                    @endif

                    <!-- Contenu -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-lg">{{ $diag->plante }}</p>
                                <p class="text-sm text-gray-600">{{ $diag->maladie }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $diag->etat === 'Sain' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $diag->etat }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                            <div>
                                <p class="text-gray-600">Confiance</p>
                                <p class="font-semibold">{{ $diag->confiance }}%</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Risque</p>
                                <p class="font-semibold">{{ $diag->niveau_risque }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mb-4">{{ $diag->created_at->diffForHumans() }}</p>

                        <a href="{{ route('scan.result', $diag->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                            Voir résultat →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $diagnostics->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-600 text-lg">Aucun diagnostic disponible</p>
                <a href="{{ route('scan.upload') }}" class="text-blue-600 hover:text-blue-700 font-semibold mt-4 inline-block">
                    Faire une analyse →
                </a>
            </div>
        @endif

        <!-- Lien retour -->
        <div class="mt-8 text-center">
            <a href="{{ route('scan.upload') }}" class="text-green-600 hover:text-green-700 font-semibold">
                ← Retour au diagnostic
            </a>
        </div>
    </div>
</div>
@endsection
