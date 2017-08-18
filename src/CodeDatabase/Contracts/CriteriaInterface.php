<?php

namespace CodePress\CodeDatabase\Contracts;

use CodePress\CodeDatabase\Contracts\RepositoryInterface;

interface CriteriaInterface{
    public function apply($model,RepositoryInterface $repository);
}

