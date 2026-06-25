<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogViewerController extends Controller
{
    protected string $logPath;

    public function __construct()
    {
        $this->middleware('can:admin');
        $this->logPath = storage_path('logs');
    }

    public function index()
    {
        $files = collect(scandir($this->logPath))
            ->filter(fn ($f) => str_ends_with($f, '.log'))
            ->values()
            ->map(fn ($f) => [
                'name' => $f,
                'size' => filesize($this->logPath . '/' . $f),
                'date' => filemtime($this->logPath . '/' . $f),
            ])
            ->sortByDesc('date')
            ->values();

        return view('logs.index', compact('files'));
    }

    public function show($filename)
    {
        $filepath = $this->logPath . '/' . basename($filename);
        if (!file_exists($filepath)) {
            abort(404);
        }

        $lines = file($filepath);
        $totalLines = count($lines);

        $page = max(1, (int) request('page', 1));
        $perPage = 200;
        $lastPage = max(1, (int) ceil($totalLines / $perPage));
        $offset = ($page - 1) * $perPage;

        $contentLines = array_slice($lines, $offset, $perPage);

        return view('logs.show', compact('filename', 'contentLines', 'page', 'lastPage', 'totalLines'));
    }

    public function destroy($filename)
    {
        $filepath = $this->logPath . '/' . basename($filename);
        if (!file_exists($filepath)) {
            abort(404);
        }
        unlink($filepath);
        return redirect()->route('logs.index')->with('success', "Log eliminado: $filename");
    }
}
