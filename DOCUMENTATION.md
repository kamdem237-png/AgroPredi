# AgroPredi — Documentation

## 1.1 Présentation du projet

**AgroPredi** est une application web (Laravel + Flask + PyTorch) destinée au **diagnostic de maladies des plantes à partir d’images de feuilles**.

- **Problème résolu** : aider un agriculteur/technicien à identifier rapidement une maladie foliaire à partir d’une photo.
- **Périmètre actuel** : **tomate uniquement** (dataset PlantVillage, 10 classes).
- **Principe produit** : l’IA doit fournir un résultat lisible « métier » (plante / maladie / confiance) et être capable d’exprimer l’incertitude.

## 1.2 Architecture du projet

### Composants

- **Laravel (PHP)**
  - UI de scan : `GET /scan`
  - Upload + orchestration : `POST /scan/analyze` (envoie l’image à l’API IA)
  - Persistance : enregistre un diagnostic dans MySQL (`diagnostics`)

- **Flask (Python)**
  - API IA locale : `http://127.0.0.1:5001`
  - Endpoints :
    - `GET /health`
    - `GET /info`
    - `POST /predict` (multipart/form-data, champ `file`)

- **Modèle IA (PyTorch)**
  - **ResNet18** fine-tuné sur PlantVillage (tomate)
  - Artefacts :
    - `ai_api/model_plantvillage.pth`
    - `ai_api/classes.json`

### Flux d’exécution

1. L’utilisateur ouvre l’UI : `http://127.0.0.1:8000/scan`
2. Il upload une image.
3. Laravel stocke l’image et appelle Flask : `POST http://127.0.0.1:5001/predict`
4. Flask charge le modèle et effectue l’inférence.
5. Flask renvoie un JSON métier (attendu par Laravel).
6. Laravel enregistre le diagnostic et affiche le résultat.

## 1.3 Installation (pas à pas)

### Prérequis

- **Windows + XAMPP** (Apache + MySQL) ou équivalent
- **PHP** (compatible Laravel) + extensions usuelles
- **Composer**
- **Node.js / npm** (si build front requis)
- **Python 3.11+** (recommandé) + pip

### Installation Laravel

Dans le dossier du projet :

1. Installer les dépendances PHP :
   - `composer install`
2. Copier la config :
   - `copy .env.example .env`
3. Générer la clé :
   - `php artisan key:generate`
4. Configurer MySQL dans `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
5. Migrer la base :
   - `php artisan migrate`

### Installation Flask / PyTorch

1. Aller dans `ai_api/`
2. Installer les dépendances Python :
   - `pip install -r ai_api/requirements.txt`

> Note : `torch/torchvision` peuvent être lourds; sur Windows CPU, privilégier des wheels compatibles.

### Lancement des serveurs

- **Flask (API IA)**
  - `python ai_api/flask_api.py`
  - Vérifier : `http://127.0.0.1:5001/health`

- **Laravel**
  - `php artisan serve`
  - Ouvrir : `http://127.0.0.1:8000/scan`

## 1.4 Utilisation

### Faire un scan

1. Ouvrir `http://127.0.0.1:8000/scan`
2. Déposer une image (ou cliquer pour sélectionner)
3. Cliquer **Analyser**

### Formats acceptés

- Côté UI/Laravel : `jpeg`, `png`, `jpg`, `gif`, `bmp`
- Taille max : **16MB**

### Cas de rejet / messages possibles

Selon l’implémentation actuelle côté Flask, un message peut être retourné si :
- **confiance faible** (ex : < 50%)
- **image invalide** (fichier manquant, image illisible)

## 1.5 Limitations actuelles

- **Tomate uniquement** (10 classes)
- Modèle entraîné sur **PlantVillage** (images propres, conditions contrôlées)
- Robustesse terrain (lumière, flou, arrière-plan) : améliorable
- Pas encore de mode multi-plantes

## 1.6 Roadmap

### Étape 3 (robustesse + évaluation)
- Seuil de confiance (rejet sous seuil)
- Détection image invalide (flou/luminosité)
- Évaluation : confusion matrix + precision/recall/F1

### Étape 4 (API stable + production)
- API versionnée (`/api/v1/...`)
- Schéma JSON stable et documenté
- Logs structurés (latence, confiance, rejets)
- Serveur WSGI (waitress/gunicorn) au lieu du serveur Flask dev

### Étape 5 (multi-plantes)
- Modèle « plant identification » ou modèle multi-classes multi-plantes
- Gestion explicite « plante inconnue »
- Collecte / validation sur images terrain
