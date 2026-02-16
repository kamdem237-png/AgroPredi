<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalScans = Diagnostic::where('user_id', $user->id)->count();
        $healthyScans = Diagnostic::where('user_id', $user->id)->where('etat', 'Sain')->count();
        $severeScans = Diagnostic::where('user_id', $user->id)->where('niveau_risque', 'Élevé')->count();
        $latestScan = Diagnostic::where('user_id', $user->id)->orderByDesc('created_at')->first();

        $diseaseDistribution = Diagnostic::where('user_id', $user->id)
            ->whereNotNull('maladie')
            ->groupBy('maladie')
            ->selectRaw('maladie, count(*) as count')
            ->orderByDesc('count')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'totalScans' => $totalScans,
                'healthyScans' => $healthyScans,
                'severeScans' => $severeScans,
                'latestScan' => $latestScan,
                'diseaseDistribution' => $diseaseDistribution,
            ]);
        }

        return view('react', [
            'page' => 'dashboard',
            'data' => [
                'totalScans' => $totalScans,
                'healthyScans' => $healthyScans,
                'severeScans' => $severeScans,
                'latestScan' => $latestScan,
                'diseaseDistribution' => $diseaseDistribution,
            ],
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $diagnostics = Diagnostic::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(12);

        if ($request->expectsJson()) {
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
            'page' => 'dashboard.history',
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
}
