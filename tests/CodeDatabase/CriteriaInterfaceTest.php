<?php
namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDataBase\Contracts\RepositoryInterface;
use CodePress\CodeCategory\Models\Category;
use CodePress\CodeDataBase\Contracts\CriteriaInterface;
use Illuminate\Database\Query\Builder;

use Mockery as m;

class CriteriaInterfaceTest extends AbstractTestCase
{
    
    public function test_if_should_return_all_without_arguments(){
        $mockQueryBuilder = m::mock(Builder::class);
        $mockRepository = m::mock(RepositoryInterface::class);
        $mockModel = m::mock(Category::class);
        $mock = m::mock(CriteriaInterface::class);
        $mock->shouldReceive('apply')
                ->with($mockModel, $mockRepository)
                ->andReturn($mockQueryBuilder);
        
        $this->assertInstanceOf(Builder::class, $mock->apply($mockModel, $mockRepository));
    }
     
}