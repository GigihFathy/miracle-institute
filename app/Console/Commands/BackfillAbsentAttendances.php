<?php

namespace App\Console\Commands;

use App\Models\VideoSession;
use App\Services\AttendanceAutomationService;
use Illuminate\Console\Command;

class BackfillAbsentAttendances extends Command
{
    protected $signature = 'attendance:backfill-absent';
    protected $description = 'Backfill absent attendance for ended sessions';

    public function handle(AttendanceAutomationService $automationService): int
    {
        VideoSession::query()
            ->with(['topic.course'])
            ->whereNotNull('end_at')
            ->where('end_at', '<=', now())
            ->chunkById(100, function ($sessions) use ($automationService) {
                foreach ($sessions as $session) {
                    $automationService->backfillAbsentForEndedSession($session);
                }
            });

        return self::SUCCESS;
    }
}