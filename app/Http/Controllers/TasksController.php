<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;

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
        $tasks = $this->service->paginated();
        return view('tasks.index')->with('tasks', $tasks);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $tasks = $this->service->search($request->search);
        return view('tasks.index')->with('tasks', $tasks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\TaskCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskCreateRequest $request)
    {
        $result = $this->service->create($request->except('_token'));

        if ($result) {
            return redirect(route('tasks.edit', ['id' => $result->id]))->with('message', 'Successfully created');
        }

        return redirect(route('tasks.index'))->withErrors('Failed to create');
    }

    /**
     * Display the task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = $this->service->find($id);
        return view('tasks.show')->with('task', $task);
    }

    /**
     * Show the form for editing the task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = $this->service->find($id);
        return view('tasks.edit')->with('task', $task);
    }

    /**
     * Update the tasks in storage.
     *
     * @param  App\Http\Requests\TaskUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            return back()->with('message', 'Successfully updated');
        }

        return back()->withErrors('Failed to update');
    }

    /**
     * Remove the tasks from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return redirect(route('tasks.index'))->with('message', 'Successfully deleted');
        }

        return redirect(route('tasks.index'))->withErrors('Failed to delete');
    }
}
