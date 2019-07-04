<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IssueService;
use App\Http\Requests\IssueCreateRequest;
use App\Http\Requests\IssueUpdateRequest;

class IssuesController extends Controller
{
    public function __construct(IssueService $issue_service)
    {
        $this->service = $issue_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $issues = $this->service->paginated();
        return view('issues.index')->with('issues', $issues);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $issues = $this->service->search($request->search);
        return view('issues.index')->with('issues', $issues);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('issues.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IssueCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueCreateRequest $request)
    {
        $result = $this->service->create($request->except('_token'));

        if ($result) {
            return redirect(route('issues.edit', ['id' => $result->id]))->with('message', 'Successfully created');
        }

        return redirect(route('issues.index'))->withErrors('Failed to create');
    }

    /**
     * Display the issue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $issue = $this->service->find($id);
        return view('issues.show')->with('issue', $issue);
    }

    /**
     * Show the form for editing the issue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $issue = $this->service->find($id);
        return view('issues.edit')->with('issue', $issue);
    }

    /**
     * Update the issues in storage.
     *
     * @param  App\Http\Requests\IssueUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IssueUpdateRequest $request, $id)
    {
        $result = $this->service->update($id, $request->except('_token'));

        if ($result) {
            return back()->with('message', 'Successfully updated');
        }

        return back()->withErrors('Failed to update');
    }

    /**
     * Remove the issues from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->service->destroy($id);

        if ($result) {
            return redirect(route('issues.index'))->with('message', 'Successfully deleted');
        }

        return redirect(route('issues.index'))->withErrors('Failed to delete');
    }
}
