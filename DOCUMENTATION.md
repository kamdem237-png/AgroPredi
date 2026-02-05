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

## Installation & Test rapide

Cette section est destinée à permettre à un collègue de **cloner le dépôt** et de **tester rapidement** la fonctionnalité de scan.

### Points importants (reproductibilité)

- **Datasets non versionnés** : les dossiers `data/`, `dataset/` et `PlantVillage/` ne sont **pas** inclus dans Git volontairement (poids + non nécessaire pour exécuter une prédiction).
- **Scan = inférence uniquement** : l’application de scan utilise **uniquement** le modèle déjà entraîné `ai_api/model_plantvillage.pth` + `ai_api/classes.json`.
- **API Flask à démarrer manuellement** : Laravel ne démarre pas Flask. Il faut lancer l’API IA dans un terminal séparé.

### Commandes (après clonage)

1. Installer les dépendances Python de l’API IA :

   ```bash
   pip install -r ai_api/requirements.txt
   ```

2. Démarrer l’API Flask (dans un terminal dédié) :

   ```bash
   python ai_api/flask_api.py
   ```

3. Vérifier que l’API IA répond :

   - Ouvrir : `http://127.0.0.1:5001/health`

4. Installer les dépendances Laravel :

   ```bash
   composer install
   ```

5. Configurer l’environnement Laravel :

   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

6. Configurer l’accès MySQL dans `.env` puis lancer les migrations :

   ```bash
   php artisan migrate
   ```

7. Démarrer Laravel :

   ```bash
   php artisan serve
   ```

### Test fonctionnel (scan)

1. Ouvrir l’interface : `http://127.0.0.1:8000/scan`
2. Importer une **image de feuille de tomate** (formats: JPEG/PNG/JPG/GIF/BMP, max 16MB)
3. Lancer l’analyse.

Résultats attendus :

- Si l’API IA est démarrée : un diagnostic JSON est renvoyé (plante/maladie/état/confiance).
- Si l’API IA n’est pas démarrée : un message explicite est renvoyé (ex: « Service d’analyse indisponible. Veuillez démarrer l’API IA. »).

## Vérification rapide de l’installation

Deux scripts sont fournis à la racine pour vérifier que tout est prêt :

- **Windows** : `check_install.ps1`
- **Linux/macOS** : `check_install.sh`

### Lancement

- Windows (PowerShell) :
  ```powershell
  .\check_install.ps1
  ```

- Linux/macOS (terminal) :
  ```bash
  chmod +x check_install.sh
  ./check_install.sh
  ```

### Ce que vérifient les scripts

- Python 3.11+ et pip
- Installation des dépendances IA (`ai_api/requirements.txt`)
- Présence du modèle `ai_api/model_plantvillage.pth`
- Disponibilité de l’API Flask (`http://127.0.0.1:5001/health`)
- PHP et présence de Laravel (`artisan` à la racine)

### En cas d’erreur

Le script affiche des commandes exactes à lancer, par exemple :
- `pip install -r ai_api/requirements.txt`
- `python ai_api/flask_api.py`

**Important** : les datasets ne sont **pas** inclus dans Git. Le scan fonctionne avec le modèle pré-entraîné déjà présent dans `ai_api/`.

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

## Limites actuelles du système

- **Scope végétal** : le modèle est restreint à la **tomate**. Toute image d’une autre plante sera forcément classifiée dans une classe tomate ou rejetée.
- **Origine des données** : entraîné sur **PlantVillage**, un dataset académique (feuilles isolées, fonds neutres, éclairage contrôlé). Les performances peuvent chuter sur des images terrain (fond complexe, ombre, flou, multiples feuilles).
- **Pas de reconnaissance automatique de la plante** : le système suppose que l’utilisateur fournit une image de tomate. Il n’y a pas de vérification explicite de l’espèce végétale.
- **Seuil de confiance et rejet** : en cas de faible confiance (ex: < 50%), le système émet un message d’avertissement mais ne rejette pas automatiquement. Une implémentation robuste devrait rejeter les prédictions incertaines.
- **Outil d’aide à la décision** : AgroPredi est un **assistant technique**, pas un diagnostic agronomique ou médical. Les résultats doivent être validés par un expert terrain avant toute décision de traitement.

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
