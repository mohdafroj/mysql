<?php
namespace SubscriptionManager\Controller;

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use SubscriptionManager\Controller\AppController;

class CustomersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SubscriptionManager.Membership');
        $this->loadComponent('SubscriptionManager.Store');
    }

    public function index()
    {
        $sort = $this->request->getQuery('sort', null);
        $direction = $this->request->getQuery('direction', null);
        $limit = $this->request->getQuery('perPage', 50);
        $this->paginate = [
            'sort' => $sort,
            'direction' => $direction,
            'limit' => $limit,
            'maxLimit' => 20000,
        ];
        $filterData = [];
        $mobile = $this->request->getQuery('mobile', '');
        $this->set('mobile', $mobile);
        if (!empty($mobile)) {$filterData['mobile'] = $mobile;}

        $firstname = $this->request->getQuery('firstname', '');
        $this->set('firstname', $firstname);
        if (!empty($firstname)) {$filterData['firstname'] = $firstname;}

        $email = $this->request->getQuery('email', '');
        $this->set('email', $email);
        if (!empty($email)) {$filterData['email'] = $email;}

        $createdFrom = $this->request->getQuery('createdFrom', '');
        $this->set('createdFrom', $createdFrom);
        $createdTo = $this->request->getQuery('createdTo', '');
        $this->set('createdTo', $createdTo);
        if (!empty($createdFrom) && !empty($createdTo)) {
            $createdFrom = $createdFrom . ' 00:00:01';
            $createdTo = $createdTo . ' 23:59:59';
        } else if (!empty($createdFrom)) {
            $createdFrom = $createdFrom . ' 00:00:01';
            $createdTo = date('Y-m-d') . ' 23:59:59';
        } else if (!empty($createdTo)) {
            $createdFrom = '2015-01-01 00:00:01';
            $createdTo = $createdTo . ' 23:59:59';
        } else {
            $createdFrom = '2015-01-01 00:00:01';
            $createdTo = date('Y-m-d') . ' 23:59:59';
        }

        $status = $this->request->getQuery('status', '');
        $this->set('status', $status);
        if ($status !== '') {$filterData['is_active'] = "$status";}
        $query = $this->Customers->find('all', ['fields' => ['id', 'mobile', 'firstname', 'lastname', 'email', 'gender', 'is_active', 'created'], 'conditions' => $filterData])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            })
            ->order(['created' => 'DESC']);
        $customers = $this->paginate($query)->toArray();
        $lastCustomerId = (count($customers) > 0) ? $customers[count($customers) - 1]->id : 0;
        //pr($queryString);die;
        $this->set('queryString', $this->request->getQueryParams());
        $this->set(compact('customers', 'lastCustomerId'));
        $this->set('_serialize', ['customers', 'lastCustomerId']);
    }

    public function exports($lastCustomerId = 1)
    {
        $this->response->withDownload('exports.csv');
        $limit = $this->request->getQuery('perPage', 50);
        $offset = $this->request->getQuery('page', 1);
        $offset = ($offset - 1) * $limit;
        $filterData = [];
        //$filterData['id >= '] = $lastCustomerId;

        $mobile = $this->request->getQuery('mobile', '');
        if (!empty($mobile)) {$filterData['mobile'] = $mobile;}

        $firstname = $this->request->getQuery('firstname', '');
        if (!empty($firstname)) {$filterData['firstname'] = $firstname;}

        $email = $this->request->getQuery('email', '');
        if (!empty($email)) {$filterData['email'] = $email;}

        $createdFrom = $this->request->getQuery('createdFrom', '');
        $this->set('createdFrom', $createdFrom);
        $createdTo = $this->request->getQuery('createdTo', '');
        $this->set('createdTo', $createdTo);
        if (!empty($createdFrom) && !empty($createdTo)) {
            $createdFrom = $createdFrom . ' 00:00:01';
            $createdTo = $createdTo . ' 23:59:59';
        } else if (!empty($createdFrom)) {
            $createdFrom = $createdFrom . ' 00:00:01';
            $createdTo = date('Y-m-d') . ' 23:59:59';
        } else if (!empty($createdTo)) {
            $createdFrom = '2015-01-01 00:00:01';
            $createdTo = $createdTo . ' 23:59:59';
        } else {
            $createdFrom = '2015-01-01 00:00:01';
            $createdTo = date('Y-m-d') . ' 23:59:59';
        }

        $status = $this->request->getQuery('status', '');
        if ($status !== '') {$filterData['is_active'] = "$status";}

        $data = $this->Customers->find('all', ['fields' => ['id', 'mobile', 'firstname', 'lastname', 'email', 'created', 'modified', 'logdate', 'lognum'], 'conditions' => $filterData, 'offset' => $offset, 'limit' => $limit])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            })
            ->order(['created' => 'DESC'])->toArray();

        //pr($data); die;
        $_serialize = 'data';
        $_header = ['ID', 'Mobile', 'First Name', 'Last Name', 'Email', 'Created Date', 'Modified Date', 'Logdate', 'Logums'];
        $_extract = ['id', 'mobile', 'firstname', 'lastname', 'email', 'created', 'modified', 'logdate', 'lognum'];
        $this->set(compact('data', '_serialize', '_header', '_extract'));
        $this->viewBuilder()->setClassName('CsvView.Csv');
        return;
    }

    //Add new customer in database
    public function add($id = null)
    {
        try {
            $customer = $this->Customers->newEntity();
            $error = [];
            if ($this->request->is(['patch', 'post', 'put'])) {
                $customer = $this->Customers->patchEntity($customer, $this->request->getData(), ['validate' => 'adminNewProfile']);
                $error = $customer->getErrors();
                if (empty($error)) {
                    if ($this->Customers->save($customer)) {
                        $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('Sorry, there are something issue, try again!'), ['key' => 'adminError']);
                    }
                } else {
                    $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
                }
            }
            $error['customer'] = $error;
            $this->set(compact('customer', 'member', 'id', 'error'));
            $this->set('_serialize', ['customer', 'member', 'id', 'error']);
        } catch (\Exception $e) {
            $this->redirect(['action' => 'index']);
        }
    }

    //view customer profile
    public function view($id = null)
    {
        //try{
        $member = $this->Membership->getMembership($id);
        $error = [];
        if ($this->request->is(['patch', 'post', 'put'])) {
            $customer = $this->Customers->get($id);
            $customer = $this->Customers->patchEntity($customer, $this->request->getData(), ['validate' => 'adminProfile']);
            $error = $customer->getErrors(); //pr($customer);
            if (empty($error)) {
                if ($this->Customers->save($customer)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                }
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['condition' => ['is_active' => 'active']])->hydrate(false)->toArray();
        $customer = $this->Customers->get($id, ['contain' => ['CustomerWallets.Locations']]);
        //pr($customer); die;
        $error['customer'] = $error;
        $this->set(compact('locations', 'customer', 'member', 'id', 'error'));
        $this->set('_serialize', ['locations', 'customer', 'member', 'id', 'error']);
        //}catch (\Exception $e){ $this->redirect(['action'=>'index']);}
    }

    //manage customer addresses
    public function addresses($id = null, $key = null, $md5 = null)
    {
        try {
            $dataTable = TableRegistry::get('SubscriptionManager.Addresses');
            $addressId = $this->request->getQuery('address-id', '0');
            $address = ($addressId > 0) ? $dataTable->get($addressId) : $dataTable->newEntity();
            $error = [];
            if ($this->request->is(['patch'])) {
                $addressId = $this->request->data('address-id');
                $dataTable->query()->update()->set(['set_default' => 0])->where(['customer_id' => $id])->execute();
                $dataTable->query()->update()->set(['set_default' => 1])->where(['id' => $addressId])->execute();
                $this->Flash->success(__('Default Address updated successfully!'), ['key' => 'adminSuccess']);
            }
            if ($this->request->is(['delete'])) {
                $addressId = $this->request->data('address-id');
                $dataTable->query()->delete()->set(['set_default' => '1'])->where(['id' => $addressId])->execute();
                $this->Flash->success(__('Address deleted successfully!'), ['key' => 'adminSuccess']);
            }
            if ($this->request->is(['post', 'put'])) {
                $address = $dataTable->patchEntity($address, $this->request->getData());
                $error = $address->getErrors(); //pr($address);
                if (empty($error)) {
                    if ($dataTable->save($address)) {
                        $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                        $this->redirect(['action' => 'addresses', $id, $key, $md5]);
                    } else {
                        $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
                    }
                } else {
                    $this->Flash->error(__('Sorry, Please fill required fields!'), ['key' => 'adminError']);
                }
            }
            $addressesList = $dataTable->find('all', ['conditions' => ['customer_id' => $id]]);
            $countryList = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields' => ['title'], 'conditions' => ['is_active' => 'active'], 'order' => ['title' => 'ASC']])->hydrate(false)->toArray();
            $countryList = array_column($countryList, 'title');
            $countryList = array_combine($countryList, $countryList);
            //pr($countryList); die;
            $this->set(compact('address', 'addressesList', 'id', 'countryList', 'error'));
            $this->set('_serialize', ['address', 'addressesList', 'id', 'countryList', 'error']);
        } catch (\Exception $e) {
            $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        //$customer = $this->Customers->get($id);
        //$this->Customers->delete($customer)
        if (2 > 1) {
            $this->Flash->error(__('You don\'t have permission to delete any account!'), ['key' => 'adminError']);
        } else {
            $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
        }
        return $this->redirect($this->referer());
    }

    public function wallet($id = 0)
    {
        $customerLogTable = TableRegistry::get('SubscriptionManager.CustomerLogs');
        $customerLogNew      = $customerLogTable->newEntity();
        if( $this->request->is(['post']) ){
			$action = 1;
			$orderId = $this->request->getData("order_id", 0);
			$transaction_type = $this->request->getData("transaction_type", 0);
			$comments = $this->request->getData("comments", '');

			$cash = $this->request->getData("cash", 0);
			$points = $this->request->getData("points", 0);
			$voucher = $this->request->getData("voucher", 0);
			
			if( !is_numeric($orderId) || ($orderId == 0) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Please enter order number!'), ['key' => 'adminError']);
			}else if( ( $voucher > 0 ) && (($voucher % 501 == 0) || ($voucher % 100 == 0)) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Voucher amount should be multiple of "501" or "100" !'), ['key' => 'adminError']);
			}else if ( empty($comments) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Please enter reason for this transaction!'), ['key' => 'adminError']);
			}

			if( $action && (($cash > 0) || ($points > 0) || ($voucher > 0)) ){
					$this->Store->logPBWallet($id, $transaction_type, 0, $orderId, $cash, $points, $voucher, $comments);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					$this->redirect(['action'=>'wallet',$id, 'key', md5($id)]);
				}
		}
        $this->set('title', 'Customer Wallet Log');
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];

        $filterData = [];

        $filterData['customer_id'] = $id;
        $this->set('customerId', $id);

        $referrer_name = $this->request->getQuery('referrer_name', '');
        $this->set('referrer_name', $referrer_name);

        $orderId = $this->request->getQuery('order_id', '');
        $this->set('orderId', $orderId);
        if (!empty($orderId)) {
            $filterData['order_id'] = $orderId;
        }

        $cash = $this->request->getQuery('cash', '');
        $this->set('cash', $cash);
        if (!empty($cash)) {
            $filterData['cash'] = $cash;
        }

        $points = $this->request->getQuery('points', '');
        $this->set('points', $points);
        if (!empty($points)) {
            $filterData['points'] = $points;
        }

        $voucher = $this->request->getQuery('voucher', '');
        $this->set('voucher', $voucher);
        if (!empty($voucher)) {
            $filterData['voucher'] = $voucher;
        }

        $transaction_type = $this->request->getQuery('transaction_type', '');
        $this->set('transactionType', $transaction_type);
        if ($transaction_type !== '') {
            $filterData['transaction_type'] = $transaction_type;
        }

        $comments = $this->request->getQuery('comments', '');
        $this->set('comments', $comments);
        if ($comments !== '') {
            $filterData['comments LIKE'] = "%" . $comments . "%";
        }

        $createdFrom = $this->request->getQuery('created_from', '');
        $this->set('createdFrom', $createdFrom);

        $createdTo = $this->request->getQuery('created_to', '');
        $this->set('createdTo', $createdTo);

        if (!empty($createdFrom) && !empty($createdTo)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['transaction_date >='] = $createdFromNew;
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['transaction_date <='] = $createdToNew;
        } else if (!empty($createdFrom)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['transaction_date >='] = $createdFromNew;
        } else if (!empty($createdTo)) {
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['transaction_date <='] = $createdToNew;
        }

        $history = TableRegistry::get('SubscriptionManager.CustomerLogs')->find('all', ['contain' => ['Locations'], 'conditions' => $filterData])
            ->contain(['Locations' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id', 'title', 'currency', 'currency_logo']);
                },
            ]])
            ->order(['CustomerLogs.id' => 'DESC']);
        $history = $this->paginate($history); //pr($history); die;
        $this->set(compact('history', 'id', 'customerLogNew'));
        $this->set('_serialize', ['history', 'id', 'customerLogNew']);
    }

    public function cart($id = 0)
    {
        $this->set('title', 'Customer Cart Details');
        $cartTable = TableRegistry::get('SubscriptionManager.Carts');
        $productTable = TableRegistry::get('SubscriptionManager.Products');
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];

				$cartProducts     = $cartTable->find()->select(['product_id'])->where(['customer_id'=>$id])->hydrate(false)->toArray();
				$cartProducts = array_column($cartProducts,'product_id');
        $filterData = [];
        $sku = $this->request->getQuery('sku', '');
        $this->set('sku', $sku);
        $title = $this->request->getQuery('title', '');
        $this->set('title', $title);
        $fromPrice = $this->request->getQuery('fromPrice', '0');
        $this->set('fromPrice', $fromPrice);
        $toPrice = $this->request->getQuery('toPrice', '5000');
        $this->set('toPrice', $toPrice);

        $checked = $this->request->getQuery('checked', []);
        $checked = array_unique($checked);
        $checked = (isset($checked[0]) && ($checked[0] == 0)) ? array_splice($checked, 1) : $checked;
        //delete unchecked items from cart
        $allChecked = $this->request->getQuery('allChecked', []);
				$allChecked = array_intersect($cartProducts, $allChecked);
				$allChecked = array_diff($allChecked, $checked);
				if (count($allChecked)) {
          $cartTable->query()->delete()->where(['customer_id' => $id, 'product_id IN' => $allChecked])->execute();
        }
        if (count($checked)) {
            $query = $cartTable->query()->insert(['customer_id', 'product_id']);
            $action = 0;
            foreach ($checked as $value) {
                if (!in_array($value, $cartProducts)) {
                    $query = $query->values(['customer_id' => $id, 'product_id' => $value]);
                    $action = 1;
                }
            }
            if ($action) {
                $query = $query->execute();
                $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
								$this->redirect(['action' => 'cart', $id,'key', md5($id)]);
            }
        }

        if ($this->request->is(['get'])) {
            if (!empty($sku) || !empty($title) || ($fromPrice != 0) || ($toPrice != 5000)) {
                $filterData['is_active'] = 'active';
                if (!empty($sku)) {
                    $filterData['sku_code'] = $sku;
                }
                $cart = $productTable->find('all', ['fields' => ['id', 'sku_code'], 'conditions' => $filterData])
                    ->contain(['ProductPrices' => function (Query $q) use ($title, $fromPrice, $toPrice) {
                        return $q->select(['product_id', 'location_id', 'title', 'price'])->where(['ProductPrices.is_active' => 'active', function ($exp, $q) use ($title, $fromPrice, $toPrice) {
                            if (empty($title)) {
                                return $exp->between('price', $fromPrice, $toPrice);
                            } else {
                                return $exp->like('ProductPrices.title', "%$title%")->between('price', $fromPrice, $toPrice);
                            }
                        }]);
                    },
                        'ProductPrices.Locations' => function (Query $q) {
                            return $q->select(['id', 'title', 'code', 'currency_logo']);
                        }]);
            } else {
                $cart = $cartTable->find('all', ['fields' => ['id', 'quantity', 'created'], 'conditions' => ['customer_id' => $id]])
												->contain(['Products.ProductPrices' => function (Query $q) {
														return $q->select(['product_id', 'location_id', 'title', 'price']);
												},
                        'Products' => function (Query $q) use ($sku) {
                            $wh['Products.is_active'] = 'active';
                            if (!empty($sku)) {
                                $wh['sku_code'] = $sku;
                            }
                            return $q->select(['id', 'sku_code'])->where($wh);
                        },
                        'Products.ProductPrices.Locations' => function (Query $q) {
                            return $q->select(['id', 'title', 'code', 'currency_logo']);
                        }]);
            }
            //pr($cart->hydrate(false)->toArray()); //die;
            $cart = $this->paginate($cart);
        }
        $this->set(compact('cart', 'id', 'cartProducts'));
        $this->set('_serialize', ['cart', 'id','cartProducts']);
    }

    public function wishlist($id = 0)
    {
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];
		$wishlistTable = TableRegistry::get('SubscriptionManager.Wishlists');
        if ($this->request->is(['get'])) {
            $wishlist = $wishlistTable->find('all', ['fields' => ['id', 'product_id', 'created'], 'conditions' => ['customer_id' => $id]])
                            ->contain(['Products.ProductPrices' => function (Query $q) {
                                    return $q->select(['product_id', 'location_id', 'title', 'price']);
                            },
                            'Products' => function (Query $q) {
                                    return $q->select(['id', 'sku_code']);
                            },
                            'Products.ProductPrices.Locations' => function (Query $q) {
                                    return $q->select(['id', 'title', 'code', 'currency_logo']);
                            }]);

            $wishlist = $this->paginate($wishlist);
        }
        $this->set(compact('wishlist', 'id'));
        $this->set('_serialize', ['wishlist', 'id']);
    }

    public function orders($id = null)
    {
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];

        $filterData = [];

        $filterData['customer_id'] = $id;
        $this->set('customerId', $id);

        $orderId = $this->request->getQuery('order_id', '');
        $this->set('orderId', $orderId);
        if ($orderId) {
            $filterData['id'] = $orderId;
        }

        $createdFrom = $this->request->getQuery('created_from', '');
        $this->set('createdFrom', $createdFrom);

        $createdTo = $this->request->getQuery('created_to', '');
        $this->set('createdTo', $createdTo);

        if (!empty($createdFrom) && !empty($createdTo)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['created >='] = $createdFromNew;
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['created <='] = $createdToNew;
        } else if (!empty($createdFrom)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['created >='] = $createdFromNew;
        } else if (!empty($createdTo)) {
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['created <='] = $createdToNew;
        }

        $ship_name = $this->request->getQuery('ship_name', '');
        $this->set('ship_name', $ship_name);
        if (!empty($ship_name)) {
            $tempData['shipping_firstname LIKE '] = '%' . $ship_name . '%';
            $tempData['shipping_lastname LIKE '] = '%' . $ship_name . '%';
            $filterData['AND'][1]['OR'] = $tempData;
        }

        $fromAmount = $this->request->getQuery('from_amount', '');
        $this->set('from_amount', $fromAmount);
        if ($fromAmount > 0) {
            $filterData['payment_amount >= '] = $fromAmount;
        }

        $toAmount = $this->request->getQuery('to_amount', '');
        $this->set('to_amount', $toAmount);
        if ($toAmount > 0) {
            $filterData['payment_amount <='] = $toAmount;
        }

        $ordersTable = TableRegistry::get('SubscriptionManager.Orders');
        $orders = $ordersTable->find('all', ['fields' => ['id', 'location_id', 'shipping_firstname', 'shipping_lastname', 'payment_amount', 'created'], 'conditions' => $filterData])
            ->contain(['Locations' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id', 'title', 'currency', 'currency_logo']);
                },
            ]])
            ->order(['created' => 'DESC']);
        $orders = $this->paginate($orders);
        //pr($orders);
        // die;
        $this->set(compact('orders', 'id'));
        $this->set('_serialize', ['orders', 'id']);
    }

    public function plans($id = null)
    {
        $customerPlansTable = TableRegistry::get('SubscriptionManager.CustomerPlans');
        $customerPlan = $customerPlansTable->newEntity();
        $this->set('customerPlan', $customerPlan);
        if( $this->request->is(['post', 'put']) ){
            $customerPlan = $customerPlansTable->get($this->request->getData('id'));
            $customerPlan = $customerPlansTable->patchEntity($customerPlan, $this->request->getData());
            if( $customerPlansTable->save($customerPlan) ){
                $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
            } else {
                $this->Flash->error(__('Sorry, somthing wrong!'), ['key' => 'adminError']);
            }
        }
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];

        $filterData = [];

        $filterData['customer_id'] = $id;
        $this->set('customerId', $id);

        $sku = $this->request->getQuery('sku', '');
        $this->set('sku', $sku);
        if ( !empty($sku) ) { $filterData['id'] = $sku; }

        $name = $this->request->getQuery('name', '');
        $this->set('name', $name);
        if ( !empty($name) ) { $filterData['id'] = $name; }

        $createdFrom = $this->request->getQuery('created_from', '');
        $this->set('createdFrom', $createdFrom);

        $createdTo = $this->request->getQuery('created_to', '');
        $this->set('createdTo', $createdTo);

        if (!empty($createdFrom) && !empty($createdTo)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['created >='] = $createdFromNew;
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['created <='] = $createdToNew;
        } else if (!empty($createdFrom)) {
            $createdFromNew = date('Y-m-d 00:00:00', strtotime($createdFrom));
            $filterData['created >='] = $createdFromNew;
        } else if (!empty($createdTo)) {
            $createdToNew = date('Y-m-d 23:59:59', strtotime($createdTo));
            $filterData['created <='] = $createdToNew;
        }

        $ship_name = $this->request->getQuery('ship_name', '');
        $this->set('ship_name', $ship_name);
        if (!empty($ship_name)) {
            $tempData['shipping_firstname LIKE '] = '%' . $ship_name . '%';
            $tempData['shipping_lastname LIKE '] = '%' . $ship_name . '%';
            $filterData['AND'][1]['OR'] = $tempData;
        }

        $fromAmount = $this->request->getQuery('from_amount', '');
        $this->set('from_amount', $fromAmount);
        if ($fromAmount > 0) {
            $filterData['payment_amount >= '] = $fromAmount;
        }

        $toAmount = $this->request->getQuery('to_amount', '');
        $this->set('to_amount', $toAmount);
        if ($toAmount > 0) {
            $filterData['payment_amount <='] = $toAmount;
        }

        $plans = $customerPlansTable->find('all', ['conditions' => $filterData])
            ->order(['created' => 'DESC']);
        $plans = $this->paginate($plans);
        //pr($plans);
        // die;
        $this->set(compact('plans', 'id'));
        $this->set('_serialize', ['plans', 'id']);
    }

    public function search()
    {
        $search = $this->request->getQuery('term');
        $responseArray = [];
        $customerData = $this->Customers->find('all', ['conditions' => ['email LIKE ' => '%' . $search . '%']])->toArray();
        if (!empty($customerData)) {
            $counter = 0;
            foreach ($customerData as $temp_customer) {
                $responseArray[$counter]['id'] = $temp_customer->id;
                $responseArray[$counter]['label'] = $temp_customer->email;
                $responseArray[$counter]['value'] = $temp_customer->email;
                $responseArray[$counter]['firstname'] = $temp_customer->firstname;
                $responseArray[$counter]['lastname'] = $temp_customer->lastname;
                $responseArray[$counter]['address'] = $temp_customer->address;
                $responseArray[$counter]['city'] = $temp_customer->city;
                $responseArray[$counter]['pincode'] = $temp_customer->pincode;
                $responseArray[$counter]['mobile'] = $temp_customer->mobile;
                $responseArray[$counter]['email'] = $temp_customer->email;
                $responseArray[$counter]['location_id'] = $temp_customer->location_id;
                $counter++;
            }
        }
        echo json_encode($responseArray);
        exit;
    }

    public function getAddresses()
    {
        $id = $this->request->getQuery('customer-id', 0);
        $dataTable = TableRegistry::get('SubscriptionManager.Addresses');
        $addressesList = $dataTable->find('all', ['conditions' => ['customer_id' => $id]])->hydrate(false)->toArray();
        echo json_encode($addressesList);
        exit;
    }

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedFields', ['id','voucher']);
    }
}
