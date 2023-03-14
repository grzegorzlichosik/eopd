<?php

namespace App\Console\Commands;

use App\Models\Encounter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChangeEncounterStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whencounter:change_encounter_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): int
    {
        Encounter::where('ends_at', '<=', now()->startOfMinute()->subHour())
            ->where('tsm_current_state', Encounter::STATE_SCHEDULED)
            ->get()
            ->each(function ($encounter) {
                $encounter->transit('encounter_finish');
            });

        return Command::SUCCESS;
    }
}
