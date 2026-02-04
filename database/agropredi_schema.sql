-- 📊 Schema de la base de données AgroPRedi
-- Utilisé pour la table diagnostics

CREATE TABLE IF NOT EXISTS diagnostics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    plante VARCHAR(100) NOT NULL,
    maladie VARCHAR(100),
    etat ENUM('Saîne', 'Malade') NOT NULL,
    confiance FLOAT NOT NULL,
    niveau_risque ENUM('Faible', 'Moyen', 'Élevé') NOT NULL,
    conseils JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_plante (plante),
    INDEX idx_etat (etat),
    INDEX idx_niveau_risque (niveau_risque),
    INDEX idx_created_at (created_at)
);

-- 📈 Exemples de vues analytiques (optionnel)

-- Vue : Statistiques par plante
CREATE OR REPLACE VIEW stats_by_plant AS
SELECT 
    plante,
    COUNT(*) as total_scans,
    SUM(CASE WHEN etat = 'Saîne' THEN 1 ELSE 0 END) as healthy_count,
    SUM(CASE WHEN etat = 'Malade' THEN 1 ELSE 0 END) as sick_count,
    ROUND(AVG(confiance) * 100, 2) as avg_confidence
FROM diagnostics
GROUP BY plante;

-- Vue : Top 10 des maladies les plus fréquentes
CREATE OR REPLACE VIEW top_diseases AS
SELECT 
    maladie,
    COUNT(*) as frequency,
    ROUND(AVG(confiance) * 100, 2) as avg_confidence
FROM diagnostics
WHERE maladie IS NOT NULL
GROUP BY maladie
ORDER BY frequency DESC
LIMIT 10;

-- Vue : Diagnostics à risque élevé
CREATE OR REPLACE VIEW high_risk_diagnostics AS
SELECT 
    id,
    image_path,
    plante,
    maladie,
    confiance,
    created_at
FROM diagnostics
WHERE niveau_risque = 'Élevé'
ORDER BY created_at DESC;
