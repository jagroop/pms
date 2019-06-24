<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
          'id'            => $this->id,
          'name'          => $this->name,
          'active'          => $this->active,
          'email'         => $this->email,
          'role'          => $this->role,
          'total_tasks'   => $this->todayTasks->count(),
          'total_issues'  => $this->todayIssues->count(),
          'tasks'         => $this->usersTasks(),
          'all_tasks'     => TaskResource::collection($this->todayTasks),
          'issues'        => $this->usersIssues(),
          'role_formated' => ucwords(str_replace('_', ' ', $this->role)),
          'created_at'    => $this->created_at->toDateTimeString()
        ];
    }
}
