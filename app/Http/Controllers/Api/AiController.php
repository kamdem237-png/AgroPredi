<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    private $flask_url = 'http://127.0.0.1:5001';

    public function health(Request $request)
    {
        // Si une clé API est fournie côté serveur, la valider
        $apiKey = env('AI_API_KEY', null);
        if ($apiKey) {
            $provided = $request->header('X-API-KEY');
            if (!$provided || $provided !== $apiKey) {
                return response()->json(['status' => 'unauthorized'], 401);
            }
        }
        try {
            $response = Http::timeout(5)->get($this->flask_url . '/health');

            if ($response->successful()) {
                return response()->json([
                    'status' => 'ok',
                    'service' => 'flask_api',
                    'detail' => $response->json()
                ]);
            }

            return response()->json([
                'status' => 'down',
                'service' => 'flask_api'
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}
