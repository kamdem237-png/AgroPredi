@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- En-tête -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🌱 Diagnostic de Plantes</h1>
            <p class="text-gray-600">Analysez vos plantes avec l'IA PyTorch en temps réel</p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <form id="uploadForm" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Zone de dépôt -->
                <div id="dropZone" class="border-2 border-dashed border-green-300 rounded-lg p-8 text-center cursor-pointer hover:border-green-500 transition"
                     style="background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(59,130,246,0.05) 100%);">
                    <div class="mb-4">
                        <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-700 mb-2">Déposez votre image ici</p>
                    <p class="text-sm text-gray-500 mb-4">ou cliquez pour sélectionner</p>
                    <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">
                </div>

                <!-- Preview -->
                <div id="preview" class="hidden">
                    <img id="previewImg" src="" alt="Aperçu" class="w-full h-64 object-cover rounded-lg">
                </div>

                <!-- Bouton d'envoi -->
                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 rounded-lg hover:shadow-lg transition disabled:opacity-50"
                        disabled>
                    <span id="btnText">Analyser</span>
                    <span id="spinner" class="hidden inline-block ml-2">⚙️</span>
                </button>
            </form>

            <!-- Résultats -->
            <div id="results" class="hidden mt-8 space-y-6">
                <!-- Alerte résultat -->
                <div id="resultAlert" class="p-4 rounded-lg">
                    <p id="resultMessage" class="font-semibold"></p>
                </div>

                <!-- Détails diagnostic -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Plante</p>
                        <p id="resultPlant" class="text-2xl font-bold text-blue-600"></p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Maladie</p>
                        <p id="resultDisease" class="text-2xl font-bold text-orange-600"></p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Confiance</p>
                        <p id="resultConfidence" class="text-2xl font-bold text-purple-600"></p>
                    </div>
                    <div id="riskDiv" class="p-4 rounded-lg">
                        <p class="text-gray-600 text-sm">Niveau de risque</p>
                        <p id="resultRisk" class="text-2xl font-bold"></p>
                    </div>
                </div>

                <!-- Conseils -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-lg">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">💡 Conseils de traitement</h3>
                    <ul id="adviceList" class="space-y-2">
                        <!-- Rempli par JavaScript -->
                    </ul>
                </div>

                <!-- Bouton Nouveau diagnostic -->
                <button type="button" onclick="location.reload()" class="w-full bg-gray-600 text-white font-bold py-2 rounded-lg hover:bg-gray-700 transition">
                    Nouveau diagnostic
                </button>
            </div>
        </div>

        <!-- Liens rapides -->
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('scan.history') }}" class="text-green-600 hover:text-green-700 font-semibold">📋 Historique</a>
            <a href="{{ route('scan.stats') }}" class="text-blue-600 hover:text-blue-700 font-semibold">📊 Statistiques</a>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('uploadForm');
const dropZone = document.getElementById('dropZone');
const imageInput = document.getElementById('imageInput');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('previewImg');
const submitBtn = document.getElementById('submitBtn');
const results = document.getElementById('results');

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-green-500', 'bg-green-50');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-green-500', 'bg-green-50');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-green-500', 'bg-green-50');
    imageInput.files = e.dataTransfer.files;
    handleFileSelect();
});

dropZone.addEventListener('click', () => imageInput.click());
imageInput.addEventListener('change', handleFileSelect);

function handleFileSelect() {
    if (imageInput.files.length > 0) {
        const file = imageInput.files[0];

        // Validation client-side: type et taille
        const allowed = ['image/png','image/jpeg','image/jpg','image/gif','image/bmp'];
        const maxSize = 16 * 1024 * 1024; // 16MB
        if (!allowed.includes(file.type)) {
            alert('Format non autorisé. Utilisez PNG, JPG, GIF ou BMP.');
            imageInput.value = '';
            return;
        }
        if (file.size > maxSize) {
            alert('Fichier trop volumineux. Taille maximale: 16MB');
            imageInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
            submitBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    }
}

// Envoi du formulaire
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(form);
    submitBtn.disabled = true;
    document.getElementById('btnText').textContent = 'Analyse en cours...';
    document.getElementById('spinner').classList.remove('hidden');

    try {
        // Double-check côté client avant envoi
        const file = imageInput.files[0];
        if (!file) { alert('Aucune image sélectionnée'); return; }

        const response = await fetch('{{ route("scan.analyze") }}', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.prediction) {
            const pred = data.prediction;
            
            // État de santé
            const isHealthy = pred.etat === 'Sain';
            const statusColor = isHealthy ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            document.getElementById('resultAlert').className = `p-4 rounded-lg ${statusColor}`;
            document.getElementById('resultMessage').textContent = isHealthy ? '✅ Plante saine' : '⚠️ Plante malade';
            
            // Infos
            document.getElementById('resultPlant').textContent = pred.plante || 'N/A';
            document.getElementById('resultDisease').textContent = pred.maladie || 'N/A';
            document.getElementById('resultConfidence').textContent = (pred.confiance || 0) + '%';
            
            // Risque
            const riskDiv = document.getElementById('riskDiv');
            const riskColors = {
                'Faible': 'bg-green-50 text-green-600',
                'Moyen': 'bg-yellow-50 text-yellow-600',
                'Élevé': 'bg-red-50 text-red-600'
            };
            riskDiv.className = `p-4 rounded-lg ${riskColors[pred.niveau_risque] || 'bg-gray-50'}`;
            document.getElementById('resultRisk').textContent = pred.niveau_risque || 'N/A';
            
            // Conseils
            const adviceList = document.getElementById('adviceList');
            adviceList.innerHTML = '';
            (pred.conseils || []).forEach(conseil => {
                const li = document.createElement('li');
                li.className = 'flex items-start';
                li.innerHTML = `<span class="text-green-600 mr-3">✓</span><span>${conseil}</span>`;
                adviceList.appendChild(li);
            });
            
            results.classList.remove('hidden');
        } else {
            alert('Erreur: ' + (data.message || 'Analyse échouée'));
        }
    } catch (error) {
        alert('Erreur: ' + error.message);
    } finally {
        submitBtn.disabled = false;
        document.getElementById('btnText').textContent = 'Analyser';
        document.getElementById('spinner').classList.add('hidden');
    }
});
</script>
@endsection
