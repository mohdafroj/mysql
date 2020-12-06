<?php
namespace SubscriptionManager\Controller;

use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Validation\Validator;
use SubscriptionManager\Controller\AppController;

class PgsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
    }

    public function index()
    {
        $this->set('title', 'Payment Methods');
        $dataTable = TableRegistry::get('SubscriptionManager.PaymentMethods');
        if ($this->request->is('post')) {
            $dataTable->query()->update()->set(['active_default' => 0])->execute();
            $id = $this->request->getData('id');
            $dataTable->query()->update()->set(['active_default' => 1])->where(['id' => $id])->execute();
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
        }
        $pgs = $dataTable->find('all', ['order' => ['sort_order' => 'ASC']])->toArray();
        $this->set(compact('pgs'));
        $this->set('_serialize', ['pgs']);
    }

    public function add($key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5('pgs'))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $dataTable = TableRegistry::get('SubscriptionManager.PaymentMethods');
        $pg = $dataTable->newEntity();
        if ($this->request->is('post')) {            
            $pg = $dataTable->patchEntity($pg, $this->request->getData());
            $pg->active_default = 0;
            $pg->sort_order = 1;
            $validator = new Validator();
            $validator
                ->notEmpty('title', 'Please enter payment method title!');

            $validator
                ->notEmpty('fees', 'Please enter fees amount!')
                ->add('fees', 'fees', [
                    'rule' => function ($value) {
                        return ($value > -1);
                    },
                    'message' => 'Sorry, Fees should be zero or greater!',
                ]);

            $validator->inList('status', ['1', '0']);
            $error = $validator->errors($this->request->getData());
            if (empty($error)) {
                $savedData = $dataTable->save($pg);
                if ( $savedData ) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        $this->set(compact('pg', 'error'));
        $this->set('_serialize', ['pg', 'error']);
    }

    public function edit($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $dataTable = TableRegistry::get('SubscriptionManager.PaymentMethods');
        $pg = $dataTable->get($id);
        if ($this->request->is(['post', 'put'])) {
            $pg = $dataTable->patchEntity($pg, $this->request->getData());
            $validator = new Validator();
            $validator
                ->notEmpty('title', 'Please enter payment method title!');

            $validator
                ->notEmpty('fees', 'Please enter fees amount!')
                ->add('fees', 'fees', [
                    'rule' => function ($value) {
                        return ($value > -1);
                    },
                    'message' => 'Sorry, Fees should be zero or greater!',
                ]);

            $validator
                ->integer('sort_order', 'Please enter number only!')
                ->notEmpty('sort_order', 'Please enter sort order number!')
                ->add('sort_order', [
                    'message' => ['rule' => function ($value) {return ($value > 0);}, 'message' => 'Sort order should be greater then zero!'],
                ]);

            $validator->inList('status', ['1', '0']);
            $error = $validator->errors($this->request->getData());
            if (empty($error)) {
                if ($dataTable->save($pg)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
                }
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        $this->set(compact('pg', 'id', 'error'));
        $this->set('_serialize', ['pg', 'id', 'error']);
    }

    public function shipvendors()
    {
        $dataTable = TableRegistry::get('SubscriptionManager.Shipvendors');
        $this->set('title', 'Shipping Vendors');
        if ($this->request->is(['post', 'put'])) {
            $dataTable->query()->update()->set(['set_default' => 0])->execute();
            $id = $this->request->getData('id');
            $dataTable->query()->update()->set(['set_default' => 1])->where(['id' => $id])->execute();
        }
        $vendorList = $dataTable->find('all', ['conditions'=>['is_active'=>'active']])->hydrate(false)->toArray();
        $this->set(compact('vendorList'));
        $this->set('_serialize', ['vendorList']);
    }

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedFields', ['id', 'active_default']);
        $actions = ['saverelated', 'updateImages'];
        if (in_array($this->request->params['action'], $actions)) {
            $this->Security->config('unlockedActions', $actions);
        }
    }

    public function notifications()
    {
        $str = '';
        if ($this->request->is(['ajax'])) {
            $countter = 0;
            $liData = '';
            $productsTable = TableRegistry::get('SubscriptionManager.Products');
            $query = $productsTable->find('all', ['conditions' => ['is_stock' => 'out_of_stock', 'is_active' => 'active']]);
            $query = $query->select(['total' => $query->func()->count('*')])
                ->hydrate(false)
                ->toArray();
            if (isset($query[0]['total']) && $query[0]['total'] > 0) {
                $countter++;
                $link = Router::url([
                    'controller' => 'Products', 'action' => 'index', '?' => ['is_stock' => 'out_of_stock'],
                ]);
                $liData .= '<li><a href="' . $link . '">Out of Stock Products</a></li>';
            }

            $str = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bell-o"></i>
						<span class="label label-warning">' . $countter . '</span>
					  </a>
					  <ul class="dropdown-menu" role="menu" style="width:20%;">' . $liData . '</ul>';

        }
        echo $str;die;
    }

}
