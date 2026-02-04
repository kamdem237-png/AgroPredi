<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Storage;

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
        return view('scan.upload');
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

                return response()->json([
                    'success' => true,
                    'diagnostic_id' => $diagnostic->id,
                    'prediction' => $prediction
                ]);
            } else {
                return response()->json([
                    'error' => 'Analyse échouée',
                    'prediction' => $prediction
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laisser Laravel gérer les erreurs de validation (422)
            throw $e;
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
        $diagnostics = Diagnostic::orderBy('created_at', 'desc')->paginate(12);
        return view('scan.history', compact('diagnostics'));
    }

    /**
     * Afficher les détails d'un diagnostic
     */
    public function show($id)
    {
        $diagnostic = Diagnostic::findOrFail($id);
        return view('scan.detail', compact('diagnostic'));
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
