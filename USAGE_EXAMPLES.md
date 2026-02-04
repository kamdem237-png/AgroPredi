# 📖 EXEMPLES D'UTILISATION - AGROPREDI

## 1️⃣ Test du module scan.py (en ligne de commande)

```python
# En Python REPL
import sys
sys.path.insert(0, 'ai_api')

from ai_api.scan import scan_image
import json

# Scanner une image
result = scan_image('data/test_image.jpg')

print(json.dumps(result, ensure_ascii=False, indent=2))
```

**Sortie attendue :**
```json
{
  "plante": "Tomate",
  "etat": "Malade",
  "maladie": "Bacterial spot",
  "confiance": 0.89,
  "niveau_risque": "Moyen",
  "conseils": [
    "Isolez les plantes atteintes.",
    "Retirez les feuilles fortement endommagées."
  ]
}
```

---

## 2️⃣ Test de l'API Flask (curl)

```bash
# Test simple
curl -F "image=@photo_feuille.jpg" http://127.0.0.1:5001/scan

# Test avec save output
curl -F "image=@photo_feuille.jpg" http://127.0.0.1:5001/scan | python -m json.tool

# Test avec verbose
curl -v -F "image=@photo_feuille.jpg" http://127.0.0.1:5001/scan
```

---

## 3️⃣ Intégration dans Laravel (ScanController)

### Cas d'usage 1 : Upload et diagnostic

```php
// routes/web.php
Route::post('/scan/analyze', [ScanController::class, 'scan']);

// Utilisateur accède http://127.0.0.1:8000/scan
// ↓
// Upload image
// ↓
// ScanController::scan() est appelé
// ↓
// Image envoyée à Flask
// ↓
// Diagnostic stocké en BD
// ↓
// Résultat retourné au frontend
```

### Cas d'usage 2 : Récupérer l'historique

```php
// routes/web.php
Route::get('/scan/history', [ScanController::class, 'history']);

// GET http://127.0.0.1:8000/scan/history
// ↓
// Retourne les 20 derniers diagnostics (JSON paginé)

// Réponse:
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "plante": "Tomate",
        "maladie": "Bacterial spot",
        "etat": "Malade",
        "confiance": 0.89,
        "niveau_risque": "Moyen",
        "conseils": [...],
        "created_at": "2026-02-01 10:30:45"
      },
      ...
    ],
    "links": {
      "first": "http://127.0.0.1:8000/scan/history?page=1",
      "last": "http://127.0.0.1:8000/scan/history?page=3",
      "next": "http://127.0.0.1:8000/scan/history?page=2"
    }
  }
}
```

### Cas d'usage 3 : Statistiques

```php
// routes/web.php
Route::get('/scan/stats', [ScanController::class, 'stats']);

// GET http://127.0.0.1:8000/scan/stats
// ↓
// Retourne les statistiques globales

// Réponse:
{
  "success": true,
  "stats": {
    "total_scans": 45,
    "healthy": 12,
    "by_plant": {
      "Tomate": 28,
      "Maïs": 17
    },
    "by_disease": {
      "Bacterial spot": 10,
      "Early blight": 8,
      "Common rust": 7,
      "Late blight": 6
    }
  }
}
```

---

## 4️⃣ Requêtes Eloquent (Tinker)

```bash
# Lancer Tinker
php artisan tinker

# Tous les diagnostics
Diagnostic::all()

# Diagnostics malades
Diagnostic::where('etat', 'Malade')->get()

# Diagnostics à risque élevé
Diagnostic::where('niveau_risque', 'Élevé')->get()

# Par plante
Diagnostic::where('plante', 'Tomate')->get()

# Dernier diagnostic
Diagnostic::latest()->first()

# Compter par plante
Diagnostic::selectRaw('plante, COUNT(*) as count')
  ->groupBy('plante')
  ->get()

# Les plus confiants (>90%)
Diagnostic::where('confiance', '>', 0.9)->get()

# Supprimer les anciens (>30 jours)
Diagnostic::where('created_at', '<', now()->subDays(30))->delete()
```

---

## 5️⃣ Scénario utilisateur complet

### Alice cultive des tomates en serre

**Étape 1 : Accès à l'interface**
```
Alice ouvre http://127.0.0.1:8000/scan
```

**Étape 2 : Upload d'image**
```
Alice photographie une feuille suspecte
Clique sur "Choisir une image"
Upload photo_feuille_tomate.jpg
```

**Étape 3 : Analyse IA**
```
Frontend envoie:
  POST /scan/analyze
  Content-Type: multipart/form-data
  image: [binary data]

ScanController reçoit:
  → Valide l'image
  → Sauvegarde temporaire
  → Envoie à Flask

Flask reçoit:
  → Charge modèle TensorFlow
  → Prédit la classe
  → Retourne JSON

Laravel reçoit:
  → Valide la réponse
  → Sauvegarde en BD
  → Retourne au frontend
```

**Étape 4 : Affichage du résultat**
```
Frontend reçoit:
{
  "plante": "Tomate",
  "etat": "Malade",
  "maladie": "Bacterial spot",
  "confiance": 0.92,
  "niveau_risque": "Élevé",
  "conseils": [
    "Isolez les plantes atteintes pour limiter la propagation.",
    "Retirez et détruisez les feuilles fortement endommagées.",
    "Évitez l'arrosage par aspersion et favorisez l'arrosage au sol."
  ]
}

Alice voit:
  ✓ Plante: Tomate
  ✓ État: Malade
  ✓ Maladie: Bacterial spot
  ✓ Confiance: 92%
  ✓ Risque: 🔴 Élevé
  ✓ Conseils à suivre
```

**Étape 5 : Historique**
```
Alice clique "Voir l'historique"
Accède à http://127.0.0.1:8000/scan/history
Voit tous ses diagnostics précédents
Peut filtrer par date, plante, maladie
```

---

## 6️⃣ Intégration dans une autre application

Si vous créez un autre service qui veut utiliser AgroPRedi :

### Via l'API Flask

```python
import requests

# Analyser une image
with open('image.jpg', 'rb') as img:
    files = {'image': img}
    response = requests.post('http://127.0.0.1:5001/scan', files=files)
    
result = response.json()
print(f"Maladie détectée: {result['maladie']}")
print(f"Risque: {result['niveau_risque']}")
```

### Via Laravel HTTP Client

```php
use Illuminate\Support\Facades\Http;

$response = Http::attach(
    'image', 
    fopen('image.jpg', 'r'), 
    'image.jpg'
)->post('http://127.0.0.1:5001/scan');

if ($response->successful()) {
    $diagnosis = $response->json();
    // Utiliser $diagnosis
}
```

### Via la base de données

```php
// Récupérer les diagnostics directement
$recentDiagnosis = Diagnostic::latest()
    ->where('plante', 'Tomate')
    ->where('etat', 'Malade')
    ->limit(10)
    ->get();

foreach ($recentDiagnosis as $diag) {
    echo "Date: " . $diag->created_at . "\n";
    echo "Confiance: " . ($diag->confiance * 100) . "%\n";
}
```

---

## 7️⃣ Cas d'usage avancé : Batch processing

Analyser 100 images à la fois :

```python
# batch_scan.py
import os
import json
import time
from pathlib import Path
from ai_api.scan import scan_image

image_dir = Path('images_to_process')
results = []

for image_path in image_dir.glob('*.jpg'):
    try:
        result = scan_image(str(image_path))
        result['image'] = image_path.name
        results.append(result)
        print(f"✓ {image_path.name}")
    except Exception as e:
        print(f"✗ {image_path.name}: {e}")

# Sauvegarder les résultats
with open('batch_results.json', 'w') as f:
    json.dump(results, f, indent=2)

# Statistiques
malades = sum(1 for r in results if r['etat'] == 'Malade')
sains = sum(1 for r in results if r['etat'] == 'Saîne')
print(f"\nRésumé: {malades} malades, {sains} sains")
```

---

## 8️⃣ Cas d'usage : Monitoring de champ

```php
// Commands/MonitorFieldCommand.php - À exécuter via scheduler

class MonitorFieldCommand extends Command
{
    public function handle()
    {
        // Récupérer les diagnostics du jour
        $today = Diagnostic::whereDate('created_at', today())
            ->where('niveau_risque', 'Élevé')
            ->get();
        
        if ($today->count() > 0) {
            // Envoyer alerte email
            Mail::send('emails.alert', 
                ['diagnostics' => $today], 
                function($m) {
                    $m->to('farmer@agropredi.com')
                      ->subject('⚠️ Plantes à risque détectées');
                }
            );
        }
    }
}

// config/schedule.php
$schedule->command('monitor:field')->dailyAt('06:00');
```

---

## 9️⃣ Dashboard analytique (optionnel)

```php
// DashboardController.php

public function index()
{
    $stats = [
        'total_scans' => Diagnostic::count(),
        'today' => Diagnostic::whereDate('created_at', today())->count(),
        'sick_ratio' => round(
            Diagnostic::where('etat', 'Malade')->count() / 
            Diagnostic::count() * 100, 2
        ),
        'top_disease' => Diagnostic::selectRaw('maladie, COUNT(*) as count')
            ->where('maladie', '<>', null)
            ->groupBy('maladie')
            ->orderBy('count', 'desc')
            ->first(),
        'avg_confidence' => round(
            Diagnostic::avg('confiance') * 100, 2
        ),
    ];
    
    return view('dashboard.index', compact('stats'));
}
```

---

## 🔟 Fichier batch SQL pour importer les résultats

```sql
-- Importer les diagnostics en masse depuis CSV
LOAD DATA LOCAL INFILE 'diagnostics.csv'
INTO TABLE diagnostics
FIELDS TERMINATED BY ','
IGNORE 1 ROWS
(image_path, plante, maladie, etat, confiance, niveau_risque, conseils);

-- Vérifier les doublons
SELECT image_path, COUNT(*) 
FROM diagnostics 
GROUP BY image_path 
HAVING COUNT(*) > 1;

-- Statistiques
SELECT 
    plante,
    etat,
    COUNT(*) as count,
    ROUND(AVG(confiance) * 100, 2) as avg_confidence
FROM diagnostics
GROUP BY plante, etat;
```

---

**Ces exemples couvrent :**
✅ Tests locaux (Python, curl)  
✅ Utilisation web (Laravel)  
✅ Intégration (APIs externes)  
✅ Batch processing  
✅ Monitoring  
✅ Analytics  
✅ Import/export  

À adapter selon vos besoins !
