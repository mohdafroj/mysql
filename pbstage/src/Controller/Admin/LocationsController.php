<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\Behavior\TreeBehavior;

class LocationsController extends AppController
{
    public function index()
    {
		$error = [];
		if( $this->request->is(['post']) ){
			$requestData = $this->Locations->patchEntity($this->Locations->newEntity(), $this->request->getData(),['validate'=>'addLocations']);
			$error = $requestData->getErrors();
			if(empty($error)){
				if ($this->Locations->save($requestData)) {					
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
        $locations = $this->Locations;
		$cateList = $locations->find('treeList', ['spacer' => '- ']);
		$cateTree = $this->Locations->find('threaded',['order'=>'Locations.lft'])->toArray();
        $this->set(compact('locations','cateTree','cateList','error'));
        $this->set('_serialize', ['locations','cateTree','cateList','error']);
    }

    public function edit($id = null)
    {
		$error = [];
        $location = $this->Locations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $requestData = $this->Locations->patchEntity($location, $this->request->getData(), ['validate' =>'updateLocations']);
			$error = $requestData->getErrors();
			if(empty($error)){
				if ($this->Locations->save($requestData)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
        $locations = $this->Locations;
		$cateList = $locations->find('treeList', ['spacer' => '- ']);
		$cateTree = $this->Locations->find('threaded',['order'=>'Locations.lft'])->toArray();
		$locations = $location;
        $this->set(compact('locations','cateTree','cateList', 'error'));
        $this->set('_serialize', ['locations','cateTree','cateList','error']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
		if( $id > 1 ){
			$location = $this->Locations->get($id);
			if ($this->Locations->delete($location)) {
				$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}else{
			$this->Flash->error(__('Sorry, root categpry not deleted!'), ['key' => 'adminError']);
		}
        return $this->redirect(['action' => 'index']);
    }
}
