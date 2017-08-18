<?php
namespace CodePress\CodeDatabase\Tests;

use CodePress\CodeDatabase\AbstractRepository;
use CodePress\CodeDatabase\Models\Category;
use CodePress\CodeDatabase\Repository\CategoryRepository;
use Mockery as m;

class CategoryRepositoryTest extends AbstractTestCase
{

    /**
     *
     * @var \CodePress\CodeDatabase\Repository\CategoryRepository 
     */
    private $repository;

    public function setUp(){
        parent::setUp();
        $this->migrate();
        $this->repository = new CategoryRepository();
        $this->createCategory();
    }
     
     /**
     * Verifica se pode instanciar um model de categoria
     */
    public function test_can_model(){
         $this->assertEquals(Category::class, $this->repository->model());
    }
    
    /**
     * Testa a constução do model
     */
    public function test_can_makemodel(){       
        $result = $this->repository->makeModel();
        $this->assertInstanceOf(Category::class, $result);
        
        /*
         * Como a variável model será privada usaremos o Reflaction para acessá-la
         */
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);
       
    }
    
    public function test_can_make_model_in_constructor(){       
        $reflectionClass = new \ReflectionClass($this->repository);
        $reflectionProperty = $reflectionClass->getProperty('model');
        $reflectionProperty->setAccessible(true);
        
        $result = $reflectionProperty->getValue($this->repository);
        $this->assertInstanceOf(Category::class, $result);
    }
    
    public function test_can_list_all_categories(){
        $result = $this->repository->all();
        $this->assertCount(3, $result);
        $this->assertNotNull($result[0]->description);
        
        $result = $this->repository->all(['name']);
        $this->assertNull($result[0]->description);
    } 
    
    public function test_can_create_category(){
        $result = $this->repository->create([
            'name' => 'Category 4',
            'description' => 'Description 4'
        ]);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category 4', $result->name);
        $this->assertEquals('Description 4', $result->description);
        
        $result = Category::find(4); //Verifica se foi realmente persitido no banco de dados
        $this->assertEquals('Category 4', $result->name);
        $this->assertEquals('Description 4', $result->description);
    }
     
    public function test_can_update_category(){
        $result = $this->repository->update([
            'name' => 'Category Atualizada',
            'description' => 'Description Atualizada'
        ], 1);
        
        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);
        
        $result = Category::find(1); //Verifica se foi realmente persitido no banco de dados
        $this->assertEquals('Category Atualizada', $result->name);
        $this->assertEquals('Description Atualizada', $result->description);
    }
    
    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_can_update_category_fail(){
        $this->repository->update([
            'name' => 'Category Atualizada',
            'description' => 'Description Atualizada'
        ], 10);
    }
    
    public function test_can_delete_category(){
        $result = $this->repository->delete(1);
        $categories = Category::all();
        $this->assertCount(2, $categories);
        $this->assertEquals(true, $result);
    }
    
    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_can_delete_category_fail(){
        $this->repository->delete(10);
    }
    
    public function test_can_find_category(){
        $result = $this->repository->find(1);
        $this->assertInstanceOf(Category::class, $result);
    }
    
    public function test_can_find_category_with_columns(){
        $result = $this->repository->find(1, ['name']);
        $this->assertInstanceOf(Category::class, $result);
        $this->assertNull($result->description);
    }
    
    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function test_can_find_category_fail(){
        $this->repository->find(10);
    }
    
    public function test_can_findby_categories(){
        $result = $this->repository->findBy('name', 'Category 1');
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertEquals('Category 1', $result[0]->name);
        
        $result = $this->repository->findBy('name', 'Category 10');
        $this->assertCount(0, $result);
        
        $result = $this->repository->findBy('name', 'Category 1', ['name']);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Category::class, $result[0]);
        $this->assertNull($result[0]->description);
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