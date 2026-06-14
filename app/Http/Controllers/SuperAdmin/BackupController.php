<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    protected string $sqlBackupPath;
    protected string $zipBackupPath;

    public function __construct()
    {
        $this->sqlBackupPath = storage_path('app/backups/sql');
        $this->zipBackupPath = storage_path('app/backups/zip');
        $this->ensureDirectoriesExist();
    }

    protected function ensureDirectoriesExist(): void
    {
        foreach ([$this->sqlBackupPath, $this->zipBackupPath] as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    public function index()
    {
        $sqlBackups = Backup::where('backup_type', 'sql')->latest()->get();
        $zipBackups = Backup::where('backup_type', 'zip')->latest()->get();
        return view('super-admin.backups.index', compact('sqlBackups', 'zipBackups'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'sql');

        if ($type === 'zip') {
            return $this->createZipBackup($request);
        }

        return $this->createSqlBackup($request);
    }

    public function createSqlBackup(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $filename = 'backup_' . date('Y_m_d_His') . '.sql';
            $relativePath = 'sql/' . $filename;
            $fullPath = $this->sqlBackupPath . '/' . $filename;

            $this->exportDatabaseToSql($fullPath);

            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

            $backup = Backup::create([
                'file_name' => $filename,
                'file_path' => $relativePath,
                'file_size' => $this->formatBytes($fileSize),
                'backup_type' => 'sql',
                'created_by' => auth()->id(),
            ]);

            AuditTrail::create([
                'actor_id' => auth()->id(),
                'actor_type' => User::class,
                'action' => 'create',
                'module' => 'Backup & Restore',
                'description' => "Created SQL backup: {$filename}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('super-admin.backups.index')->with('success', 'SQL backup created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function createZipBackup(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $timestamp = date('Y_m_d_His');
            $sqlFilename = "backup_{$timestamp}.sql";
            $zipFilename = "backup_{$timestamp}.zip";

            $sqlPath = $this->sqlBackupPath . '/' . $sqlFilename;
            $zipPath = $this->zipBackupPath . '/' . $zipFilename;

            // Create SQL dump first
            $this->exportDatabaseToSql($sqlPath);

            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Failed to create ZIP archive.');
            }

            // Add SQL file
            $zip->addFile($sqlPath, 'database/' . $sqlFilename);

            // Add storage files
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage');

            $zip->close();

            // Clean up temp SQL file
            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }

            $fileSize = file_exists($zipPath) ? filesize($zipPath) : 0;

            $backup = Backup::create([
                'file_name' => $zipFilename,
                'file_path' => 'zip/' . $zipFilename,
                'file_size' => $this->formatBytes($fileSize),
                'backup_type' => 'zip',
                'created_by' => auth()->id(),
            ]);

            AuditTrail::create([
                'actor_id' => auth()->id(),
                'actor_type' => User::class,
                'action' => 'create',
                'module' => 'Backup & Restore',
                'description' => "Created ZIP backup: {$zipFilename}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('super-admin.backups.index')->with('success', 'ZIP backup created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'ZIP backup failed: ' . $e->getMessage());
        }
    }

    protected function exportDatabaseToSql(string $filePath): void
    {
        $db = config('database.connections.mysql');

        // Method 1: Try mysqldump
        $mysqldump = env('DB_DUMP_PATH', 'mysqldump');
        $passwordArg = filled($db['password'] ?? null) ? '--password=' . $db['password'] : '';

        $command = sprintf(
            '%s --host=%s --port=%d --user=%s %s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($mysqldump),
            escapeshellarg($db['host']),
            $db['port'],
            escapeshellarg($db['username']),
            $passwordArg,
            escapeshellarg($db['database']),
            escapeshellarg($filePath)
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($filePath) || filesize($filePath) === 0) {
            // Method 2: Laravel-based export as fallback
            $this->exportDatabaseViaLaravel($filePath);
        }
    }

    protected function exportDatabaseViaLaravel(string $filePath): void
    {
        $database = config('database.connections.mysql.database');
        $pdo = DB::connection()->getPdo();

        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        $output = '';

        foreach ($tables as $table) {
            // Create table
            $createTable = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(\PDO::FETCH_ASSOC);
            $output .= "\n\n" . $createTable['Create Table'] . ";\n\n";

            // Insert data
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $values = array_map([$pdo, 'quote'], array_values($row));
                $keys = array_keys($row);
                $output .= "INSERT INTO `$table` (`" . implode('`, `', $keys) . "`) VALUES (" . implode(', ', $values) . ");\n";
            }
        }

        File::put($filePath, $output);
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $prefix = ''): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $localPath = $file->getRealPath();
            $archivePath = $prefix . '/' . $iterator->getSubPathName();
            $zip->addFile($localPath, $archivePath);
        }
    }

    public function download(Backup $backup)
    {
        $filePath = $this->getBackupFilePath($backup);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Backup file not found.');
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'download',
            'module' => 'Backup & Restore',
            'description' => "Downloaded backup: {$backup->file_name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->download($filePath, $backup->file_name);
    }

    public function restore(Request $request, Backup $backup)
    {
        try {
            if ($backup->backup_type === 'zip') {
                return $this->restoreZipBackup($request, $backup);
            }

            return $this->restoreSqlBackup($request, $backup);
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    protected function restoreSqlBackup(Request $request, Backup $backup): \Illuminate\Http\RedirectResponse
    {
        $path = $this->getBackupFilePath($backup);

        if (!file_exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        $db = config('database.connections.mysql');
        $mysql = env('DB_RESTORE_PATH', 'mysql');

        $command = sprintf(
            '%s --host=%s --port=%d --user=%s %s %s < %s',
            escapeshellarg($mysql),
            escapeshellarg($db['host']),
            $db['port'],
            escapeshellarg($db['username']),
            filled($db['password'] ?? null) ? '--password=' . escapeshellarg($db['password']) : '',
            escapeshellarg($db['database']),
            escapeshellarg($path)
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception('Restore command failed with code: ' . $resultCode);
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'restore',
            'module' => 'Backup & Restore',
            'description' => "Restored SQL backup: {$backup->file_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super-admin.backups.index')->with('success', 'Database restored successfully.');
    }

    protected function restoreZipBackup(Request $request, Backup $backup): \Illuminate\Http\RedirectResponse
    {
        $path = $this->getBackupFilePath($backup);

        if (!file_exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== TRUE) {
            throw new \Exception('Invalid ZIP file.');
        }

        // Extract database SQL file
        $sqlFile = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (str_ends_with($stat['name'], '.sql')) {
                $sqlFile = $stat['name'];
                break;
            }
        }

        if (!$sqlFile) {
            $zip->close();
            throw new \Exception('No SQL file found in ZIP archive.');
        }

        // Restore database
        $tempSqlPath = storage_path('app/temp_restore.sql');
        copy('zip://' . $path . '#' . $sqlFile, $tempSqlPath);

        $this->restoreSqlFromFile($tempSqlPath);

        // Extract storage files
        $zip->extractTo(storage_path('app/public'), 'storage/');

        $zip->close();
        unlink($tempSqlPath);

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'restore',
            'module' => 'Backup & Restore',
            'description' => "Restored ZIP backup: {$backup->file_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('super-admin.backups.index')->with('success', 'System restored successfully.');
    }

    protected function restoreSqlFromFile(string $filePath): void
    {
        $db = config('database.connections.mysql');
        $mysql = env('DB_RESTORE_PATH', 'mysql');

        $command = sprintf(
            '%s --host=%s --port=%d --user=%s %s %s < %s',
            escapeshellarg($mysql),
            escapeshellarg($db['host']),
            $db['port'],
            escapeshellarg($db['username']),
            filled($db['password'] ?? null) ? '--password=' . escapeshellarg($db['password']) : '',
            escapeshellarg($db['database']),
            escapeshellarg($filePath)
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception('Database restore failed.');
        }
    }

    protected function getBackupFilePath(Backup $backup): string
    {
        return storage_path('app/backups/' . $backup->file_path);
    }

    public function destroy(Request $request, Backup $backup)
    {
        $path = $this->getBackupFilePath($backup);
        if (file_exists($path)) {
            unlink($path);
        }

        AuditTrail::create([
            'actor_id' => auth()->id(),
            'actor_type' => User::class,
            'action' => 'delete',
            'module' => 'Backup & Restore',
            'description' => "Deleted backup: {$backup->file_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $backup->delete();

        return redirect()->route('super-admin.backups.index')->with('success', 'Backup deleted successfully.');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}