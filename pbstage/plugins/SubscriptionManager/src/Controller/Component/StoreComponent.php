<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\Exception;
use Cake\Network\Exception\NotFoundException;
use Cake\I18n\Date;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use SubscriptionManager\Controller\Component\Customer;
use SubscriptionManager\Controller\Component\Delhivery;

class StoreComponent extends Component
{
	use MailerAwareTrait;
	public function updateStockAfterOrderPlaced($details)
	{
		$productsTable = TableRegistry::get('SubscriptionManager.Products');		
        foreach( $details as $value ) {
            if ( is_int($value['product_id']) && ( $value['product_id'] > 0 ) ) {
                $product		= $productsTable->get($value['product_id']);
                $remainQty		= $product->quantity - $value['quantity'];			
                if( $remainQty <= $product->out_stock_qty){
                    $product->is_stock = 'out_of_stock';
                }
                $product->quantity = $remainQty;
                $product->best_seller = $product->best_seller + 1;
                $productsTable->save($product);
            }
		}
		return true;
	}
	
	public function updateWalletAfterPayment($orderId)
	{
		$orderTable = TableRegistry::get('SubscriptionManager.Orders');
		$order		= $orderTable->get($orderId);
		if($order)
		{
			$customer_id			= $order->customer_id;
			$transaction_type		= 0; //debit type transaction
			$id_referrered_customer	= 0;
			$cash				    = $order->debit_cash;
			$points				    = $order->debit_points;
			$voucher			    = $order->debit_voucher;
			$comments				= "Wallet deducted for placing Order #".$orderId;
			$this->logPBWallet($customer_id, $transaction_type, $id_referrered_customer, $orderId, $cash, $points, $voucher, $comments);
		}
	}
	
    public function updateWalletAfterDelivery($orderId)
    {
        $orderTable = TableRegistry::get('SubscriptionManager.Orders');
        $order = $orderTable->get($orderId);
        if ($order && $order->is_points_credited == 0) {
            $order->is_points_credited = 1;
            $orderTable->save($order);
            $transaction_type = 1;
            $id_referrered = 0;
            $cash = $order->credit_cash;
            $points = $order->credit_points;
            $voucher = $order->credit_voucher;
            $comments = "Wallet credited after placing Order #" . $orderId;
            $this->logPBWallet($order->customer_id, $transaction_type, $id_referrered, $orderId, $cash, $points, $voucher, $comments);

            $customerTable = TableRegistry::get('SubscriptionManager.Customers');
            $customer = $customerTable->get($order->customer_id);
            if ($customer) {
                $id_referrer = $customer->id_referrer;
                if ( ( $id_referrer > 0 ) && ( $customer->referral_status == 0 ) ) {
                    $customer->referral_status = 1; // Set referral status to done
                    $customerTable->save($customer);
                    $cash     = 200.00; //($order->product_total * 5) / 100;
                    $points   = 0; //($order->product_total * 10) / 100;
                    $voucher  = 0;
                    $comments = "Wallet credited for reffering a customer who placed successfull Order #" . $orderId;
                    $this->logPBWallet($id_referrer, $transaction_type, $order->customer_id, $orderId, $cash, $points, $voucher, $comments);
                    $userData = ['id'=>$customer->id, 'email'=>$customer->email, 'name'=>$customer->firstname.' '.$customer->lastname, 'mobile'=>$customer->mobile, 'cash'=>$cash, 'toName'=>$order->shipping_firstname.' '.$order->shipping_lastname];
                    $this->getMailer('SubscriptionManager.Customer')->send('orderReferCredit', [$userData]);
                    $this->Sms = new SmsComponent(new ComponentRegistry());
                    $this->Sms->orderReferralTo($customer->mobile, $cash, $order->shipping_firstname.' '.$order->shipping_lastname);
                }
            }
        }
	}
	
	public function logPBWallet($customer_id, $transaction_type, $id_referrered_customer = 0, $order_id = 0, $cash = 0, $points = 0, $voucher = 0, $comments = '')
	{
		if($cash > 0 || $points > 0 || $voucher > 0)
		{
			if($customer_id > 0)
			{
				$customerTable 				= TableRegistry::get('SubscriptionManager.Customers');
				$customer					= $customerTable->get($customer_id);
				if($transaction_type == 1) {
					$customer->voucher		= $customer->voucher + $voucher;
					$customer->points		= $customer->points + $points;
					$customer->cash			= $customer->cash + $cash;
					$customerTable->save($customer);
				} else if ( $transaction_type == 0 )
				{
					$customer->voucher		= $customer->voucher - $voucher;
					$customer->points		= $customer->points - $points;
					$customer->cash			= $customer->cash - $cash;
					$customerTable->save($customer);
				}
				
				$customerLogTable	= TableRegistry::get('SubscriptionManager.CustomerLogs');
				$wallet_log 	= $customerLogTable->newEntity();
				$wallet_log->customer_id			= $customer_id;
				$wallet_log->id_referrered_customer	= $id_referrered_customer;
				$wallet_log->order_id				= $order_id;
				$wallet_log->cash					= $cash;
				$wallet_log->points					= $points;
				$wallet_log->voucher				= $voucher;
				$wallet_log->transaction_type		= $transaction_type;
				$wallet_log->comments				= $comments;
				$wallet_log->transaction_ip			= $_SERVER['REMOTE_ADDR'];
				$customerLogTable->save($wallet_log);
			}
		}
	}
	
	
	public function createInvoice($orderId)
	{
		try{
			$invoiceTable	= TableRegistry::get('SubscriptionManager.Invoices');
			$invoiceOrder	= $invoiceTable->find('all', ['fields'=>['id'], 'conditions'=>['order_id'=>$orderId], 'limit'=>1])->toArray();
			
			if( empty($invoiceOrder) ){
				$orderTable 		  = TableRegistry::get('SubscriptionManager.Orders');
				$order				  = $orderTable->get($orderId, ['contain'=>['OrderDetails']]);
				$invoice 			  = $invoiceTable->newEntity();
                $invoice->customer_id = $order->customer_id;
                $invoice->location_id = $order->location_id;
                $invoice->payment_method_id = $order->payment_method_id;
                $invoice->order_id = $orderId;
                $invoice->payment_mode = $order->payment_mode;
                $invoice->product_total = $order->product_total;
                $invoice->payment_amount = $order->payment_amount;
                $invoice->discount = $order->discount;
                $invoice->ship_method = $order->ship_method;
                $invoice->ship_amount = $order->ship_amount;
                $invoice->mode_amount = $order->mode_amount;
                $invoice->coupon_code = $order->coupon_code;
                $invoice->tracking_code = $order->tracking_code;
                $invoice->created = $order->created;
                $invoice->status = $order->status;
                $invoice->shipping_firstname = $order->shipping_firstname;
                $invoice->shipping_lastname = $order->shipping_lastname;
                $invoice->shipping_address = $order->shipping_address;
                $invoice->shipping_city = $order->shipping_city;
                $invoice->shipping_state = $order->shipping_state;
                $invoice->shipping_country = $order->shipping_country;
                $invoice->shipping_pincode = $order->shipping_pincode;
                $invoice->shipping_email = $order->shipping_email;
                $invoice->shipping_phone = $order->shipping_phone;
                $invoice->transaction_ip = $order->transaction_ip;
                $invoice->courier_id = $order->courier_id;				
                //$test = $invoiceTable->save($invoice); pr($invoice); die;
				if ( $invoiceTable->save($invoice) && ($invoice->id > 0) ) {
					$invoiceDetailsTable		= TableRegistry::get('SubscriptionManager.InvoiceDetails');
					foreach($order['order_details'] as $row)
					{
						$invoice_detail 					= $invoiceDetailsTable->newEntity();
						$invoice_detail->invoice_id			= $invoice->id;
						$invoice_detail->title				= $row['title'];
						$invoice_detail->sku_code			= $row['sku_code'];
						$invoice_detail->size				= $row['size'];
						$invoice_detail->price				= $row['price'];
						$invoice_detail->quantity			= $row['quantity']; //PC-103
						$invoice_detail->short_description	= $row['short_description'];
						$invoiceDetailsTable->save($invoice_detail);
					}
				}
			}
        }catch(\Exception $e){}
        return true;
	}
	
	public function changeInvoiceStatus($orderId, $status)
	{
		$dataTable 		= TableRegistry::get('SubscriptionManager.Invoices');
		$query 			= $dataTable->find('all', ['conditions' => ['Invoices.order_id' => $orderId]])->toArray();
		foreach($query as $value)
		{
			$invoice 			= $dataTable->get($value->id);
			$invoice->status 	= $status;
			if($dataTable->save($invoice))
			{
				return true;
			}
		}
		return false;
	}

	public function orderStatusEmails($orderId, $mailType) {
        $customerId = 0;
        $oDetails = [];
        $orderTable = TableRegistry::get('SubscriptionManager.Orders');
        $order = $orderTable->get($orderId);
        if (empty($order)) {
            exit;
        } else {
            $customerId = $order->customer_id;
        }//die('Afroj');
        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $this->Sms = new SmsComponent(new ComponentRegistry());
        $oDetails = $this->Customer->getOrdersDetails($customerId, $orderId);

        if (!empty($oDetails)) {
            $oDetails['customerId'] = $customerId;
            $oDetails['currentDate'] = date("d F Y");
            $callMailFun = '';
            switch ( $mailType ) {
                case 'confirmed':
                    $callMailFun = 'orderConfirmed';
                    $this->updateStockAfterOrderPlaced($oDetails['order_details']);
                    $text = '';
                    $total = count($oDetails['order_details']);
                    if ( $total > 1) {
                        $total = $total - 1;
                        $text = $oDetails['order_details'][0]['title'] . " + $total";
                    } else {
                        $text = isset($oDetails['order_details'][0]['title']) ? $oDetails['order_details'][0]['title']:'';
                    }
                    $this->Sms->orderSend($oDetails['shipping_phone'], $orderId, $oDetails['payment_amount'], $oDetails['payment_method'], $text);
                    break;
                case 'delivered':
                    $callMailFun = 'orderDelivered';
                    $this->Sms->orderDelivered($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    if ( ($oDetails['credit_cash'] > 0) || ($oDetails['credit_points'] > 0) || ($oDetails['credit_voucher'] > 0) ) {
                        $credits = [
                            'cash' => $oDetails['credit_cash'],
                            'points' => $oDetails['credit_points'],
                            'voucher' => $oDetails['credit_voucher']
                        ];    
                        $this->Sms->orderAccountCredit($oDetails['shipping_phone'], $credits);
                        $this->getMailer('SubscriptionManager.Customer')->send('orderAccountCredit', [$oDetails]);
                    }
                    break;
                case 'intransit':
                    $callMailFun = 'orderIntransit';
                    $this->Sms->orderIntransit($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    break;
                case 'dispatched':
                    $callMailFun = 'orderDispatched';
                    $this->Sms->orderDispatched($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    break;
                case 'cancelled':
                    $callMailFun = 'orderCancelled';
                    $this->Sms->orderCancellled($oDetails['shipping_phone'], $orderId);
                    break;
                case 'review':
                    $callMailFun = 'orderReview';
                    break;
                default: 
            }
            if ( !empty($callMailFun) ) {
                $this->getMailer('SubscriptionManager.Customer')->send($callMailFun, [$oDetails]);
            }
        }
        return $oDetails;
    }


}