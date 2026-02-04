<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgroPRedi - @yield('title', 'Diagnostic de Maladies des Plantes')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">
                🌱 AgroPRedi
            </a>
            <div class="flex gap-6 items-center">
                <a href="{{ route('scan.upload') }}" class="text-gray-700 hover:text-green-600 font-semibold">Diagnostic</a>
                <a href="{{ route('scan.history') }}" class="text-gray-700 hover:text-green-600 font-semibold">Historique</a>
                <a href="{{ route('scan.stats') }}" class="text-gray-700 hover:text-green-600 font-semibold">Stats</a>
                <div id="aiStatus" class="ml-4 text-sm text-gray-600">AI: <span id="aiBadge" class="font-semibold">—</span></div>
            </div>
        </div>
    </nav>

    <!-- Contenu -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-8 text-center">
            <p class="mb-2">🌱 AgroPRedi - Diagnostic IA de maladies des plantes</p>
            <p class="text-gray-400 text-sm">PyTorch | ResNet50 | PlantVillage Dataset</p>
        </div>
    </footer>
    </body>
    <script>
    // Vérifier le statut de l'API Flask via l'endpoint Laravel
    async function fetchAiStatus(){
        try{
            const res = await fetch('/api/ai/health');
            if(!res.ok){ document.getElementById('aiBadge').textContent = 'down'; document.getElementById('aiBadge').className = 'text-red-600'; return; }
            const j = await res.json();
            if(j && j.status === 'ok'){ document.getElementById('aiBadge').textContent = 'ok'; document.getElementById('aiBadge').className = 'text-green-600'; }
            else { document.getElementById('aiBadge').textContent = j.status || 'unknown'; document.getElementById('aiBadge').className = 'text-yellow-600'; }
        }catch(e){ document.getElementById('aiBadge').textContent = 'error'; document.getElementById('aiBadge').className = 'text-red-600'; }
    }

    document.addEventListener('DOMContentLoaded', ()=>{ fetchAiStatus(); setInterval(fetchAiStatus, 30000); });
    </script>
    </html>
</body>
</html>
