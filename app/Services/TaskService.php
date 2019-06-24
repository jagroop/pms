<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Schema;

class TaskService
{
    /**
     * Service Model
     *
     * @var Model
     */
    public $model;

    /**
     * Pagination
     *
     * @var integer
     */
    public $pagination;

    /**
     * Service Constructor
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->model = $task;
        $this->pagination = 25;
    }

    /**
     * All Model Items
     *
     * @return array
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Paginated items
     *
     * @return LengthAwarePaginator
     */
    public function paginated($filters)
    {
        return $this->model->when($filters->user_id == true, function($q){
          $q->where('assigned_to', auth()->user()->id);
        })->when($filters->date == true, function($q){
          $q->whereDate('created_at', now()->toDateString());
        })->when(($filters->project_id != 'all' && $filters->project_id > 0), function($q) use ($filters){
          $q->where('project_id', $filters->project_id);
        })->latest()->paginate($this->pagination);
    }

    /**
     * Search the model
     *
     * @param  mixed $payload
     * @return LengthAwarePaginator
     */
    public function search($payload)
    {
        $query = $this->model->orderBy('created_at', 'desc');
        $query->where($this->model->primaryKey, 'LIKE', '%'.$payload.'%');

        $columns = Schema::getColumnListing('tasks');

        foreach ($columns as $attribute) {
            $query->orWhere($attribute, 'LIKE', '%'.$payload.'%');
        };

        return $query->paginate($this->pagination)->appends([
            'search' => $payload
        ]);
    }

    /**
     * Create the model item
     *
     * @param  array $payload
     * @return Model
     */
    public function create($payload)
    {
        return $this->model->create($payload);
    }

    /**
     * Find Model by ID
     *
     * @param  integer $id
     * @return Model
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Model update
     *
     * @param  integer $id
     * @param  array $payload
     * @return Model
     */
    public function update($id, $payload)
    {
        return $this->find($id)->update($payload);
    }

    /**
     * Destroy the model
     *
     * @param  integer $id
     * @return bool
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }
}
