<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class AttributesController extends AppController
{
	public function index()
	{
		$attributes= [];		
		$this->set(compact('attributes'));
		$this->set('_serialize', ['attributes']);
	}
	
	public function brands($id=NULL){
		$brand = [];		
		try{
			if( $this->request->is(['post','put']) ){
				
			}else if($this->request->is('delete')){
				
			}else{
				if(is_numeric($id)){
					$brand = TableRegistry::get('Brands')->find('All',['conditions' => ['Brands.id' => $id]]);
				}
			}
		}catch(Exception $e){
			$error = $e->getMessage();
		}
		$brands = TableRegistry::get('Brands')->find('all',['order' => ['Brands.title' => 'ASC']]);
		$this->set(compact('brands', 'brand'));
		$this->set('_serialize', ['brands','brand']);
	}
	
	public function families($id=NULL){
		$family = [];
		$familyTable = TableRegistry::get('Families');
		try{
			if( $this->request->is(['post','put']) ){
				
			}else if( $this->request->is('delete') ){
				
			}else{
				if(is_numeric($id)){
					$family = $familyTable->get($id);
				} else {
					$family = $familyTable->newEntity();
				}
			}
		}catch(Exception $e){
			$error = $e->getMessage();
		}
		$families = $familyTable->find('all', ['order'=>['title' => 'ASC']]);
		//pr($families); die;
		$this->set(compact('families', 'family'));
		$this->set('_serialize', ['families','family']);
	}
}
