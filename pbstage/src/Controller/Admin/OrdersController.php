<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Picqer\Barcode;
use Cake\Collection\Collection;

class OrdersController extends AppController
{
    use MailerAwareTrait;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Store');
        $this->loadComponent('Sms');
        $this->loadComponent('Customer');
        $this->loadComponent('Shipvendor');
        $this->loadComponent('Coupon');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Security');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

    }

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedFields', ['shipmentIds', 'id_customer_new', 'payment_method', 'product_id', 'product_price', 'product_quantity', 'product_sku', 'shipping_state', 'comment']);
        $this->Security->config('unlockedActions', ['getLabels']);
    }

    public function index()
    {   
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('perPage', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 20000,
        ];
        $filterData = [];
        $orderNumber = $this->request->getQuery('id', '');
        $this->set('orderNumber', $orderNumber);
        if (!empty($orderNumber)) { $filterData['Orders.id'] = $orderNumber; }

        $trackingCode = $this->request->getQuery('tracking_code', '');
        $this->set('trackingCode', $trackingCode);
        if (!empty($trackingCode)) {$filterData['tracking_code'] = $trackingCode;}

        $email = $this->request->getQuery('email', '');
        $this->set('email', $email);
        if (!empty($email)) {$filterData['email'] = $email;}

        $mobile = $this->request->getQuery('mobile', '');
        $this->set('mobile', $mobile);
        if (!empty($mobile)) {$filterData['mobile'] = $mobile;}

        $shippingFirstname = $this->request->getQuery('shipping_firstname', '');
        $this->set('shippingFirstname', $shippingFirstname);
        if (!empty($shippingFirstname)) {$filterData['shipping_firstname'] = $shippingFirstname;}

        $courierId = $this->request->getQuery('courierId', '');
        $this->set('courierId', $courierId);
        if ( !empty($courierId) && ($courierId > 0) ) {
            $filterData['delhivery_pickup_id'] = $courierId;
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

        $zone = $this->request->getQuery('zoneId', '');
        $this->set('zoneId', $zone);
        if ($zone !== '') {$filterData['zone'] = $zone;}

        $orders = $this->Orders->find('all', ['fields' => ['Orders.id', 'tracking_code', 'email', 'mobile', 'payment_amount', 'created', 'delhivery_pickup_id', 'delhivery_response', 'payment_mode', 'status'], 'conditions' => $filterData, 'order' => ['Orders.id' => 'DESC']])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('Orders.created', $createdFrom, $createdTo);
            })
            ->contain([
                'Couriers' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title']);
                    }
                ],
                'OrderDetails' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['order_id', 'product_id', 'OrderDetails.qty']);
                    }
                ],
                'OrderDetails.Products' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id','cost_price']);
                    }
                ],
                'OrderDetails.Products.ProductsCategories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id','category_id']);
                    }
                ],
                'OrderDetails.Products.ProductsCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['name']);
                    }
                ]
            ]);
            
        //pr($orders->hydrate(0)->limit(5)->toArray()); die;
        $orders = $this->paginate($orders);
        $shipmentIds = [];
        $labelPdf = 0;
        foreach ($orders as $value) {;
            $a = unserialize($value->delhivery_response);
            $id = $a['payload']['shipment_id'] ?? 0;
            if ($id > 0) {
                $shipmentIds[] = $id;
            }
        }
        if (count($shipmentIds)) {
            $labelPdf = 1;
        }
        $couriers = TableRegistry::get("Couriers")->find('all',['fields'=>['id','title'],'order'=>['title'=>'asc']]);
        $couriers = new Collection($couriers);
        $couriers = $couriers->combine('id','title');
        $couriers = $couriers->toArray();
		
        $zones = TableRegistry::get("ZoneCodes")->find('all',['fields'=>['id','zone_type'],'order'=>['zone_type'=>'asc']]);
        $zones = new Collection($zones);
        $zones = $zones->combine('id','zone_type');
        $zones = $zones->toArray();
		
        $this->set(compact('orders', 'couriers', 'zones', 'labelPdf', 'shipmentIds'));
        $this->set('_serialize', ['orders', 'couriers', 'zones', 'labelPdf', 'shipmentIds']);
    }

    public function getLabels()
    {
        $res = '';
        $shipmentIds = $this->request->data('shipmentIds');
        if (count($shipmentIds)) {
            $res = $this->Shipvendor->getLabels($shipmentIds);
        }
        if (empty($res)) {
            $res = '<span>Sorry, Labels not available</span>';
        } else {
            $res = '<a href="' . $res . '" target="_blank">Click to Download</a>';
        }
        echo $res;die;
    }

    public function exports()
    {
        $this->response->withDownload('exports.csv');
        $limit = $this->request->getQuery('perPage', 50);
        $offset = $this->request->getQuery('page', 1);
        $offset = ($offset - 1) * $limit;

        $filterData = [];
        $orderNumber = $this->request->getQuery('id', '');
        $this->set('orderNumber', $orderNumber);
        if (!empty($orderNumber)) { $filterData['Orders.id'] = $orderNumber; }

        $trackingCode = $this->request->getQuery('tracking_code', '');
        if (!empty($trackingCode)) {$filterData['tracking_code'] = $trackingCode;}

        $email = $this->request->getQuery('email', '');
        if (!empty($email)) {$filterData['email'] = $email;}

        $mobile = $this->request->getQuery('mobile', '');
        if (!empty($mobile)) {$filterData['mobile'] = $mobile;}

        $courierId = $this->request->getQuery('courierId', '');
        $this->set('courierId', $courierId);
        if ( !empty($courierId) && ($courierId > 0) ) {
            $filterData['delhivery_pickup_id'] = $courierId;
        }

        $fromAmount = $this->request->getQuery('fromAmount', '');
        $this->set('fromAmount', $fromAmount);
        if (!empty($fromAmount)) {$filterData['payment_amount >= '] = $fromAmount;}

        $toAmount = $this->request->getQuery('toAmount', '');
        $this->set('toAmount', $toAmount);
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

        $zone = $this->request->getQuery('zoneId', '');
        if ($zone !== '') {$filterData['zone'] = $zone;}

        $data = $this->Orders->find('all', ['conditions' => $filterData, 'limit' => $limit, 'offset' => $offset])
            ->select(['id', 'email', 'mobile', 'payment_amount', 'discount', 'payment_mode', 'mode_amount', 'ship_method', 'ship_amount', 'coupon_code', 'tracking_code', 'created', 'status', 'is_admin_order', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone'])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            })
            ->contain([
                'Couriers' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title']);
                    }
                ],
                'OrderDetails' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['order_id', 'sku_code', 'title', 'size', 'price', 'qty', 'goods_tax']);
                    },
                ],
                'OrderDetails.Products' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id','cost_price']);
                    }
                ],
                'OrderDetails.Products.ProductsCategories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id','category_id']);
                    }
                ],
                'OrderDetails.Products.ProductsCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['name']);
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
                    if ($emptyRow) {
                        $dataList[$i]['email'] = '';
                        $dataList[$i]['mobile'] = '';

                        $dataList[$i]['discount'] = '';
                        $dataList[$i]['payment_mode'] = '';
                        $dataList[$i]['mode_amount'] = '';
                        $dataList[$i]['ship_method'] = '';
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
                    $dataList[$i]['courier'] = $value['courier']['title'] ?? '';
                    $dataList[$i]['status'] = $value['status'];
                    $dataList[$i]['order_by'] = ($value['is_admin_order'] == 1) ? 'Admin' : 'Customer';

                    $dataList[$i]['title'] = $variant['title'];
                    $dataList[$i]['sku_code'] = $variant['sku_code'];
                    $dataList[$i]['size'] = $variant['size'];
                    $dataList[$i]['price'] = $variant['price'];
                    $dataList[$i]['qty'] = $variant['qty'];
                    $dataList[$i]['goods_tax'] = $variant['goods_tax'];

                    $category = '';
                    $categories = $variant['product']['products_categories'] ?? [];
                    foreach( $categories as $cat ){
                        $category = $cat['category']['name'].', ';
                    }
                    $dataList[$i]['categories'] = substr($category, 0, -2);
                    $emptyRow = true;
                    $i++;
                }
            }
        }
        $_serialize = 'dataList';
        $_header = ['Order ID', 'Email', 'Mobile', 'Total Amount', 'Discount', 'Payment Mode', 'Mode Amount', 'Courier Name', 'Ship Method', 'Ship Amount', 'Coupon Code', 'Tracking Code', 'Created', 'Status', 'Order By', 'SKU Code', 'Categories', 'Title', 'Size', 'Price', 'Qty', 'Tax', 'Shipping Firstname', 'Shipping Lastname', 'Shipping Address', 'Shipping City', 'Shipping State', 'Shipping Country', 'Shipping Pincode', 'Shipping Email', 'Shipping Phone'];
        $_extract = ['id', 'email', 'mobile', 'payment_amount', 'discount', 'payment_mode', 'mode_amount', 'courier', 'ship_method', 'ship_amount', 'coupon_code', 'tracking_code', 'created', 'status', 'order_by', 'sku_code', 'categories', 'title', 'size', 'price', 'qty', 'goods_tax', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone'];
        $this->set(compact('dataList', '_serialize', '_header', '_extract'));
        $this->viewBuilder()->setClassName('CsvView.Csv');
        return;
    }

    public function view($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post'])) {

            $order = $this->Orders->get($id, [
                'contain' => ['Customers', 'OrderDetails', 'OrderComments'],
            ]);
            $isOrderReversed = $order->is_points_reversed;
            $currentStatus = $order->status;
            $status = $this->request->getData('status');
            $comment = $this->request->getData('comment');
            $order->status = $status;
            if ( in_array($currentStatus, ['cancelled','cancelled_by_customer']) && ($status == 'delivered') ){
                $this->Flash->error(__('Sorry, You can not change "Cancelled" to "Delivered" status!'), ['key' => 'adminError']);
            }else if ( $this->Orders->save($order)) {
                $invoice = TableRegistry::get('Invoices')->find('all', ['conditions' => ['order_number' => $id]])->toArray();
                if ((count($invoice) > 0) && ($currentStatus != 'delivered')) {
                    $invoice = $invoice[0];
                    $invoiceTable = TableRegistry::get('Invoices');
                    $invData = $invoiceTable->get($invoice['id']);
                    $invData->status = $status;
                    $invoiceTable->save($invData);
                }
                $commentTable = TableRegistry::get('OrderComments');
                $comData = $commentTable->newEntity();
                $comData->order_id = $id;
                $comData->given_by = 'admin';
                $comData->status = $status;
                $this->Flash->success(__('The order has been updated!'), ['key' => 'adminSuccess']);                
                $comData->comment = $comment;
                $commentTable->save($comData);
                if (($currentStatus != 'delivered') && ($status == 'delivered')) {
                    $this->Store->updateWalletAfterDelivery($id);
                    $this->Store->orderDelivered($id);
                }

                if (($currentStatus != 'intransit') && ($status == 'intransit')) {
                    $this->Store->orderIntransit($id);
                }

                if (($currentStatus != 'accepted') && ($status == 'accepted')) {
                    $oDetails = $this->Customer->getOrdersDetails($order->customer_id, $id);
                    $text = '';
                    $total = count($oDetails['details']);
                    if (count($oDetails['details']) > 1) {
                        $total = $total - 1;
                        $text = $oDetails['details'][0]['title'] . " + $total";
                    } else {
                        $text = $oDetails['details'][0]['title'];
                    }

                    $this->Sms->orderSend($oDetails['shippingPhone'], $id, $oDetails['paymentAmount'], $oDetails['paymentMethodName'], $text);
                    $this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);

                    $this->Store->updateWalletAfterPayment($id);
                    $this->Store->updateStockAfterOrderPlaced($oDetails['details']);
                    //$this->Store->createInvoice($id);
                    //send order to delhivery
                    if (empty($oDetails['trackingCode'])) {
                        $this->Shipvendor->pushOrderByAdmin($id);
                    }
                }

                if ( !in_array($currentStatus, ['cancelled','cancelled_by_customer']) && in_array($status, ['cancelled','cancelled_by_customer']) ) {
                    if( $isOrderReversed == 0 ){
                        $order->is_points_reversed  = 1;
                        $this->Orders->save($order);
                        //credited wallets
                        if($order->pb_cash_amount > 0 || $order->pb_points_amount > 0 || $order->gift_voucher_amount > 0){
                            $transaction_type		= 1;
                            $pb_cash				= $order->pb_cash_amount;
                            $pb_points				= $order->pb_points_amount;
                            $voucher_amount			= $order->gift_voucher_amount;
                            $comments				= "Wallet credited after cancelling an Order #".$id;
                            $this->Store->logPBWallet($order->customer_id, $transaction_type, 0, $id, $pb_cash, $pb_points, $voucher_amount, $comments);
                        }
                        //debited wallets
                        if($order->credit_cash_amount > 0 || $order->credit_points_amount > 0 || $order->credit_gift_amount > 0){
                            $transaction_type		= 0;
                            $pb_cash				= $order->credit_cash_amount;
                            $pb_points				= $order->credit_points_amount;
                            $voucher_amount			= $order->credit_gift_amount;
                            $comments				= "Wallet deducted after cancelling an Order #".$id;
                            $this->Store->logPBWallet($order->customer_id, $transaction_type, 0, $id, $pb_cash, $pb_points, $voucher_amount, $comments);
                        }
                    }
                }

            } else {
                $this->Flash->error(__('Sorry, the order could not be updated!'), ['key' => 'adminError']);
            }
        }

        $order = $this->Orders->get($id, [
            'contain' => ['Customers', 'OrderDetails', 'OrderComments', 'PaymentMethods'],
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
            $couponData = $this->Coupon->orderedData($order->coupon_code, $id, $order->customer->email);
        }
        $this->set(compact('order', 'couponData', 'barcode'));
        $this->set('_serialize', ['order', 'couponData', 'barcode']);
    }

    public function invoice($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }
        $order = $invoice = $invoiceDetails = [];
        $order = $this->Orders->find('all', ['conditions' => ['id' => $id]])->toArray();
        $order = (count($order) > 0) ? $order[0] : [];

        $invoiceDetails = [];
        $invoice = TableRegistry::get('Invoices')->find('all', ['conditions' => ['order_number' => $id]])->toArray();
        if (count($invoice) > 0) {
            $invoice = $invoice[0];
            $invoiceDetails = TableRegistry::get('InvoiceDetails')->find('all', ['conditions' => ['invoice_id' => $invoice->id]])->toArray();
            //pr($invoiceDetails);
        }
        $this->set(compact('invoice', 'invoiceDetails', 'order'));
        $this->set('_serialize', ['invoice', 'invoiceDetails', 'order']);
    }

    public function awbcode($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post'])) {

            $order = $this->Orders->get($id, [
                'contain' => ['Customers', 'OrderDetails', 'OrderComments'],
            ]);

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
                $invoice = TableRegistry::get('Invoices')->find('all', ['conditions' => ['order_number' => $id]])->toArray();
                if (count($invoice) > 0) {
                    $invoice = $invoice[0];
                    $invoiceTable = TableRegistry::get('Invoices');
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
                    $this->Store->createInvoice($id);
                }
                if (empty($order->tracking_code)) {
                    $this->Shipvendor->pushOrderByAdmin($id);
                }
            } else {
                $this->Flash->error(__('Sorry, the order could not be updated!'), ['key' => 'adminError']);
            }
        }
        $order = $this->Orders->get($id, [
            'contain' => ['Customers', 'OrderDetails', 'OrderComments', 'PgResponses']
        ]);
        //pr($order->pg_response->pg_data); die;
        $this->set('order', $order);
        $this->set('_serialize', ['order']);
    }

    public function add($id = 0)
    {
        $error = false;
        $message = "";
        if ($this->request->is(['post', 'put'])) {
            $id_customer = $this->request->getData('id_customer_new', 0);

            $shipping_firstname = $this->request->getData('shipping_firstname', "");
            $shipping_lastname = $this->request->getData('shipping_lastname', "");
            $shipping_address = $this->request->getData('shipping_address', "");
            $shipping_city = $this->request->getData('shipping_city', "");
            $shipping_pincode = $this->request->getData('shipping_pincode', "");
            $shipping_phone = $this->request->getData('shipping_phone', "");
            $shipping_email = $this->request->getData('shipping_email', "");
            $shipping_state = $this->request->getData('shipping_state', "");

            $payment_method  = $this->request->getData('payment_method', "0");
            $shipping_amount = $this->request->getData('shipping_amount', "0");
            $product_total  = $this->request->getData('sub_total', "0");
            $payment_amount = $this->request->getData('grand_total', "0");

            $product_id = $this->request->getData('product_id', []);
            $product_sku = $this->request->getData('product_sku', []);
            $product_quantity = $this->request->getData('product_quantity', []);
            $product_price = $this->request->getData('product_price', []);
            
            //pr($this->request->getData()); die;
            
            /*$product_total = 0;
            foreach ($product_id as $temp_key => $temp_id) {
                $product_total += ($product_quantity[$temp_key] * $product_price[$temp_key]);
            }
            $payment_amount = $product_total + $shipping_amount;
            */

            $customerTable = TableRegistry::get('Customers');
            $customer = $customerTable->get($id_customer);

            $order = $this->Orders->newEntity();

            $order->customer_id = $id_customer;
            $order->payment_method_id = $payment_method;
            $order->payment_mode = ($payment_method == 1) ? 'postpaid' : 'prepaid';
            $order->product_total = $product_total;
            $order->payment_amount = $payment_amount;
            $order->discount = 0;
            $order->ship_amount = $shipping_amount;
            $order->ship_discount = 0;
            $order->mode_amount = 0;
            $order->coupon_code = '';
            $order->mobile = $customer->mobile;
            $order->email = $customer->email;
            $order->status = 'accepted';
            $order->zone = $this->Store->getZoneIdByPincode($shipping_pincode);
            $order->shipping_firstname = $shipping_firstname;
            $order->shipping_lastname = $shipping_lastname;
            $order->shipping_address = $shipping_address;
            $order->shipping_city = $shipping_city;
            $order->shipping_state = $shipping_state;
            $order->shipping_country = 'India';
            $order->shipping_pincode = $shipping_pincode;
            $order->shipping_email = $shipping_email;
            $order->shipping_phone = $shipping_phone;
            $order->gift_voucher_amount = 0;
            $order->pb_points_amount = 0;
            $order->pb_cash_amount = 0;
            $order->credit_gift_amount = 0;
            $order->credit_points_amount = 0;
            $order->credit_cash_amount = 0;
            $order->transaction_ip = $_SERVER['REMOTE_ADDR'];
            $order->is_admin_order = '1';
            if ($this->Orders->save($order)) //$this->Orders->save($order)
            {
                $order_id = $order->id;
                if (count($product_id) > 0) {
                    $orderDetailTable = TableRegistry::get('OrderDetails');
                    $productTable = TableRegistry::get('Products');
                    $orderDetails = array();
                    $temp_counter = 0;
                    foreach ($product_id as $temp_key => $temp_id) {
                        $product = $productTable->get($temp_id);
                        $order_detail = $orderDetailTable->newEntity();
                        $order_detail->order_id = $order_id;
                        $order_detail->product_id = $temp_id;
                        $order_detail->title = $product->title;
                        $order_detail->sku_code = $product->sku_code;
                        $order_detail->size = $product->size . ' ' . strtoupper($product->size_unit);
                        $order_detail->price = $product_price[$temp_key];
                        $order_detail->qty = $product_quantity[$temp_key];
                        $order_detail->short_description = $product->short_description;
                        $orderDetailTable->save($order_detail);

                        $orderDetails[$temp_counter]['productId'] = $temp_id;
                        $orderDetails[$temp_counter]['qty'] = $product_quantity[$temp_key];
                        $temp_counter++;
                    }
                    $this->Store->updateStockAfterOrderPlaced($orderDetails);
                    $this->Store->createInvoice($order_id);
                    $this->Shipvendor->pushOrderByAdmin($order_id);

                    $oDetails = $this->Customer->getOrdersDetails($id_customer, $order_id);
                    $text = '';
                    $total = isset($oDetails['details']) ? count($oDetails['details']) : 0;
                    if ($total > 0) {
                        if ($total > 1) {
                            $total = $total - 1;
                            $text = $oDetails['details'][0]['title'] . " + $total";
                        } else {
                            $text = $oDetails['details'][0]['title'];
                        }
                        $this->Sms->orderSend($oDetails['shippingPhone'], $order_id, $oDetails['paymentAmount'], $oDetails['paymentMethodName'], $text);
                        $this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);
                    }
                }

                $this->Flash->success(__('The order has been saved!'), ['key' => 'adminSuccess']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'), ['key' => 'adminError']);
        }
        if ($id > 0) {
            $order = $this->Orders->get($id, ['contain' => ['Customers', 'OrderDetails']]);
        } else {
            $order = $this->Orders->newEntity();
        }
        $locationTable = TableRegistry::get('Locations');
        $states = $locationTable->find('all', ['fields' => ['title'], 'conditions' => ['parent_id' => 2], 'order' => ['title']])->hydrate(false)->toArray();
        $productTable = TableRegistry::get('Products');
        $inProduct = [572, 573, 574, 575, 576, 577];
        $products = $productTable->find('all', ['conditions' => ['is_active' => 'active', 'qty >' => 0], 'order' => ['title']])->orWhere(['id IN' => $inProduct])->toArray();

        $this->set('order', $order);
        $this->set('states', $states);
        $this->set('products', $products);
        $this->set('error', $error);
        $this->set('message', $message);
        $this->set('_serialize', ['order', 'states', 'products', 'error', 'message']);
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
                case 3:$this->Flash->error(__('Sorry, Service not available on entered pincode'), ['key' => 'adminError']);
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

        $orderTable = TableRegistry::get('Orders');
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
        $filterData = [];
        $id = $this->request->getQuery('id', '');
        if (!empty($id)) { $filterData['Orders.id'] = $id; }

        $trackingCode = $this->request->getQuery('tracking_code', '');
        if (!empty($trackingCode)) {$filterData['tracking_code'] = $trackingCode;}

        $email = $this->request->getQuery('email', '');
        if (!empty($email)) {$filterData['email'] = $email;}

        $mobile = $this->request->getQuery('mobile', '');
        if (!empty($mobile)) {$filterData['mobile'] = $mobile;}

        $shippingFirstname = $this->request->getQuery('shipping_firstname', '');
        if (!empty($shippingFirstname)) {$filterData['shipping_firstname'] = $shippingFirstname;}

        $courierId = $this->request->getQuery('courierId', '');
        if ( !empty($courierId) && ($courierId > 0) ) {
            $filterData['delhivery_pickup_id'] = $courierId;
        }

        $fromAmount = $this->request->getQuery('fromAmount', '');
        if (!empty($fromAmount)) {$filterData['payment_amount >= '] = $fromAmount;}

        $toAmount = $this->request->getQuery('toAmount', '');
        if (!empty($toAmount)) {$filterData['payment_amount <= '] = $toAmount;}

        $createdFrom = $this->request->getQuery('created_from', '');
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
        if ($mode !== '') {$filterData['payment_mode'] = $mode;}

        $status = $this->request->getQuery('status', '');
        if ($status != '') {$filterData['status'] = $status;}
        $orders = [];
        if ( !empty ($filterData) ) {
            $orders = $this->Orders->find('all', ['fields' => ['id'], 'conditions' => $filterData])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            })->hydrate(0)->toArray();
            $orders = array_column($orders, 'id');
        }
        $this->Shipvendor->syncStatus($orders);
        $this->redirect($this->referer());
    }

    public function pushVendors()
    {
        $now = Time::now();
        $now->timezone = 'Asia/Kolkata';
        $createdTo = $now->format('Y-m-d H:m:s') . ' | ';
        $createdFrom = $now->modify('- 10 days')->format('Y-m-d H:m:s');
        $orders = TableRegistry::get('Orders')->find('all', ['fields' => ['id', 'tracking_code'], 'conditions' => ['status' => 'accepted'], 'order' => ['id' => 'DESC']])
            ->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            })
            ->hydrate(false)->toArray();
        //pr($orders);
        foreach ($orders as $value) {
            if (empty($value['tracking_code'])) {
                //echo $value['id'];
                $this->Shipvendor->pushOrderByAdmin($value['id']);
            }
        }
        $this->redirect($this->referer());
    }

}
