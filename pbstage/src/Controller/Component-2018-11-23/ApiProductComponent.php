<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Admin component
 */
class ApiProductComponent extends Component
{	
	public function getProductByCategory($categoryId = null, $limit=20){
		$filterData['Products.is_active'] = 'active';
		$products = $this->Products->find('all', ['contain'=>['ProductsImages'],'fields'=>['id','title','sku_code','price','is_stock'],'limit'=>$limit,'conditions'=>$filterData])
					->matching('ProductsCategories', function($q) use ($categoryId){
						return $q->where(['ProductsCategories.category_id'=>$categoryId]);
					})
					->order(['created'=>'DESC'])
					->toArray();
		return $products;
	}
}
