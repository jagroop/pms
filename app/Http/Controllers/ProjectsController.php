<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Http\Requests\ProjectCreateRequest;
use App\Http\Requests\ProjectUpdateRequest;

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
        $projects = $this->service->paginated();
        return view('projects.index')->with('projects', $projects);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $projects = $this->service->search($request->search);
        return view('projects.index')->with('projects', $projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProjectCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectCreateRequest $request)
    {
        $result = $this->service->create($request->except('_token'));

        if ($result) {
            return redirect(route('projects.edit', ['id' => $result->id]))->with('message', 'Successfully created');
        }

        return redirect(route('projects.index'))->withErrors('Failed to create');
    }

    /**
     * Display the project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = $this->service->find($id);
        return view('projects.show')->with('project', $project);
    }

    /**
     * Show the form for editing the project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = $this->service->find($id);
        return view('projects.edit')->with('project', $project);
    }

    /**
     * Update the projects in storage.
     *
     * @param  App\Http\Requests\ProjectUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            return back()->with('message', 'Successfully updated');
        }

        return back()->withErrors('Failed to update');
    }

    /**
     * Remove the projects from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return redirect(route('projects.index'))->with('message', 'Successfully deleted');
        }

        return redirect(route('projects.index'))->withErrors('Failed to delete');
    }
}
