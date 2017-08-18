<?php
namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\Models\Category;
use CodePress\CodeDatabase\Repository\CategoryRepository;
use CodePress\CodeDatabase\Contracts\CriteriaInterface;
use CodePress\CodeDatabase\Contracts\CriteriaCollection;
use CodePress\CodeDatabase\Criteria\FindByName;
use CodePress\CodeDatabase\Criteria\FindByNameAndDescription;
use CodePress\CodeDatabase\Criteria\FindByDescription;
use CodePress\CodeDatabase\Criteria\OrderDescByName;
use CodePress\CodeDatabase\Criteria\OrderDescById;

use Mockery as m;

class CategoryRepositoryCriteriaTest extends AbstractTestCase
{
    /**
     *
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository 
     */
    private $repository;
    
    public function setUp() {
        parent::setUp();
        $this->migrate(); 
        $this->repository = new CategoryRepository();
        $this->createCategory();
    }
    
    /**
     * Testando se AbstractRepository implementa CriteriaCollection
     */
    public function test_if_istanceof_criteriacollection(){
        $this->assertInstanceOf(CriteriaCollection::class, $this->repository);
    }
    
    public function test_can_get_criteriacollection(){
        $result = $this->repository->getCriteriaCollection();
        $this->assertCount(0, $result);
    }
    
    public function test_can_add_criteria(){
        $mockCriteria = m::mock(CriteriaInterface::class);
        $result = $this->repository->addCriteria($mockCriteria);
        $this->assertInstanceOf(CategoryRepository::class, $result);
        $this->assertCount(1, $this->repository->getCriteriaCollection());
    }
    
    public function test_can_getbycriteria(){
        $criteria = new FindByNameAndDescription('Category 1', 'Description 1');
        $repository = $this->repository->getByCriteria($criteria);
        $this->assertInstanceOf(CategoryRepository::class, $repository);
        
        $result = $repository->all();
        $this->assertCount(1, $result);
        $result = $result->first();
        $this->assertEquals('Category 1', $result->name);
        $this->assertEquals('Description 1', $result->description);
        
    }
    
    public function test_can_applyCriteria(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2);
        
        $repository = $this->repository->applyCriteria();       
        $this->assertInstanceOf(CategoryRepository::class, $repository);
        
        $result = $repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('Category Um', $result[0]->name);
        $this->assertEquals('Category Dois', $result[1]->name);    
    }
    
    public function test_can_list_all_categories_with_criteria(){
        $this->createCategoryDescription();
        
        
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2); 
        
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('Category Um', $result[0]->name);
        $this->assertEquals('Category Dois', $result[1]->name);
    } 
    
    public function test_can_find_category_with_criteria(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new FindByName('Category Um');
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2); 
        
        $result = $this->repository->find(5);
        
        $this->assertEquals('Category Um', $result->name);
        
    }
    
    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_can_find_category_with_criteria_and_exception(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new FindByName('Category Dois');
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2); 
        
        $this->repository->find(5);
        
    }
    
    public function test_can_findby_categories_with_criteria(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByName('Category Dois');
        $criteria2 = new OrderDescById();
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2); 
        
        $result = $this->repository->findby('description', 'Description');
        $this->assertCount(2, $result);
        $this->assertEquals(6, $result[0]->id);
        $this->assertEquals('Category Dois', $result[0]->name);
        $this->assertEquals(4, $result[1]->id);
        $this->assertEquals('Category Dois', $result[1]->name);
        
    }
    
    public function test_can_ignore_criteria(){   
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('isIgnoreCriteria');
        $reflectionProperty->setAccessible(true);
        
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result); 
        
        $this->repository->ignoreCriteria();
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);
        
        $this->repository->ignoreCriteria(true);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertTrue($result);
        
        $class = $this->repository->ignoreCriteria(false);
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertFalse($result); 
        
        $this->assertInstanceOf(CategoryRepository::class, $class);
    }
    
    public function test_can_ignoreCriteria_with_applyCriteria(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByDescription('Description');
        $criteria2 = new OrderDescByName();
        
         $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2);
        
        $this->repository->ignoreCriteria();
        $this->repository->applyCriteria();
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);        
        $result = $reflectionProperty->getValue($this->repository);   
        // Verifica se é uma instancia de Category e não uma QueryBuild modificada pelos critérios
        $this->assertInstanceOf(Category::class, $result);
        
        
        $this->repository->ignoreCriteria(false);
        
        $repository = $this->repository->applyCriteria();       
        $this->assertInstanceOf(CategoryRepository::class, $repository);
        
        $result = $repository->all();
        $this->assertCount(3, $result);
        $this->assertEquals('Category Um', $result[0]->name);
        $this->assertEquals('Category Dois', $result[1]->name);    
          
    }
    
    public function test_can_clear_criterias(){
        $this->createCategoryDescription();
        
        $criteria1 = new FindByName('Category Dois');
        $criteria2 = new OrderDescById();
        
        $this->repository
                ->addCriteria($criteria1)
                ->addCriteria($criteria2); 
        
        $this->assertInstanceOf(CategoryRepository::class, $this->repository->clearCriteria());
        
        $result = $this->repository->findby('description', 'Description');
        $this->assertCount(3, $result);
        
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);        
        $result = $reflectionProperty->getValue($this->repository);   
        
        $this->assertInstanceOf(Category::class, $result);
        
    }
    
    private function createCategoryDescription(){
        Category::create([
            'name' => 'Category Dois',
            'description' => 'Description'
        ]);
        
        Category::create([
            'name' => 'Category Um',
            'description' => 'Description'
        ]);
        
        Category::create([
            'name' => 'Category Dois',
            'description' => 'Description'
        ]);
    }
    
    public function createCategory(){
        Category::create([
            'name' => 'Category 1',
            'description' => 'Description 1'
        ]);
        
        Category::create([
            'name' => 'Category 2',
            'description' => 'Description 2'
        ]);
        
        Category::create([
            'name' => 'Category 3',
            'description' => 'Description 3'
        ]);
    }
    
    
}