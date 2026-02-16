<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    private string $flask_url;

    public function __construct()
    {
        $this->flask_url = rtrim(env('AI_API_URL', 'http://127.0.0.1:5001'), '/');
    }

    /**
     * Afficher le formulaire de téléchargement
     */
    public function scan()
    {
        return view('react', ['page' => 'scan']);
    }

    /**
     * Traiter le téléchargement et l'analyse
     */
    public function analyze(Request $request)
    {
        try {
            // Valider l'image
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,bmp|max:16384'
            ], [
                'image.required' => 'Veuillez sélectionner une image',
                'image.image' => 'Le fichier doit être une image valide',
                'image.mimes' => 'Format accepté: JPEG, PNG, JPG, GIF, BMP',
                'image.max' => 'Taille maximale: 16MB'
            ]);

            // Sauvegarder le fichier
            if (!$request->hasFile('image')) {
                return response()->json([
                    'error' => 'Image non reçue',
                    'message' => 'Erreur lors du téléchargement'
                ], 400);
            }

            $file = $request->file('image');
            $path = $file->store('scans', 'public');
            $full_path = storage_path('app/public/' . $path);

            // Envoyer à l'API Flask
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($full_path), $file->getClientOriginalName())
                ->post($this->flask_url . '/predict');

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Erreur API',
                    'message' => 'Le serveur PyTorch ne répond pas'
                ], 503);
            }

            $prediction = $response->json();

            // Sauvegarder le diagnostic
            if (isset($prediction['plante']) && $prediction['plante']) {
                $diagnostic = Diagnostic::create([
                    'image_path' => $path,
                    'plante' => $prediction['plante'] ?? null,
                    'maladie' => $prediction['maladie'] ?? null,
                    'etat' => $prediction['etat'] ?? null,
                    'confiance' => $prediction['confiance'] ?? 0,
                    'niveau_risque' => $prediction['niveau_risque'] ?? null,
                    'conseils' => $prediction['conseils'] ?? []
                ]);

                if (auth()->check()) {
                    $diagnostic->user_id = auth()->id();
                    $diagnostic->save();
                } else {
                    $ids = session()->get('anon_diagnostic_ids', []);
                    $ids[] = $diagnostic->id;
                    session()->put('anon_diagnostic_ids', array_values(array_unique($ids)));
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'diagnostic_id' => $diagnostic->id,
                        'prediction' => $prediction
                    ]);
                }

                return redirect()->route('scan.result', $diagnostic->id);
            } else {
                return response()->json([
                    'error' => 'Analyse échouée',
                    'prediction' => $prediction
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laisser Laravel gérer les erreurs de validation (422)
            throw $e;
        } catch (ConnectionException $e) {
            return response()->json([
                'error' => 'Service indisponible',
                'message' => "Service d’analyse indisponible. Veuillez démarrer l’API IA."
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher la liste des diagnostics
     */
    public function history()
    {
        $userId = auth()->id();
        $diagnostics = Diagnostic::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        if (request()->expectsJson()) {
            return response()->json([
                'diagnostics' => $diagnostics->items(),
                'pagination' => [
                    'current_page' => $diagnostics->currentPage(),
                    'last_page' => $diagnostics->lastPage(),
                    'per_page' => $diagnostics->perPage(),
                    'total' => $diagnostics->total(),
                ],
            ]);
        }

        return view('react', [
            'page' => 'history',
            'data' => [
                'diagnostics' => $diagnostics->items(),
                'pagination' => [
                    'current_page' => $diagnostics->currentPage(),
                    'last_page' => $diagnostics->lastPage(),
                    'per_page' => $diagnostics->perPage(),
                    'total' => $diagnostics->total(),
                ],
            ],
        ]);
    }

    /**
     * Afficher les détails d'un diagnostic
     */
    public function show($id)
    {
        $diagnostic = Diagnostic::findOrFail($id);

        if (!is_null($diagnostic->user_id)) {
            if (!auth()->check() || auth()->id() !== (int) $diagnostic->user_id) {
                abort(403);
            }
        } else {
            $allowedIds = session()->get('anon_diagnostic_ids', []);
            if (!in_array((int) $diagnostic->id, array_map('intval', (array) $allowedIds), true)) {
                abort(403);
            }
        }

        return view('scan.detail', compact('diagnostic'));
    }

    public function result($id)
    {
        $diagnostic = Diagnostic::findOrFail($id);

        if (!is_null($diagnostic->user_id)) {
            if (!auth()->check() || auth()->id() !== (int) $diagnostic->user_id) {
                abort(403);
            }
        } else {
            $allowedIds = session()->get('anon_diagnostic_ids', []);
            if (!in_array((int) $diagnostic->id, array_map('intval', (array) $allowedIds), true)) {
                abort(403);
            }
        }

        $diseases = config('diseases', []);
        $defaultDoc = $diseases['default'] ?? [
            'scientific_name' => '',
            'description' => 'Documentation en cours de mise à jour.',
            'causes' => [],
            'symptoms' => [],
            'impact' => 'Documentation en cours de mise à jour.',
            'prevention' => [],
            'treatment' => [],
            'best_practices' => [],
            'severity' => 'moderate',
        ];

        $rawKey = trim((string) $diagnostic->maladie);
        $map = [
            'Tomato___Early_blight' => 'Early Blight',
            'Tomato___Late_blight' => 'Late Blight',
            'Tomato___Septoria_leaf_spot' => 'Septoria Leaf Spot',
            'Septoria leaf spot' => 'Septoria Leaf Spot',
            'Tomato___Bacterial_spot' => 'Bacterial Spot',
            'Tomato___Bacterial_speck' => 'Bacterial Speck',
            'Tomato___Leaf_Mold' => 'Leaf Mold',
            'Tomato___Tomato_mosaic_virus' => 'Tomato Mosaic Virus',
            'Tomato___Tomato_Yellow_Leaf_Curl_Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato___Target_Spot' => 'Target Spot',
            'Tomato___Spider_mites Two-spotted_spider_mite' => 'Spider Mites Damage',
            'Tomato___healthy' => 'Healthy',
            'YellowLeaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Yellow Leaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato YellowLeaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato Yellow Leaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
        ];

        $key = $map[$rawKey] ?? $rawKey;
        $keyLower = mb_strtolower($key);
        if (str_contains($keyLower, 'curl') && str_contains($keyLower, 'virus') && str_contains($keyLower, 'yellow')) {
            $key = 'Tomato Yellow Leaf Curl Virus';
        }

        if (!isset($diseases[$key])) {
            $diseasesKeyMap = [];
            foreach (array_keys($diseases) as $dKey) {
                $diseasesKeyMap[mb_strtolower((string) $dKey)] = $dKey;
            }

            $maybeKey = $diseasesKeyMap[mb_strtolower((string) $key)] ?? null;
            if ($maybeKey) {
                $key = $maybeKey;
            }
        }

        $doc = $diseases[$key] ?? $defaultDoc;

        $imageUrl = null;
        if ($diagnostic->image_path) {
            $imageUrl = Storage::url($diagnostic->image_path);
        }
        $diagnostic->setAttribute('image_url', $imageUrl);

        if (request()->expectsJson()) {
            return response()->json([
                'diagnostic' => $diagnostic,
                'doc' => $doc,
            ]);
        }

        return view('react', [
            'page' => 'result',
            'data' => [
                'diagnostic' => $diagnostic,
                'doc' => $doc,
            ],
        ]);
    }

    public function resultPdf($id)
    {
        $diagnostic = Diagnostic::findOrFail($id);

        if (!is_null($diagnostic->user_id)) {
            if (!auth()->check() || auth()->id() !== (int) $diagnostic->user_id) {
                abort(403);
            }
        } else {
            $allowedIds = session()->get('anon_diagnostic_ids', []);
            if (!in_array((int) $diagnostic->id, array_map('intval', (array) $allowedIds), true)) {
                abort(403);
            }
        }

        $diseases = config('diseases', []);
        $defaultDoc = $diseases['default'] ?? [
            'scientific_name' => '',
            'description' => 'Documentation en cours de mise à jour.',
            'causes' => [],
            'symptoms' => [],
            'impact' => 'Documentation en cours de mise à jour.',
            'prevention' => [],
            'treatment' => [],
            'best_practices' => [],
            'severity' => 'moderate',
        ];

        $rawKey = trim((string) $diagnostic->maladie);
        $map = [
            'Tomato___Early_blight' => 'Early Blight',
            'Tomato___Late_blight' => 'Late Blight',
            'Tomato___Septoria_leaf_spot' => 'Septoria Leaf Spot',
            'Tomato___Bacterial_spot' => 'Bacterial Spot',
            'Tomato___Bacterial_speck' => 'Bacterial Speck',
            'Tomato___Leaf_Mold' => 'Leaf Mold',
            'Tomato___Tomato_mosaic_virus' => 'Tomato Mosaic Virus',
            'Tomato___Tomato_Yellow_Leaf_Curl_Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato___Target_Spot' => 'Target Spot',
            'Tomato___Spider_mites Two-spotted_spider_mite' => 'Spider Mites Damage',
            'Tomato___healthy' => 'Healthy',
            'YellowLeaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Yellow Leaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato YellowLeaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
            'Tomato Yellow Leaf Curl Virus' => 'Tomato Yellow Leaf Curl Virus',
        ];

        $key = $map[$rawKey] ?? $rawKey;
        $keyLower = mb_strtolower($key);
        if (str_contains($keyLower, 'curl') && str_contains($keyLower, 'virus') && str_contains($keyLower, 'yellow')) {
            $key = 'Tomato Yellow Leaf Curl Virus';
        }
        $doc = $diseases[$key] ?? $defaultDoc;

        if (!class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            abort(501, 'PDF non disponible (DomPDF non installé).');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('scan.result_pdf', [
            'diagnostic' => $diagnostic,
            'doc' => $doc,
        ]);

        $safeName = Str::slug(($diagnostic->plante ?: 'plante') . '-' . ($diagnostic->maladie ?: 'diagnostic'));
        $filename = $safeName . '-' . $diagnostic->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Afficher les statistiques
     */
    public function stats()
    {
        $total = Diagnostic::count();
        $plants = Diagnostic::groupBy('plante')
            ->selectRaw('plante, count(*) as count')
            ->get();
        $diseases = Diagnostic::groupBy('maladie')
            ->selectRaw('maladie, count(*) as count')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
        $healthy = Diagnostic::where('etat', 'Sain')->count();

        return view('scan.stats', compact('total', 'plants', 'diseases', 'healthy'));
    }

    /**
     * Supprimer un diagnostic
     */
    public function destroy($id)
    {
        $diagnostic = Diagnostic::findOrFail($id);
        Storage::disk('public')->delete($diagnostic->image_path);
        $diagnostic->delete();

        return response()->json(['success' => true]);
    }
}
