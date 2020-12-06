<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Plans Controller
 *
 *
 * @method \SubscriptionManager\Model\Entity\Plan[] paginate($object = null, array $settings = [])
 */
class PlansController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
		$this->set('queryString', $this->request->getQueryParams());
		$filterData = []; //debug(); die;
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$name = $this->request->getQuery('name', '');
		$this->set('name', $name);
        if( !empty($name) ){ $filterData['name LIKE'] = "$name%"; }
        
		$sku = $this->request->getQuery('sku', '');
		$this->set('sku', $sku);
        if( !empty($sku) ){ $filterData['sku LIKE'] = "$sku"; }
        
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
        if( !empty($price) ){ $filterData['price'] = $price; }
        
		$duration = $this->request->getQuery('duration', '');
		$this->set('duration', $duration);
		if( !empty($duration) ){ $filterData['duration'] = $duration; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
        if( $isActive !== '' ) { $filterData['is_active'] = $isActive; }
        
		$plans = TableRegistry::get('SubscriptionManager.Plans')->find('all', ['order'=>['id'=>'DESC'],'conditions'=>$filterData]);
		//pr($plans);die;
        $this->set(compact('plans'));
        $this->set('_serialize', ['plans']);
    }

    public function exports()
    {
    	$this->response->withDownload('exports.csv');
		$this->set('queryString', $this->request->getQueryParams());
		$this->set('queryString', $this->request->getQueryParams());
		$filterData = []; //debug(); die;
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$name = $this->request->getQuery('name', '');
		$this->set('name', $name);
        if( !empty($name) ){ $filterData['name LIKE'] = "$name%"; }
        
		$sku = $this->request->getQuery('sku', '');
		$this->set('sku', $sku);
        if( !empty($sku) ){ $filterData['sku LIKE'] = "$sku"; }
        
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
        if( !empty($price) ){ $filterData['price'] = $price; }
        
		$duration = $this->request->getQuery('duration', '');
		$this->set('duration', $duration);
		if( !empty($duration) ){ $filterData['duration'] = $duration; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
        if( $isActive !== '' ) { $filterData['is_active'] = $isActive; }
        
		$plans = TableRegistry::get('SubscriptionManager.Plans')->find('all', ['order'=>['id'=>'DESC'],'conditions'=>$filterData])->hydrate(0)->toArray();
        $dataList = []; $header = $extract = ''; $i = 0;
        $duration = ['1' =>'Yearly', '2'=>'Half Yearly', '3'=>'Quaterly', '4'=>'Monthly'];

		foreach($plans as $value){
            $dataList[$i]['id'] = $value['id'];
            $dataList[$i]['name'] = $value['name'];
            $dataList[$i]['sku'] = $value['sku'];
            $dataList[$i]['price'] = $value['price']. ' '.$value['currency'];
            $dataList[$i]['duration'] = $duration[$value['duration']] ?? 'N/A';
            $dataList[$i]['description'] = $value['description'];
            $dataList[$i]['created'] = $value['created'];
            $dataList[$i]['modified'] = $value['modified'];
            $dataList[$i++]['status'] = $value['is_active'];            
        }
    	$_serialize='dataList';
    	$_header = ['ID', 'name', 'SKU Code', 'Price','Duration','Description', 'Created', 'Modified','Status'];
    	$_extract = ['id', 'name', 'sku', 'price','duration','description', 'created', 'modified','status'];
    	$this->set(compact('dataList', '_serialize', '_header', '_extract'));		
    	$this->viewBuilder()->setClassName('CsvView.Csv');
    	return;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $plan = $this->Plans->newEntity();
        if ($this->request->is('post')) {
            $plan = $this->Plans->patchEntity($plan, $this->request->getData());
            if ($this->Plans->save($plan)) {
                $this->Flash->success(__('The plan has been saved.'), ['key' => 'adminSuccess']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Sorry, Record are not save!'), ['key' => 'adminError']);
        }
        $this->set(compact('plan'));
        $this->set('_serialize', ['plan']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $plan = $this->Plans->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $plan = $this->Plans->patchEntity($plan, $this->request->getData());
            if ($this->Plans->save($plan)) {
                $this->Flash->success(__('The plan has been saved.'), ['key' => 'adminSuccess']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Sorry, Record are not save!'), ['key' => 'adminError']);
        }
        $this->set(compact('plan', 'id'));
        $this->set('_serialize', ['plan', 'id']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        $plan = $this->Plans->get($id);
        if ($this->Plans->delete($plan)) {
            $this->Flash->success(__('The plan has been deleted.'), ['key' => 'adminSuccess']);
        } else {
            $this->Flash->error(__('Sorry, record are not deleted!'), ['key' => 'adminError']);
        }
        return $this->redirect(['action' => 'index']);
    }
}
