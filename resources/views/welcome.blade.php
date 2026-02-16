@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-green-50 via-white to-green-50">
    <section class="min-h-[calc(100vh-72px)] flex items-center">
        <div class="max-w-7xl mx-auto px-4 py-16 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900">
                        Diagnostiquez les maladies des plantes grâce à l’Intelligence Artificielle
                    </h1>
                    <p class="mt-5 text-lg text-gray-600 max-w-xl">
                        Prenez une photo ou importez une image pour obtenir un diagnostic en quelques secondes.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('scan.upload') }}" class="agro-btn-primary">Scanner maintenant</a>

                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="agro-btn-outline">Se connecter</a>
                        @else
                            <a href="#" class="agro-btn-outline" aria-disabled="true">Se connecter</a>
                        @endif
                    </div>
                </div>

                <div class="agro-card p-8">
                    <div class="rounded-xl bg-[--color-agro-gray] aspect-[16/11] flex items-center justify-center">
                        <div class="text-center">
                            <div class="mx-auto h-14 w-14 rounded-2xl bg-white ring-1 ring-black/5 flex items-center justify-center">
                                <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 21s7-4.35 7-11a7 7 0 0 0-14 0c0 6.65 7 11 7 11Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" class="text-[--color-agro-green]"/>
                                    <path d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2" class="text-[--color-agro-green]"/>
                                </svg>
                            </div>
                            <p class="mt-4 text-sm font-semibold text-gray-800">Illustration neutre</p>
                            <p class="text-sm text-gray-500">placeholder</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Comment ça marche</h2>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="agro-card p-6">
                    <div class="h-12 w-12 rounded-xl bg-[--color-agro-gray] flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" class="text-[--color-agro-green]"/>
                            <path d="M8 16l3.5-3.5a2 2 0 0 1 2.8 0L18 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[--color-agro-green]"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-gray-900">Importer ou Scanner</h3>
                    <p class="mt-2 text-sm text-gray-600">Ajoutez une image ou utilisez la caméra sur mobile/tablette.</p>
                </div>

                <div class="agro-card p-6">
                    <div class="h-12 w-12 rounded-xl bg-[--color-agro-gray] flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="text-[--color-agro-green]"/>
                            <path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2" class="text-[--color-agro-green]"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-gray-900">Analyse par IA</h3>
                    <p class="mt-2 text-sm text-gray-600">Le serveur IA traite l’image et renvoie une prédiction.</p>
                </div>

                <div class="agro-card p-6">
                    <div class="h-12 w-12 rounded-xl bg-[--color-agro-gray] flex items-center justify-center">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 7 10 17l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[--color-agro-green]"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-bold text-gray-900">Résultat & recommandations</h3>
                    <p class="mt-2 text-sm text-gray-600">Diagnostic, niveau de risque et recommandations.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Avantages</h2>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="agro-card p-5">
                    <p class="font-semibold text-gray-900">Rapide</p>
                    <p class="mt-1 text-sm text-gray-600">Analyse en quelques secondes.</p>
                </div>
                <div class="agro-card p-5">
                    <p class="font-semibold text-gray-900">Accessible</p>
                    <p class="mt-1 text-sm text-gray-600">Interface simple et claire.</p>
                </div>
                <div class="agro-card p-5">
                    <p class="font-semibold text-gray-900">Historique sécurisé</p>
                    <p class="mt-1 text-sm text-gray-600">Conservation possible via compte.</p>
                </div>
                <div class="agro-card p-5">
                    <p class="font-semibold text-gray-900">Multi-plateforme</p>
                    <p class="mt-1 text-sm text-gray-600">Desktop, mobile, tablette.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="agro-card p-10 text-center">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Essayez AgroPredi dès maintenant</h2>
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('scan.upload') }}" class="agro-btn-primary">Scanner une plante</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
