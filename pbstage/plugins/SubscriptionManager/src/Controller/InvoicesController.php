<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\I18n\Date;

class InvoicesController extends AppController
{
	public function initialize()
    {
		parent::initialize();
        $this->loadComponent('SubscriptionManager.Delhivery');
		Configure::write('CakePdf', [
			'engine' 	=> [
				'className' => 'CakePdf.WkHtmlToPdf',
				'options' => [
						'print-media-type' 	=> false,
						'outline' 			=> true,
						'dpi' 				=> 96
					],
				],
			'download' 	=> true
		]);
    }

    public function index()
    {
		$this->set('queryString', $this->request->getQueryParams());
		$download_invoice	= $this->request->getQuery('download_invoice', 0);
		if($download_invoice > 0)
		{ 
			$id_array		= $this->request->getQuery('select', []);
			if(count($id_array) > 0)
			{
				$this->downloadInvoices($id_array);
				exit;
			}
		}
		
		$limit = $this->request->getQuery('perPage', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		
		$filterData = [];
		
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$orderId = $this->request->getQuery('order_id', '');
		$this->set('orderId', $orderId);
		if(!empty($orderId)) { $filterData['order_id'] = $orderId; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['Customers.email'] = $email; }
		
		$mobile = $this->request->getQuery('mobile', '');
		$this->set('mobile', $mobile);
		if(!empty($mobile)) { $filterData['Customers.mobile'] = $mobile; }
		
		$shippingFirstname = $this->request->getQuery('shipping_firstname', '');
		$this->set('shippingFirstname', $shippingFirstname);
		if(!empty($shippingFirstname)) { $filterData['shipping_firstname'] = $shippingFirstname; }

		$courierId = $this->request->getQuery('courierId', 0);
		$this->set('courierId', $courierId);
		if( $courierId > 0) {
			$filterData['courier_id'] = $courierId;
		}
		
        $locationId = $this->request->getQuery('locationId', 0);
        $this->set('locationId', $locationId);
        if ($locationId > 0) {
            $filterData['location_id'] = $locationId;
        }
		
		$paymentAmount = $this->request->getQuery('payment_amount', '');
		$this->set('paymentAmount', $paymentAmount);
		if(!empty($paymentAmount)) { $filterData['payment_amount'] = $paymentAmount; }
		
		$createdFrom = $this->request->getQuery('created_from', '');
		$this->set('createdFrom', $createdFrom);		
		$createdTo = $this->request->getQuery('created_to', '');
		$this->set('createdTo', $createdTo);
		
		if(!empty($createdFrom) && !empty($createdTo))
		{
			$date = new Date($createdFrom.' 00:00:01');
			$createdFrom = $date->format('Y-m-d');
			$date = new Date($createdTo.' 23:59:59 +1 day');
			$createdTo = $date->format('Y-m-d');
		}else if(!empty($createdFrom)){
			$date = new Date($createdFrom);
			$createdFrom = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$createdFrom%";
		}else if(!empty($createdTo)){
			$date = new Date($createdTo);
			$createdTo = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$createdTo%";
		}
		$mode = $this->request->getQuery('payment_mode', '');
		$this->set('mode', $mode);
		if( $mode != '' ) { $filterData['payment_mode'] = $mode; }

		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if( $status != '' ) { $filterData['status'] = $status; }
		//Start of Codes for order status update by delhivery
		$invoices = $this->Invoices->find('all', ['fields'=>['tracking_code'],'conditions'=>$filterData]);
		if( !empty($createdFrom) && !empty($createdTo) ){
			$invoices = $invoices->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					});
		}
		$query = $this->paginate($invoices)->toArray();
        $trackCodes = array_column($query,'tracking_code'); 
        $trackCodes = implode(',',$trackCodes);
        //$this->Delhivery->trackOrders($trackCodes);
        //End of Codes for order status update by delhivery
		$invoices = $this->Invoices->find('all', ['fields'=>['Invoices.id','order_id','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','courier_id','shipping_firstname','status'],'conditions'=>$filterData])->order(['Invoices.id' =>'DESC'])
					->contain([
						'Customers'=>[ 
							'queryBuilder'=>function($q){
								return $q->select(['id','email','mobile']);
							}
						],
						'Locations'=>[ 
							'queryBuilder'=>function($q){
								return $q->select(['id', 'title', 'code','currency', 'currency_logo']);
							}
						],
						'Couriers'=>[ 
							'queryBuilder'=>function($q){
								return $q->select(['id', 'title']);
							}
						]
					]);
		//pr($invoices->hydrate(0)->toArray()); die;
		if( !empty($createdFrom) && !empty($createdTo) ){
			$invoices = 	$invoices->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('Invoices.created', $createdFrom, $createdTo);
					});
		}
		$invoices= $this->paginate($invoices);
		$couriers = TableRegistry::get('SubscriptionManager.Couriers')->find('all',['id','title'])->hydrate(false)->toArray();
		$couriers = array_combine(array_column($couriers,'id'),array_column($couriers,'title'));
		$locations = TableRegistry::get('SubscriptionManager.Locations')->find('all', ['fields'=>['id', 'title'],'order'=>['title'=>'ASC']])->hydrate(false)->toArray();
		$locations = array_combine(array_column($locations, 'id'), array_column($locations, 'title'));

        $this->set(compact('invoices','couriers','locations'));
        $this->set('_serialize', ['invoices','couriers','locations']);
    }

    public function exports()
    {
		$this->response->withDownload('exports.csv');

	    	$limit = $this->request->getQuery('perPage', 50);
		$offset = $this->request->getQuery('page', 1);
		$offset = ($offset - 1)*$limit;
		
		$filterData = [];
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['Customers.email'] = $email; }
		
		$mobile = $this->request->getQuery('mobile', '');
		$this->set('mobile', $mobile);
		if(!empty($mobile)) { $filterData['Customers.mobile'] = $mobile; }
		
		$shippingFirstname = $this->request->getQuery('shipping_firstname', '');
		if(!empty($shippingFirstname)) { $filterData['shipping_firstname'] = $shippingFirstname; }
		
		$paymentAmount = $this->request->getQuery('payment_amount', '');
		if(!empty($paymentAmount)) { $filterData['payment_amount'] = $paymentAmount; }
		
		$courierId = $this->request->getQuery('courierId', 0);
		$this->set('courierId', $courierId);
		if( $courierId > 0) {
			$filterData['courier_id'] = $courierId;
		}
		
        $locationId = $this->request->getQuery('locationId', 0);
        if ($locationId > 0) {
            $filterData['location_id'] = $locationId;
        }
		$createdFrom = $this->request->getQuery('created_from', '');
		$createdTo = $this->request->getQuery('created_to', '');
		
		if(!empty($createdFrom) && !empty($createdTo))
		{
			$date = new Date($createdFrom.' 00:00:01');
			$createdFrom = $date->format('Y-m-d');
			$date = new Date($createdTo.' 23:59:59 +1 day');
			$createdTo = $date->format('Y-m-d');
		}else if(!empty($createdFrom)){
			$date = new Date($createdFrom);
			$createdFrom = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$createdFrom%";
		}else if(!empty($createdTo)){
			$date = new Date($createdTo);
			$createdTo = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$createdTo%";
		}
		$mode = $this->request->getQuery('payment_mode', '');
		if( $mode !== '' ) { $filterData['payment_mode'] = $mode; }

		$status = $this->request->getQuery('status', '');
		if( $status !== '' ) { $filterData['status'] = $status; }
		
		$data = $this->Invoices->find('all', ['fields'=>['Invoices.id','order_id','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','status','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','shipping_email','shipping_phone'],'conditions'=>$filterData, 'order'=>['Invoices.id'=>'DESC']]);
			if( !empty($createdFrom) && !empty($createdTo) ){
		$data = 	$data->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('Invoices.created', $createdFrom, $createdTo);
					});
			}
		$data = $data->contain([
					'Customers'=>[
						'queryBuilder'=>function($q) use ($email, $mobile){
							return $q->select(['email','mobile']);
						}
					],
					'Locations'=>[ 
						'queryBuilder'=>function($q){
							return $q->select(['id', 'title', 'code','currency', 'currency_logo']);
						}
					],
					'Couriers'=>[ 
						'queryBuilder'=>function($q){
							return $q->select(['id', 'title']);
						}
					],
					'InvoiceDetails'=>[
						'queryBuilder'=>function($q){
							return $q->select(['invoice_id', 'sku_code', 'title','size', 'price', 'quantity']);
						}
					]
				])
				->hydrate(false)->toArray();
		
		$dataList = [];
		$i = 0;
		foreach($data as $value){
			$variants = $value['invoice_details'];
			//pr($variants);
			array_splice($value,32,1);//remove order_details
			if( !empty($variants) ){
				$dataList[$i] = $value;
				$emptyRow = false;
				foreach($variants as $variant){
                    $dataList[$i]['country'] = $value['location']['title'] ?? '';
					$dataList[$i]['email'] = $value['customer']['email'] ?? '';
					$dataList[$i]['mobile'] = $value['customer']['mobile'] ?? '';
					if( $emptyRow ){
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
					$dataList[$i]['order_id'] = $value['order_id'];
					$dataList[$i]['payment_amount'] = $value['payment_amount'];
					$dataList[$i]['tracking_code'] = $value['tracking_code'];
					$dataList[$i]['shipping_state'] = $value['shipping_state'];
					$dataList[$i]['shipping_pincode'] = $value['shipping_pincode'];
					$dataList[$i]['status'] = $value['status'];

					$dataList[$i]['title'] = $variant['title'];
					$dataList[$i]['sku_code'] = $variant['sku_code'];
					$dataList[$i]['size'] = $variant['size'];
					$dataList[$i]['price'] = $variant['price'];
					$dataList[$i]['quantity'] = $variant['quantity'];
					$emptyRow = true;
					$i++;
				}
			}
		}
		$_serialize='dataList';
    	$_header = ['Invoice ID','Order ID','Country','Email','Mobile','Total Amount','Discount','Payment Mode','Mode Amount','Ship Method','Ship Amount','Coupon Code', 'Tracking Code','Created','Status','SKU Code','Title','Size','Price','Qty','Shipping Firstname','Shipping Lastname','Shipping Address','Shipping City','Shipping State','Shipping Country','Shipping Pincode','Shipping Email','Shipping Phone'];
    	$_extract = ['id','order_id','country','email','mobile','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','status','sku_code','title','size','price','quantity','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','shipping_email','shipping_phone'];
    	$this->set(compact('dataList', '_serialize', '_header', '_extract'));
    	$this->viewBuilder()->setClassName('CsvView.Csv');
		return;
    }
	
    public function view($id=0, $key=null, $md5=null)
    {	//echo APP . 'Files' . DS;
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}		
		$this->viewBuilder()->setLayout('SubscriptionManager.Pdf/invoice');
        $invoice = $this->Invoices->get($id, ['contain'=>['Customers','Locations','Couriers','InvoiceDetails']]);
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode   = base64_encode($generator->getBarcode($invoice->tracking_code, $generator::TYPE_CODE_128));
		$this->set(compact('barcode', 'invoice'));
        $this->set('_serialize', ['invoice','barcode']);
    }

    public function download($id=null, $key=null, $md5=null)
    {
		if( ($key != 'pdf') || ( $md5 != md5($id) ) )
		{
			return $this->redirect(['action' => 'index']);
		}
				
		//$this->viewBuilder()->setLayout('SubscriptionManager.Pdf/default');
        $invoice = $this->Invoices->get($id, [
            'contain' => ['Customers','Locations', 'Couriers', 'InvoiceDetails']
        ]);
		
		$this->viewBuilder()->options([
                'pdfConfig' => [
                    'orientation' => 'portrait',
                    'filename' => 'Invoice_' . $id
                ]
		]);
		
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode   = base64_encode($generator->getBarcode($invoice->tracking_code, $generator::TYPE_CODE_128));
		$this->set('barcode', $barcode);
		
        $this->set('invoice', $invoice);
        $this->set('_serialize', ['invoice']);
		
		$CakePdf 	= new \CakePdf\Pdf\CakePdf();
		$CakePdf->template('SubscriptionManager.subscription_invoice', 'SubscriptionManager.invoice');
		$CakePdf->viewVars($this->viewVars);
		//$pdf 		= $CakePdf->write(ROOT. DS.'plugins'. DS .'SubscriptionManager'. DS .'webroot'.DS.'server' . DS . 'Invoice_'.$id.'.pdf');
		$pdf 		= $CakePdf->write(ROOT. DS .'webroot'.DS.'perfumersclub' . DS . 'invoices' . DS . 'Invoice_'.$id.'.pdf');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Invoice_'.$id.'.pdf"');
		echo $pdf = $CakePdf->output();
		return;
    }

    public function downloadInvoices($id_array)
    {
		
        $file_string	= '';
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		sort($id_array);
		foreach($id_array as $id)
		{
			$filename		= 'Invoice_'.$id.'.pdf';
			$invoice_path	= WWW_ROOT . 'perfumersclub' . DS. 'invoices' . DS . $filename;
			if(!file_exists($invoice_path))
			{
				$invoice = $this->Invoices->get($id, ['contain'=>['Customers','Locations','Couriers','InvoiceDetails']]);
				$this->viewBuilder()->options([
						'pdfConfig' => [
							'orientation' => 'portrait',
							'filename' => 'Invoice_' . $id
						]
				]);
				$barcode   = base64_encode($generator->getBarcode($invoice->tracking_code, $generator::TYPE_CODE_128));
				$this->set('barcode', $barcode);
				$this->set('invoice', $invoice);
				$this->set('_serialize', ['invoice']);
				
				$CakePdf 	= new \CakePdf\Pdf\CakePdf();
				$CakePdf->template('SubscriptionManager.invoice', 'SubscriptionManager.invoice');
				$CakePdf->viewVars($this->viewVars);
				$pdf 		= $CakePdf->write($invoice_path);
			}
			$file_string	.= $invoice_path." ";
		}
		$file_string	= trim($file_string);
		//$output_file	= ROOT. DS.'plugins'. DS .'SubscriptionManager'. DS .'webroot'.DS.'server' . DS . 'download' . DS. 'Invoice_'.time().'.pdf';
		$output_file	= ROOT. DS.'webroot'.DS.'perfumersclub' . DS . 'invoices' . DS. 'download' . DS. 'Invoice_'.time().'.pdf';
		exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile='.$output_file.' '.$file_string);
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Invoices.pdf"');
		echo file_get_contents($output_file, 1);
		exit;
    }

    public function delete($id = null)
    {   //8960426041 Aslam Mansuri
        $this->request->allowMethod(['post', 'delete']);
        $invoice = $this->Invoices->get($id);
        if ($this->Invoices->delete($invoice)) {
			$this->Flash->success(__('The invoice has been deleted.'), ['key' => 'adminSuccess']);
        } else {
			$this->Flash->error(__('The invoice could not be deleted. Please, try again.'), ['key' => 'adminError']);
        }
        return $this->redirect(['action' => 'index']);
    }

}
