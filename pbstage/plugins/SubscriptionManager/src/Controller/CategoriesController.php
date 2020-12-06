<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\ORM\TableRegistry;

class CategoriesController extends AppController
{
	public function initialize(){
		parent::initialize();
	}
    public function index()
    {
		$error = [];
		if( $this->request->is(['post']) ){
			$requestData = $this->Categories->patchEntity($this->Categories->newEntity(), $this->request->getData(),['validate'=>'addCategories']);
			$error = $requestData->getErrors();
			if(empty($error)){
				if ($this->Categories->save($requestData)) {					
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
        $categories = $this->Categories;
		$cateList = $categories->find('treeList', ['spacer' => '- ']);
		$cateTree = $this->Categories->find('threaded',['order'=>'Categories.lft'])->toArray();
        $this->set(compact('categories','cateTree','cateList','error'));
        $this->set('_serialize', ['categories','cateTree','cateList','error']);
    }

    public function edit($id = null)
    {
		$error = [];
        $category = $this->Categories->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $requestData = $this->Categories->patchEntity($category, $this->request->getData(), ['validate'=>'updateCategories']);
			$error = $requestData->getErrors();
			if(empty($error)){
				if ($this->Categories->save($requestData)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
        $categories = $this->Categories;
		$cateList = $categories->find('treeList', ['spacer' => '- ']);
		$cateTree = $this->Categories->find('threaded',['order'=>'Categories.lft'])->toArray();
		$categories = $category;
        $this->set(compact('categories','cateTree','cateList', 'error'));
        $this->set('_serialize', ['categories','cateTree','cateList','error']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
		if( $id > 1 ){
			$category = $this->Categories->get($id);
			if ($this->Categories->delete($category)) {
				$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}else{
			$this->Flash->error(__('Sorry, root categpry not deleted!'), ['key' => 'adminError']);
		}
        return $this->redirect(['action' => 'index']);
    }
	
	public function scopeCategorized($query, Category $category=null)
	{
		if ( is_null($category) ) return $query->with('categories');
		$categoryIds = $category->getDescendantsAndSelf()->lists('id');
		
		return $query->with('categories')
			->join('products_categories', 'products_categories.product_id', '=', 'products.id')
			->whereIn('products_categories.category_id', $categoryIds);
	}
	
	
	public function brand($action='view', $id=0)
    {
		
		$dataTable = TableRegistry::get('SubscriptionManager.CategoryBrands');
		$record = [
				'id'=>0,
				'category_id'=>1,
				'brand_id'=>'',
				'url_key'=>'',
				'tag_line'=>'',
				'logo1'=>'',
				'logo2'=>'',
				'description'=>'',
				'sort_order'=>0
		];
		if( $action == 'edit' ){
			$record = $dataTable->get($id, [])->toArray();
		}
		
		if( $action == 'delete' ){
			if ($dataTable->delete($dataTable->get($id))) {
				$this->Flash->success(__('One record has been delete!'), ['key' => 'adminSuccess']);
				$this->redirect(['action'=>'brand', 'view']);
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
		
		if( $this->request->is(['post']) ){
			if( $this->request->getData('id') > 0 ){
				$insertData = $dataTable->get($this->request->getData('id'), []);
			}else{
				$insertData = $dataTable->newEntity();
			}
            $requestData = $dataTable->patchEntity($insertData, $this->request->getData());
			$error = $requestData->getErrors();
			if(empty($error)){// pr($requestData);
				if ($dataTable->save($requestData)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					//$this->redirect(['action'=>'brand', 'view']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
		
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = [];		
		$categoryId = $this->request->getQuery('category_id', '');
		$this->set('categoryId', $categoryId);
		if(!empty($categoryId)) { $filterData['category_id'] = $categoryId; }
		$brandId = $this->request->getQuery('brand_id', '');
		$this->set('brandId', $brandId);
		if(!empty($brandId)) { $filterData['brand_id'] = $brandId; }
		$urlKey = $this->request->getQuery('url_key', '');
		$this->set('urlKey', $urlKey);
		if(!empty($urlKey)) { $filterData['url_key'] = $urlKey; }
		$query = TableRegistry::get('SubscriptionManager.CategoryBrands')->find('all', ['conditions'=>$filterData])->order(['sort_order' => 'ASC']);
		$products= $this->paginate($query)->toArray();		
		$brandsList = TableRegistry::get('SubscriptionManager.Brands')->find('list', ['conditions' => ['Brands.is_active'=>'active'], 'order' => ['Brands.title'=>'ASC']])->toArray();
		$cateList = TableRegistry::get('SubscriptionManager.Categories')->find('treeList', ['spacer' => '- '])->toArray();
        
		$this->set(compact('products','brandsList','cateList','record'));
        $this->set('_serialize', ['products','brandsList','cateList','record']);
    }

}
