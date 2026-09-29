<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupJsonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-json {--retention=7 : Number of latest backup snapshots to retain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely create timestamped backup snapshots of JSON database files with integrity validation and retention cleanup.';

    /**
     * Target JSON database files in storage/app/
     */
    protected array $targetFiles = [
        'bookings.json',
        'packages.json',
        'categories.json',
        'enquiries.json',
        'faqs.json',
        'settings.json',
        'cms.json',
        'artists.json',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Artizen JSON Database Automated Backup...');
        Log::info('JSON Backup started.');

        $timestamp = date('Y-m-d_H-i-s');
        $backupsBaseDir = storage_path('backups');
        $snapshotDir = $backupsBaseDir . '/' . $timestamp;

        // Step 1: Ensure root backup directory exists
        try {
            if (!File::isDirectory($backupsBaseDir)) {
                File::makeDirectory($backupsBaseDir, 0755, true, true);
            }
            if (!File::isDirectory($snapshotDir)) {
                File::makeDirectory($snapshotDir, 0755, true, true);
            }
        } catch (\Throwable $e) {
            $errorMsg = 'Failed to create backup directory: ' . $e->getMessage();
            $this->error($errorMsg);
            Log::error('JSON Backup failed: ' . $errorMsg);
            return Command::FAILURE;
        }

        $copiedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        // Step 2: Perform atomic read-only backup & post-validation
        foreach ($this->targetFiles as $filename) {
            $sourcePath = storage_path('app/' . $filename);

            if (!File::exists($sourcePath)) {
                $this->warn("Skipped (file missing): {$filename}");
                Log::info("JSON Backup skipped missing file: {$filename}");
                $skippedCount++;
                continue;
            }

            try {
                // Read source content without modifying original file
                $sourceContent = File::get($sourcePath);

                // Verify source JSON validity before copying
                json_decode($sourceContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Failed: Source file {$filename} contains invalid JSON!");
                    Log::error("JSON Backup source file invalid JSON: {$filename}");
                    $failedCount++;
                    continue;
                }

                $destinationPath = $snapshotDir . '/' . $filename;
                $written = File::put($destinationPath, $sourceContent);

                if ($written === false || !File::exists($destinationPath)) {
                    $this->error("Failed: Could not write backup for {$filename}");
                    Log::error("JSON Backup write failed: {$filename}");
                    $failedCount++;
                    continue;
                }

                // Post-Backup Integrity Validation
                $backupContent = File::get($destinationPath);
                json_decode($backupContent, true);

                if (json_last_error() !== JSON_ERROR_NONE || empty($backupContent)) {
                    $this->error("Failed: Backup file {$filename} integrity check failed!");
                    Log::error("JSON Backup post-validation failed: {$filename}");
                    // Delete invalid backup file to prevent partial backup
                    File::delete($destinationPath);
                    $failedCount++;
                    continue;
                }

                $this->info("Backed up & verified: {$filename}");
                Log::info("JSON Backup verified & saved: {$filename}");
                $copiedCount++;

            } catch (\Throwable $e) {
                $this->error("Exception while backing up {$filename}: " . $e->getMessage());
                Log::error("JSON Backup exception for {$filename}: " . $e->getMessage());
                $failedCount++;
            }
        }

        // Step 3: Handle retention policy cleanup (Keep latest N backups)
        $retentionLimit = max(1, (int)$this->option('retention'));
        $this->applyRetentionPolicy($backupsBaseDir, $retentionLimit);

        // Step 4: Final Summary & Exit Code
        if ($failedCount > 0) {
            $this->error("JSON Backup finished with {$failedCount} failure(s). Copied: {$copiedCount}, Skipped: {$skippedCount}");
            Log::error("JSON Backup finished with errors. Copied: {$copiedCount}, Failed: {$failedCount}, Skipped: {$skippedCount}");
            return Command::FAILURE;
        }

        if ($copiedCount === 0) {
            $this->warn("JSON Backup completed but 0 files were copied.");
            Log::warning("JSON Backup completed with 0 files copied.");
            return Command::SUCCESS;
        }

        $successMsg = "JSON Backup completed successfully! Snapshot: {$timestamp} | Copied: {$copiedCount} | Skipped: {$skippedCount}";
        $this->info($successMsg);
        Log::info($successMsg);

        return Command::SUCCESS;
    }

    /**
     * Apply retention policy keeping only the N most recent backup directories.
     */
    protected function applyRetentionPolicy(string $backupsBaseDir, int $retentionLimit): void
    {
        if (!File::isDirectory($backupsBaseDir)) {
            return;
        }

        $directories = File::directories($backupsBaseDir);
        if (count($directories) <= $retentionLimit) {
            return;
        }

        // Sort directories by name descending (Timestamp YYYY-MM-DD_HH-mm-ss sorts chronologically)
        usort($directories, function ($a, $b) {
            return strcmp(basename($b), basename($a));
        });

        // Retain top $retentionLimit, remove remaining older directories
        $dirsToDelete = array_slice($directories, $retentionLimit);

        foreach ($dirsToDelete as $oldDir) {
            $dirName = basename($oldDir);
            try {
                File::deleteDirectory($oldDir);
                $this->info("Retention cleanup: Deleted old backup snapshot [{$dirName}]");
                Log::info("JSON Backup retention policy deleted old snapshot: {$dirName}");
            } catch (\Throwable $e) {
                $this->warn("Failed to delete old backup directory [{$dirName}]: " . $e->getMessage());
                Log::error("JSON Backup retention policy failed to delete: {$dirName}");
            }
        }
    }
}
