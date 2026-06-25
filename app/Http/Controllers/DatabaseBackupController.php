<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseBackupController extends Controller
{
    protected string $disk = 'local';

    protected string $path = 'backups';

    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index()
    {
        $files = collect(Storage::disk($this->disk)->files($this->path))
            ->filter(fn ($f) => str_ends_with($f, '.sql'))
            ->map(fn ($f) => [
                'name' => basename($f),
                'size' => Storage::disk($this->disk)->size($f),
                'date' => Storage::disk($this->disk)->lastModified($f),
            ])
            ->sortByDesc('date')
            ->values();

        return view('database.backups', compact('files'));
    }

    public function create()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::connection()->getDatabaseName();
        $key = 'Tables_in_' . $dbName;
        $sql = "-- Backup generated at " . now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
            $createStr = $createTable[0]->{'Create Table'};
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= $createStr . ";\n\n";

            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) continue;

            $columns = array_keys((array) $rows->first());
            $chunks = $rows->chunk(100);

            foreach ($chunks as $chunk) {
                $values = $chunk->map(function ($row) use ($columns) {
                    $escaped = [];
                    foreach ($columns as $col) {
                        $val = $row->$col;
                        if ($val === null) {
                            $escaped[] = 'NULL';
                        } elseif (is_numeric($val) && !is_string($val)) {
                            $escaped[] = $val;
                        } else {
                            $escaped[] = "'" . str_replace("'", "\\'", (string) $val) . "'";
                        }
                    }
                    return '(' . implode(',', $escaped) . ')';
                })->implode(',');

                $cols = '`' . implode('`,`', $columns) . '`';
                $sql .= "INSERT INTO `$tableName` ($cols) VALUES $values;\n";
            }
            $sql .= "\n";
        }

        $filename = 'backup-' . now()->format('Y-m-d-Hi') . '.sql';
        Storage::disk($this->disk)->put($this->path . '/' . $filename, $sql);

        return redirect()->route('database.backups')->with('success', "Backup creado: $filename");
    }

    public function download($filename)
    {
        $filepath = $this->path . '/' . basename($filename);
        if (!Storage::disk($this->disk)->exists($filepath)) {
            abort(404);
        }
        return Storage::disk($this->disk)->download($filepath);
    }

    public function destroy($filename)
    {
        $filepath = $this->path . '/' . basename($filename);
        if (!Storage::disk($this->disk)->exists($filepath)) {
            abort(404);
        }
        Storage::disk($this->disk)->delete($filepath);
        return redirect()->route('database.backups')->with('success', "Backup eliminado: $filename");
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|string']);

        $filename = basename($request->backup_file);
        $filepath = $this->path . '/' . $filename;
        if (!Storage::disk($this->disk)->exists($filepath)) {
            return back()->withErrors(['backup_file' => 'Archivo no encontrado.']);
        }

        $sql = Storage::disk($this->disk)->get($filepath);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach (explode(";\n", $sql) as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !str_starts_with($statement, '--')) {
                try {
                    DB::unprepared($statement);
                } catch (\Exception $e) {
                    // skip errors per statement
                }
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('database.backups')->with('success', "Base de datos restaurada desde: $filename");
    }
}
