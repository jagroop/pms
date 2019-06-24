<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileCreateRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\Issue;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileCreateRequest $request)
    {
        if($request->model_name == 'projects') {
          $project = Project::find($request->model_id);
          $project->addMediaFromRequest('file')->toMediaCollection('projects');
          return response()->json(['success' => 1, 'data' => $project->getUploadedFiles(), 'message' => 'Done']);
        } elseif($request->model_name == 'issues') {
          $issue = Issue::find($request->model_id);
          $issue->addMediaFromRequest('file')->toMediaCollection('issues');
          return response()->json(['success' => 1, 'data' => $issue->getUploadedFiles(), 'message' => 'Done']);
        } elseif($request->model_name == 'tasks') {
          $task = Task::find($request->model_id);
          $task->addMediaFromRequest('file')->toMediaCollection('tasks');
          return response()->json(['success' => 1, 'data' => $task->getUploadedFiles(), 'message' => 'Done']);
        } elseif($request->model_name == 'users') {
          // not in scope yet
        } else {
          return response()->json(['error' => 'Invalid model type selected.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
