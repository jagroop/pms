<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Schema;

class ProjectService
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
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->model = $project;
        $this->pagination = 25;
    }

    /**
     * All Model Items
     *
     * @return array
     */
    public function all()
    {
        return $this->model->latest()->get();
    }

    /**
     * Paginated items
     *
     * @return LengthAwarePaginator
     */
    public function paginated($getAll = false)
    {
        return $this->model->latest()->paginate($this->pagination);
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

        $columns = Schema::getColumnListing('projects');

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
