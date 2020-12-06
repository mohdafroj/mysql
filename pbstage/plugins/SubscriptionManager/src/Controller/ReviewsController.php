<?php
namespace SubscriptionManager\Controller;

use Cake\ORM\TableRegistry;
use SubscriptionManager\Controller\AppController;

class ReviewsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SubscriptionManager.Store');
    }

    public function index()
    {
        $this->set('title', 'Customer Reviews');
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];
        $filterData = [];
        $id = $this->request->getQuery('id', '');
        $this->set('id', $id);
        if (!empty($id)) {$filterData['Reviews.id'] = $id;}

        $email = $this->request->getQuery('email', '');
        $this->set('email', $email);
        if (!empty($email)) {$filterData['Customers.email'] = $email;}

        $locationId = $this->request->getQuery('location_id', '');
        $this->set('locationId', $locationId);
        if ($locationId > 0) {$filterData['Reviews.location_id'] = $locationId;}

        $skuCode = $this->request->getQuery('sku_code', '');
        $this->set('skuCode', $skuCode);
        if (!empty($skuCode)) {$filterData['Products.sku_code'] = $skuCode;}

        $title = $this->request->getQuery('title', '');
        $this->set('title', $title);
        if (!empty($title)) {$filterData['Reviews.title'] = $title;}

        $description = $this->request->getQuery('description', '');
        $this->set('description', $description);
        if (!empty($description)) {
            $filterData['Reviews.description LIKE'] = "%$description%";
        }

        $rating = $this->request->getQuery('rating', '');
        $this->set('rating', $rating);
        if (!empty($rating)) {$filterData['Reviews.rating'] = $rating;}

        $locationIP = $this->request->getQuery('location_ip', '');
        $this->set('locationIP', $locationIP);
        if (!empty($locationIP)) {$filterData['Reviews.location_ip'] = $locationIP;}

        $created = $this->request->getQuery('created', '');
        $this->set('created', $created);
        if (!empty($created)) {
            $date = new Date($created);
            $created = $date->format('Y-m-d');
            $filterData['Reviews.created LIKE'] = "$created%";
        }

        $isActive = $this->request->getQuery('is_active', '');
        $this->set('isActive', $isActive);
        if ($isActive !== '') {$filterData['Reviews.is_active'] = $isActive;}

        $query = $this->Reviews->find('all', ['contain' => ['Customers', 'Products', 'Locations'], 'fields' => ['Customers.email', 'Locations.title', 'Products.sku_code', 'Reviews.id', 'Reviews.title', 'Reviews.description', 'Reviews.rating', 'Reviews.location_ip', 'Reviews.created', 'Reviews.is_active'], 'conditions' => $filterData])->order(['Reviews.created' => 'DESC']);
        $reviews = $this->paginate($query);
        $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields'=>['id', 'title'],'order'=>['title'=>'ASC']])->hydrate(false)->toArray();
        $locations = array_combine(array_column($locations, 'id'), array_column($locations, 'title'));
        $this->set(compact('reviews', 'locations'));
        $this->set('_serialize', ['reviews', 'locations']);
    }

    public function exports()
    {
        $this->response->withDownload('exports.csv');
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];
        $filterData = [];
        $id = $this->request->getQuery('id', '');
        $this->set('id', $id);
        if (!empty($id)) {$filterData['Reviews.id'] = $id;}

        $email = $this->request->getQuery('email', '');
        if (!empty($email)) {$filterData['Customers.email'] = $email;}

        $locationId = $this->request->getQuery('location_id', '');
        if ($locationId > 0) {$filterData['Reviews.location_id'] = $locationId;}

        $skuCode = $this->request->getQuery('sku_code', '');
        if (!empty($skuCode)) {$filterData['Products.sku_code'] = $skuCode;}

        $title = $this->request->getQuery('title', '');
        if (!empty($title)) {$filterData['Reviews.title'] = $title;}

        $description = $this->request->getQuery('description', '');
        if (!empty($description)) {
            $filterData['Reviews.description LIKE'] = "%$description%";
        }

        $rating = $this->request->getQuery('rating', '');
        if (!empty($rating)) {$filterData['Reviews.rating'] = $rating;}

        $locationIP = $this->request->getQuery('location_ip', '');
        if (!empty($locationIP)) {$filterData['Reviews.location_ip'] = $locationIP;}

        $created = $this->request->getQuery('created', '');
        if (!empty($created)) {
            $date = new Date($created);
            $created = $date->format('Y-m-d');
            $filterData['Reviews.created LIKE'] = "$created%";
        }

        $isActive = $this->request->getQuery('is_active', '');
        if ($isActive !== '') {$filterData['is_active'] = $isActive;}

        $data = $this->Reviews->find('all', ['contain' => ['Customers', 'Products', 'Locations'], 'fields' => ['Customers.email', 'Locations.title', 'Products.sku_code', 'Reviews.id', 'Reviews.title', 'Reviews.description', 'Reviews.rating', 'Reviews.location_ip', 'Reviews.created', 'Reviews.modified', 'Reviews.is_active'], 'conditions' => $filterData])->order(['Reviews.created' => 'DESC'])->toArray();

        $_serialize = 'data';
        $_header = ['ID', 'Country', 'Customer Email', 'SKU Code', 'Title', 'Description', 'Rating', 'IP Address', 'Created Date', 'Modified Date', 'Status'];
        $_extract = ['id', 'location.title', 'product.sku_code', 'title', 'description', 'rating', 'location_ip', 'created', 'modified', 'is_active'];
        $this->set(compact('data', '_serialize', '_header', '_extract'));
        $this->viewBuilder()->setClassName('CsvView.Csv');
        return;
    }

    public function add($key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5('reviews'))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $review = $this->Reviews->newEntity();
        if ($this->request->is('post')) {
            $review = $this->Reviews->patchEntity($review, $this->request->getData());
            $error = $review->getErrors();
            if (empty($error)) {
                $review->location_ip = $this->request->clientIp();
                if ($this->Reviews->save($review)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                    $this->redirect(['action' => 'add', $key, $md5]);
                }
                $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields' => ['id', 'title'], 'order' => ['title' => 'ASC']])->hydrate(false)->toArray();
        $locations = array_combine(array_column($locations, 'id'), array_column($locations, 'title'));
        $this->set(compact('review', 'locations', 'error'));
        $this->set('_serialize', ['review', 'locations', 'error']);
    }

    public function edit($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $review = $this->Reviews->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $review = $this->Reviews->patchEntity($review, $this->request->getData());
            $error = $review->getErrors();
            if (empty($error)) {
                if ($review->offer && ($review->is_active == 'approved')) {
                    $transaction_type = 1;
                    $id_referrered_customer = 0;
                    $orderId = 0;
                    $cash = 0;
                    $points = 50;
                    $voucher = 0;
                    $comments = "Wallet credited for placed review to product #" . $review->product_id;
                    //$this->Store->logPBWallet($review->customer_id, $transaction_type, $id_referrered_customer, $orderId, $cash, $points, $voucher, $comments);
                    $review->offer = 0;
                }
                if ($this->Reviews->save($review)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                    $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
                }
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['id', 'title'])->hydrate(false)->toArray();
        $locations = array_combine(array_column($locations, 'id'), array_column($locations, 'title'));
        $customers = TableRegistry::get('SubscriptionManager.Customers')->get($review->customer_id, ['fields' => ['id', 'email', 'firstname', 'lastname']])->toArray();
        $products = TableRegistry::get('SubscriptionManager.ProductPrices')->find('all', ['fields' => ['name', 'title'], 'conditions' => ['product_id' => $review->product_id, 'location_id' => 1]])->hydrate(false)->toArray();
        $products = $products[0] ?? [];
        $products['id'] = $review->product_id;
        //pr($products);die;
        $this->set(compact('review', 'error', 'customers', 'products', 'locations', 'id'));
        $this->set('_serialize', ['review', 'error', 'customers', 'products', 'locations', 'id']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $review = $this->Reviews->get($id);
        if ($this->Reviews->delete($review)) {
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        $this->redirect($this->referer());
    }

    public function search()
    {
        $keyword = $this->request->getQuery('term');
        $response = [];
        if (!empty($keyword)) {
            $query = TableRegistry::get('SubscriptionManager.ProductPrices')->find('all', ['fields' => ['Products.id', 'Products.sku_code', 'ProductPrices.name', 'ProductPrices.title'], 'contain' => ['Products']])
                ->where(['ProductPrices.location_id' => 1, 'OR' => ['ProductPrices.name LIKE' => "%$keyword%", 'ProductPrices.title' => "%$keyword%"]])
                ->toArray();
            //pr($query);
            if (!empty($query)) {
                foreach ($query as $value) {
                    $response[] = [
                        'id' => $value->product->id,
                        'label' => $value->title,
                        'value' => $value->title,
                        'name' => $value->title,
                        'title' => $value->title,
                    ];
                }
            }
        }
        echo json_encode($response);
        die;
    }

}
