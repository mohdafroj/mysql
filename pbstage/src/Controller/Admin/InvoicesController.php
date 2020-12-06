<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;

class InvoicesController extends AppController
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '0');
    }

    public function index()
    {
		$this->set('queryString', $this->request->getQueryParams());
		
		$download_invoice	= $this->request->getQuery('download_invoice', 0);
		if($download_invoice > 0)
		{ 
			$id_array		= $this->request->getQuery('select', array());
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
		
		$order_number = $this->request->getQuery('order_number', '');
		$this->set('order_number', $order_number);
		if(!empty($order_number)) { $filterData['order_number'] = $order_number; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['email'] = $email; }
		
		$mobile = $this->request->getQuery('mobile', '');
		$this->set('mobile', $mobile);
		if(!empty($mobile)) { $filterData['mobile'] = $mobile; }
		
		$shippingFirstname = $this->request->getQuery('shipping_firstname', '');
		$this->set('shippingFirstname', $shippingFirstname);
		if(!empty($shippingFirstname)) { $filterData['shipping_firstname'] = $shippingFirstname; }

		$courierId = $this->request->getQuery('courierId', '');
		$this->set('courierId', $courierId);
		if( !empty($courierId) && ($courierId > 0) ) {
			$filterData['pickup_id'] = $courierId;
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
		if( $mode !== '' ) { $filterData['payment_mode'] = $mode; }

		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if( $status !== '' ) { $filterData['status'] = $status; }
		
		$zone = $this->request->getQuery('zoneId', '');
		$this->set('zoneId', $zone);
		if( $zone !== '' ) { $filterData['zone'] = $zone; }
		
		$invoices = $this->Invoices->find('all', ['fields'=>['id','order_number','shipping_firstname','email','mobile','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','pickup_id','status'],'conditions'=>$filterData])->order(['id' =>'DESC']);
			if( !empty($createdFrom) && !empty($createdTo) ){
		$invoices = 	$invoices->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					});
			}
		
		$invoices= $this->paginate($invoices);
        $couriers = TableRegistry::get("Couriers")->find('all',['fields'=>['id','title'],'order'=>['title'=>'asc']]);
        $couriers = new Collection($couriers);
        $couriers = $couriers->combine('id','title');
        $couriers = $couriers->toArray();
		
        $zones = TableRegistry::get("ZoneCodes")->find('all',['fields'=>['id','zone_type'],'order'=>['zone_type'=>'asc']]);
        $zones = new Collection($zones);
        $zones = $zones->combine('id','zone_type');
        $zones = $zones->toArray();
		
        $this->set(compact('invoices','couriers', 'zones'));
        $this->set('_serialize', ['invoices','couriers', 'zones']);
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
		if(!empty($email)) { $filterData['email'] = $email; }
		
		$mobile = $this->request->getQuery('mobile', '');
		if(!empty($mobile)) { $filterData['mobile'] = $mobile; }
		
		$shippingFirstname = $this->request->getQuery('shipping_firstname', '');
		if(!empty($shippingFirstname)) { $filterData['shipping_firstname'] = $shippingFirstname; }
		
		$paymentAmount = $this->request->getQuery('payment_amount', '');
		if(!empty($paymentAmount)) { $filterData['payment_amount'] = $paymentAmount; }
		
		$courierId = $this->request->getQuery('courierId', '');
		$this->set('courierId', $courierId);
		if( !empty($courierId) && ($courierId > 0) ) {
			$filterData['pickup_id'] = $courierId;
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
		
		$zone = $this->request->getQuery('zoneId', '');
		if( $zone !== '' ) { $filterData['zone'] = $zone; }
		
		$data = $this->Invoices->find('all', ['fields'=>['Invoices.id','order_number','email','mobile','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','status','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','shipping_email','shipping_phone'],'conditions'=>$filterData, 'order'=>['Invoices.id'=>'DESC']]);
			if( !empty($createdFrom) && !empty($createdTo) ){
		$data = 	$data->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					});
			}
		$data = 	$data->matching('InvoiceDetails', function($q){
					return $q->select(['title','sku_code','size','price','qty','goods_tax','short_description']);
				});
		
		$ref = '_matchingData.InvoiceDetails.';
		$_serialize='data';
    	$_header = ['Invoice ID','Order ID','Email','Mobile','Total Amount','Discount','Payment Mode','Mode Amount','Ship Method','Ship Amount','Coupon Code', 'Tracking Code','Created','Status','SKU Code','Title','Size','Price','Qty','Tax','Description','Shipping Firstname','Shipping Lastname','Shipping Address','Shipping City','Shipping State','Shipping Country','Shipping Pincode','Shipping Email','Shipping Phone'];
    	$_extract = ['id','order_number','email','mobile','payment_amount','discount','payment_mode','mode_amount','ship_method','ship_amount','coupon_code','tracking_code','created','status',$ref.'sku_code',$ref.'title',$ref.'size',$ref.'price',$ref.'qty',$ref.'goods_tax',$ref.'short_description','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','shipping_email','shipping_phone'];
    	$this->set(compact('data', '_serialize', '_header', '_extract'));
    	$this->viewBuilder()->setClassName('CsvView.Csv');
		return;
    }
	
    public function manifests()
    {
		$this->viewBuilder()->setLayout('Pdf/invoice');
		$createdAt =  date('Y-m-d h:s:i A');
		$docmentNumber = time();
		$this->set('createdAt', $createdAt);
		$this->set('docmentNumber', $docmentNumber);
		
		$filterData = [];
		$id = $this->request->getQuery('id', '');
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$email = $this->request->getQuery('email', '');
		if(!empty($email)) { $filterData['email'] = $email; }
		
		$mobile = $this->request->getQuery('mobile', '');
		if(!empty($mobile)) { $filterData['mobile'] = $mobile; }
		
		$shippingFirstname = $this->request->getQuery('shipping_firstname', '');
		if(!empty($shippingFirstname)) { $filterData['shipping_firstname'] = $shippingFirstname; }
		
		$paymentAmount = $this->request->getQuery('payment_amount', '');
		if(!empty($paymentAmount)) { $filterData['payment_amount'] = $paymentAmount; }
		
		$courierId = $this->request->getQuery('courierId', 0);
		
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
		
		$filterData['tracking_code !='] = '';
		$query = TableRegistry::get('Couriers')->find('all', ['fields'=>['id','title','logo'], 'order'=>['Couriers.id'=>'ASC']]);
		if  ($courierId > 0) {
			$query = $query->where(['Couriers.id'=>$courierId]);
		}
		$query = $query->contain([
					'Invoices'=>[
						'queryBuilder'=>function($q) use( $filterData, $createdFrom, $createdTo ){
							$q = $q->select(['id','order_number','tracking_code','payment_mode','pickup_id','payment_amount']);
							if( !empty($filterData) ){
								$q = $q->where($filterData);
							}
							if( !empty($createdFrom) && !empty($createdTo) ){
								$q = $q->where(function ($exp, $q) use($createdFrom,$createdTo) {
									return $exp->between('created', $createdFrom, $createdTo);
								});
							}
							return $q;
						}
				]])
				->hydrate(0)->toArray();
		//$this->set('dataList', $query[0]);
		//$this->render('/Pdf/manifests');

		Configure::write('CakePdf', [
			'engine' 	=> [
				'className' => 'CakePdf.WkHtmlToPdf',
				'options' => [
						'print-media-type' 	=> false,
						'outline' 			=> true,
						'dpi' 				=> 96
					],
				],
			'padding' => [
				'bottom' => 20,
				'top' => 20
			],	
			'download' 	=> true
		]);
		$id = 1;
		$file_string	= '';
		foreach($query as $value)
		{
			if( !empty($value['invoices']) ){
				$invoice_path	= WWW_ROOT . 'Manifests' . DS. 'Couriers' . DS. str_replace(" ","_",$value['title'].'_'.$createdAt).'.pdf';
				$this->viewBuilder()->options([
						'pdfConfig' => [
							'orientation' => 'portrait',
							'filename' => str_replace(" ","_",$value['title'].'_'.$createdAt)
						]
				]);
				$this->set('dataList', $value);
				$CakePdf 	= new \CakePdf\Pdf\CakePdf();
				$CakePdf->template('manifests', 'new_layout');
				$CakePdf->viewVars($this->viewVars);
				$pdf 		= $CakePdf->write($invoice_path);
				$file_string	.= $invoice_path." ";
			}
		}
		$file_string	= trim($file_string);
		$output_file	= WWW_ROOT . 'Manifests' . DS. 'manifests_'.$docmentNumber.'.pdf';
		exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile='.$output_file.' '.$file_string);
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="manifests.pdf"');
		echo file_get_contents($output_file, 1);
		exit;
    }
	
    public function view($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$this->viewBuilder()->setLayout('Pdf/invoice');
        $invoice = $this->Invoices->get($id, [
            'contain' => ['Customers', 'InvoiceDetails']
        ]);
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode   = base64_encode($generator->getBarcode($invoice->tracking_code, $generator::TYPE_CODE_128));
		$this->set('barcode', $barcode);
        $this->set('invoice', $invoice);
        $this->set('_serialize', ['invoice']);
    }

    public function download($id=null, $key=null, $md5=null)
    {
		if( ($key != 'pdf') || ( $md5 != md5($id) ) )
		{
			return $this->redirect(['action' => 'index']);
		}
		
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
		
		$this->viewBuilder()->setLayout('Pdf/invoice');
        $invoice = $this->Invoices->get($id, [
            'contain' => ['Customers', 'InvoiceDetails']
        ]);
		
		$this->viewBuilder()->options([
                'pdfConfig' => [
                    'orientation' => 'portrait',
                    'filename' => 'Invoice_' . $id
                ]
		]);
        $couriers = TableRegistry::get("Couriers")->find('all',['fields'=>['id','title'],'order'=>['title'=>'asc']]);
        $couriers = new Collection($couriers);
        $couriers = $couriers->combine('id','title');
        $couriers = $couriers->toArray(); //pr($couriers);
		$this->set('couriers', $couriers);
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$barcode   = base64_encode($generator->getBarcode($invoice->tracking_code, $generator::TYPE_CODE_128));
		$this->set('barcode', $barcode);
		
        $this->set('invoice', $invoice);
        $this->set('_serialize', ['invoice']);
		
		$CakePdf 	= new \CakePdf\Pdf\CakePdf();
		$CakePdf->template('new_invoice', 'new_layout');
		$CakePdf->viewVars($this->viewVars);
		$pdf 		= $CakePdf->write(WWW_ROOT . 'Invoices' . DS . 'Invoice_'.$id.'.pdf');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Invoice_'.$id.'.pdf"');
		echo $pdf = $CakePdf->output();
		return;
    }

    public function downloadInvoices($id_array)
    {
		
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
		
		$this->viewBuilder()->setLayout('Pdf/invoice');
        $file_string	= '';
        $couriers = TableRegistry::get("Couriers")->find('all',['fields'=>['id','title'],'order'=>['title'=>'asc']]);
        $couriers = new Collection($couriers);
        $couriers = $couriers->combine('id','title');
        $couriers = $couriers->toArray(); //pr($couriers);
		$this->set('couriers', $couriers);
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		sort($id_array);
		foreach($id_array as $id)
		{
			$filename		= 'Invoice_'.$id.'.pdf';
			$invoice_path	= WWW_ROOT . 'Invoices' . DS . $filename;
			if(!file_exists($invoice_path))
			{
				$invoice = $this->Invoices->get($id, [
					'contain' => ['Customers', 'InvoiceDetails']
				]);
				
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
				$CakePdf->template('new_invoice', 'new_layout');
				$CakePdf->viewVars($this->viewVars);
				$pdf 		= $CakePdf->write($invoice_path);
			}
			$file_string	.= $invoice_path." ";
		}
		$file_string	= trim($file_string);
		$output_file	= WWW_ROOT . 'Invoices' . DS . 'download' . DS. 'Invoice_'.time().'.pdf';
		exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile='.$output_file.' '.$file_string);
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="Invoices.pdf"');
		echo file_get_contents($output_file, 1);
		exit;
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $invoice = $this->Invoices->get($id);
        if ($this->Invoices->delete($invoice)) {
            $this->Flash->success(__('The invoice has been deleted.'));
        } else {
            $this->Flash->error(__('The invoice could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


}
