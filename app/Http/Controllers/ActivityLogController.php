<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:activity-log.view');
    }

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->boolean('notable')) {
            $query->where('notable', true);
        }

        $logs = $query->paginate(50);

        $users = \App\Models\User::orderBy('name')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $severities = ['info', 'warning', 'critical'];

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'severities'));
    }

    public function recentAlerts()
    {
        $alerts = ActivityLog::with('user')
            ->notable()
            ->latest('created_at')
            ->limit(20)
            ->get();

        return response()->json($alerts);
    }

    public function stream()
    {
        $since = now()->subMinutes(5);

        if (request()->filled('since')) {
            $since = now()->subSeconds(min(300, max(10, (int) request('since'))));
        }

        $alerts = ActivityLog::with('user')
            ->notable()
            ->where('created_at', '>=', $since)
            ->latest('created_at')
            ->limit(50)
            ->get();

        return response()->json([
            'alerts' => $alerts,
            'count' => $alerts->count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
