<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use Spatie\Activitylog\Models\Activity;

class TasksController extends Controller
{
    public function __construct(TaskService $task_service)
    {
        $this->service = $task_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        $filters = json_decode($request->filters);
        $tasks = $this->service->paginated($filters);
        return TaskResource::collection($tasks);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $tasks = $this->service->search($request->search);
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\TaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request)
    {
        $data = $request->except('_token');
        if(trim($request->input('assigned_to')) == '') {
          $data['assigned_to'] = auth()->user()->id;
        }

        if($data['work_hours'] > 0) {
          $data['hours_updated_at'] = now()->toDateTimeString();
        }

        $data['total_work_hours'] = $data['work_hours'];
        $result = $this->service->create($data);

        if ($result) {
           //log activity
           activity('tasks')
               ->performedOn($result)
               ->causedBy(auth()->user())
               ->withProperties(['current_status' => ucwords(str_replace('_', ' ', $request->task_status))])
               ->log(auth()->user()->name . ' updated task status to ' . $request->task_status);
            if($data['assigned_to'] != auth()->user()->id) {
              // Send NOtification
              \Notifications::notify($data['assigned_to'], 'success', auth()->user()->name  . ' have assigned you a task #' . $result->id, 'tasks');
            }  
            $task = new TaskResource($this->service->find($result->id));
            return response()->json($task);
        }

        return response()->json(['error' => 'Unable to create task'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = $this->service->find($id);
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\TaskRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, $id)
    {        
        $task = $this->service->find($id);
        $data = $request->except(['index']);
        $data['total_work_hours'] = $task->total_work_hours + $data['work_hours'];

        if($data['work_hours'] != $task->work_hours && $data['work_hours'] > 0) {
          $data['hours_updated_at'] = now()->toDateTimeString();
        }

        $result = $this->service->update($id, $data);
        if ($result) {
            
            if($task->task_status != $request->task_status) {
              // log activity
              activity('tasks')
               ->performedOn($task)
               ->causedBy(auth()->user())
               ->withProperties(['current_status' => ucwords(str_replace('_', ' ', $request->task_status))])
               ->log(auth()->user()->name . ' updated task status from ' . $task->task_status . ' to ' . $request->task_status);
            }
            $task = $this->service->find($id);
            $collection = new TaskResource($task);
            return response()->json($collection);
        }

        return response()->json(['error' => 'Unable to update task'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return response()->json(['success' => 'Task was deleted'], 200);
        }

        return response()->json(['error' => 'Unable to delete task'], 500);
    }

    public function logs()
    {
        return Activity::whereDate('created_at', '>=', now()->subdays(23)->toDateString())->get()->map(function($activity){
          
          $subject = 'Deleted !!';

          if(@$activity->subject->id) {
            $subject = '#' . @$activity->subject->id .' '. (($activity->log_name == 'tasks') ? @$activity->subject->task_name : @$activity->subject->issue_name);
          }

          return [
            'title'            => $activity->description, 
            'label'            => $activity->getExtraProperty('current_status'),
            'created_at'       => $activity->created_at->format('d-M-Y h:m:i A'),
            'created_at_human' => $activity->created_at->diffForhumans(),
            'mine'             => $activity->causer_id == auth()->user()->id,
            'subject'          => $subject,
          ];

      })->all(); 
    }
}
