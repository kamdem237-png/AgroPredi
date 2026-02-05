#!/bin/bash
# check_install.sh
# Script de vérification d'installation pour AgroPredi (Linux/macOS)
# Usage : chmod +x check_install.sh && ./check_install.sh

echo "=== AgroPredi — Vérification de l'installation ==="
echo

ok=true

# 1. Python
if command -v python3 &> /dev/null; then
    pyv=$(python3 --version 2>&1)
    echo "✔ Python : $pyv"
else
    echo "✖ Python3 non trouvé. Installez Python 3.11+ (python.org, brew, apt, etc.)"
    ok=false
fi

# 2. pip
if command -v pip3 &> /dev/null; then
    pipv=$(pip3 --version 2>&1)
    echo "✔ pip : $pipv"
else
    echo "✖ pip3 non trouvé. Installez pip (python3 -m ensurepip --upgrade ou via gestionnaire de paquets)."
    ok=false
fi

# 3. dépendances IA (requirements.txt)
if [ -f "ai_api/requirements.txt" ]; then
    missing=()
    while read -r line; do
        pkg=$(echo "$line" | cut -d'=' -f1)
        if pip3 show "$pkg" &> /dev/null; then
            continue
        else
            missing+=("$pkg")
        fi
    done < ai_api/requirements.txt
    if [ ${#missing[@]} -eq 0 ]; then
        echo "✔ Dépendances IA installées (requirements.txt)"
    else
        echo "✖ Packages Python manquants : ${missing[*]}"
        echo "  Commande à lancer : pip3 install -r ai_api/requirements.txt"
        ok=false
    fi
else
    echo "✖ ai_api/requirements.txt absent."
    ok=false
fi

# 4. Modèle IA
if [ -f "ai_api/model_plantvillage.pth" ]; then
    echo "✔ Modèle IA trouvé : ai_api/model_plantvillage.pth"
else
    echo "✖ Modèle IA absent : ai_api/model_plantvillage.pth"
    echo "  Assurez-vous que le modèle entraîné est présent dans ai_api/"
    ok=false
fi

# 5. API Flask accessible ?
echo "🔎 Test de l'API Flask (http://127.0.0.1:5001/health)..."
if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:5001/health | grep -q "200"; then
    echo "✔ API Flask accessible (health OK)"
else
    echo "✖ API Flask inaccessible. Démarrez-la avec : python3 ai_api/flask_api.py"
    ok=false
fi

# 6. PHP
if command -v php &> /dev/null; then
    phpv=$(php --version | head -n1)
    echo "✔ PHP : $phpv"
else
    echo "✖ PHP non trouvé. Installez PHP (via brew, apt, etc.)."
    ok=false
fi

# 7. Laravel (artisan)
if [ -f "artisan" ]; then
    echo "✔ Laravel (artisan) présent à la racine"
else
    echo "✖ artisan absent. Ce dossier ne semble pas être un projet Laravel."
    ok=false
fi

echo
echo "=== Résumé ==="
if $ok; then
    echo "✔ Installation OK. Vous pouvez lancer les serveurs :"
    echo "  - Flask : python3 ai_api/flask_api.py"
    echo "  - Laravel : php artisan serve"
else
    echo "✖ Des problèmes ont été détectés. Corrigez-les avant de continuer."
fi
