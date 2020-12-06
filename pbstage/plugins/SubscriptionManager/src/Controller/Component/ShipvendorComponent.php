<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

class ShipvendorComponent extends Component
{
    use MailerAwareTrait;
    public function getActiveVendor()
    {
        $shipvendor = [];
        try {
            $shipvendor = TableRegistry::get('SubscriptionManager.Shipvendors')->find('all', ['fields' => ['id', 'title'], 'conditions' => ['set_default' => 1]])->hydrate(false)->toArray();
            $shipvendor = $shipvendor[0] ?? [];
        } catch (\Exception $e) {}
        return $shipvendor;
    }

    public function checkPincode($pincode)
    {
        $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
        $response = $this->Shiproket->checkPincode($pincode);
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
            switch ($vendorId) {
                case 1: //for delhivery only
                    $status = $this->Delhivery->sendOrderNew($orderId);
                    break;
                case 2: // For both delhivery and shiprokets
                    $order = TableRegistry::get('SubscriptionManager.Orders')->get($orderId);
                    if (strtolower($order->shipping_state) == 'delhi') {
                        $status = $this->Delhivery->sendOrderNew($orderId);
                    } else {
                        /*
                        $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                        if( $response['status'] ){
                        $courierId = 0;
                        if( $order->payment_mode == 'prepaid' ){
                        $courierId= $response['data'][0]['id'] ?? 0; //fedex FR
                        }else{
                        $courierId = $response['data'][0]['id'] ?? 0; //bludart/others
                        if( $courierId == 41 ){ //ignore fedex fr
                        $courierId = $response['data'][1]['id'] ?? $response['data'][0]['id'];
                        }
                        }
                        if( $courierId > 0 ){
                        $status  = $this->Shiproket->sendOrder($orderId, $courierId);
                        }
                        }
                         */
                        $status = 1;
                    }
                    break;
                case 3: // for shiprokets only
                    /*
                    $order       = TableRegistry::get('SubscriptionManager.Orders')->get($orderId);
                    $response  = $this->Shiproket->checkPincode($order->shipping_pincode);
                    //pr($response);die;
                    if( $response['status'] ){
                    $courierId = 0;
                    if( $order->payment_mode == 'prepaid' ){
                    $courierId= $response['data'][0]['id'] ?? 0; //fedex
                    }else{
                    $courierId = $response['data'][0]['id'] ?? 0; //bludart/others
                    if( $courierId == 41 ){ //ignore fedex fr
                    $courierId = $response['data'][1]['id'] ?? $response['data'][0]['id'];
                    }
                    }
                    //echo $courierId.'hiii';die;
                    if( $courierId > 0 ){
                    $status  = $this->Shiproket->sendOrder($orderId, $courierId);
                    }
                    }
                     */
                    $status = 1;
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
            switch ($vendorId) {
                case 1: //for delhivery only
                    $status = $this->Delhivery->sendOrderNew($orderId);
                    break;
                case 2: // For both delhivery and shiprokets
                    $order = TableRegistry::get('SubscriptionManager.Orders')->get($orderId);
                    if (strtolower($order->shipping_state) == 'delhi') {
                        $status = $this->Delhivery->sendOrderNew($orderId);
                    } else {
                        $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                        if ($response['status']) {
                            $courierId = 0;
                            if ($order->payment_mode == 'prepaid') {
                                $courierId = $response['data'][0]['id'] ?? 0; //fedex FR
                            } else {
                                $courierId = $response['data'][0]['id'] ?? 0; //bludart/others
                                if ($courierId == 41) { //ignore fedex fr
                                    $courierId = $response['data'][1]['id'] ?? $response['data'][0]['id'];
                                }
                            }
                            if ($courierId > 0) {
                                $status = $this->Shiproket->sendOrderByAdmin($orderId, $courierId);
                            }
                        }
                    }
                    break;
                case 3: // for shiprokets only
                    $order = TableRegistry::get('SubscriptionManager.Orders')->get($orderId);
                    $response = $this->Shiproket->checkPincode($order->shipping_pincode);
                    //pr($response);die;
                    if ($response['status']) {
                        $courierId = 0;
                        if ($order->payment_mode == 'prepaid') {
                            $courierId = $response['data'][0]['id'] ?? 0; //fedex
                        } else {
                            $courierId = $response['data'][0]['id'] ?? 0; //bludart/others
                            if ($courierId == 41) { //ignore fedex fr
                                $courierId = $response['data'][1]['id'] ?? $response['data'][0]['id'];
                            }
                        }
                        //echo $courierId.'hiii';die;
                        if ($courierId > 0) {
                            $status = $this->Shiproket->sendOrderByAdmin($orderId, $courierId);
                        }
                    }
                    break;
                default:
            }
        }
        return $status;
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
        $orderTable = TableRegistry::get('SubscriptionManager.Orders');
        $invoiceTable = TableRegistry::get('SubscriptionManager.Invoices');
        $order = $orderTable->get($orderId);
        if (in_array($order->status, ['accepted', 'proccessing'])) {
            $order->status = $setStatus;
            $orderTable->save($order);
            $invoiceData = $invoiceTable->find('all', ['fields' => ['id'], 'conditions' => ['order_id' => $orderId]])->toArray();
            if (isset($invoiceData[0])) {
                $invoice = $invoiceTable->get($invoiceData[0]->id);
                $invoice->status = $setStatus;
                $invoiceTable->save($invoice);
            }
            //Email to customer for order cancelled
            $this->Customer = new CustomerComponent(new ComponentRegistry());
            $oDetails = $this->Customer->getOrdersDetails($order->customer_id, $orderId);
            if (!empty($oDetails)) {
                $oDetails['customerId'] = $order->customer_id;
                $this->getMailer('SubscriptionManager.Customer')->send('orderCancelled', [$oDetails]); //orderCancelled
            }
            $status = 1;
            //Order cancelled request to venders
            $vendor = $this->getActiveVendor();
            $vendorId = $vendor['id'] ?? 0;

            $this->Delhivery = new DelhiveryComponent(new ComponentRegistry());
            $this->Shiproket = new ShiproketComponent(new ComponentRegistry());

            switch ($vendorId) {
                case 1: //check for delhivery end;
                    $this->Delhivery->cancelOrderNew($orderId); //return 1/0;
                    break;
                case 2: // check at delhivery and shiprocket
                    $res = $this->Delhivery->cancelOrderNew($orderId); //return 1/0;
                    if ($res == 0) {
                        $this->Shiproket->cancelOrder($orderId); //return 1/0;
                    }
                    break;
                case 3: // check at shiprocket end;
                    $this->Shiproket->cancelOrder($orderId); //return 1/0;
                    break;
                default:
            }
        }
        return $status;
    }

    public function syncStatus()
    {
        $this->Shiproket = new ShiproketComponent(new ComponentRegistry());
        $this->Shiproket->syncStatus();
    }

}
