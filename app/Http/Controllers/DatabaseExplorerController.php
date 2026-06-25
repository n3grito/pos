<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseExplorerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index()
    {
        $dbName = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLE STATUS');
        $dbSize = 0;
        foreach ($tables as $table) {
            $dbSize += $table->Data_length + $table->Index_length;
        }

        $tables = collect($tables)->map(fn ($t) => [
            'name' => $t->Name,
            'engine' => $t->Engine,
            'rows' => $t->Rows,
            'size' => $t->Data_length + $t->Index_length,
            'collation' => $t->Collation,
            'comment' => $t->Comment,
        ]);

        return view('database.explorer', compact('tables', 'dbName', 'dbSize'));
    }

    public function show($table)
    {
        if (!DB::select('SHOW TABLES LIKE ?', [$table])) {
            abort(404);
        }

        $columns = DB::select("SHOW FULL COLUMNS FROM `$table`");
        $indexes = DB::select("SHOW INDEX FROM `$table`");

        $page = request('page', 1);
        $perPage = 25;
        $total = DB::table($table)->count();
        $rows = DB::table($table)->paginate($perPage);

        return view('database.table-data', compact('table', 'columns', 'indexes', 'rows', 'total'));
    }

    public function query(Request $request)
    {
        $request->validate(['sql' => 'required|string']);

        $sql = trim($request->sql);
        $upper = strtoupper($sql);

        if (!str_starts_with($upper, 'SELECT') && !str_starts_with($upper, 'SHOW') && !str_starts_with($upper, 'DESCRIBE')) {
            return back()->withErrors(['sql' => 'Solo se permiten consultas SELECT, SHOW y DESCRIBE.']);
        }

        $startTime = microtime(true);
        try {
            $results = DB::select($sql);
        } catch (\Exception $e) {
            return back()->withErrors(['sql' => $e->getMessage()]);
        }
        $execTime = round((microtime(true) - $startTime) * 1000, 2);

        $columns = !empty($results) ? array_keys((array) $results[0]) : [];

        return back()->with(compact('results', 'columns', 'execTime', 'sql'));
    }
}
