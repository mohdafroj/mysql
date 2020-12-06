<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Event\Event;

class CmsController extends AppController
{
	public function initialize(){
		parent::initialize();
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->loadComponent('Product');

	}

	public function index()
	{	
			$limit = $this->request->getQuery('limit', 50);
			$offset = $this->request->getQuery('page', 1);
			$offset = ($offset - 1)*$limit;
			
			$this->paginate = [
					'limit' =>$limit, // limits the rows per page
					'maxLimit' => 2000,
			];
			
			$filterData = [];
			$name = $this->request->getQuery('name', '');
			$this->set('name', $name);
			if(!empty($name)) { $filterData['name'] = $name; }
	
			$cmsTitle = $this->request->getQuery('title', '');
			$this->set('cmsTitle', $cmsTitle);
			if(!empty($cmsTitle)) { $filterData['title'] = $cmsTitle; }
	
			$url_key = $this->request->getQuery('url_key', '');
			$this->set('url_key', $url_key);
			if(!empty($url_key)) { $filterData['url_key'] = $url_key; }
					
			
			$status = $this->request->getQuery('status', '');
			$this->set('status', $status);		
			if( $status !== '' ) { $filterData['is_active'] = $status; }
	
			$query 		= TableRegistry::get('Cms')->find('all', ['conditions'=>$filterData])->order(['id'=>'DESC']);
			$cms	= $this->paginate($query)->toArray();
			$this->set('title','CMS Static Pages');
			$this->set('queryString', $this->request->getQueryParams());
			$this->set(compact('cms'));
			$this->set('_serialize', ['cms']);
	}
	
	public function pages($id=0, $key=NULL, $md5=NULL)
    {
		//try{
			$dataTable 	= TableRegistry::get('Cms');
			$cms = ($id > 0) ? $dataTable->get($id) : $dataTable->newEntity();
			$oldKey = $cms->url_key ?? '';
			$errors = [];
			if( $this->request->is(['post','put']) ){
				$newKey = $this->request->getData('url_key');
				$cms = $dataTable->patchEntity($cms, $this->request->getData());
				$errors = $cms->getErrors();
				if( ($oldKey != $newKey) && $this->Product->findUrlRewrite($newKey) ){
					$errors['url_key']['urlKey']  = 'This url key already exists in system!';
				}
				$myConditions = [
					'categories'=>$this->request->getData('categories'),
					'brands'=>$this->request->getData('brands'),
					'sku'=>$this->request->getData('sku'),
					'prices'=>[$this->request->getData('start'),$this->request->getData('end')]
				];
				if( empty($errors) ){
					$cms->conditions = json_encode($myConditions);
					if ( $dataTable->save($cms) ) {
						$this->Product->updateUrlRewrite(['old_key'=>$oldKey,'new_key'=>$newKey,'type'=>'static']);
						$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
						$this->redirect(['action'=>'index']);
					}else{
						$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
					}
				}else{
					$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
				}
			} //echo json_encode(['delivered', 'accepted']);
			$categories = TableRegistry::get('Categories')->find('threaded',['fields'=>['id','parent_id','name','text'=>'name'],'conditions'=>['parent_id >'=>0],'order'=>'Categories.lft'])->hydrate(0)->toArray();
			$brands = TableRegistry::get('Brands')->find('all',['fields'=>['id','title'],'conditions'=>['is_active'=>'active'],'order'=>['title'=>'ASC']])->hydrate(0)->toArray();
			$siteTitle = $cms->title ?? 'Add New Page';
			$this->set('title','CMS: '.$siteTitle);
			$this->set('categories',$categories);
			$this->set('brands',$brands);
			$this->set('cms',$cms);
			$this->set('id',$id);
			$this->set('errors',$errors);
		//}catch(\Exception $e){
			//$this->redirect(['action'=>'index']);
		//}
	}
	
	public function delete($id = 0){
			try{
				if( $this->request->is(['delete']) ){
					$dataTable 	= TableRegistry::get('Cms');
					$cms = $dataTable->get($id);
					$key = $cms->url_key;
					if ($dataTable->delete($cms)) {
						TableRegistry::get('UrlRewrite')->query()->delete()->where(['request_path' => $key])->execute();
						$this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
					} else {
						$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
					}
				}else{
					$this->Flash->error(__('Sorry, this method not allow!'), ['key' => 'adminError']);
				}
			}catch(\Exception $e){}
			$this->redirect($this->referer());
	}
	
	public function beforeFilter(Event $event){
		$fields = ['categories','brands','start','end'];
		$this->Security->config('unlockedFields', $fields);
		$actions = ['send','getStats','mailerList'];
		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}

	public function setInvalidPincodes () {
		$pincodes= $this->request->getData('pincodes');
		$dataTable 	= TableRegistry::get('Systems');
		if ( !empty ($pincodes) ) {
			if ( $dataTable->setInvalidPincodes($pincodes) ) {
				$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				$this->redirect(['action'=>'setInvalidPincodes']);
			} else {
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}
		}
		$pincodes = $dataTable->getInvalidPincodes();
		$pincodes = $pincodes['core_value'] ?? '';
		$this->set('title','Systems: Set invalid pincodes');
		$this->set('pincodes',$pincodes);
	}

}