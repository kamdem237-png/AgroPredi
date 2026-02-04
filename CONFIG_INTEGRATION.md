# Configuration d'intégration Laravel ↔ Flask pour AgroPRedi

## 🔐 Sécurité

### 1. URL de l'API Flask
Configurer l'URL dans `ScanController.php` (ligne 11) :

```php
private const FLASK_API_URL = 'http://127.0.0.1:5001/scan';
```

Pour production, utiliser une variable d'environnement `.env` :

```bash
FLASK_API_URL=https://api-ia.agropredi.com/scan
```

Et modifier le contrôleur :

```php
private const FLASK_API_URL = env('FLASK_API_URL', 'http://127.0.0.1:5001/scan');
```

### 2. CORS (Cross-Origin Resource Sharing)

Si la API Flask est sur un domaine différent, activer CORS :

```python
# Dans flask_api.py, ajouter après app = Flask(__name__):
from flask_cors import CORS
CORS(app)
```

Puis installer :

```bash
pip install flask-cors
```

### 3. Authentication (optionnel pour production)

Pour sécuriser l'endpoint `/scan`, ajouter une clé API :

**Flask (flask_api.py) :**

```python
@app.route('/scan', methods=['POST'])
def scan_endpoint():
    api_key = request.headers.get('X-API-Key')
    if api_key != os.getenv('FLASK_API_KEY', 'your-secret-key'):
        return jsonify({'error': 'Invalid API key'}), 403
    
    # ... reste du code
```

**Laravel (ScanController.php) :**

```php
private function callFlaskAPI(string $imagePath)
{
    return Http::timeout(60)
        ->withHeaders(['X-API-Key' => env('FLASK_API_KEY')])
        ->attach('image', fopen($imagePath, 'r'), basename($imagePath))
        ->post(env('FLASK_API_URL'));
}
```

## 🔄 Communication Flask ↔ Laravel

### Format de requête (Laravel → Flask)

```
POST /scan HTTP/1.1
Content-Type: multipart/form-data

[image binary data]
```

### Format de réponse (Flask → Laravel)

```json
{
  "plante": "Tomate",
  "etat": "Malade",
  "maladie": "Bacterial spot",
  "confiance": 0.94,
  "niveau_risque": "Élevé",
  "conseils": [
    "Isolez les plantes...",
    "Retirez les feuilles..."
  ]
}
```

## 📝 Logs et Monitoring

### Laravel

Les erreurs sont loggées dans `storage/logs/laravel.log` :

```
[2026-02-01 10:30:45] local.ERROR: Scan error: Connection refused
```

### Flask

Les logs Flask s'affichent dans le terminal ou peuvent être redirigés :

```bash
python flask_api.py > flask.log 2>&1 &
```

## ⏱️ Timeouts et Performance

### Laravel Timeout
```php
Http::timeout(60)  // 60 secondes
```

### Flask Timeout
Configuration dans `app.config` :

```python
app.config['JSON_SORT_KEYS'] = False
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16 MB max
```

## 🧪 Tests d'intégration

### 1. Test Flask uniquement
```bash
curl -F "image=@/chemin/image.jpg" http://127.0.0.1:5001/scan
```

### 2. Test Laravel → Flask
```bash
php artisan tinker
Http::attach('image', fopen('/chemin/image.jpg', 'r'), 'image.jpg')
    ->post('http://127.0.0.1:5001/scan')
    ->json()
```

### 3. Test de l'interface web
Accédez à `http://127.0.0.1:8000/scan` et uploadez une image.

## 📊 Métriques de performance

| Opération | Durée estimée |
|-----------|---------------|
| Upload image | 100-500 ms |
| Envoi à Flask | 50-200 ms |
| Prédiction modèle | 500-2000 ms |
| Sauvegarde BD | 50-100 ms |
| **Total** | **700-2800 ms** |

Pour optimiser :
- Compresser les images avant upload
- Cacher les prédictions identiques
- Utiliser GPU sur le serveur Flask

## 🚀 Production Checklist

- [ ] Variables d'environnement configurées (FLASK_API_URL, FLASK_API_KEY, etc.)
- [ ] HTTPS activé sur les deux services
- [ ] API Key configurée et sécurisée
- [ ] Logs configurés
- [ ] Base de données migrée
- [ ] Modèle ML chargé et validé
- [ ] Tests d'intégration passés
- [ ] Rate limiting activé
- [ ] Backup de la BD programmé
- [ ] Monitoring/Alertes configurés

## 📞 Support

Pour les erreurs de connexion :

1. Vérifier que Flask est actif : `curl http://127.0.0.1:5001/scan`
2. Vérifier les logs Laravel : `tail -f storage/logs/laravel.log`
3. Vérifier les logs Flask : Console du terminal
4. Vérifier le firewall/pare-feu

---

**Dernière mise à jour** : Février 2026
