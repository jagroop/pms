<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Issue extends Model implements HasMedia
{
    use HasMediaTrait;
    
    public $table = "issues";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
  		'id',
  		'project_id',
  		'assigned_by',
  		'assigned_to',
  		'issue_name',
  		'issue_desc',
  		'issue_status',
  		'started_date',
  		'closed_date',
  		'created_at',
  		'updated_at'
    ];

    public static $rules = [
       'project_id' => 'required|exists:projects,id',
       'issue_name' => 'required',
       'issue_desc' => 'required',
       // 'assigned_to' => 'required|exists:users,id',
       'issue_status' => 'required',
    ];

    public function project() {
      return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function assignedBy() {
      return $this->hasOne(User::class, 'id', 'assigned_by');
    }

    public function assignedTo() {
      return $this->hasOne(User::class, 'id', 'assigned_to');
    }

    /**
     * Get Model uploaded media files
     * @return Array
     */
    public function getUploadedFiles()
    {
        return $this->getMedia($this->table)->map(function($media){
          $data = $media->only(['id', 'model_id', 'file_name', 'collection_name', 'size', 'mime_type', 'created_at']);
          $data['full_url'] = $media->getFullUrl();
          $data['file_type'] = trim(str_before($data['mime_type'], '/'));
          $data['created_at'] = $data['created_at']->diffForhumans();
          return $data;
        });
    }
}
