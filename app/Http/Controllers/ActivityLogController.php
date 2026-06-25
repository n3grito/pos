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

        $logs = $query->paginate(50);

        $users = \App\Models\User::orderBy('name')->get();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'actions'));
    }
}
