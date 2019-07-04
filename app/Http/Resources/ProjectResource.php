<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
          'id'              => $this->id,
          'name'            => $this->name,
          'status'          => $this->status,
          'status_formated' => ucwords(str_replace('_', ' ', $this->status)),
          'created_at'      => $this->created_at->toDateTimeString(),
          'started_date'    => (string) $this->started_date,
          'closed_date'     => (string) $this->closed_date,
          'billing_hours'   => (string) $this->billingHours(),
          'work_hours'   => (string) $this->workHours(),
          'files'           => $this->getUploadedFiles()
        ];
    }
}
