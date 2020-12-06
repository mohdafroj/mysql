<?php
namespace App\Controller\Api;

use Cake\ORM\TableRegistry;
use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Filesystem\File;
use Cake\View\View;
use Cake\View\ViewBuilder;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\Datasource\ConnectionManager;
class CronsController extends AppController
{
    use MailerAwareTrait;
    private $skus = ['PB00000051','PB00000052','PB00000053','PB00000054','PB00000055','PB00000056','PB00000057','PB00000058','PB00000059','PB00000060','PB00000061','PB00000062'];    
	public function initialize()
    {
        parent::initialize();
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->loadComponent('Delhivery');
		$this->loadComponent('Sms');
		$this->loadComponent('Store');
		$this->loadComponent('Membership');
		$this->loadComponent('Customer');
		$this->loadComponent('Facebook');
    }

    public function index(){
        $a = [];
        $method = $this->request->getQuery('method');
        $id = $this->request->getQuery('sku');
        switch( $method ){
            case 1: $a = $this->Facebook->addAllItem(); break;
            case 2: $a = $this->Facebook->addItem($id); break;
            case 3: $a = $this->Facebook->removeItem($id); break;
            default:
        }
        pr($a);
        die;
    }
    
    //update facebook catalogue
    public function facebookCatalogue(){
        $this->Facebook->addAllItem();
        die;
    }
    
    public function notifyMe(){
        $notifyMeTable = TableRegistry::get('NotifyMe');
        $productsTable = TableRegistry::get('Products');
        $conn = ConnectionManager::get('default');
        $query = $conn->execute('SELECT `id`, `email`, GROUP_CONCAT(DISTINCT CONCAT(`product_id`)) AS `product_id` FROM `notify_me` WHERE `status` = "1" GROUP by `email`')->fetchAll('assoc');
        if(!empty($query)){
            foreach( $query as $value ){
                if( !empty($value['product_id']) ){
                    //echo json_encode(explode(',',$value['product_id']));
                    $productsList = $productsTable->find('all', ['fields'=>['id','title','qty','description'=>'short_description', 'url'=>'url_key'],'conditions'=>['Products.id IN'=>explode(',',$value['product_id']),'Products.is_stock'=>'in_stock','Products.is_active'=>'active']])
                    ->contain([
                        'ProductsImages'=>[
                            'queryBuilder'=>function($q){
                                return $q->select(['product_id','url'=>'img_large'])->where(['is_large'=>'1']);
                            }
                        ]
                    ])
                    ->hydrate(false)
                    ->toArray();
                    $productIds =  array_column($productsList, 'id'); //echo json_encode($productsList);
                    if( !empty($productIds) ){
                        $data = ['subject'=>'Hurry…your Wait List item is now available','template'=>'notify_me','customer'=>['email'=>$value['email']], 'products'=>$productsList];
                        $this->getMailer('Customer')->send('abendedCart', [$data]);
                        $notifyMeTable->query()->update()->set(['status'=>'0'])->where(['email'=>$value['email']])->where(['product_id IN'=>$productIds])->execute();
                    }
                }
            }
        }
        die;
    }
    
    //shoot mailer in every 30 days
    public function reminderForThirtyDays(){
        $dataTable = TableRegistry::get('Orders'); //,'16351','16948'
        $fromDate = date("Y-m-d", strtotime('-31 days') ).' 00:00:01';  
        $crtDate = date("Y-m-d", strtotime('-30 days') ).' 23:59:59';  
        $filter = ['Orders.status'=>'delivered'];
        $query = $dataTable->find('all', ['conditions'=>$filter,'limit'=>10000])
                ->hydrate(false);
        $query = $query->select(['Orders.id','Orders.email', 'name'=>$query->func()->concat(['shipping_firstname'=>'identifier',' ','shipping_lastname'=>'identifier'])])
                ->contain('OrderDetails', function($q){
                    return $q->autoFields(true)->select(['sku_code','qty']);
                })
                ->where(function ($exp, $q) use($fromDate, $crtDate) {
                    return $exp->between('Orders.modified', $fromDate, $crtDate);
                })
                ->toArray();
        if( count($query) > 0 ){
            foreach($query as $value){
                $data = [];
                $cart = $this->Store->getItems($value['order_details']);
                $data = ['customer'=>['id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email']],'cart'=>$cart];
                //check for scent shot exist or not
                if( count(array_intersect(array_column($value['order_details'],'sku_code'), $this->skus)) ){
                    $data['subject']  = 'Hey …….…… It’s time for a refill!';
                    $data['template'] = 'remainder_scentshot_30_days';
                }else{
                    $data['subject']  = 'Don’t worry, we have got your back!';
                    $data['template'] = 'remainder_non_scentshot_30_days';
                }
                $this->getMailer('Customer')->send('abendedCart', [$data]);
            }
        }
        die; 
    }

    //shoot mailer in every 45 days
    public function reminderForFourtyFiveDays(){
        $dataTable = TableRegistry::get('Orders'); //,'16351','16948'
        $fromDate = date("Y-m-d", strtotime('-46 days') ).' 00:00:01';  
        $crtDate = date("Y-m-d", strtotime('-45 days') ).' 23:59:59';  
        $filter = ['Orders.status'=>'delivered'];
        $query = $dataTable->find('all', ['conditions'=>$filter,'limit'=>10000])
                ->hydrate(false);
        $query = $query->select(['Orders.id','Orders.email', 'name'=>$query->func()->concat(['shipping_firstname'=>'identifier',' ','shipping_lastname'=>'identifier'])])
                ->contain('OrderDetails', function($q){
                    return $q->autoFields(true)->select(['sku_code','qty']);
                })
                ->where(function ($exp, $q) use($fromDate, $crtDate) {
                    return $exp->between('Orders.modified', $fromDate, $crtDate);
                })
                ->toArray();
        if( count($query) > 0 ){
            foreach($query as $value){
                $data = [];
                $cart = $this->Store->getItems($value['order_details']);
                $data = ['customer'=>['id'=>$value['id'],'name'=>$value['name'],'email'=>$value['name']],'cart'=>$cart];
                //check for scent shot exist or not
                if( count(array_intersect(array_column($value['order_details'],'sku_code'), $this->skus)) ){
                    $data['subject']  = 'You are going love this!';
                    $data['template'] = 'remainder_scentshot_45_days';
                }else{
                    $data['subject']  = 'Shhh… Don’t tell your friends about this…';
                    $data['template'] = 'remainder_non_scentshot_45_days';
                }
                $this->getMailer('Customer')->send('abendedCart', [$data]);
            }
        }
        die; 
    }


    //shoot mailer in every 2 hours
    public function abendedCartForTwoHours(){
        $dataTable = TableRegistry::get('Customers');
        $fromDate = date("Y-m-d h:m:s", strtotime('-50 days') );  
        $crtDate = date("Y-m-d h:m:s", strtotime('-2 hours') );
        $filter = ['track_page IN'=>['login','cart','checkout'], 'is_active'=>'active'];
        $query = $dataTable->find('all', ['fields'=>['id'],'conditions'=>$filter, 'limit'=>10000])
                ->where(function ($exp, $q) use($fromDate, $crtDate) {
                    return $exp->between('Customers.logdate', $fromDate, $crtDate);
                })
                ->toArray();
        if( count($query) > 0 ){
            foreach($query as $value){
                $cart = $this->Store->getAbendedCart($value->id);
                if( count($cart['cart']) ){
                    $customer   = $dataTable->get($value->id)->toArray();
                    $prive = $this->Membership->getMembership($value->id);
                    if($prive['status']){
                        $data = ['customer'=>$customer,'cart'=>$cart,'subject'=>'Get’em while they last!','template'=>'abended_cart_for_prive_member_in_2_hours'];
                    }else{
                        $data = ['customer'=>$customer,'cart'=>$cart,'subject'=>'Your friends will be jealous!','template'=>'abended_cart_for_non_prive_member_in_2_hours'];
                    }
                    $this->getMailer('Customer')->send('abendedCart', [$data]);
                    $this->Customer->trackPage($value->id, 'two-hours');
                }
            }
        }
        die;
    }

    //shoot mailer in every 7 Days
    public function abendedCartForSevenDays(){
        $fromDate = date("Y-m-d h:m:s", strtotime('-10 days') );  
        $crtDate = date("Y-m-d h:m:s", strtotime('-7 days') );
        $dataTable = TableRegistry::get('Customers');
        $query = $dataTable->find('all', ['fields'=>['id'],'conditions'=>['track_page'=>'two-days', 'is_active'=>'active'], 'limit'=>10000])
                    ->where(function ($exp, $q) use($fromDate, $crtDate) {
                        return $exp->between('Customers.logdate', $fromDate, $crtDate);
                    })
                    ->toArray();
        if( count($query) > 0 ){
            foreach($query as $value){
                $cart = $this->Store->getAbendedCart($value->id);
                if( count($cart['cart']) ){
                    $customer   = $dataTable->get($value->id)->toArray();
                    $data = ['customer'=>$customer,'cart'=>$cart,'subject'=>'You will not believe this…','template'=>'abended_cart_for_7_days'];
                    $this->getMailer('Customer')->send('abendedCart', [$data]);
                    $this->Customer->trackPage($value->id, 'seven-days');
                }
            }
        }
        die;
    }

    //shoot mailer in every 14 Days
    public function abendedCartForFourteenDays(){
        $fromDate = date("Y-m-d h:m:s", strtotime('-20 days') );  
        $crtDate = date("Y-m-d h:m:s", strtotime('-14 days') );
        $ordersTable = TableRegistry::get('Orders');
        $orders = $ordersTable->find('all', ['conditions'=>['status'=>'delivered']]);
        $orders = $orders->select(['customer_id', 'total'=>$orders->func()->count('*')])
                ->hydrate(false)
                ->group(['customer_id'])
                ->having(['total'=>1])
                ->where(function ($exp, $q) use($fromDate, $crtDate) {
                    return $exp->between('modified', $fromDate, $crtDate);
                })
                ->toArray();        
        if( count($orders) > 0 ){    
            foreach($orders as $value){            
                $cart = $this->Store->getAbendedCart($value['customer_id']);
                if( count($cart) ){
                    $customer   = $dataTable->get($value['customer_id'])->toArray();
                    $data = ['customer'=>$customer,'cart'=>$cart,'subject'=>'Valid for 24 hours. Only for you.','template'=>'abended_cart_for_14_days'];
                    $this->getMailer('Customer')->send('abendedCart', [$data]);
                    $this->Customer->trackPage($value['customer_id'], '14-days');
                }
            }
        }    
        die;
    }

    public function productStock()
    {
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            //'modified'=>'0000-00-00 00:00:00',
            'status'=>'pending'
        ];
        $dataTable = TableRegistry::get('Orders');
        $productsTable = TableRegistry::get('Products');
        $query = $dataTable->find('all',['contain'=>['OrderDetails'],'fields'=>['id'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('created', $createdFrom, $createdTo);
                  })
                 ->toArray();
        if( !empty($query) ){
            foreach($query as $value){
                foreach($value->order_details as $v){
                    //echo "$v->id | $v->product_id | $v->sku_code | $v->qty | ";
                    $product = $productsTable->get($v->product_id);
                    $remainQty = $product->qty + $v->qty;
                    if( $remainQty > $product->out_stock_qty){
                        $product->is_stock = 'in_stock';
                    }
                    $product->qty = $remainQty;
                    $productsTable->save($product);
                }
            }
        }
        //pr($query);
        die;
    }

    public function productOutOfStock()
    {   //echo APP.'Files'.DS.'products'.DS; die;
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            'is_stock'=>'out_of_stock'
        ];
        $dataTable = TableRegistry::get('Products');
        $data = $dataTable->find('all',['fields'=>['id','name','title','sku_code','qty','is_stock','is_active','modified'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('modified', $createdFrom, $createdTo);
                  })
                 ->toArray();                 
        //pr($query);
        if(!empty($data)){
            $this->response->withDownload('exports.csv');
            $_serialize='data';
            $_header = ['ID', 'SKU Code', 'Title', 'name', 'Quantity', 'Stock', 'Stock', 'Modified'];
            $_extract = ['id', 'sku_code', 'title', 'name', 'qty', 'is_stock', 'is_active', 'modified'];
            
            // Create the builder
            $builder = new ViewBuilder;
            $builder->layout = false;
            $builder->setClassName('CsvView.Csv');
    
            // Then the view
            $view = $builder->build($data);
            $view->set(compact('data', '_serialize', '_header', '_extract'));
    
            // And Save the file
            $file = new File(APP.'Files'.DS.'products'.DS.'OutOfStockProduct.csv', true, 0777);
            $file->write($view->render());

            $email = new Email('Sendgrid');
            $email->from(['connect@perfumebooth.com'=>'Web Manager'])
                  ->to('customerservice@perfumebooth.com','Stock Manager')
                  ->cc(['mohd.afroj@perfumebooth.com','rohit@perfumebooth.com'])
                  ->subject('List of out of stock products')
                  ->attachments(APP.'Files'.DS.'products'.DS.'OutOfStockProduct.csv')
                  ->send('PFA out of stock products from last 24 hours!');
        }
        die;
    }

    public function test(){
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            'is_stock'=>'out_of_stock'
        ];
        $dataTable = TableRegistry::get('Products');
        $data = $dataTable->find('all',['fields'=>['id','name','title','sku_code','qty','is_stock','is_active','modified'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('modified', $createdFrom, $createdTo);
                  })
                 ->toArray();                 
        pr($data);
        //$this->Sms->otpSend(7838799646, $otp=12345, $amount=1);
        die;
    }

    public function updatePackingSlip($waybill)
	{
		$id_order			= '';		
        $packingSlip				= $this->Delhivery->getPackingSlip($waybill);
        //pr($packingSlip);
        //die;
        $orderTable 				= TableRegistry::get('Orders');
		$orderData		= $orderTable->find('all', ['conditions' => ['tracking_code' => $waybill]])->toArray();
		if(!empty($orderData) && isset($orderData[0]))
		{
			$id_order						= $orderData[0]->id;
            $order						    = $orderTable->get($id_order);		
            $order->packing_slip		    = serialize($packingSlip);
            $orderTable->save($order);
        }
        
		$invoiceTable		= TableRegistry::get('Invoices');
		$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number'=>$id_order]])->toArray();
		if(!empty($invoiceData) && isset($invoiceData[0]))
		{
			$id_invoice						= $invoiceData[0]->id;
			$invoice						= $invoiceTable->get($id_invoice);
			$invoice->tracking_code			= $waybill;
			$invoice->packing_slip			= serialize($packingSlip);
			$invoiceTable->save($invoice);
        }

        die;
    }
    
    
    
}
