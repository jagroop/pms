<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\Project;
use App\Mail\DailyStatus;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;
use App\Http\Controllers\Controller;

class TestController extends Controller
{

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

    public function tasks()
    {     
        // die('here');   
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
          Mail::to('jagroop.singh@kindlebit.com')->send(new DailyStatus($tasks, $projectsStats));
        }
        die('Done');
    }
}
