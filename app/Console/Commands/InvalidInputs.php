<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Project;
use App\Mail\InvalidInputMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;

class InvalidInputs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:status_invalid';

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
        $now = now()->toDateTimeString();
        $past10Hours = now()->subHours(12)->toDateTimeString();  
        TaskResource::withoutWrapping();
        
        $todayTasks = Task::whereBetween('created_at', [$past10Hours, $now])->where(function($query){
          return $query->whereIn('task_status', ['in_progress', 'done', 'feedback', 'deployed'])->where(function($q){
            return $q->whereNull('work_hours')->orWhere('work_hours', '<=', 0);
          });
        })->get();

        $tasks  = collect(TaskResource::collection($todayTasks))->sortBy('project_name');      
        $cc = array_unique($tasks->pluck('assigned_to_email')->all());
        if(count($todayTasks) > 0) {
          Mail::to('jagroop.singh@kindlebit.com')->cc($cc)->send(new InvalidInputMail($tasks));
        }
    }
}
