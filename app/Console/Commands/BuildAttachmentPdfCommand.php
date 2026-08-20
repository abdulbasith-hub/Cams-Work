<?php

namespace App\Console\Commands;

use App\Http\Controllers\FormatController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BuildAttachmentPdfCommand extends Command
{
    protected $signature = 'cams:build-attachment-pdf
        {scheduleId}
        {lang}
        {spilloverflag}
        {instid}
        {financialyearcode}';

    protected $description = 'Build the cached attachment PDF in the background';

    public function handle(FormatController $formatController): int
    {
        try {
            $formatController->DownloadAuditReport(
                (int) $this->argument('scheduleId'),
                (string) $this->argument('lang'),
                $this->argument('spilloverflag'),
                $this->argument('instid'),
                $this->argument('financialyearcode'),
                'download_attachments_worker'
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Attachment PDF background build failed', [
                'schedule_id' => $this->argument('scheduleId'),
                'message' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
