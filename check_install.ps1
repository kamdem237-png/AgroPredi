# check_install.ps1
# Script de vérification d'installation pour AgroPredi (Windows)
# Usage : .\check_install.ps1

Write-Host "=== AgroPredi — Vérification de l'installation ===" -ForegroundColor Cyan

$ok = $true

# 1. Python
$python = Get-Command python -ErrorAction SilentlyContinue
if ($python) {
    $v = & python --version 2>&1
    Write-Host "✔ Python : $v" -ForegroundColor Green
} else {
    Write-Host "✖ Python non trouvé. Installez Python 3.11+ depuis python.org" -ForegroundColor Red
    $ok = $false
}

# 2. pip
$pip = Get-Command pip -ErrorAction SilentlyContinue
if ($pip) {
    $v = & pip --version 2>&1
    Write-Host "✔ pip : $v" -ForegroundColor Green
} else {
    Write-Host "✖ pip non trouvé. Réinstallez Python ou assurez-vous que pip est dans le PATH." -ForegroundColor Red
    $ok = $false
}

# 3. dépendances IA (requirements.txt)
$req = Test-Path "ai_api\requirements.txt"
if (-not $req) {
    Write-Host "✖ ai_api\requirements.txt absent." -ForegroundColor Red
    $ok = $false
} else {
    # Vérifier si les packages sont installés
    $missing = @()
    $list = Get-Content "ai_api\requirements.txt" | ForEach-Object { if ($_ -match '^(?<pkg>\w+)') { $Matches['pkg'] } }
    foreach ($pkg in $list) {
        $installed = & pip show $pkg 2>$null
        if (-not $installed) {
            $missing += $pkg
        }
    }
    if ($missing) {
        Write-Host "✖ Packages Python manquants : $($missing -join ', ')" -ForegroundColor Red
        Write-Host "  Commande à lancer : pip install -r ai_api\requirements.txt" -ForegroundColor Yellow
        $ok = $false
    } else {
        Write-Host "✔ Dépendances IA installées (requirements.txt)" -ForegroundColor Green
    }
}

# 4. Modèle IA
$model = Test-Path "ai_api\model_plantvillage.pth"
if ($model) {
    Write-Host "✔ Modèle IA trouvé : ai_api\model_plantvillage.pth" -ForegroundColor Green
} else {
    Write-Host "✖ Modèle IA absent : ai_api\model_plantvillage.pth" -ForegroundColor Red
    Write-Host "  Assurez-vous que le modèle entraîné est présent dans ai_api/" -ForegroundColor Yellow
    $ok = $false
}

# 5. API Flask accessible ?
Write-Host "🔎 Test de l'API Flask (http://127.0.0.1:5001/health)..." -ForegroundColor Cyan
try {
    $resp = Invoke-WebRequest -Uri "http://127.0.0.1:5001/health" -TimeoutSec 5 -UseBasicParsing
    if ($resp.StatusCode -eq 200) {
        Write-Host "✔ API Flask accessible (health OK)" -ForegroundColor Green
    } else {
        Write-Host "✖ API Flask répond mais statut != 200" -ForegroundColor Red
        $ok = $false
    }
} catch {
    Write-Host "✖ API Flask inaccessible. Démarrez-la avec : python ai_api\flask_api.py" -ForegroundColor Red
    $ok = $false
}

# 6. PHP
$php = Get-Command php -ErrorAction SilentlyContinue
if ($php) {
    $v = & php --version 2>&1 | Select-Object -First 1
    Write-Host "✔ PHP : $v" -ForegroundColor Green
} else {
    Write-Host "✖ PHP non trouvé. Installez PHP (via XAMPP ou php.net)" -ForegroundColor Red
    $ok = $false
}

# 7. Laravel (artisan)
$artisan = Test-Path "artisan"
if ($artisan) {
    Write-Host "✔ Laravel (artisan) présent à la racine" -ForegroundColor Green
} else {
    Write-Host "✖ artisan absent. Ce dossier ne semble pas être un projet Laravel." -ForegroundColor Red
    $ok = $false
}

Write-Host "`n=== Résumé ===" -ForegroundColor Cyan
if ($ok) {
    Write-Host "✔ Installation OK. Vous pouvez lancer les serveurs :" -ForegroundColor Green
    Write-Host "  - Flask : python ai_api\flask_api.py" -ForegroundColor Gray
    Write-Host "  - Laravel : php artisan serve" -ForegroundColor Gray
} else {
    Write-Host "✖ Des problèmes ont été détectés. Corrigez-les avant de continuer." -ForegroundColor Red
}
