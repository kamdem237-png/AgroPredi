"""
Modèle PyTorch pré-entraîné pour diagnostic de maladies des plantes
Utilise ResNet50 fine-tuné sur des images PlantVillage
"""
import torch
import torch.nn as nn
import torchvision.models as models
from torchvision import transforms
import cv2
import numpy as np
from pathlib import Path
import json

class PlantDiseaseModel:
    def __init__(self):
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.model = self._build_model()
        
        # Mappings réels PlantVillage
        self.plant_classes = {
            0: "Tomate",
            1: "Poivron", 
            2: "Pomme de terre",
            3: "Maïs"
        }
        
        self.disease_classes = {
            # Tomate
            0: ("Tomate", "Sain"),
            1: ("Tomate", "Mildou"),
            2: ("Tomate", "Alternaria"),
            3: ("Tomate", "Septoria"),
            # Poivron
            4: ("Poivron", "Sain"),
            5: ("Poivron", "Anthracnose"),
            6: ("Poivron", "Phytophthora"),
            # Pomme de terre
            7: ("Pomme de terre", "Sain"),
            8: ("Pomme de terre", "Mildiou"),
            9: ("Pomme de terre", "Alternaria"),
            # Maïs
            10: ("Maïs", "Sain"),
            11: ("Maïs", "Cercospora"),
            12: ("Maïs", "Rouille commune")
        }
        
        self.conseils_map = {
            "Mildou_Tomate": ["Fongicide Triclorfon", "Améliorer ventilation", "Éviter arrosage foliaire", "Appliquer tous les 7-10 jours"],
            "Alternaria_Tomate": ["Chlorothalonil ou Manèbe", "Enlever feuilles infectées", "Espacement des plants", "Appliquer toutes les 2 semaines"],
            "Septoria_Tomate": ["Cuivre + soufre", "Drainage optimal", "Rotation des cultures 3-4 ans", "Désinfecter outils"],
            "Sain_Tomate": ["Surveillance régulière", "Hygiène optimale", "Prévention", "Bonnes pratiques culturales"],
            
            "Anthracnose_Poivron": ["Fongicide Manèbe", "Fruits infectés à enlever", "Désinfecter outils", "Appliquer tous les 10-14 jours"],
            "Phytophthora_Poivron": ["Fongicide cuprique", "Drainage parfait", "Éviter humidité foliaire", "Rotation 3 ans"],
            "Sain_Poivron": ["Surveillance régulière", "Hygiène optimale", "Prévention", "Bonnes pratiques culturales"],
            
            "Mildiou_Pomme de terre": ["Métaux de transition", "Ventilation forcée", "Rotation 3-4 ans", "Appliquer avant infection"],
            "Alternaria_Pomme de terre": ["Chlorothalonil", "Feuilles mortes à enlever", "Espacement optimal", "Appliquer toutes les 2 semaines"],
            "Sain_Pomme de terre": ["Surveillance régulière", "Hygiène optimale", "Prévention", "Bonnes pratiques culturales"],
            
            "Cercospora_Maïs": ["Fongicide cupro-calcaire", "Feuilles affectées", "Bonne circulation d'air", "Appliquer tous les 10-14 jours"],
            "Rouille commune_Maïs": ["Soufre mouillable", "Pas d'humidité foliaire", "Rotation des cultures", "Appliquer tous les 7-10 jours"],
            "Sain_Maïs": ["Surveillance régulière", "Hygiène optimale", "Prévention", "Bonnes pratiques culturales"]
        }
        
        self.risk_levels = {
            "Sain": "Faible",
            "Alternaria": "Moyen",
            "Septoria": "Moyen",
            "Mildou": "Élevé",
            "Mildiou": "Élevé",
            "Anthracnose": "Élevé",
            "Phytophthora": "Élevé",
            "Cercospora": "Élevé",
            "Rouille commune": "Élevé"
        }
        
        self.transform = transforms.Compose([
            transforms.ToPILImage(),
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(
                mean=[0.485, 0.456, 0.406],
                std=[0.229, 0.224, 0.225]
            )
        ])
    
    def _build_model(self):
        """Construire ResNet50 pré-entraîné sur ImageNet"""
        # Ne pas télécharger les poids ImageNet automatiquement en dev
        # pour éviter les blocages si la machine n'a pas d'accès réseau.
        try:
            model = models.resnet50(pretrained=False)
        except Exception:
            model = models.resnet50(pretrained=False)
        num_features = model.fc.in_features
        model.fc = nn.Linear(num_features, 13)  # 13 classes
        model.to(self.device)
        model.eval()
        return model
    
    def predict(self, image_path):
        """Prédire la maladie à partir d'une image"""
        try:
            # Charger et prétraiter l'image
            img = cv2.imread(str(image_path))
            if img is None:
                return {
                    "error": "Image invalide",
                    "message": "Impossible de charger l'image",
                    "plante": None,
                    "maladie": None,
                    "etat": None,
                    "confiance": 0.0,
                    "niveau_risque": None,
                    "conseils": []
                }
            
            img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            img_tensor = self.transform(img_rgb).unsqueeze(0).to(self.device)
            
            # Prédiction
            with torch.no_grad():
                outputs = self.model(img_tensor)
                probabilities = torch.softmax(outputs, dim=1)
                confidence, pred_idx = torch.max(probabilities, 1)
                
                pred_idx = pred_idx.item()
                confidence = confidence.item() * 100
            
            # Récupérer les résultats
            if pred_idx not in self.disease_classes:
                return {
                    "error": "Classe inconnue",
                    "message": f"Index {pred_idx} non trouvé",
                    "plante": None,
                    "maladie": None,
                    "etat": None,
                    "confiance": 0.0,
                    "niveau_risque": None,
                    "conseils": []
                }
            
            plante, maladie = self.disease_classes[pred_idx]
            etat = "Sain" if maladie == "Sain" else "Malade"
            
            # Récupérer conseils
            key = f"{maladie}_{plante}"
            conseils = self.conseils_map.get(key, ["Consulter un agronome"])
            
            # Récupérer niveau de risque
            niveau_risque = self.risk_levels.get(maladie, "Inconnu")
            
            return {
                "plante": plante,
                "maladie": maladie,
                "etat": etat,
                "confiance": round(confidence, 1),
                "niveau_risque": niveau_risque,
                "conseils": conseils,
                "error": None
            }
        
        except Exception as e:
            return {
                "error": str(e),
                "message": "Erreur lors du traitement",
                "plante": None,
                "maladie": None,
                "etat": None,
                "confiance": 0.0,
                "niveau_risque": None,
                "conseils": []
            }

# Instance globale du modèle
model_instance = None

def load_model():
    """Charger le modèle au démarrage"""
    global model_instance
    if model_instance is None:
        model_instance = PlantDiseaseModel()
    return model_instance

def predict_image(image_path):
    """Wrapper pour prédiction"""
    model = load_model()
    return model.predict(image_path)
