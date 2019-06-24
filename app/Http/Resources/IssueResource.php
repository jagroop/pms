<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          'id'                    => $this->id,
          'project_id'            => $this->project_id,
          'project_name'          => $this->project->name,
          'assigned_to'           => $this->assignedTo->name,
          'assigned_to_id'        => $this->assigned_to,
          'assigned_by'           => $this->assignedBy->name,
          'assigned_by_id'        => $this->assigned_by,
          'issue_name'            => $this->issue_name,
          'issue_desc'            => $this->issue_desc,
          'issue_status'          => $this->issue_status,
          'issue_status_formated' => ucwords(str_replace('_', ' ', $this->issue_status)),
          'created_at'            => $this->created_at->toDateTimeString(),
          'started_date'          => $this->started_date,
          'closed_date'           => $this->closed_date,
          'files' => $this->getUploadedFiles()
        ];
    }
}
