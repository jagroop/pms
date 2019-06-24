<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IssueService;
use App\Http\Requests\IssueCreateRequest;
use App\Http\Requests\IssueUpdateRequest;
use App\Http\Resources\IssueResource;

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
        $filters = json_decode($request->filters);
        $issues = $this->service->paginated($filters);
        return IssueResource::collection($issues);
    }

    /**
     * Display a listing of the resource searched.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $issues = $this->service->search($request->search);
        return response()->json($issues);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IssueRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueCreateRequest $request)
    {
        $data = $request->except('_token');
        if(trim($request->input('assigned_to')) == '') {
          $data['assigned_to'] = auth()->user()->id;
        }
        $result = $this->service->create($data);

        if ($result) {  
            if($data['assigned_to'] != auth()->user()->id) {
              // Send NOtification
              \Notifications::notify($data['assigned_to'], 'success', auth()->user()->name  . ' have assigned you an issue #' . $result->id, 'issues');
            }
            $issue = new IssueResource($this->service->find($result->id));
            return response()->json($issue);
        }

        return response()->json(['error' => 'Unable to create issue'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $issue = $this->service->find($id);
        return response()->json($issue);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IssueRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IssueUpdateRequest $request, $id)
    {
        $getIssue = $this->service->find($id);
        $result = $this->service->update($id, $request->except(['index']));

        if ($result) {
            if($getIssue->issue_status != $request->issue_status) {
              $sendNOtifTo = null;
              if(auth()->user()->id == $request->assigned_to) {
                $sendNOtifTo = $request->assigned_by;
              } else {
                $sendNOtifTo = $request->assigned_to;                
              }
              if($sendNOtifTo) {
                $statusFrom = ucwords(str_replace('_', ' ', $getIssue->issue_status));
                $statusTo = ucwords(str_replace('_', ' ', $request->issue_status));
                \Notifications::notify($sendNOtifTo, 'success', auth()->user()->name  . ' have changed issue ( #'.$id.' ) status from *** '.$statusFrom.'*** to ***'.$statusTo.'*** .', 'issues');
              }
              // Send NOtification
            }
            $task = new IssueResource($this->service->find($id));
            return response()->json($task);
        }

        return response()->json(['error' => 'Unable to update issue'], 500);
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
            return response()->json(['success' => 'Issue was deleted'], 200);
        }

        return response()->json(['error' => 'Unable to delete issue'], 500);
    }
}
