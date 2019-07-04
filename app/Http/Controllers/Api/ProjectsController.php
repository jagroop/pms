<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;

class ProjectsController extends Controller
{
    public function __construct(ProjectService $project_service)
    {
        $this->service = $project_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = [];
        if($request->has('all')) {
          $projects = $this->service->all();          
        } else {
          $projects = $this->service->paginated();          
        }
        return ProjectResource::collection($projects);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $projects = $this->service->search($request->search);
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectCreateRequest $request)
    {
        $data = $request->only(['name', 'status', 'started_date', 'closed_date']);   
        $data['user_id'] = auth()->user()->id;
        $result = $this->service->create($data);

        if ($result) {
            $project = new ProjectResource($this->service->find($result->id));
            return response()->json($project);
        }

        return response()->json(['error' => 'Unable to create project'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = $this->service->find($id);
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\ProjectRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            $project = new ProjectResource($this->service->find($id));
            return response()->json($project);
        }

        return response()->json(['error' => 'Unable to update project'], 500);
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
            return response()->json(['success' => 'Project was deleted'], 200);
        }

        return response()->json(['error' => 'Unable to delete project'], 500);
    }
}
