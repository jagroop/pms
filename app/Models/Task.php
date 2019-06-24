<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Activitylog\Models\Activity;

class Task extends Model implements HasMedia
{
    use HasMediaTrait;
    
    public $table = "tasks";

    public $primaryKey = "id";

    public $timestamps = true;

    public $dates = ['hours_updated_at'];
    
    public $fillable = [
  		'id',
  		'project_id',
  		'assigned_by',
  		'assigned_to',
      'task_name',
  		'completion_precentage',
  		'task_desc',
  		'task_status',
  		'started_date',
      'closed_date',
      'billing_hours',
      'work_hours',
  		'total_work_hours',
      'hours_updated_at',
  		'created_at',
  		'updated_at',
    ];
    
    public static $rules = [
       'project_id' => 'required|exists:projects,id',
       'assigned_by' => 'required|exists:users,id',
       // 'assigned_to' => 'required|exists:users,id',
       'task_name' => 'required',
       'task_desc' => 'required',
       'work_hours' => 'numeric|max:7|min:0',
       'task_status' => 'required',
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

  public function activity()
  {
      return Activity::where(['log_name' => 'tasks', 'subject_id' => $this->id])->get()->map(function($activity){
        return [
          'title'            => $activity->description, 
          'label'            => $activity->getExtraProperty('current_status'),
          'created_at'       => $activity->created_at->format('d-M-Y h:m:i A'),
          'created_at_human' => $activity->created_at->diffForhumans(),
        ];
      })->all(); 
  }

  public function percentageStatus()
  {
      if($this->completion_precentage <= 30) {
        return 'exception';
      } elseif ($this->completion_precentage > 30 && $this->completion_precentage <= 60) {
        return 'primary';
      } elseif ($this->completion_precentage > 60 && $this->completion_precentage <= 100) {
        return 'success';
      }
  }
}
