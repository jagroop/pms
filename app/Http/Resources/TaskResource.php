<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
          'assigned_to_email'     => $this->assignedTo->email,
          'assigned_to_id'        => $this->assigned_to,
          'assigned_by'           => $this->assignedBy->name,
          'assigned_by_email'     => $this->assignedBy->email,
          'assigned_by_id'        => $this->assigned_by,
          'task_name'             => $this->task_name,
          'task_name_short'       => str_limit($this->task_name, 20),
          'task_desc'             => $this->task_desc,
          'task_status'           => $this->task_status,
          'completion_precentage' => (int) $this->completion_precentage,
          'percentage_status'     => $this->percentageStatus(),
          'activity'              => $this->activity(),
          'billing_hours'         => (trim($this->billing_hours) == '') ? 0 : $this->billing_hours,
          'work_hours'            => (trim($this->work_hours) == '') ? 0 : $this->work_hours,
          'task_status_formated'  => ucwords(str_replace('_', ' ', $this->task_status)),
          'created_at'            => $this->created_at->toDateTimeString(),
          'started_date'          => $this->started_date,
          'closed_date'           => $this->closed_date,
          'disable_check'         => $this->created_at->toDateString() != now()->toDateString(),
          'disable_work_hours'    => ($this->work_hours > 0 && $this->hours_updated_at && $this->hours_updated_at->toDateString() == now()->toDateString()) ? true : false,
          'files'                 => $this->getUploadedFiles()
        ];
    }
}
