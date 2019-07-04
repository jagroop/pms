<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Issue;

class UpdateProtalData extends Command
{

    protected $skipStatus = [
      'done',
      'closed',
      'fixed',
      'resolved'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:update_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Task::whereNotIn('task_status', $this->skipStatus)
              ->whereDate('created_at', '!=', now()->toDateTimeString())
              ->update(['work_hours' => 0, 'created_at' => now()->toDateTimeString()]);

        Issue::whereNotIn('issue_status', $this->skipStatus)
               ->whereDate('created_at', '!=', now()->toDateTimeString())
               ->update(['created_at' => now()->toDateTimeString()]);
    }
}
