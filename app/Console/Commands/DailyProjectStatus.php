<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Project;
use App\Mail\DailyStatus;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;

class DailyProjectStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:status_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily status update command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function projectsStats($ids) {
      return Project::find($ids)->map(function($project){
        $item = [];
        $item['name'] = $project->name;
        $item['status'] = ucwords(str_replace('_', ' ', $project->status));
        $item['billing_hours'] = $project->billingHours();
        $item['billing_hours_today'] = $project->billingHours(true);
        $item['work_hours'] = $project->workHours();
        $item['work_hours_today'] = $project->workHours(true);
        return $item;
      });
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cc = ['jagroop.singh@kindlebit.com'];
        $now = now()->toDateTimeString();
        $past10Hours = now()->subHours(12)->toDateTimeString();  
        TaskResource::withoutWrapping();      
        $todayTasks = Task::whereBetween('created_at', [$past10Hours, $now])->get();
        $tasks  = collect(TaskResource::collection($todayTasks))->sortBy('project_name');
        $projectIds = array_unique($tasks->pluck('project_id')->all());
        $projectsStats = $this->projectsStats($projectIds);
        $cc = array_unique(array_merge($cc, $tasks->pluck('assigned_to_email')->all()));
        if(count($todayTasks) > 0) {
          Mail::to('ram.sharma@kindlebit.com')->cc($cc)->send(new DailyStatus($tasks, $projectsStats));
        }
    }
}
