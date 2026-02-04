# 🚀 GUIDE DE DÉMARRAGE RAPIDE - AGROPREDI

Suivez ces étapes pour mettre en place le système complet.

## ⏱️ Étapes rapides (10-15 min setup + 30-60 min entraînement)

### 1️⃣ Prérequis

```bash
# Vérifier Python 3.8+
python --version

# Vérifier PHP 8.2+
php --version

# Vérifier MySQL/SQLite
```

### 2️⃣ Configuration Laravel

```bash
cd C:\xampp\htdocs\AgroPredi
cp .env.example .env
php artisan key:generate
```

Éditer `.env` :

```env
DB_CONNECTION=sqlite  # ou mysql
DB_DATABASE=agropredi.sqlite  # ou autre

FLASK_API_URL=http://127.0.0.1:5001/scan
```

### 3️⃣ Installer les dépendances Python

```bash
cd ai_api
python -m venv venv

# Windows
venv\Scripts\activate
# Mac/Linux
source venv/bin/activate

pip install -r requirements.txt
```

### 4️⃣ Vérifier la configuration

```bash
python check_setup.py
```

Vous devriez voir ✓ partout (sauf le modèle qui n'existe pas encore).

### 5️⃣ Préparer les données

```bash
python prepare_data_by_disease.py
```

Durée : 2-5 min (copie des images)

**Résultat attendu :**

```
✅ 8 classe(s) identifiée(s)
   train/: 8 classe(s), 7856 image(s)
   val/: 8 classe(s), 1876 image(s)
   test/: 8 classe(s), 1254 image(s)
```

### 6️⃣ Entraîner le modèle

```bash
python train_model.py
```

Durée : 30-60 min (selon CPU/GPU)

**Résultat attendu :**

```
✅ ENTRAÎNEMENT TERMINÉ
✅ Modèle sauvegardé dans ai_api/plant_disease_model
✅ Mapping des classes sauvegardé dans ai_api/class_indices.json
```

### 7️⃣ Initialiser la base de données Laravel

```bash
cd ..
php artisan migrate
```

### 8️⃣ Lancer Flask (Terminal 1)

```bash
cd ai_api
python flask_api.py
```

Sortie :

```
 * Running on http://127.0.0.1:5001
```

### 9️⃣ Lancer Laravel (Terminal 2)

```bash
php artisan serve
```

Sortie :

```
Local:   http://127.0.0.1:8000
```

### 🔟 Accédez à l'interface

Ouvrez : **http://127.0.0.1:8000/scan**

Uploadez une image et testez ! 🎉

---

## 📋 Checklist du déploiement

- [ ] Python 3.8+ installé
- [ ] PHP 8.2+ et Laravel 12 configurés
- [ ] `.env` créé avec `php artisan key:generate`
- [ ] Dépendances Python installées (`pip install -r requirements.txt`)
- [ ] `prepare_data_by_disease.py` exécuté
- [ ] `train_model.py` exécuté (attendez 30-60 min)
- [ ] `php artisan migrate` exécuté
- [ ] Flask lancé (`python flask_api.py`)
- [ ] Laravel lancé (`php artisan serve`)
- [ ] Interface accessible à `http://127.0.0.1:8000/scan`

---

## 🧪 Tests rapides

### Test Flask seul

```bash
curl -F "image=@image.jpg" http://127.0.0.1:5001/scan
```

### Test Laravel → Flask

```php
php artisan tinker

# Puis :
use Illuminate\Support\Facades\Http;
Http::attach('image', fopen('image.jpg', 'r'), 'image.jpg')
    ->post('http://127.0.0.1:5001/scan')
    ->json()
```

### Vérifier la base de données

```php
php artisan tinker
App\Models\Diagnostic::all()
```

---

## 🆘 Dépannage

| Problème | Solution |
|----------|----------|
| `ModuleNotFoundError: tensorflow` | `pip install -r requirements.txt` |
| Flask "Connection refused" | Vérifier que `python flask_api.py` est actif |
| "Modèle non trouvé" | Exécuter `train_model.py` |
| "Image non lisible" | Vérifier le format (JPG/PNG) |
| Erreur "CORS" | Ajouter `pip install flask-cors` |
| Django error "database" | Exécuter `php artisan migrate` |

---

## 📊 Ressources produites

Après démarrage complet, vous aurez :

```
ai_api/
├── dataset/                    # ~10 GB
├── plant_disease_model/        # Modèle TensorFlow
├── class_indices.json          # Mapping classes
├── checkpoints/best_model.h5   # Meilleur checkpoint
└── uploads/                    # Images uploadées (web)

database/
└── agropredi.sqlite / MySQL    # Diagnostics

storage/
└── uploads/temp/               # Images temporaires
```

---

## 🎯 Prochaines étapes (après validation)

1. **Optimisation** : Fine-tune le modèle avec plus de données
2. **Production** : Déployer avec Gunicorn + Nginx
3. **Extension** : Ajouter d'autres plantes (Arachide, Cacao)
4. **Analytics** : Créer un dashboard de statistiques
5. **Mobile** : Créer une app mobile React Native/Flutter

---

**Bonne chance ! 🌱** 

Pour toute question, consultez : [README.md](ai_api/README.md) et [CONFIG_INTEGRATION.md](CONFIG_INTEGRATION.md)
