<?php

namespace App\Livewire\Logs;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class LogViewer extends Component
{
    public $search = '';
    public $level = '';
    public $date = '';
    public $logFile = '';
    public $showModal = false;
    public $selectedLog = null;
    public $availableLogFiles = [];
    public $perPage = 25;
    public $currentPage = 1;
    public $autoRefresh = false;
    public $refreshInterval = 30; // segundos
    public $sortBy = 'timestamp';
    public $sortDirection = 'desc';
    public $showStats = true;
    public $logCache = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'level' => ['except' => ''],
        'date' => ['except' => ''],
        'logFile' => ['except' => ''],
        'currentPage' => ['except' => 1],
        'perPage' => ['except' => 25],
    ];

    protected $listeners = ['refreshLogs' => 'refreshLogs'];

    public function mount()
    {
        $this->loadAvailableLogFiles();
        $this->logFile = $this->availableLogFiles[0] ?? '';
    }

    public function loadAvailableLogFiles()
    {
        $cacheKey = 'log_files_' . md5(storage_path('logs'));
        
        $this->availableLogFiles = Cache::remember($cacheKey, 60, function () {
            $logPath = storage_path('logs');
            $files = [];
            
            if (File::exists($logPath)) {
                $fileObjects = File::files($logPath);
                foreach ($fileObjects as $file) {
                    if (str_ends_with($file->getFilename(), '.log')) {
                        $files[] = [
                            'name' => $file->getFilename(),
                            'size' => $file->getSize(),
                            'modified' => $file->getMTime(),
                        ];
                    }
                }
            }
            
            // Ordenar por fecha de modificación (más recientes primero)
            usort($files, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });
            
            return array_column($files, 'name');
        });
    }

    public function updatedLogFile()
    {
        $this->currentPage = 1;
    }

    public function updatedSearch()
    {
        $this->currentPage = 1;
    }

    public function updatedLevel()
    {
        $this->currentPage = 1;
    }

    public function updatedDate()
    {
        $this->currentPage = 1;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->level = '';
        $this->date = '';
        $this->currentPage = 1;
        $this->clearCache();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function refreshLogs()
    {
        $this->clearCache();
        $this->loadAvailableLogFiles();
    }

    public function clearCache()
    {
        Cache::forget('log_files_' . md5(storage_path('logs')));
        Cache::forget('logs_' . md5($this->logFile . $this->search . $this->level . $this->date));
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->clearCache();
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->currentPage = 1;
    }

    public function exportLogs()
    {
        if (!$this->logFile) {
            session()->flash('error', 'No hay archivo de log seleccionado.');
            return;
        }

        $logs = $this->logs;
        $csvData = "Timestamp,Level,Message\n";
        
        foreach ($logs as $log) {
            $csvData .= '"' . $log['timestamp'] . '","' . $log['level'] . '","' . str_replace('"', '""', $log['message']) . '"' . "\n";
        }

        $filename = 'logs_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function viewLog($logEntry)
    {
        try {
            $decodedLog = json_decode(base64_decode($logEntry), true);
            $this->selectedLog = $decodedLog;
            $this->showModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el log seleccionado.');
            $this->selectedLog = null;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedLog = null;
    }

    public function downloadLog()
    {
        if (!$this->logFile) {
            return;
        }

        $logPath = storage_path('logs/' . $this->logFile);
        
        if (!File::exists($logPath)) {
            session()->flash('error', 'El archivo de log no existe.');
            return;
        }

        return response()->download($logPath);
    }

    public function clearLog()
    {
        if (!$this->logFile) {
            return;
        }

        $logPath = storage_path('logs/' . $this->logFile);
        
        if (File::exists($logPath)) {
            File::put($logPath, '');
            session()->flash('success', 'El archivo de log ha sido limpiado.');
            $this->loadAvailableLogFiles();
        }
    }

    #[Computed]
    public function logs()
    {
        if (!$this->logFile) {
            return collect();
        }

        $cacheKey = 'logs_' . md5($this->logFile . $this->search . $this->level . $this->date);
        
        return Cache::remember($cacheKey, 30, function () {
            $logPath = storage_path('logs/' . $this->logFile);
            
            if (!File::exists($logPath)) {
                return collect();
            }

            $logs = collect();
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            
            // Procesar solo las últimas 1000 líneas para mejor rendimiento
            $lines = array_slice($lines, -1000);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $logEntry = $this->parseLogLine($line);
                
                if ($logEntry) {
                    // Aplicar filtros
                    if ($this->search && !$this->matchesSearch($logEntry, $this->search)) {
                        continue;
                    }
                    
                    if ($this->level && $logEntry['level'] !== $this->level) {
                        continue;
                    }
                    
                    if ($this->date && !$this->matchesDate($logEntry, $this->date)) {
                        continue;
                    }
                    
                    $logs->push($logEntry);
                }
            }

            // Ordenar
            return $this->sortLogs($logs);
        });
    }

    private function sortLogs($logs)
    {
        if ($this->sortBy === 'timestamp') {
            return $this->sortDirection === 'desc' 
                ? $logs->sortByDesc('timestamp')->values()
                : $logs->sortBy('timestamp')->values();
        }
        
        if ($this->sortBy === 'level') {
            return $this->sortDirection === 'desc' 
                ? $logs->sortByDesc('level')->values()
                : $logs->sortBy('level')->values();
        }
        
        return $logs->sortByDesc('timestamp')->values();
    }

    private function parseLogLine($line)
    {
        // Patrón para logs de Laravel
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] local\.(\w+): (.+)$/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'level' => strtoupper($matches[2]),
                'message' => $matches[3],
                'raw' => $line,
                'formatted_time' => Carbon::parse($matches[1])->format('d/m/Y H:i:s'),
                'level_color' => $this->getLevelColor(strtoupper($matches[2])),
            ];
        }

        // Si no coincide con el patrón estándar, crear entrada básica
        return [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'level' => 'INFO',
            'message' => $line,
            'raw' => $line,
            'formatted_time' => now()->format('d/m/Y H:i:s'),
            'level_color' => 'blue',
        ];
    }

    private function matchesSearch($logEntry, $search)
    {
        return stripos($logEntry['message'], $search) !== false ||
               stripos($logEntry['level'], $search) !== false ||
               stripos($logEntry['raw'], $search) !== false;
    }

    private function matchesDate($logEntry, $date)
    {
        try {
            $logDate = Carbon::parse($logEntry['timestamp'])->format('Y-m-d');
            return $logDate === $date;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getLevelColor($level)
    {
        return match($level) {
            'ERROR' => 'red',
            'CRITICAL' => 'red',
            'ALERT' => 'red',
            'EMERGENCY' => 'red',
            'WARNING' => 'yellow',
            'NOTICE' => 'blue',
            'INFO' => 'green',
            'DEBUG' => 'gray',
            default => 'blue',
        };
    }

    #[Computed]
    public function levels()
    {
        return ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
    }

    #[Computed]
    public function availableDates()
    {
        return $this->logs->pluck('timestamp')
            ->map(function($timestamp) {
                try {
                    return Carbon::parse($timestamp)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    #[Computed]
    public function paginatedLogs()
    {
        return $this->logs->forPage($this->currentPage, $this->perPage);
    }

    #[Computed]
    public function totalLogs()
    {
        return $this->logs->count();
    }

    #[Computed]
    public function totalPages()
    {
        return ceil($this->totalLogs / $this->perPage);
    }

    #[Computed]
    public function logStats()
    {
        $logs = $this->logs;
        return [
            'total' => $logs->count(),
            'errors' => $logs->whereIn('level', ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'])->count(),
            'warnings' => $logs->where('level', 'WARNING')->count(),
            'info' => $logs->where('level', 'INFO')->count(),
            'debug' => $logs->where('level', 'DEBUG')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.logs.log-viewer');
    }
}
