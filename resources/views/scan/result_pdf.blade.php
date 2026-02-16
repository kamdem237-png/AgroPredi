<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic #{{ $diagnostic->id }}</title>
</head>
<body>
    <h1>Diagnostic agricole (AgroPredi)</h1>

    <p>
        <strong>ID:</strong> #{{ $diagnostic->id }}<br>
        <strong>Date:</strong> {{ $diagnostic->created_at?->format('d/m/Y H:i') }}
    </p>

    <h2>Résultat</h2>
    <p><strong>Plante:</strong> {{ $diagnostic->plante }}</p>
    <p><strong>Maladie:</strong> {{ $diagnostic->maladie }}</p>
    <p><strong>État:</strong> {{ $diagnostic->etat }}</p>
    <p><strong>Confiance:</strong> {{ $diagnostic->confiance }}%</p>
    <p><strong>Niveau de risque:</strong> {{ $diagnostic->niveau_risque }}</p>

    <h2>Image analysée</h2>
    @php
        $imgPath = public_path('storage/' . $diagnostic->image_path);
        $imgSrc = 'file:///' . str_replace('\\', '/', $imgPath);
    @endphp
    @if($diagnostic->image_path && file_exists($imgPath))
        <img src="{{ $imgSrc }}" width="700" alt="Image analysée">
    @endif

    <h2>Recommandations IA</h2>
    @if(is_array($diagnostic->conseils) && count($diagnostic->conseils) > 0)
        <ul>
            @foreach($diagnostic->conseils as $c)
                <li>{{ $c }}</li>
            @endforeach
        </ul>
    @else
        <p>Aucune recommandation disponible.</p>
    @endif

    <h2>Documentation complète</h2>

    @if(!empty($doc['scientific_name']))
        <p><strong>Nom scientifique:</strong> {{ $doc['scientific_name'] }}</p>
    @endif

    <h3>Description détaillée</h3>
    <p>{{ $doc['description'] ?? 'Documentation en cours de mise à jour.' }}</p>

    <h3>Causes</h3>
    @if(!empty($doc['causes']))
        <ul>
            @foreach($doc['causes'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Documentation en cours de mise à jour.</p>
    @endif

    <h3>Symptômes</h3>
    @if(!empty($doc['symptoms']))
        <ul>
            @foreach($doc['symptoms'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Documentation en cours de mise à jour.</p>
    @endif

    <h3>Méthodes de prévention</h3>
    @if(!empty($doc['prevention']))
        <ul>
            @foreach($doc['prevention'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Documentation en cours de mise à jour.</p>
    @endif

    <h3>Traitements recommandés</h3>
    @if(!empty($doc['treatment']))
        <ul>
            @foreach($doc['treatment'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Documentation en cours de mise à jour.</p>
    @endif

    <h3>Impact sur le rendement</h3>
    <p>{{ $doc['impact'] ?? '' }}</p>

    <h3>Bonnes pratiques agricoles</h3>
    @if(!empty($doc['best_practices']))
        <ul>
            @foreach($doc['best_practices'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p>Documentation en cours de mise à jour.</p>
    @endif
</body>
</html>
