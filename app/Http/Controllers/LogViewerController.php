<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogViewerController extends Controller
{
    protected string $logPath;

    public function __construct()
    {
        $this->middleware('can:log.viewer');
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
                'error_count' => 0,
                'warning_count' => 0,
            ])
            ->sortByDesc('date')
            ->values();

        foreach ($files as &$file) {
            $filepath = $this->logPath . '/' . $file['name'];
            if (!file_exists($filepath)) {
                continue;
            }
            $content = file_get_contents($filepath);
            preg_match_all('/\.(ERROR|CRITICAL|ALERT|EMERGENCY):/', $content, $errMatches);
            $file['error_count'] = count($errMatches[0]);
            preg_match_all('/\.(WARNING):/', $content, $warnMatches);
            $file['warning_count'] = count($warnMatches[0]);
        }
        unset($file);

        return view('logs.index', compact('files'));
    }

    public function show(Request $request, $filename)
    {
        $filepath = $this->logPath . '/' . basename($filename);
        if (!file_exists($filepath)) {
            abort(404);
        }

        $lines = file($filepath);
        $entries = $this->parseEntries($lines);

        $level = $request->input('level');
        $search = $request->input('search');

        if ($level) {
            $entries = $entries->filter(fn ($e) => $e['level'] === strtoupper($level));
        }

        if ($search) {
            $entries = $entries->filter(fn ($e) => str_contains(
                mb_strtolower($e['message'] . ' ' . ($e['context'] ?? '')),
                mb_strtolower($search)
            ));
        }

        $totalEntries = $entries->count();
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 100;
        $lastPage = max(1, (int) ceil($totalEntries / $perPage));
        $offset = ($page - 1) * $perPage;

        $paginated = $entries->slice($offset, $perPage)->values();

        $levels = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];

        return view('logs.show', compact(
            'filename', 'paginated', 'page', 'lastPage', 'totalEntries',
            'levels', 'level', 'search'
        ));
    }

    public function destroy($filename)
    {
        $filepath = $this->logPath . '/' . basename($filename);
        if (!file_exists($filepath)) {
            abort(404);
        }
        unlink($filepath);
        toast("Log eliminado: $filename", 'success');
        return redirect()->route('logs.index');
    }

    protected function parseEntries(array $lines): \Illuminate\Support\Collection
    {
        $entries = collect();
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:?\d{2})?)\]\s+(\w+)\.(\w+):\s*(.+)/', $line, $m)) {
                if ($current) {
                    $entries->push($current);
                }
                $current = [
                    'datetime' => $m[1],
                    'environment' => $m[2],
                    'level' => $m[3],
                    'message' => $m[4],
                    'context' => '',
                    'stack_trace' => '',
                ];
            } elseif ($current) {
                if (str_starts_with(trim($line), '{') || str_starts_with(trim($line), '[')) {
                    $current['context'] .= $line;
                } elseif (str_starts_with(trim($line), '#')) {
                    $current['stack_trace'] .= $line;
                } else {
                    $current['message'] .= "\n" . $line;
                }
            }
        }

        if ($current) {
            $entries->push($current);
        }

        return $entries;
    }
}
