<?php

namespace CodePress\CodeDatabase\Criteria;

use CodePress\CodeDatabase\Contracts\RepositoryInterface;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;

class OrderDescById implements CriteriaInterface{
    
    public function apply($model, RepositoryInterface $repository) {
        return $model->orderBy('id', 'desc');
    }

}

