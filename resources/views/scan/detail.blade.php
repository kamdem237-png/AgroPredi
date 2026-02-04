@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <a href="{{ route('scan.history') }}" class="text-blue-600 hover:text-blue-700 mb-6 inline-block">← Retour</a>

        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Image -->
            @if($diagnostic->image_path)
            <div class="relative h-96 bg-gray-100">
                <img src="{{ asset('storage/' . $diagnostic->image_path) }}" 
                     alt="Analyse" class="w-full h-full object-cover">
            </div>
            @endif

            <!-- Détails -->
            <div class="p-8">
                <!-- En-tête -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">
                            {{ $diagnostic->plante }}
                        </h1>
                        <p class="text-2xl text-gray-600">{{ $diagnostic->maladie }}</p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-lg font-semibold {{ $diagnostic->etat === 'Sain' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $diagnostic->etat }}
                    </span>
                </div>

                <!-- Métriques -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">CONFIANCE</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $diagnostic->confiance }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">RISQUE</p>
                        <p class="text-3xl font-bold {{ $diagnostic->niveau_risque === 'Faible' ? 'text-green-600' : ($diagnostic->niveau_risque === 'Moyen' ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $diagnostic->niveau_risque }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">ANALYSÉ</p>
                        <p class="text-lg font-bold text-gray-900">{{ $diagnostic->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">ID</p>
                        <p class="text-lg font-bold text-gray-900">#{{ $diagnostic->id }}</p>
                    </div>
                </div>

                <!-- Conseils -->
                @if($diagnostic->conseils && count($diagnostic->conseils) > 0)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-lg mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">💡 Conseils de traitement</h2>
                    <ul class="space-y-3">
                        @foreach($diagnostic->conseils as $conseil)
                        <li class="flex items-start">
                            <span class="text-green-600 font-bold mr-3 text-lg">✓</span>
                            <span class="text-gray-700">{{ $conseil }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex gap-4">
                    <a href="{{ route('scan.upload') }}" 
                       class="flex-1 bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition text-center">
                        Nouvelle analyse
                    </a>
                    <button onclick="deleteDiagnostic({{ $diagnostic->id }})"
                            class="flex-1 bg-red-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-red-700 transition">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteDiagnostic(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce diagnostic ?')) {
        fetch(`/scan/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(r => {
            if (r.ok) window.location.href = '{{ route("scan.history") }}';
        });
    }
}
</script>
@endsection
