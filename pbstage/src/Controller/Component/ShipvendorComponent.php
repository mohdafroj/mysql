<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

class ShipvendorComponent extends Component
{
    use MailerAwareTrait;
    private $ignorePostpaidCourierId = 41; // ignore fedex for postpaid order
    public function getActiveVendor()
    {
        $shipvendor = [];
        try {
            $shipvendor = TableRegistry::get('Shipvendors')->find('all', ['fields' => ['id', 'title'], 'conditions' => ['set_default' => 1]])->hydrate(false)->toArray();
            $shipvendor = $shipvendor[0] ?? [];
        } catch (\Exception $e) {}
        return $shipvendor;
    }

    public function selectVendor($pincode)
    {
        $query = TableRegistry::get('ShipvendorPincodes')->find('all', ['fields' => ['shipvendor_id'], 'conditions' => ['pincode' => $pincode]])->hydrate(false)->toArray();
        return array_column($query, 'shipvendor_id');            
    }

    public function checkPincode($pincode)
    {
        $vendor = $this->getActiveVendor();
        $vendorId = $vendor['id'] ?? 0;
        switch( $vendorId ) {
            case 1:
                $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
                $response = $this->Delhivery->getPincode($pincode);
                break;
            case 2: 
                $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
                $response = $this->Delhivery->getPincode($pincode);
                $service = $response['data']['service'] ?? '';
                if ( empty($service) ) {
                    $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                    $response = $this->Shiproket->checkPincode($pincode);
                }
                break;
            case 3: 
                $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                $response = $this->Shiproket->checkPincode($pincode);
                break;
            default:    
        }
        return $response;
    }

    public function pushOrder($orderId = 0)
    {
        $response = [];
        $status = 0;
        if ($orderId > 0) {
            $vendor = $this->getActiveVendor();
            $vendorId = $vendor['id'] ?? 0;
            $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
            $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
            $order = TableRegistry::get('Orders')->get($orderId);
            switch ($vendorId) {
                case 1: //for delhivery only
                    $res = $this->Delhivery->getPincode($order->shipping_pincode);
                    if ($res['status'] > 0) {
                        $status = $this->Delhivery->sendOrderNew($orderId);
                    } else {
                        $status = 3; // service not available
                    }
                    break;
                case 2: // For both delhivery and shiprokets
                    $vendorsArr = $this->selectVendor($order->shipping_pincode);
                    if ( in_array(1, $vendorsArr) ) {
                        $res = $this->Delhivery->getPincode($order->shipping_pincode);
                        if ($res['status'] > 0) {
                            $status = $this->Delhivery->sendOrderNew($orderId);
                        } else {
                            $status = 3; // service not available
                        }
                    } 
                    if( $status != 1 ) {
                        $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                        if ($response['status']) {
                            $courierId = $response['data']['couriers'][0]['id'] ?? 0; // may be fedex
                            if ($order->payment_mode == 'prepaid') {
                                $couriers = array_column($response['data']['couriers'], 'id');
                                $defaultPrepaidCourierId = $this->Shiproket->getDefaultPrepaidCourier();
                                if( ( $defaultPrepaidCourierId > 0 ) && in_array($defaultPrepaidCourierId,  $couriers) ) {
                                    $courierId = $defaultPrepaidCourierId;
                                }
                            } else {
                                if ( $courierId == $this->ignorePostpaidCourierId ) { 
                                    $courierId = $response['data']['couriers'][1]['id'] ?? $courierId;
                                }
                            }
                            if ($courierId > 0) {
                                $status = $this->Shiproket->sendOrder($orderId, $courierId);
                            }
                        }
                    }
                    break;
                case 3: // for shiprokets only
                    $order = TableRegistry::get('Orders')->get($orderId);
                    $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                    //pr($response);die;
                    if ($response['status']) {
                        $courierId = $response['data']['couriers'][0]['id'] ?? 0; // may be fedex
                        if ($order->payment_mode == 'prepaid') {
                            $couriers = array_column($response['data']['couriers'], 'id');
                            $defaultPrepaidCourierId = $this->Shiproket->getDefaultPrepaidCourier();
                            if( ( $defaultPrepaidCourierId > 0 ) && in_array($defaultPrepaidCourierId,  $couriers) ) {
                                $courierId = $defaultPrepaidCourierId;
                            }
                        } else {
                            if ( $courierId == $this->ignorePostpaidCourierId ) {
                                $courierId = $response['data']['couriers'][1]['id'] ?? $courierId;
                            }
                        }
                        //echo $courierId.'hiii';die;
                        if ($courierId > 0) {
                            $status = $this->Shiproket->sendOrder($orderId, $courierId);
                        }
                    }
                    break;
                default:
            }
        }
        return $status;
    }

    public function pushOrderByAdmin($orderId = 0)
    {
        $response = [];
        $status = 0;
        if ($orderId > 0) {
            $vendor = $this->getActiveVendor();
            $vendorId = $vendor['id'] ?? 0;
            $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
            $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
            $order = TableRegistry::get('Orders')->get($orderId);
            switch ($vendorId) {
                case 1: //for delhivery only
                    $res = $this->Delhivery->getPincode($order->shipping_pincode);
                    //pr($res); die;
                    if ($res['status'] > 0) {
                        $status = $this->Delhivery->sendOrderByAdmin($orderId);
                    } else {
                        $status = 3; // service not available
                    }
                    break;
                case 2: // For both delhivery and shiprokets
                    $vendorsArr = $this->selectVendor($order->shipping_pincode);
                    if ( in_array(1, $vendorsArr) ) {
                        $res = $this->Delhivery->getPincode($order->shipping_pincode);
                        if ($res['status'] > 0) {
                            $status = $this->Delhivery->sendOrderByAdmin($orderId);
                        } else {
                            $status = 3; // service not available
                        }
                    } 
                    if( $status != 1 ) {
                        $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                        $courierId = $response['data']['couriers'][0]['id'] ?? 0; // may be fedex FR
                        if ($response['status']) {
                            if ($order->payment_mode == 'prepaid') {
                                $couriers = array_column($response['data']['couriers'], 'id');
                                $defaultPrepaidCourierId = $this->Shiproket->getDefaultPrepaidCourier();
                                if( ($defaultPrepaidCourierId > 0) && in_array($defaultPrepaidCourierId,  $couriers) ) {
                                    $courierId = $defaultPrepaidCourierId;
                                }
                            } else {
                                if ( $courierId == $this->ignorePostpaidCourierId ) {
                                    $courierId = $response['data']['couriers'][1]['id'] ?? $courierId;
                                }
                            }
                        }
                        if ($courierId > 0) {
                            $status = $this->Shiproket->sendOrderByAdmin($orderId, $courierId);
                        } else {
                            $status = 3; //for service not available
                        }
                    }
                    break;
                case 3: // for shiprokets only
                    $order = TableRegistry::get('Orders')->get($orderId);
                    $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                    //pr($response);die;
                    $courierId = $response['data']['couriers'][0]['id'] ?? 0; // may be fedex
                    if ($response['status']) {
                        if ($order->payment_mode == 'prepaid') {
                            $couriers = array_column($response['data']['couriers'], 'id');
                            $defaultPrepaidCourierId = $this->Shiproket->getDefaultPrepaidCourier();
                            if( ( $defaultPrepaidCourierId > 0 ) && in_array($defaultPrepaidCourierId,  $couriers) ) {
                                $courierId = $defaultPrepaidCourierId;
                            }
                        } else {
                            if ( $courierId == $this->ignorePostpaidCourierId ) {
                                $courierId = $response['data']['couriers'][1]['id'] ?? $courierId;
                            }
                        }
                        //echo $courierId.'hiii';die;
                    }
                    if ($courierId > 0) {
                        $status = $this->Shiproket->sendOrderByAdmin($orderId, $courierId);
                    } else {
                        $status = 3; //for service not available
                    }
                    break;
                default:
            }
        }
        return $status;
    }

    public function pickupRequest()
    {

    }

    public function checkPickupLocation()
    {
        $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
        //$this->Shiproket->getPickupLocation();
        return true;
    }

    public function trackPackage($awb)
    {
        $response = [];
        $vendor = $this->getActiveVendor();
        $vendorId = $vendor['id'] ?? 0;
        switch ($vendorId) {
            case 1: //check for delhivery end;
                break;
            case 2: // check at delhivery and shiprocket
                break;
            case 3: // check at shiproket end;
                $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                $response = $this->Shiproket->getTrackingDetail($awb);
                break;
            default:
        }
        //{"shipment":{"id":1679659,"awb_code":"290427839","courier_company_id":14,"shipment_id":null,"order_id":6766717,"pickup_date":"2018-12-27 20:00:00","delivered_date":null,"weight":"0.3","packages":1,"current_status":"In Transit-EN-ROUTE","delivered_to":"PANSEMAL","destination":"PANSEMAL","consignee_name":"Shubham","origin":"New Delhi"},"activities":[{"date":"2018-12-27 21:41:00","activity":"Bag scanned at Hub - 003","location":"INDIA ONE - I1H"},{"date":"2018-12-27 21:39:00","activity":"Bag scanned at Hub - 003","location":"INDIA ONE - I1H"},{"date":"2018-12-27 19:44:00","activity":"In-Transit - 003","location":"DELHI - DMX"},{"date":"2018-12-27 19:33:00","activity":"Bag connected from DC - 003","location":"DELHI - DMX"},{"date":"2018-12-27 18:41:00","activity":"Shipment connected to INDORE - INH (Bag No. UK00233895) - 003","location":"DELHI - DMX"},{"date":"2018-12-27 16:58:00","activity":"Shipment in-scan - 002","location":"DELHI - DLM"},{"date":"2018-12-27 11:42:00","activity":"Out for Pickup - 1230","location":"DELHI - DLM"},{"date":"2018-12-27 11:40:00","activity":"Pickup Assigned - 1220","location":"DELHI - DLM"},{"date":"2018-12-27 11:40:00","activity":"Pickup Assigned - 1220","location":"DELHI - DLM"},{"date":"2018-12-27 10:55:00","activity":"Soft data uploaded - 001","location":"DELHI - DLM"}],"link":"https:\/\/app.shiprocket.in\/tracking\/awb\/290427839"}
        return json_encode($response);
    }

    public function getLabels($shipmentIds)
    {
        $response = [];
        $vendor = $this->getActiveVendor();
        $vendorId = $vendor['id'] ?? 0;
        switch ($vendorId) {
            case 1: //check for delhivery end;
                break;
            case 2: // check at delhivery and shiprocket
                $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                $response = $this->Shiproket->getLabels($shipmentIds);
                break;
            case 3: // check at shiproket end;
                $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                $response = $this->Shiproket->getLabels($shipmentIds);
                break;
            default:
        }
        return $response;
    }

    public function cancelOrder($orderId, $setStatus = 'cancelled')
    { //set status cancelled/cancelled_by_customer
        $status = 0;
        $orderTable = TableRegistry::get('Orders');
        $invoiceTable = TableRegistry::get('Invoices');
        $order = $orderTable->get($orderId, ['contain'=>['Customers']]);
        if (in_array($order->status, ['accepted', 'proccessing'])) {
            $isOrderReversed = $order->is_points_reversed;
            $order->status = $setStatus;
            $order->is_points_reversed = 1;
            $orderTable->save($order);
            $invoiceData = $invoiceTable->find('all', ['fields' => ['id'], 'conditions' => ['order_number' => $orderId]])->toArray();
            if (isset($invoiceData[0])) {
                $invoice = $invoiceTable->get($invoiceData[0]->id);
                $invoice->status = $setStatus;
                $invoiceTable->save($invoice);
            }
            //reversed wallets data after order cancelled
            if($isOrderReversed == 0){
                $this->Store = new StoreComponent(new ComponentRegistry());
                //credited wallets
                if($order->pb_cash_amount > 0 || $order->pb_points_amount > 0 || $order->gift_voucher_amount > 0){
                    $transaction_type		= 1;
                    $pb_cash				= $order->pb_cash_amount;
                    $pb_points				= $order->pb_points_amount;
                    $voucher_amount			= $order->gift_voucher_amount;
                    $comments				= "Wallet credited after cancelled Order #".$orderId;
                    $this->Store->logPBWallet($order->customer_id, $transaction_type, 0, $orderId, $pb_cash, $pb_points, $voucher_amount, $comments);
                }
                //debited wallets
                if($order->credit_cash_amount > 0 || $order->credit_points_amount > 0 || $order->credit_gift_amount > 0){
                    $transaction_type		= 0;
                    $pb_cash				= $order->credit_cash_amount;
                    $pb_points				= $order->credit_points_amount;
                    $voucher_amount			= $order->credit_gift_amount;
                    $comments				= "Wallet deducted after cancelled Order #".$orderId;
                    $this->Store->logPBWallet($order->customer_id, $transaction_type, 0, $orderId, $pb_cash, $pb_points, $voucher_amount, $comments);
                }
			}

            //Email to customer for order cancelled
            $this->Customer = new CustomerComponent(new ComponentRegistry());
            $oDetails = $this->Customer->getOrdersDetails($order->customer_id, $orderId);
            if (!empty($oDetails)) {
                $oDetails['customerId'] = $order->customer_id;
                $this->getMailer('Customer')->send('orderCancelled', [$oDetails]);
            }

            // Send SMS for order have been cancelled
            $this->Sms = new SmsComponent(new ComponentRegistry());
            $this->Sms->cancelSend($order->customer->mobile, $orderId);
            $status = 1;

            //Order cancelled request to venders
            if ( $order->delhivery_pickup_id == 3 ) {
                $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
                $this->Delhivery->cancelOrderNew($orderId); //return 1/0;
            } else {
                $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
                $this->Shiproket->cancelOrder($orderId); //return 1/0;
            }
        }
        return $status;
    }

    public function syncStatus($orders=[])
    {
        $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
        $this->Shiproket->syncStatus($orders);
    }

}
