<?php

namespace App\Repositories\Eloquent\OJS;

use App\Models\OJS\Submission;
use App\Http\Resources\OJS\SubmissionCollection;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubmissionRepository extends BaseRepository
{
    protected $model;

    public function __construct(Submission $model)
    {
        $this->model = $model;
    }

    public function all(array $params = []): ResourceCollection
    {
        $data = $this->model;

        foreach ($this->model->getFillable() as $fillable) {
            if (isset($params[$fillable]) && $fillable !== 'order' && !is_null($params[$fillable]) && $params[$fillable] !== '') {
                $data = $data->where($fillable, 'LIKE', '%' . $params[$fillable] . '%');
            }
        }

        if (isset($params["user_id"]) && !is_null($params["user_id"]) && $params["user_id"] !== '') {
            $data = $data->whereHas('stageAssignment', fn($query) => $query->where('user_id', $params['user_id']));
        }

        if ($this->model->timestamps) {
            if (isset($params['order']) && in_array($params['order'], $this->model->getFillable())) {
                $data = $data->orderBy($params['order'], isset($params['ascending']) && $params['ascending'] == 0 ? 'DESC' : 'ASC');
            } else {
                $data = $data->orderBy('created_at', 'ASC');
            }
        }

        return new SubmissionCollection($data->paginate(isset($params['limit']) ? $params['limit'] : 25));
    }
}
