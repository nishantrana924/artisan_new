<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupJsonTest extends TestCase
{
    /**
     * Test artisan command app:backup-json executes successfully.
     */
    public function test_backup_json_command_executes_successfully(): void
    {
        $this->artisan('app:backup-json')
            ->assertExitCode(0);
    }

    /**
     * Test backup snapshot directory is created and files are copied & verified.
     */
    public function test_backup_creates_timestamped_directory_and_copies_valid_json(): void
    {
        $this->artisan('app:backup-json')->assertExitCode(0);

        $backupsBaseDir = storage_path('backups');
        $this->assertTrue(File::isDirectory($backupsBaseDir), 'Backups root directory should exist');

        $directories = File::directories($backupsBaseDir);
        $this->assertNotEmpty($directories, 'At least one backup directory snapshot should exist');

        $latestDir = end($directories);
        $files = File::files($latestDir);

        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            $decoded = json_decode($content, true);
            $this->assertNotNull($decoded, "File {$file->getFilename()} in backup should contain valid JSON");
        }
    }

    /**
     * Test retention policy limits snapshots to 7.
     */
    public function test_retention_policy_limits_snapshots_to_seven(): void
    {
        $backupsBaseDir = storage_path('backups');

        // Create 10 dummy snapshot directories
        for ($i = 1; $i <= 10; $i++) {
            $dummyDate = sprintf('2026-08-%02d_00-00-00', $i);
            $dummyPath = $backupsBaseDir . '/' . $dummyDate;
            if (!File::isDirectory($dummyPath)) {
                File::makeDirectory($dummyPath, 0755, true, true);
            }
        }

        // Run backup with retention option = 7
        $this->artisan('app:backup-json --retention=7')->assertExitCode(0);

        $directories = File::directories($backupsBaseDir);
        $this->assertLessThanOrEqual(7, count($directories), 'Retention policy should keep at most 7 backup directories');
    }
}
