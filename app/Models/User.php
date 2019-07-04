<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;
    
    public $table = "users";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
  		'id',
  		'name',
  		'role',
  		'email',
  		'password',
  		'remember_token',
  		'created_at',
  		'updated_at',
    ];

    public static $rules = [
        'name' => 'required',
        'role' => 'required',
        'email' => 'required|email|unique:users',
    ];

    public function todayTasks()
    {
      return $this->hasMany(Task::class, 'assigned_to', 'id')->whereDate('created_at', now()->toDateString());
    }

    public function todayIssues()
    {
      return $this->hasMany(Issue::class, 'assigned_to', 'id')->whereDate('created_at', now()->toDateString());
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to', 'id');
    }

    public function usersTasks()
    {
        $tasks = $this->tasks()->whereDate('created_at', now()->toDateString())->get()->groupBy('task_status')->map(function($tasks, $status){
          return [
            'status' => ucwords(str_replace('_', ' ', $status)),
            'count' => count($tasks)
          ];
        })->all();

        return array_values($tasks);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'assigned_to', 'id');
    }

    public function usersIssues()
    {
        $issues = $this->issues()->whereDate('created_at', now()->toDateString())->get()->groupBy('issue_status')->map(function($issues, $status){
          return [
            'status' => ucwords(str_replace('_', ' ', $status)),
            'count' => count($issues)
          ];
        })->all();

        return array_values($issues);
    }
}
