<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Picqer\Barcode;
use Cake\ORM\Query;

class OrdersController extends AppController
{
    use MailerAwareTrait;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SubscriptionManager.Store');
        $this->loadComponent('SubscriptionManager.Shipvendor');
        $this->loadComponent('SubscriptionManager.Delhivery');
        $this->loadComponent('SubscriptionManager.Coupon');
        $this->loadComponent('SubscriptionManager.Customer');
    }

    // Start of PC-103
	public function beforeFilter(Event $event)
	{
		$fields = ['id_customer_new', 'payment_method', 'product_id', 'product_price', 'product_quantity', 'product_sku', 'shipping_state', 'comment'];
		$this->Security->config('unlockedFields', $fields);
		$actions = ['add'];
		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}
    // End of PC-103

    public function index()
    {
        $this->set('title', 'Admin Panel: Order List');
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('perPage', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 20000,
		];
        $filterData = [];
        $id = $this->request->getQuery('id', '');
        $this->set('id', $id);
        if (!empty($id)) {$filterData['Orders.id'] = $id;}

        $trackingCode = $this->request->getQuery('tracking_code', '');
        $this->set('trackingCode', $trackingCode);
        if (!empty($trackingCode)) {$filterData['tracking_code'] = $trackingCode;}
        //pr($this->request->getQueryParams()); die;

        $email = $this->request->getQuery('email', '');
        $this->set('email', $email);
        if (!empty($email)) {$filterData['Customers.email'] = $email;}

        $mobile = $this->request->getQuery('mobile', '');
        $this->set('mobile', $mobile);
        if (!empty($mobile)) {$filterData['Customers.mobile'] = $mobile;}

        $courierId = $this->request->getQuery('courierId', 0);
        $this->set('courierId', $courierId);
        if ($courierId > 0) {
            $filterData['courier_id'] = $courierId;
        }

        $locationId = $this->request->getQuery('locationId', 0);
        $this->set('locationId', $locationId);
        if ($locationId > 0) {
            $filterData['location_id'] = $locationId;
        }
        $fromAmount = $this->request->getQuery('fromAmount', '');
        $this->set('fromAmount', $fromAmount);
        if (!empty($fromAmount)) {$filterData['payment_amount >= '] = $fromAmount;}

        $toAmount = $this->request->getQuery('toAmount', '');
        $this->set('toAmount', $toAmount);
        if (!empty($toAmount)) {$filterData['payment_amount <= '] = $toAmount;}

        $createdFrom = $this->request->getQuery('created_from', '');
        $this->set('createdFrom', $createdFrom);
        $createdTo = $this->request->getQuery('created_to', '');
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

        $mode = $this->request->getQuery('payment_mode', '');
        $this->set('mode', $mode);
        if ($mode !== '') {$filterData['payment_mode'] = $mode;}

        $status = $this->request->getQuery('status', '');
        $this->set('status', $status);
        if ($status !== '') {$filterData['status'] = $status;}
        //Start of Codes for order status update by delhivery
        $orders = $this->Orders->find('all', ['fields' => ['tracking_code'], 'conditions' => $filterData])
        ->contain([
            'Customers'=>[
                'queryBuilder' => function (Query $q){
                    return $q->select(['id', 'email', 'mobile']);
                }
            ],
            'Locations' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id', 'title', 'code','currency', 'currency_logo']);
                }
            ],
            'Couriers'=>[ 
                'queryBuilder'=>function($q){
                    return $q->select(['id', 'title']);
                }
            ]    
        ])->where(function ($exp, $q) use ($createdFrom, $createdTo) {
            return $exp->between('Orders.created', $createdFrom, $createdTo);
        });
        $query = $this->paginate($orders)->toArray();
        $trackCodes = array_column($query,'tracking_code'); //pr(count($query));
        $trackCodes = implode(',',$trackCodes);
        $this->Delhivery->trackOrders($trackCodes);
        //End of Codes for order status update by delhivery

        $orders = $this->Orders->find('all', ['fields' => ['id','tracking_code', 'courier_id', 'payment_amount', 'created', 'payment_mode', 'status'], 'conditions' => $filterData, 'order' => ['Orders.id' => 'DESC']])
            ->contain([
			    'Customers'=>[
                    'queryBuilder' => function (Query $q){
						return $q->select(['id', 'email', 'mobile']);
                    }
				],
				'Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'code','currency', 'currency_logo']);
                    }
                ],
                'Couriers'=>[ 
                    'queryBuilder'=>function($q){
                        return $q->select(['id', 'title']);
                    }
                ]    
			])->where(function ($exp, $q) use ($createdFrom, $createdTo) {
				return $exp->between('Orders.created', $createdFrom, $createdTo);
			});
            $orders = $this->paginate($orders)->toArray();
            $couriers = TableRegistry::get('SubscriptionManager.Couriers')->find('all',['id','title'])->hydrate(false)->toArray();
            $couriers = array_combine(array_column($couriers,'id'),array_column($couriers,'title'));
            $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields'=>['id', 'title'],'order'=>['title'=>'ASC']])->hydrate(false)->toArray();
            $locations = array_combine(array_column($locations, 'id'), array_column($locations, 'title'));
            //pr($couriers); die;
        $this->set(compact('orders','couriers', 'locations'));
        $this->set('_serialize', ['orders','couriers','locations']);
    }

    public function exports()
    {
        $this->response->withDownload('exports.csv');
        $limit = $this->request->getQuery('perPage', 50);
        $offset = $this->request->getQuery('page', 1);
        $offset = ($offset - 1) * $limit;

        $filterData = [];
        $id = $this->request->getQuery('id', '');
        if (!empty($id)) {$filterData['id'] = $id;}

        $trackingCode = $this->request->getQuery('tracking_code', '');
        if (!empty($trackingCode)) {$filterData['tracking_code'] = $trackingCode;}

        $email = $this->request->getQuery('email', '');
        if (!empty($email)) {$filterData['Customers.email'] = $email;}

        $mobile = $this->request->getQuery('mobile', '');
        if (!empty($mobile)) {$filterData['Customers.mobile'] = $mobile;}

        $courierId = $this->request->getQuery('courierId', 0);
        if ($courierId > 0) {
            $filterData['courier_id'] = $courierId;
        }

        $locationId = $this->request->getQuery('locationId', 0);
        if ($locationId > 0) {
            $filterData['location_id'] = $locationId;
        }

        $fromAmount = $this->request->getQuery('fromAmount', '');
        if (!empty($fromAmount)) {$filterData['payment_amount >= '] = $fromAmount;}

        $toAmount = $this->request->getQuery('toAmount', '');
        if (!empty($toAmount)) {$filterData['payment_amount <= '] = $toAmount;}

        $createdFrom = $this->request->getQuery('created_from', '');
        $createdTo = $this->request->getQuery('created_to', '');

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

        $mode = $this->request->getQuery('payment_mode', '');
        if ($mode !== '') {$filterData['payment_mode'] = $mode;}

        $status = $this->request->getQuery('status', '');
        if ($status !== '') {$filterData['status'] = $status;}

        $data = $this->Orders->find('all', ['conditions' => $filterData, 'limit' => $limit, 'offset' => $offset])
            ->select(['id', 'payment_amount', 'discount', 'payment_mode', 'mode_amount', 'ship_amount', 'coupon_code', 'tracking_code', 'created', 'status', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone'])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('Orders.created', $createdFrom, $createdTo);
            })
            ->contain([
				'Customers' => [
                    'queryBuilder' => function ($q) use ($email, $mobile) {
                        return $q->select(['email', 'mobile']);
                    }
				],
				'OrderDetails' => [
					'queryBuilder' => function ($q) {
						return $q->select(['order_id', 'sku_code', 'title', 'size', 'price', 'quantity']);
					}
				],
				'Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'code']);
                    }
				]
            ])
            ->hydrate(false)
            ->order(['Orders.id' => 'DESC'])->toArray();

        $dataList = [];
        $i = 0;
        foreach ($data as $value) {
            $variants = $value['order_details'];
            //pr($variants);
            array_splice($value, 32, 1); //remove order_details
            if (!empty($variants)) {
                $dataList[$i] = $value;
                $emptyRow = false;
                foreach ($variants as $variant) {
                    $dataList[$i]['country'] = $value['location']['title'] ?? '';
                    $dataList[$i]['email'] = $value['customer']['email'] ?? '';
                    $dataList[$i]['mobile'] = $value['customer']['mobile'] ?? '';
                    if ($emptyRow) {
                        $dataList[$i]['email'] = '';
                        $dataList[$i]['mobile'] = '';

                        $dataList[$i]['discount'] = '';
                        $dataList[$i]['payment_mode'] = '';
                        $dataList[$i]['mode_amount'] = '';
                        //$dataList[$i]['ship_method'] = '';
                        $dataList[$i]['ship_amount'] = '';
                        $dataList[$i]['coupon_code'] = '';
                        $dataList[$i]['tracking_code'] = '';
                        $dataList[$i]['created'] = '';
                        $dataList[$i]['status'] = '';
                        $dataList[$i]['shipping_firstname'] = '';
                        $dataList[$i]['shipping_lastname'] = '';
                        $dataList[$i]['shipping_address'] = '';
                        $dataList[$i]['shipping_city'] = '';
                        $dataList[$i]['shipping_state'] = '';
                        $dataList[$i]['shipping_country'] = '';
                        $dataList[$i]['shipping_pincode'] = '';
                        $dataList[$i]['shipping_email'] = '';
                        $dataList[$i]['shipping_phone'] = '';
                    }
                    $dataList[$i]['id'] = $value['id'];
                    $dataList[$i]['payment_amount'] = $value['payment_amount'];
                    $dataList[$i]['tracking_code'] = $value['tracking_code'];
                    $dataList[$i]['shipping_state'] = $value['shipping_state'];
                    $dataList[$i]['shipping_pincode'] = $value['shipping_pincode'];
                    $dataList[$i]['status'] = $value['status'];

                    $dataList[$i]['title'] = $variant['title'];
                    $dataList[$i]['sku_code'] = $variant['sku_code'];
                    $dataList[$i]['size'] = $variant['size'];
                    $dataList[$i]['price'] = $variant['price'];
                    $dataList[$i]['qty'] = $variant['quantity'];
                    $emptyRow = true;
                    $i++;
                }
            }
        } //pr($dataList); die;
        $_serialize = 'dataList';
        $_header = ['Order ID', 'Email', 'Mobile', 'Country', 'Total Amount', 'Discount', 'Payment Mode', 'Mode Amount', 'Ship Amount', 'Coupon Code', 'Tracking Code', 'Created', 'Status', 'SKU Code', 'Title', 'Size', 'Price', 'Qty', 'Shipping Firstname', 'Shipping Lastname', 'Shipping Address', 'Shipping City', 'Shipping State', 'Shipping Country', 'Shipping Pincode', 'Shipping Email', 'Shipping Phone'];
        $_extract = ['id', 'email', 'mobile', 'country', 'payment_amount', 'discount', 'payment_mode', 'mode_amount', 'ship_amount', 'coupon_code', 'tracking_code', 'created', 'status', 'sku_code', 'title', 'size', 'price', 'qty', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone'];
        $this->set(compact('dataList', '_serialize', '_header', '_extract'));
        $this->viewBuilder()->setClassName('CsvView.Csv');
        return;
    }

    public function view($orderId = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($orderId))) {
            return $this->redirect(['action' => 'index']);
        }
        $this->set('title', 'Admin Panel: Order-View');
        if ($this->request->is(['post'])) {

            $order = $this->Orders->get($orderId, [
                'contain' => ['Customers', 'OrderDetails', 'OrderComments'],
            ]);

            $currentStatus = $order->status;
            $status = $this->request->getData('status');
            $comment = $this->request->getData('comment');
            $order->status = $status;
            if ($this->Orders->save($order)) {
                $invoice = TableRegistry::get('SubscriptionManager.Invoices')->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
                if ((count($invoice) > 0) && ($currentStatus != 'delivered')) {
                    $invoice = $invoice[0];
                    $invoiceTable = TableRegistry::get('SubscriptionManager.Invoices');
                    $invData = $invoiceTable->get($invoice['id']);
                    $invData->status = $status;
                    $invoiceTable->save($invData);
                }
                $commentTable = TableRegistry::get('SubscriptionManager.OrderComments');
                $comData = $commentTable->newEntity();
                $comData->order_id = $orderId;
                $comData->given_by = 'admin';
                if ($currentStatus != 'delivered') {
                    $comData->status = $status;
                    $this->Flash->success(__('The order has been updated!'), ['key' => 'adminSuccess']);
                } else {
                    $this->Flash->error(__('Sorry, the delivered status can not be changed!'), ['key' => 'adminError']);
                }
                $comData->comment = $comment;
                $commentTable->save($comData);
                if (($currentStatus != 'delivered') && ($status == 'delivered')) {
                    $this->Store->updateWalletAfterDelivery($orderId);
                    $this->Store->orderStatusEmails($orderId, 'delivered');
                }

                if (($currentStatus != 'intransit') && ($status == 'intransit')) {
                    $this->Store->orderStatusEmails($orderId,'intransit');
                }

                if (($currentStatus != 'accepted') && ($status == 'accepted')) {
                    $this->Store->orderStatusEmails($orderId,'confirmed');
                    $this->Store->updateWalletAfterPayment($id);
                }
            } else {
                $this->Flash->error(__('Sorry, the order could not be updated!'), ['key' => 'adminError']);
            }
        }

        $order = $this->Orders->get($orderId, [ //9891164356
            'contain' => ['Customers', 'Locations', 'OrderDetails', 'OrderComments', 'PaymentMethods'],
        ]);

        $barcode['code'] = 0;
        $barcode['tracking_code'] = 0;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        if (!empty($order->tracking_code)) {
            $barcode['tracking_code'] = $order->tracking_code;
        }
        $barcode['code'] = base64_encode($generator->getBarcode($barcode['tracking_code'], $generator::TYPE_CODE_128));

        $couponData = [];
        if (!empty($order->coupon_code)) {
            $couponData = $this->Coupon->orderedData($order->coupon_code, $orderId, $order->customer->email);
        }
        $this->set(compact('order', 'orderId', 'couponData', 'barcode'));
        $this->set('_serialize', ['order', 'orderId', 'couponData', 'barcode']);
    }

    public function invoice($orderId = null, $key = null, $md5 = null)
    {
        $this->set('title', 'Admin Panel: Order-Invoice');
        if (($key != 'key') || ($md5 != md5($orderId))) {
            return $this->redirect(['action' => 'index']);
        }
        $invoice = [];
        $invoice = TableRegistry::get('SubscriptionManager.Invoices')->find('all', ['contain' => ['Locations','InvoiceDetails'], 'conditions' => ['order_id' => $orderId]])->toArray();
        $invoice = $invoice[0] ?? []; //pr($invoice);
        $this->set(compact('invoice', 'orderId'));
        $this->set('_serialize', ['invoice', 'orderId']);
    }

    public function awbcode($orderId = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($orderId))) {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post'])) {
            $order = $this->Orders->get($orderId, ['contain' => ['Customers']]);
            $shipping_phone = $this->request->getData('shipping_phone');
            $shipping_email = $this->request->getData('shipping_email');
            $shipping_pincode = $this->request->getData('shipping_pincode');
            $shipping_country = $this->request->getData('shipping_country');
            $shipping_state = $this->request->getData('shipping_state');
            $shipping_city = $this->request->getData('shipping_city');
            $shipping_address = $this->request->getData('shipping_address');
            $shipping_lastname = $this->request->getData('shipping_lastname');
            $shipping_firstname = $this->request->getData('shipping_firstname');

            if ($shipping_firstname != '') {
                $order->shipping_firstname = $shipping_firstname;
            }

            if ($shipping_lastname != '') {
                $order->shipping_lastname = $shipping_lastname;
            }

            if ($shipping_address != '') {
                $order->shipping_address = $shipping_address;
            }

            if ($shipping_city != '') {
                $order->shipping_city = $shipping_city;
            }

            if ($shipping_state != '') {
                $order->shipping_state = $shipping_state;
            }

            if ($shipping_country != '') {
                $order->shipping_country = $shipping_country;
            }

            if ($shipping_pincode != '') {
                $order->shipping_pincode = $shipping_pincode;
            }

            if ($shipping_email != '') {
                $order->shipping_email = $shipping_email;
            }

            if ($shipping_phone != '') {
                $order->shipping_phone = $shipping_phone;
            }

            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been updated!'), ['key' => 'adminSuccess']);
                $invoiceTable = TableRegistry::get('SubscriptionManager.Invoices');
                $invoice = $invoiceTable->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
                if (count($invoice) > 0) {
                    $invoice = $invoice[0];
                    $invData = $invoiceTable->get($invoice['id']);
                    if ($shipping_firstname != '') {
                        $invData->shipping_firstname = $shipping_firstname;
                    }

                    if ($shipping_lastname != '') {
                        $invData->shipping_lastname = $shipping_lastname;
                    }

                    if ($shipping_address != '') {
                        $invData->shipping_address = $shipping_address;
                    }

                    if ($shipping_city != '') {
                        $invData->shipping_city = $shipping_city;
                    }

                    if ($shipping_state != '') {
                        $invData->shipping_state = $shipping_state;
                    }

                    if ($shipping_country != '') {
                        $invData->shipping_country = $shipping_country;
                    }

                    if ($shipping_pincode != '') {
                        $invData->shipping_pincode = $shipping_pincode;
                    }

                    if ($shipping_email != '') {
                        $invData->shipping_email = $shipping_email;
                    }

                    if ($shipping_phone != '') {
                        $invData->shipping_phone = $shipping_phone;
                    }
                    $invoiceTable->save($invData);
                } else {
                    $this->Store->createInvoice($orderId);
                }
                if (empty($order->tracking_code)) {
                    //$this->Shipvendor->pushOrderByAdmin($id);
                }
            } else {
                $this->Flash->error(__('Sorry, the order could not be updated!'), ['key' => 'adminError']);
            }
        }
        $order = $this->Orders->get($orderId, ['contain' => ['Customers']]);
        $country = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields' => ['title'], 'conditions' => ['is_active' => 'active'], 'order' => ['title']])->hydrate(false)->toArray();
        $country = array_column($country,'title');
        $country = array_combine($country,$country);
        //pr($country);
        $this->set(compact('order','orderId','country'));
        $this->set('_serialize', ['order','orderId','country']);
    }

    public function add($id = 0)
    {
        $error = false;
        $message = "";
        $orderDetailTable = TableRegistry::get('SubscriptionManager.OrderDetails');
        $productTable = TableRegistry::get('SubscriptionManager.Products');
        //$product = $productTable->get(17, ['contain' => ['ProductPrices']]);
        //pr($product);die;
        $locationId  = $this->request->getQuery('location-id', 1);
        if ($this->request->is(['post', 'put'])) {
            $id_customer = $this->request->getData('id_customer_new', 0);
            $payment_method = $this->request->getData('payment_method', "0");
            $shipping_amount = $this->request->getData('shipping_amount', "0");
            $product_id = $this->request->getData('product_id', []);
            $product_sku = $this->request->getData('product_sku', []);
            $product_quantity = $this->request->getData('product_quantity', []);
            $product_price = $this->request->getData('product_price', []);
            $product_total = 0;
            foreach ($product_id as $temp_key => $temp_id) {
                $product_total += ($product_quantity[$temp_key] * $product_price[$temp_key]);
            }
            $paymentMethod = TableRegistry::get('SubscriptionManager.PaymentMethods')->getPaymentGatewayById($payment_method);
            $paymentCode = $paymentMethod['code'] ?? 0;
            $payment_amount = $product_total + $shipping_amount;
            $order = $this->Orders->newEntity();
            $order->customer_id = $id_customer;
            $order->location_id = $locationId;
            $order->courier_id = 3;
            $order->payment_method_id = $payment_method;
            $order->payment_mode = ($paymentCode == 'cod') ? 'postpaid' : 'prepaid';
            $order->product_total = $product_total;
            $order->payment_amount = $this->request->getData('grand_total', '0');
            $order->ship_amount = $shipping_amount;
            $order->status = 'accepted';
            $order->shipping_firstname = $this->request->getData('shipping_firstname', "");
            $order->shipping_lastname = $this->request->getData('shipping_lastname', "");
            $order->shipping_address = $this->request->getData('shipping_address', "");
            $order->shipping_city = $this->request->getData('shipping_city', "");
            $order->shipping_state = $this->request->getData('shipping_state', "");
            $order->shipping_country = $this->request->getData('shipping_country', "");
            $order->shipping_pincode = $this->request->getData('shipping_pincode', "");
            $order->shipping_email = $this->request->getData('shipping_email', "");
            $order->shipping_phone = $this->request->getData('shipping_phone', "");
            $order->transaction_ip = $_SERVER['REMOTE_ADDR'];
            $order->is_admin_order = '1';
            if ($this->Orders->save($order)) //$this->Orders->save($order)
            {
                $order_id = $order->id;
                if (count($product_id) > 0) {
                    $orderDetails = [];
                    foreach ($product_id as $temp_key => $temp_id) {
                        $product = $productTable->get($temp_id, ['contain' => ['ProductPrices']]);
                        $order_detail = $orderDetailTable->newEntity();
                        $order_detail->order_id = $order_id;
                        $order_detail->product_id = $temp_id;
                        $order_detail->title = $product->product_prices[0]->title;
                        $order_detail->sku_code = $product->sku_code;
                        $order_detail->size = $product->size . ' ' . strtoupper($product->unit);
                        $order_detail->price = $product_price[$temp_key];
                        $order_detail->quantity = $product_quantity[$temp_key];
                        $order_detail->short_description = $product->product_prices[0]->short_description;
                        $orderDetailTable->save($order_detail);
                        $orderDetails[] = [
                            'product_id' => $temp_id,
                            'quantity' => $product_quantity[$temp_key]
                        ];
                    }
                    $this->Store->updateStockAfterOrderPlaced($orderDetails);
                    $this->Store->createInvoice($order_id);
                    $this->Shipvendor->pushOrderByAdmin($order_id);
                    $this->Store->orderStatusEmails($order_id, 'confirmed');
                }

                $this->Flash->success(__('The order has been saved!'), ['key' => 'adminSuccess']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'), ['key' => 'adminError']);
        }

        $order = ($id > 0) ? $this->Orders->get($id, ['contain' => ['Customers', 'OrderDetails']]) : $this->Orders->newEntity();
        $locationId  = $order->location_id ?? $locationId;
        if( $locationId > 0 ){
            $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields' => ['id','title'], 'conditions' => ['id'=>$locationId, 'is_active' =>'active'], 'order' => ['title']])->hydrate(false)->toArray();
        }else{
            $locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields' => ['id','title'], 'conditions' => ['is_active' =>'active'], 'order' => ['title']])->hydrate(false)->toArray();
        }
        $country = array_column($locations,'title');
        $country = array_combine($country,$country);

        $pgMethods = TableRegistry::get('SubscriptionManager.PaymentMethods')->find('all', ['fields' => ['id','title'], 'conditions'=>['status'=>1], 'order' => ['title'=>'ASC']])->hydrate(false)->toArray();
        $pgId = array_column($pgMethods,'id');
        $pgTitle = array_column($pgMethods,'title');
        $pgMethods = array_combine($pgId, $pgTitle);
        //pr($pgMethods);
        //id, title, sku_code, price
        $products = TableRegistry::get('SubscriptionManager.ProductPrices')->find('all', ['fields'=>['Products.id','Products.sku_code','title','price'], 'contain'=>['Products'],'conditions'=>['ProductPrices.is_active' => 'active', 'ProductPrices.location_id' => $locationId, 'Products.is_active' => 'active', 'quantity >' => 0]])->hydrate(false)->toArray();
        //pr($products);
        $this->set(compact('order', 'country', 'locations', 'locationId', 'products','pgMethods', 'error','message'));        
        $this->set('_serialize', ['order', 'country', 'locations', 'locationId', 'products','pgMethods', 'error', 'message']);
    }

    public function cancel($id = null, $key = null, $md5 = null)
    {
        if (($key == 'key') && ($md5 == md5($id))) {
            $result = $this->Shipvendor->cancelOrder($id);
            if ($result) {
                $this->Flash->success(__('Order #' . $id . ' has been cancelled successfully.'), ['key' => 'adminSuccess']);
            } else {
                $this->Flash->error(__('Order #' . $id . ' has not cancelled. Please, try again.'), ['key' => 'adminError']);
            }
        }
        return $this->redirect(['action' => 'index']);
    }

    public function generate($id = null, $key = null, $md5 = null)
    {
        if (($key == 'key') && ($md5 == md5($id))) {
            $result = $this->Shipvendor->pushOrderByAdmin($id);
            switch ($result) {
                case 1:$this->Flash->success(__('AWB number generated successfully.'), ['key' => 'adminSuccess']);
                    break;
                case 2:$this->Flash->error(__('Sorry, This order already push at shiprocket panel #' . $id), ['key' => 'adminError']);
                    break;
                default:$this->Flash->error(__('Sorry, please try again.'), ['key' => 'adminError']);
            }
        }
        $this->redirect($this->referer());
    }

    public function delivered($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        $orderTable = TableRegistry::get('SubscriptionManager.Orders');
        $order = $orderTable->get($id);
        $order->status = 'delivered';
        $orderTable->save($order);
        $this->Store->updateWalletAfterDelivery($id);
        $this->Store->changeInvoiceStatus($id, 'delivered');
        $this->Flash->success(__('Order #' . $id . ' status has been changed to Delivered.'), ['key' => 'adminSuccess']);

        return $this->redirect(['action' => 'index']);
    }

    public function shiprocket()
    {
        $this->Shipvendor->syncStatus();
        $this->redirect($this->referer());
    }
}
