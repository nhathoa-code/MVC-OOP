<?php

namespace NhatHoa\App\Repositories;
use NhatHoa\Framework\Abstract\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }
}