<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function overview(Request $request)
    {
        $totalUsers = User::count();
        $totalScans = Diagnostic::count();

        $activeUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
            ->distinct()
            ->pluck('user_id');

        $activeUsers = $activeUserIds->count();

        $series = Diagnostic::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topDiseases = Diagnostic::query()
            ->selectRaw('maladie, COUNT(*) as count')
            ->groupBy('maladie')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        $latestDiagnostics = Diagnostic::query()
            ->with('user:id,name,email')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($d) {
                $imageUrl = $d->image_path ? Storage::url($d->image_path) : null;
                $d->setAttribute('image_url', $imageUrl);
                return $d;
            });

        return response()->json([
            'totals' => [
                'users' => $totalUsers,
                'active_users' => $activeUsers,
                'scans' => $totalScans,
            ],
            'scans_per_day' => $series,
            'top_diseases' => $topDiseases,
            'latest_diagnostics' => $latestDiagnostics,
        ]);
    }

    public function users(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->withCount('diagnostics')
            ->orderByDesc('diagnostics_count')
            ->orderBy('id')
            ->take(100)
            ->get(['id', 'name', 'email', 'is_admin', 'is_banned', 'created_at']);

        return response()->json([
            'users' => $users,
        ]);
    }

    public function userDiagnostics(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $diagnostics = Diagnostic::query()
            ->where('user_id', $user->id)
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($d) {
                $imageUrl = $d->image_path ? Storage::url($d->image_path) : null;
                $d->setAttribute('image_url', $imageUrl);
                return $d;
            });

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'is_admin', 'is_banned', 'created_at']),
            'diagnostics' => $diagnostics,
        ]);
    }

    public function toggleBan(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas bannir votre propre compte.',
            ], 422);
        }

        $user->is_banned = !$user->is_banned;
        $user->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'user' => $user->fresh()->only(['id', 'name', 'email', 'is_admin', 'is_banned']),
        ]);
    }

    public function diagnostics(Request $request)
    {
        $perPage = (int) $request->query('per_page', 25);
        if ($perPage < 5) $perPage = 5;
        if ($perPage > 100) $perPage = 100;

        $diagnostics = Diagnostic::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate($perPage);

        $diagnostics->getCollection()->transform(function ($d) {
            $imageUrl = $d->image_path ? Storage::url($d->image_path) : null;
            $d->setAttribute('image_url', $imageUrl);
            return $d;
        });

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
}
