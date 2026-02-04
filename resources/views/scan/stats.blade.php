@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">📊 Statistiques</h1>
            <p class="text-gray-600">{{ $total }} diagnostic(s) au total</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-semibold">TOTAL</p>
                <p class="text-4xl font-bold text-blue-600">{{ $total }}</p>
            </div>

            <!-- Saines -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-semibold">SAINES</p>
                <p class="text-4xl font-bold text-green-600">{{ $healthy }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $total > 0 ? round(($healthy/$total)*100, 1) : 0 }}%</p>
            </div>

            <!-- Malades -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-semibold">MALADES</p>
                <p class="text-4xl font-bold text-red-600">{{ $total - $healthy }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $total > 0 ? round((($total - $healthy)/$total)*100, 1) : 0 }}%</p>
            </div>

            <!-- Plantes -->
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-semibold">ESPÈCES</p>
                <p class="text-4xl font-bold text-purple-600">{{ count($plants) }}</p>
                <p class="text-sm text-gray-500 mt-2">variétés analysées</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Plantes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">🌿 Répartition par plante</h2>
                @if(count($plants) > 0)
                <div class="space-y-3">
                    @foreach($plants as $plant)
                    <div>
                        <div class="flex justify-between mb-2">
                            <p class="font-semibold text-gray-800">{{ $plant->plante }}</p>
                            <p class="text-gray-600">{{ $plant->count }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($plant->count/$total)*100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">Aucune donnée</p>
                @endif
            </div>

            <!-- Maladies -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">🦠 Top 5 maladies</h2>
                @if(count($diseases) > 0)
                <div class="space-y-3">
                    @foreach($diseases as $disease)
                    <div>
                        <div class="flex justify-between mb-2">
                            <p class="font-semibold text-gray-800">{{ $disease->maladie }}</p>
                            <p class="text-gray-600">{{ $disease->count }}</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: {{ ($disease->count/$total)*100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">Aucune donnée</p>
                @endif
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="mt-8 text-center">
            <a href="{{ route('scan.upload') }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                ← Retour au diagnostic
            </a>
        </div>
    </div>
</div>
@endsection
