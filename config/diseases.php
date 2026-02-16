<?php

return [
    'default' => [
        'scientific_name' => '',
        'description' => 'Documentation en cours de mise à jour.',
        'causes' => [],
        'symptoms' => [],
        'impact' => 'Documentation en cours de mise à jour.',
        'prevention' => [],
        'treatment' => [],
        'best_practices' => [],
        'severity' => 'moderate',
    ],

    'Early Blight' => [
        'scientific_name' => 'Alternaria solani',
        'description' => "L’alternariose précoce (Early Blight) est l’une des maladies foliaires les plus fréquentes de la tomate en zones tropicales, surtout en saison chaude avec alternance pluie/soleil. Elle attaque d’abord les feuilles âgées, puis progresse vers le haut du couvert si les conditions restent favorables. La maladie affaiblit la plante en réduisant la surface foliaire active, ce qui limite la photosynthèse et ralentit le grossissement des fruits. En contexte de production maraîchère africaine, l’alternariose est souvent aggravée par des stress : carences (notamment azote/potassium), irrigation irrégulière, densité excessive, ou plants vieillissants. Les pertes ne viennent pas seulement des feuilles détruites : une défoliation importante expose les fruits au soleil (coup de soleil) et dégrade la qualité marchande. Une gestion professionnelle combine prévention (hygiène, rotation, aération), surveillance hebdomadaire et interventions raisonnées. Avec une stratégie intégrée, la maladie est contrôlable, mais une intervention tardive peut entraîner une baisse nette du rendement et une hausse des coûts de traitement.",
        'causes' => [
            'Conditions climatiques : alternance chaleur + humidité (rosées nocturnes, pluies intermittentes, brouillard matinal).',
            'Sol : sols pauvres ou déséquilibrés, stress nutritif, excès de salinité ou stress hydrique favorisant la sensibilité.',
            'Humidité : mouillure foliaire prolongée > 8–10 h, irrigation par aspersion en fin de journée.',
            'Transmission : spores transportées par le vent, éclaboussures de pluie, résidus végétaux contaminés et outils.',
        ],
        'symptoms' => [
            'Feuilles : taches brunes circulaires avec anneaux concentriques (“cible”), jaunissement et chute des feuilles basses.',
            'Tiges/pétioles : lésions allongées brun-noir, parfois fissures sur tiges affaiblies.',
            'Fruits : taches sombres près du pédoncule, zones enfoncées, dépréciation commerciale et surinfections possibles.',
        ],
        'impact' => "Perte estimée : 15–50% selon précocité de l’attaque et pression climatique. Risque économique : élevé pour les petits producteurs (baisse de calibre, fruits exposés au soleil, augmentation du nombre de traitements et des pertes post-récolte).",
        'prevention' => [
            'Rotation 2–3 ans hors Solanacées (tomate, pomme de terre, aubergine, piment).',
            'Espacement suffisant et taille légère pour améliorer l’aération; éviter les plantations trop denses.',
            'Irrigation : privilégier goutte-à-goutte; éviter aspersion et arrosage tardif.',
            'Hygiène : enlever feuilles basses très atteintes; détruire les résidus; désinfecter outils et caisses.',
            'Nutrition : corriger carences (K, Ca); éviter stress hydrique et excès d’azote.',
        ],
        'treatment' => [
            'Biologique : applications préventives de biocontrôles homologués (Bacillus subtilis/amylo.) et extraits végétaux, en alternance.',
            'Chimique : fongicides homologués anti-Alternaria (contacts et systémiques) en rotation de familles FRAC pour limiter résistances.',
            'Fréquence : 7–10 jours en période à risque; renforcer après pluie; toujours respecter les délais avant récolte.',
        ],
        'best_practices' => [
            'Mettre en place une surveillance (scouting) : 1–2 fois/semaine et après épisode pluvieux.',
            'Commencer tôt : la lutte est plus efficace au stade des premières taches sur feuilles basses.',
            'Alterner modes d’action et éviter les demi-doses; appliquer avec un bon volume d’eau et une couverture homogène.',
        ],
        'severity' => 'high',
    ],

    'Late Blight' => [
        'scientific_name' => 'Phytophthora infestans',
        'description' => "Le mildiou tardif (Late Blight) est une maladie redoutée car sa progression peut être explosive, en particulier pendant les périodes fraîches et humides ou en altitude et zones côtières. En Afrique tropicale, elle apparaît souvent en saison des pluies ou en fin de cycle lorsque le microclimat devient humide sous un couvert dense. Les lésions peuvent détruire rapidement feuilles, tiges et fruits, entraînant une défoliation massive et une perte quasi totale si aucune mesure n’est prise. Le pathogène se développe très vite lorsque la mouillure foliaire est longue; les champs proches, les jardins familiaux et les résidus contaminés jouent un rôle majeur dans la dissémination. Une gestion professionnelle repose sur la prévention : choix variétal, aération, irrigation adaptée, élimination des foyers et programme de protection fongicide raisonné. L’objectif n’est pas seulement de “traiter” mais de casser la chaîne épidémiologique. En cas de forte pression, l’agriculteur doit agir immédiatement : retirer les plants très atteints, protéger le reste de la parcelle et sécuriser la récolte.",
        'causes' => [
            'Conditions climatiques : températures modérées (souvent 15–22°C) + humidité élevée + brouillard/rosées prolongées.',
            'Sol : parcelles mal drainées, zones ombragées, bas-fonds et bordures humides.',
            'Humidité : mouillure foliaire longue; aspersion et irrigation nocturne augmentent le risque.',
            'Transmission : spores via vent, pluie, plants contaminés; résidus de Solanacées et tas de déchets végétaux.',
        ],
        'symptoms' => [
            'Feuilles : taches brun-vert “aqueuses”, bords flous; duvet blanc au revers en forte humidité.',
            'Tiges : lésions sombres pouvant ceinturer et casser les tiges; flétrissement rapide.',
            'Fruits : taches brunes fermes puis pourriture; contamination post-récolte possible.',
        ],
        'impact' => "Perte estimée : 30–100% si non maîtrisé. Risque économique : très élevé (perte de récolte, rejet au marché, coûts d’urgence).",
        'prevention' => [
            'Utiliser des variétés tolérantes lorsque disponibles et des plants sains certifiés.',
            'Espacement et tuteurage pour réduire la durée de mouillure; désherber pour améliorer la circulation d’air.',
            'Éviter l’aspersion; irriguer tôt le matin; drainer les zones d’eau stagnante.',
            'Détruire les résidus; éloigner et composter correctement; éviter les repousses de Solanacées.',
        ],
        'treatment' => [
            'Biologique : biocontrôles à base de Bacillus spp. en prévention, efficacité limitée en forte pression.',
            'Chimique : programmes alternant fongicides de contact et anti-mildiou systémiques/locaux-systémiques homologués (rotation FRAC).',
            'Fréquence : 5–7 jours en saison humide; renforcer après pluies; traiter avant symptômes si alerte climatique.',
        ],
        'best_practices' => [
            'Mettre en place un calendrier de protection basé sur météo locale (pluie, brouillard, humidité).',
            'Isoler les foyers : enlever feuilles/plants très attaqués et les sortir de la parcelle.',
            'Éviter la récolte de fruits mouillés; trier et ventiler pour réduire la pourriture en stockage.',
        ],
        'severity' => 'high',
    ],

    'Septoria Leaf Spot' => [
        'scientific_name' => 'Septoria lycopersici',
        'description' => "La septoriose est une maladie foliaire qui se manifeste souvent en saison des pluies ou dans les parcelles irriguées par aspersion. Elle progresse généralement depuis les feuilles basses, surtout lorsque les feuilles restent mouillées longtemps et que la parcelle est dense. Les petites taches se multiplient rapidement et entraînent un jaunissement généralisé puis une défoliation. En maraîchage tropical, la septoriose est fréquemment confondue avec d’autres taches foliaires; un diagnostic rigoureux repose sur l’observation : taches rondes grisâtres avec petits points noirs (pycnides) au centre. Les pertes viennent de la réduction de surface foliaire et de l’exposition des fruits au soleil, ainsi que du stress général de la plante. La gestion efficace combine hygiène, réduction des éclaboussures (paillage), aération et protection fongicide préventive. Une fois installée, la septoriose est difficile à “rattraper”; la prévention et l’intervention précoce sont donc essentielles.",
        'causes' => [
            'Conditions climatiques : chaleur modérée + pluies répétées; humidité constante dans le feuillage.',
            'Sol : présence de résidus contaminés; éclaboussures du sol sur feuilles basses.',
            'Humidité : aspersion, mauvaises pratiques d’irrigation et couverture végétale trop dense.',
            'Transmission : spores via éclaboussures de pluie/irrigation, travailleurs, outils, caisses.',
        ],
        'symptoms' => [
            'Feuilles : petites taches circulaires à centre gris et bord brun; points noirs visibles (structures du champignon).',
            'Tiges : lésions rarement importantes, mais pétioles peuvent être touchés.',
            'Fruits : atteinte directe rare, mais pertes indirectes par défoliation et coups de soleil.',
        ],
        'impact' => "Perte estimée : 10–40% selon intensité. Risque économique : modéré à élevé (baisse de calibre, plus de tri, moins bonne tenue au marché).",
        'prevention' => [
            'Paillage (organiques ou plastique) pour limiter éclaboussures du sol.',
            'Élaguer les feuilles basses en contact avec le sol; tuteurage et aération.',
            'Rotation et destruction des résidus; éviter la proximité avec anciennes parcelles contaminées.',
            'Irrigation goutte-à-goutte; éviter de mouiller le feuillage.',
        ],
        'treatment' => [
            'Biologique : programmes préventifs à base de Bacillus spp. et cuivre (si autorisé) en alternance.',
            'Chimique : fongicides homologués anti-taches foliaires, en alternance de familles.',
            'Fréquence : 7–10 jours en période humide; ajuster après pluie et croissance active.',
        ],
        'best_practices' => [
            'Former les équipes à reconnaître les pycnides (points noirs) pour intervenir tôt.',
            'Éviter de travailler dans les champs lorsque le feuillage est mouillé (réduction de dissémination).',
            'Assurer une bonne couverture au traitement : dessous des feuilles et zone basse du plant.',
        ],
        'severity' => 'moderate',
    ],

    'Bacterial Spot' => [
        'scientific_name' => 'Xanthomonas spp.',
        'description' => "La tache bactérienne (Bacterial Spot) est une maladie majeure des tomates en climat tropical humide, où les pluies, le vent et les éclaboussures favorisent une dissémination rapide. Elle touche feuilles, tiges et surtout fruits, entraînant des pertes économiques importantes par déclassement au marché. Contrairement aux maladies fongiques, les bactéries se propagent efficacement via semences contaminées, plants en pépinière, gouttelettes d’eau et blessures. Les symptômes s’aggravent lorsque les plantes subissent des stress (chaleur, excès d’azote, blessures mécaniques). En production africaine, les pépinières non protégées et l’irrigation par aspersion sont des facteurs de risque fréquents. Il n’existe pas de “curatif” total : la gestion est principalement préventive et repose sur l’hygiène stricte, la maîtrise de l’eau, la réduction des manipulations sur feuillage mouillé et des traitements protecteurs (souvent à base de cuivre, selon réglementation). L’objectif est de réduire la pression bactérienne dès la pépinière et d’éviter que les fruits soient marqués.",
        'causes' => [
            'Conditions climatiques : pluies battantes, vent, températures chaudes; épisodes orageux fréquents.',
            'Sol : éclaboussures et boue sur feuillage; parcelles mal drainées augmentant l’humidité persistante.',
            'Humidité : aspersion et brumisation en pépinière; densité trop élevée.',
            'Transmission : semences/plants contaminés, outils, mains, eau, insectes; pénétration via stomates/plaies.',
        ],
        'symptoms' => [
            'Feuilles : petites taches brun-noir parfois entourées d’un halo jaune; aspect “criblé” en vieillissant.',
            'Tiges : lésions sombres superficielles; aggravation par blessures et frottements.',
            'Fruits : lésions rugueuses, surélevées, liégeuses; déclassement et baisse de valeur marchande.',
        ],
        'impact' => "Perte estimée : 10–60% (souvent par déclassement). Risque économique : élevé pour les circuits frais où l’apparence du fruit est déterminante.",
        'prevention' => [
            'Semences et plants : privilégier matériel sain; pépinière sous abri anti-pluie; éviter surdensité.',
            'Irrigation : goutte-à-goutte; réduire mouillure foliaire; drainer; éviter éclaboussures.',
            'Hygiène : désinfecter outils, tuteurs, caissettes; limiter travail sur feuillage mouillé.',
            'Rotation et destruction des résidus; contrôle des adventices hôtes possibles.',
        ],
        'treatment' => [
            'Biologique : biocontrôles (Bacillus spp.) et stimulateurs de défenses en programme préventif.',
            'Chimique : produits homologués à base de cuivre (selon réglementation) éventuellement en mélange/alternance; éviter sur-usage (résistance, phytotoxicité).',
            'Fréquence : 7 jours en saison des pluies; renforcer après orage; toujours respecter étiquettes.',
        ],
        'best_practices' => [
            'Séparer clairement pépinière et parcelle de production; éliminer plants symptomatiques tôt.',
            'Mettre des chemins pour limiter éclaboussures et contamination par passage.',
            'Choisir des variétés avec tolérance quand disponible; gérer nutrition pour limiter tissus trop tendres.',
        ],
        'severity' => 'high',
    ],

    'Bacterial Speck' => [
        'scientific_name' => 'Pseudomonas syringae pv. tomato',
        'description' => "La moucheture bactérienne (Bacterial Speck) provoque de petites lésions sombres, surtout visibles sur fruits, qui réduisent fortement la qualité commerciale. Elle est favorisée par des conditions relativement plus fraîches que la tache bactérienne, mais en zones tropicales elle peut apparaître pendant les périodes plus fraîches, en altitude, ou lorsque les nuits sont humides. La maladie s’installe souvent à partir de plants contaminés ou de semences, et se propage par l’eau, les manipulations et les blessures. Les fruits peuvent présenter de nombreux petits points noirs superficiels entourés d’un halo, conduisant à un déclassement. Comme pour la plupart des bactéries, la stratégie de contrôle est essentiellement préventive : hygiène, pépinière saine, gestion de l’humidité et interventions protectrices. Une approche professionnelle vise à réduire le risque dès la production de plants, car une contamination précoce est difficile à éliminer ensuite. En agriculture tropicale, l’enjeu est d’éviter les pertes de qualité sur marchés urbains où l’aspect visuel des tomates conditionne le prix.",
        'causes' => [
            'Conditions climatiques : nuits fraîches et humides, brouillard, rosées; stress climatique.',
            'Sol : éclaboussures et boue augmentant la contamination; parcelles ventées favorisant microblessures.',
            'Humidité : mouillure foliaire prolongée, aspersion, pépinière mal ventilée.',
            'Transmission : semences/plants, eau, outils, mains; entrée via stomates et microblessures.',
        ],
        'symptoms' => [
            'Feuilles : petites taches noires avec halo jaune, souvent plus discrètes que tache bactérienne.',
            'Tiges : lésions superficielles occasionnelles; plus fréquent sur pétioles.',
            'Fruits : petits points noirs légèrement en relief, parfois halo verdâtre; forte baisse de qualité.',
        ],
        'impact' => "Perte estimée : 5–30% (souvent par déclassement). Risque économique : modéré à élevé selon exigence du marché.",
        'prevention' => [
            'Matériel végétal sain : semences fiables; éliminer plants suspects en pépinière.',
            'Réduire l’humidité : ventilation, espacement, goutte-à-goutte, éviter aspersion.',
            'Hygiène : désinfecter outils, éviter de toucher les plants mouillés; gérer circulation des travailleurs.',
            'Rotation et élimination des résidus; contrôler adventices hôtes.',
        ],
        'treatment' => [
            'Biologique : Bacillus spp., produits inducteurs de résistance en prévention.',
            'Chimique : cuivre homologué (selon réglementation) en protection; éviter applications excessives.',
            'Fréquence : 7–10 jours en période à risque; renforcer après événements humides.',
        ],
        'best_practices' => [
            'Prioriser la protection des fruits (calendrier de traitement avant période de récolte).',
            'Gérer la nutrition pour éviter tissus trop tendres (excès d’azote).',
            'Trier et ventiler après récolte pour limiter surinfections.',
        ],
        'severity' => 'moderate',
    ],

    'Leaf Mold' => [
        'scientific_name' => 'Passalora fulva (syn. Fulvia fulva)',
        'description' => "La cladosporiose (Leaf Mold) est fréquente lorsque l’humidité relative est élevée, notamment sous abris, serres simples, tunnels, ou dans des parcelles très denses où l’air circule mal. En zones tropicales, elle apparaît souvent en saison des pluies ou dans les bas-fonds humides. La maladie se caractérise par des taches jaunes sur la face supérieure des feuilles et un feutrage olive à brun sur la face inférieure. Elle réduit la photosynthèse, provoque une chute de feuilles et peut affecter le remplissage des fruits. La lutte est fortement basée sur la gestion du microclimat : ventilation, espacement, suppression des feuilles infectées, et maîtrise de la condensation. L’irrigation au goutte-à-goutte et l’aération du couvert sont des mesures prioritaires. Les traitements fongicides sont efficaces en programme préventif, mais la meilleure stratégie reste d’éviter la saturation en humidité sous le feuillage. Pour une exploitation tropicale, c’est une maladie “d’ambiance” : améliorer la circulation d’air et réduire la condensation donne souvent un gain immédiat.",
        'causes' => [
            'Conditions climatiques : humidité relative > 85% et faible ventilation; condensation sous abri.',
            'Sol : moins déterminant, mais excès d’irrigation et zones mal drainées favorisent microclimat humide.',
            'Humidité : feuilles mouillées, serre/tunnel fermés, densité excessive.',
            'Transmission : spores via air, outils, résidus; survie sur débris végétaux.',
        ],
        'symptoms' => [
            'Feuilles : taches jaunes à l’endroit; feutrage olive/brun au revers; dessèchement progressif.',
            'Tiges : rarement atteintes de façon importante.',
            'Fruits : atteinte directe rare; impact indirect par baisse de vigueur et de remplissage.',
        ],
        'impact' => "Perte estimée : 10–35% si la défoliation est importante. Risque économique : modéré (baisse de rendement et coût de gestion du microclimat).",
        'prevention' => [
            'Aérer : espacement, tuteurage, taille raisonnée; enlever feuilles basses trop denses.',
            'Sous abri : ouvrir tôt le matin pour limiter condensation; éviter arrosage tardif.',
            'Hygiène : retirer feuilles infectées, évacuer hors parcelle; nettoyer abris et outils.',
            'Rotation et gestion des résidus; éviter la monoculture continue.',
        ],
        'treatment' => [
            'Biologique : biocontrôles homologués (Bacillus spp.) en prévention; efficacité dépend de la pression.',
            'Chimique : fongicides homologués anti-Leaf Mold en alternance; respecter rotation des modes d’action.',
            'Fréquence : 7–10 jours en environnement humide; ajuster selon ventilation et symptômes.',
        ],
        'best_practices' => [
            'Mesurer ou estimer l’humidité sous le couvert (moment critique : nuit et petit matin).',
            'Éviter de fertiliser excessivement en azote (feuillage trop dense).',
            'Former l’équipe à observer le feutrage au revers des feuilles (symptôme clé).',
        ],
        'severity' => 'moderate',
    ],

    'Tomato Mosaic Virus' => [
        'scientific_name' => 'Tomato mosaic virus (ToMV)',
        'description' => "Le virus de la mosaïque de la tomate (ToMV) est une maladie virale particulièrement problématique car il se transmet très facilement par contact mécanique. Il peut persister sur les mains, les outils, les tuteurs et même sur certains résidus, ce qui en fait une menace dans les systèmes maraîchers intensifs où les manipulations sont fréquentes (taille, attachage, récolte). Les symptômes incluent mosaïque, déformations foliaires, réduction de vigueur et baisse de qualité des fruits. En agriculture tropicale, les pertes sont souvent liées à une combinaison : virus + stress thermique + carences + pression d’autres maladies. Il n’existe pas de traitement curatif du virus : la stratégie est strictement préventive. La gestion professionnelle repose sur la sanitation (désinfection), l’élimination rapide des plants symptomatiques, l’utilisation de semences/plants sains et l’organisation du travail (commencer par les parcelles saines). Pour une soutenance, il est important de souligner que la lutte contre les virus est une lutte de “bio-sécurité” plus qu’une lutte chimique.",
        'causes' => [
            'Conditions climatiques : la chaleur peut amplifier les symptômes; stress hydrique accentue la sévérité.',
            'Sol : indirect; carences et stress augmentent l’expression des symptômes.',
            'Humidité : pas un facteur direct, mais favorise manipulations (taille) et blessures.',
            'Transmission : principalement mécanique (mains, outils, tuteurs); semences contaminées possibles.',
        ],
        'symptoms' => [
            'Feuilles : mosaïque vert clair/vert foncé, gaufrage, enroulement et réduction du limbe.',
            'Tiges : ralentissement de croissance; plantes chétives; entre-nœuds courts.',
            'Fruits : maturation irrégulière, marbrures, baisse de calibre et qualité.',
        ],
        'impact' => "Perte estimée : 10–70% selon variété et stade d’infection. Risque économique : élevé (baisse de rendement, qualité et durée de cycle).",
        'prevention' => [
            'Utiliser des semences/plants sains; éviter échange informel de plants non contrôlés.',
            'Désinfecter outils régulièrement; se laver les mains; porter gants et les changer entre parcelles.',
            'Éliminer rapidement les plants symptomatiques et les sortir de la parcelle.',
            'Organiser le travail : passer des parcelles saines vers les parcelles suspectes (jamais l’inverse).',
        ],
        'treatment' => [
            'Biologique : aucun curatif; renforcer la vigueur (biostimulants) peut réduire l’impact mais n’élimine pas le virus.',
            'Chimique : aucun antiviral curatif en production; éviter fausses solutions; concentrer sur prévention.',
            'Fréquence : sanitation quotidienne; inspection hebdomadaire et élimination immédiate des foyers.',
        ],
        'best_practices' => [
            'Mettre en place une zone “propre” pour la pépinière et limiter l’accès.',
            'Former les travailleurs : le virus se propage par les mains et outils.',
            'Éviter de tailler lorsque les plants sont mouillés (risque de blessures et transfert).',
        ],
        'severity' => 'high',
    ],

    'Tomato Yellow Leaf Curl Virus' => [
        'scientific_name' => 'Tomato yellow leaf curl virus (TYLCV, Begomovirus)',
        'description' => "Le virus de l’enroulement jaunissant des feuilles de la tomate (TYLCV) est l’une des maladies virales les plus destructrices en production de tomate, particulièrement en zones tropicales et subtropicales. La maladie est surtout problématique car elle est transmise par la mouche blanche (Bemisia tabaci) et peut se propager très rapidement dans une parcelle. Les plants infectés tôt deviennent chétifs, les feuilles s’enroulent et jaunissent, et la fructification est fortement réduite. Comme pour la plupart des viroses, il n’existe pas de traitement curatif : la stratégie repose sur la prévention (plants sains), la réduction de la pression vectorielle (mouches blanches), l’élimination rapide des plants symptomatiques et la mise en place de mesures de biosécurité. Dans un contexte tropical, la gestion intégrée combine : filets/serres anti-insectes, surveillance, contrôle raisonné des vecteurs, et variétés tolérantes quand disponibles.",
        'causes' => [
            'Agent pathogène : virus TYLCV (Begomovirus).',
            'Vecteur : mouche blanche (Bemisia tabaci) – transmission persistante.',
            'Conditions climatiques : chaleur et périodes sèches favorisent souvent les populations de mouches blanches.',
            'Sources : plants/semences infectés, repousses, hôtes alternatifs (adventices).',
        ],
        'symptoms' => [
            'Feuilles : jaunissement (chlorose) et enroulement vers le haut; feuilles épaissies et cassantes.',
            'Plante : nanisme, réduction de vigueur, entre-nœuds courts.',
            'Fleurs/fruits : chute de fleurs, faible nouaison; fruits peu nombreux et petits.',
        ],
        'impact' => "Perte estimée : 20–100% si infection précoce et forte pression de vecteurs. Risque économique : très élevé.",
        'prevention' => [
            'Utiliser des plants sains (pépinière protégée, filets anti-insectes).',
            'Mettre en place une surveillance stricte des mouches blanches (pièges jaunes, observation sous feuilles).',
            'Désherber les hôtes alternatifs (adventices) autour de la parcelle.',
            'Éviter d’installer une nouvelle parcelle de tomate à proximité immédiate d’une parcelle déjà infestée.',
            'Favoriser les variétés tolérantes/résistantes au TYLCV si disponibles.',
        ],
        'treatment' => [
            'Biologique : gestion intégrée des vecteurs (auxiliaires, biocontrôles) selon disponibilité locale.',
            'Chimique : insecticides homologués contre la mouche blanche, en alternant les familles (gestion des résistances).',
            'Mesures sanitaires : arracher et éliminer rapidement les plants fortement symptomatiques.',
        ],
        'best_practices' => [
            'Protéger la pépinière : c’est la zone la plus critique (entrée du virus).',
            'Travailler “propre” : commencer par les zones saines puis aller vers les zones suspectes.',
            'Éviter les excès d’azote qui rendent les plants plus attractifs pour les mouches blanches.',
            'Mettre des barrières physiques (filets) quand c’est possible en production intensive.',
        ],
        'severity' => 'high',
    ],

    'Target Spot' => [
        'scientific_name' => 'Corynespora cassiicola',
        'description' => "La tache cible (Target Spot) causée par Corynespora cassiicola est une maladie de plus en plus signalée en zones tropicales et subtropicales, notamment quand l’humidité est élevée. Elle produit des taches concentriques qui peuvent ressembler à l’alternariose, mais elle peut aussi toucher les fruits et provoquer des pertes commerciales. Les attaques sont favorisées par un feuillage dense, une mauvaise aération et des cycles humides prolongés. Sur feuilles, les taches s’agrandissent et entraînent une défoliation; sur fruits, les lésions peuvent être brunâtres et déprimées. En contexte tropical, la maladie est souvent associée à des parcelles où la rotation est courte et où les résidus restent au champ. Une gestion intégrée est nécessaire : hygiène, réduction de l’humidité au niveau du couvert, surveillance, et traitements fongicides en alternance. L’objectif est de distinguer rapidement les maladies foliaires afin d’utiliser des programmes de protection pertinents et éviter des applications inefficaces.",
        'causes' => [
            'Conditions climatiques : humidité élevée, pluies fréquentes, chaleur; périodes de mouillure longues.',
            'Sol : résidus contaminés; rotations courtes; parcelles avec mauvaises pratiques d’assainissement.',
            'Humidité : densité de plantation, abris mal ventilés, arrosage sur feuillage.',
            'Transmission : spores par le vent et éclaboussures; survie sur débris végétaux.',
        ],
        'symptoms' => [
            'Feuilles : taches brunes avec anneaux concentriques, pouvant confluer; jaunissement et chute.',
            'Tiges : lésions ponctuelles; affaiblissement si nombreuses.',
            'Fruits : taches brunes parfois enfoncées, dépréciation commerciale et risque de pourriture secondaire.',
        ],
        'impact' => "Perte estimée : 10–40% selon pression et atteinte des fruits. Risque économique : modéré à élevé (déclassement et pertes post-récolte).",
        'prevention' => [
            'Rotation et destruction des résidus; éviter monoculture continue.',
            'Aération : tuteurage, taille raisonnée, espacement adapté.',
            'Irrigation : goutte-à-goutte; éviter arrosage sur feuillage; réduire mouillure.',
            'Surveillance hebdomadaire et interventions précoces sur foyers.',
        ],
        'treatment' => [
            'Biologique : biocontrôles en prévention (Bacillus spp.) et extraits homologués.',
            'Chimique : fongicides homologués anti-taches foliaires, alternance de familles pour limiter résistances.',
            'Fréquence : 7–10 jours en période humide; renforcer après pluie et en phase de fructification.',
        ],
        'best_practices' => [
            'Améliorer la couverture de pulvérisation sous le feuillage (zones basses critiques).',
            'Retirer les feuilles très atteintes pour réduire inoculum, sans défolier excessivement d’un coup.',
            'Éviter la récolte et manipulation des plants lorsqu’ils sont mouillés.',
        ],
        'severity' => 'moderate',
    ],

    'Spider Mites Damage' => [
        'scientific_name' => 'Tetranychus urticae (acariens tétranyques)',
        'description' => "Les dégâts d’acariens (Spider Mites) ne sont pas une maladie infectieuse mais un problème phytosanitaire majeur, surtout en saison sèche et chaude, fréquente dans de nombreuses zones tropicales africaines. Les acariens se développent rapidement lorsque la température est élevée et que l’humidité relative est faible. Ils piquent les cellules des feuilles, provoquant un aspect moucheté, un bronzage puis un dessèchement; en forte infestation, on observe des toiles fines. La plante perd sa capacité photosynthétique, les fruits restent petits et la maturation peut être irrégulière. Les infestations sont souvent favorisées par la poussière, le stress hydrique et l’absence d’ennemis naturels (à cause d’insecticides non sélectifs). Une gestion professionnelle repose sur la surveillance précoce (dessous des feuilles), la réduction du stress (irrigation régulière), la maîtrise de la poussière, et l’utilisation raisonnée d’acaricides/biocontrôles. La stratégie doit aussi prévenir les résistances : alterner les modes d’action et cibler les traitements sur le foyer.",
        'causes' => [
            'Conditions climatiques : chaleur élevée et temps sec; vents poussiéreux favorisant la dispersion.',
            'Sol : stress hydrique et sols desséchants augmentent la sensibilité; poussière sur feuillage.',
            'Humidité : faible humidité relative accélère la multiplication; irrigation irrégulière.',
            'Transmission : transport par le vent, outils, vêtements; multiplication rapide en foyers.',
        ],
        'symptoms' => [
            'Feuilles : ponctuations jaunes, aspect marbré, bronzage; enroulement puis chute; présence de toiles fines.',
            'Tiges : faiblesse générale; croissance ralentie; dessèchement des pousses en forte attaque.',
            'Fruits : réduction de calibre, maturation irrégulière; baisse de rendement et qualité.',
        ],
        'impact' => "Perte estimée : 10–50% selon précocité et intensité. Risque économique : élevé en saison sèche (baisse de calibre, plus de tri et coût de traitements).",
        'prevention' => [
            'Éviter le stress hydrique : irrigation régulière; paillage pour stabiliser l’humidité du sol.',
            'Réduire la poussière (arrosage des allées, couvert végétal contrôlé).',
            'Préserver les auxiliaires : limiter insecticides à large spectre; favoriser lutte intégrée.',
            'Inspecter le dessous des feuilles, surtout en bordures et zones chaudes.',
        ],
        'treatment' => [
            'Biologique : savon insecticide/huiles horticoles homologuées; utilisation d’auxiliaires (selon disponibilité).',
            'Chimique : acaricides homologués, rotation stricte des modes d’action; éviter répétitions.',
            'Fréquence : traiter tôt sur foyers; répéter selon cycle et étiquette; vérifier efficacité 3–5 jours après.',
        ],
        'best_practices' => [
            'Traiter localement les foyers si possible avant généralisation.',
            'Alterner produits et respecter doses; les acariens développent vite des résistances.',
            'Maintenir une bonne vigueur de la plante (fertilité équilibrée, irrigation) pour limiter l’impact.',
        ],
        'severity' => 'high',
    ],

    'Healthy' => [
        'scientific_name' => '—',
        'description' => "Un diagnostic “Healthy” indique qu’aucun symptôme majeur n’a été détecté sur l’image analysée au moment de la prise de vue. Cela ne signifie pas “absence totale de risque” : certaines maladies débutent par des signes très discrets, et des stress non visibles (carences, salinité, stress hydrique) peuvent précéder l’apparition de symptômes. En agriculture tropicale, la prévention reste déterminante : gestion de l’eau, fertilisation raisonnée, hygiène, rotation et surveillance régulière. Un plant sain est un plant dont la croissance est équilibrée, dont le feuillage reste vert et fonctionnel, et dont le microclimat autour du couvert n’est pas favorable aux agents pathogènes. L’objectif pour l’agriculteur est de maintenir cet état : limiter la durée de mouillure foliaire, éviter la surdensité, surveiller les ravageurs (acariens, aleurodes, thrips) et intervenir rapidement au premier signe. Une démarche professionnelle repose sur un calendrier de suivi, des observations structurées et des mesures correctives précoces. En pratique, une parcelle “saine” est le résultat d’un système de production bien géré, pas seulement d’une absence de maladie.",
        'causes' => [
            'Gestion correcte de l’irrigation (goutte-à-goutte, horaires adaptés) et bonne aération du couvert.',
            'Nutrition équilibrée (N-P-K, calcium, oligo-éléments) et correction rapide des carences.',
            'Hygiène et rotation limitant l’inoculum (résidus, mauvaises herbes hôtes).',
            'Surveillance et gestion intégrée des ravageurs pour éviter blessures et vecteurs.',
        ],
        'symptoms' => [
            'Feuilles : vert uniforme, absence de taches, pas de déformation ou mosaïque.',
            'Tiges : croissance régulière, entre-nœuds normaux, absence de nécroses.',
            'Fruits : coloration homogène, absence de lésions, bonne fermeté.',
        ],
        'impact' => "Rendement : potentiel optimal si les bonnes pratiques sont maintenues. Risque économique : faible tant que la surveillance est régulière et que la prévention est appliquée.",
        'prevention' => [
            'Maintenir l’aération (espacement, tuteurage, taille raisonnée).',
            'Irriguer tôt le matin et éviter de mouiller le feuillage.',
            'Nettoyer la parcelle, enlever les feuilles basses en contact avec le sol, gérer les adventices.',
            'Mettre en place une surveillance hebdomadaire (feuilles basses, revers des feuilles, fruits).',
        ],
        'treatment' => [
            'Biologique : applications préventives légères de biocontrôle en périodes à risque (selon contexte).',
            'Chimique : non recommandé sans pression; privilégier interventions ciblées et raisonnées si risque confirmé.',
            'Fréquence : suivi hebdomadaire; interventions uniquement si indicateurs de risque ou symptômes.',
        ],
        'best_practices' => [
            'Tenir un carnet de champ : dates, météo, interventions, observations et rendement.',
            'Former les équipes à reconnaître les premiers symptômes (taches, halos, mosaïques).',
            'Prévenir plutôt que guérir : la plupart des pertes viennent de diagnostics et actions tardifs.',
        ],
        'severity' => 'low',
    ],
];
