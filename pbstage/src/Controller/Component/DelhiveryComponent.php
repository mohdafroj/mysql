<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;

//use Cake\Http\Client;
use Cake\ORM\TableRegistry;

/**
 * Admin component
 */
class DelhiveryComponent extends Component
{
    // Demo Details
    // private $token                 = 'cea5a528401fc29e00b9cceb7cc9cf8e070aab8c';
    // private $api                 = 'https://test.delhivery.com/c/api/';
    // private $api_base_url         = 'https://test.delhivery.com';
    // private $seller_tin         = '7267134092';
    // private $client_name         = 'Perfume Booth';
    // private $pickup_location     = 'Perfume Booth';

    // Live Details

    private $token = 'f4266821e00e204558af7f53478a30a5d3421fb6';
    private $api = 'https://track.delhivery.com/c/api/';
    private $api_base_url = 'https://track.delhivery.com';
    private $seller_tin = '7267134092';
    private $seller_gst_tin = '07AAICP6302R1ZX';
    private $client_name = 'PERFUMEBOOTH';
    private $pickup_location = 'PERFUMEBOOTH';

    public function getPincode($pincode)
    {
        $http = new Client();
        $data = [];
        $status = 0;
        $service = '';
        $message = 'Sorry, service not available at pincode: ' . $pincode;
		$checkPincode = TableRegistry::get('Systems')->checkInvalidPincodes($pincode);
		if ( $checkPincode !== 1 ) {
            $res = $http->get($this->api . 'pin-codes/json/', ['token' => $this->token, 'filter_codes' => $pincode], ['headers' => ['Accept' => 'application/json']]);
            $res = $res->json;
            //pr($res);
            if (isset($res['delivery_codes'][0]['postal_code'])) {
                $res = $res['delivery_codes'][0]['postal_code'];
                switch ($res['state_code']) {
                    case 'KL_test':
                        $service = 'no';
                        break;
                    default:
                        $status = 1;
                        if ($res['pre_paid'] == 'Y' && $res['cash'] == 'Y') {
                            $service = 'both';
                            $message = 'Prepaid and Postpaid both are available!';
                        } else if ($res['pre_paid'] == 'Y') {
                            $message = 'Only Prepaid are available!';
                            $service = 'prepaid';
                        } else if ($res['cash'] == 'Y') {
                            $message = 'Only Postpaid are available!';
                            $service = 'postpaid';
                        }else{
                            $status = 0;
                        }
                }
                $data['district'] = $res['district'];
                $data['pincode'] = $pincode;
                $data['service'] = $service;
            }    
        } else {
			$message = 'Sorry, Service not provided by PerfumeBooth at pincode: '.$pincode;
        }

        return ['message' => $message, 'status' => $status, 'data' => $data];
    }

    public function getPincodeTest($pincode)
    {
        $http = new Client();
        $data = [];
        $status = 0;
        $message = 'Sorry, service not available at pincode: ' . $pincode;
        $res = $http->get($this->api . 'pin-codes/json/', ['token' => $this->token, 'filter_codes' => $pincode], ['headers' => ['Accept' => 'application/json']]);
        $res = $res->json;
        //pr($res['delivery_codes']); die;
        if (isset($res['delivery_codes'][0]['postal_code'])) {
            $res = $res['delivery_codes'][0]['postal_code'];
            switch ($res['state_code']) {
                case 'KL':
                    break;
                default:
                    if ($res['pre_paid'] == 'Y' && $res['cash'] == 'Y') {
                        $status = 1;
                        $message = 'Prepaid and Postpaid both are available!';
                    } else if ($res['pre_paid'] == 'Y') {
                        $status = 2;
                        $message = 'Only Prepaid are available!';
                    } else if ($res['cash'] == 'Y') {
                        $status = 3;
                        $message = 'Only Postpaid are available!';
                    }
            }
            $data['district'] = $res['district'];
            $data['pincode'] = $pincode;
        }
        return ['message' => $message, 'status' => $status, 'data' => $data];
    }

    public function sendOrder($id_order)
    {
        if ($id_order > 0) {
            $orderTable = TableRegistry::get('Orders');
            $order = $orderTable->get($id_order);

            $orderArray = array();
            $orderArray['shipments'] = array();
            $orderArray['shipments'][0] = array();
            $orderArray['pickup_location'] = array();

            # order details #
            $orderArray['shipments'][0]['order'] = $id_order;
            $orderArray['shipments'][0]['order_date'] = date('Y-m-d H:m:s', strtotime($order->created));
            //$orderArray['shipments'][0]['waybill']            = '';
            $orderArray['shipments'][0]['total_amount'] = $order->payment_amount;
            if ($order->payment_mode == 'postpaid') {
                $orderArray['shipments'][0]['cod_amount'] = $order->payment_amount;
                $orderArray['shipments'][0]['payment_mode'] = 'COD';
            } else {
                $orderArray['shipments'][0]['cod_amount'] = 0;
                $orderArray['shipments'][0]['payment_mode'] = 'Pre­paid';
            }

            $invoiceTable = TableRegistry::get('Invoices');
            $invoiceData = $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
            $invoice = $invoiceData[0];
            $orderDetailTable = TableRegistry::get('OrderDetails');
            $orderDetailData = $orderDetailTable->find('all', ['conditions' => ['order_id' => $id_order]])->toArray();
            $products_desc = '';
            $total_quantity = 0;
            foreach ($orderDetailData as $order_detail) {
                // $products_desc    = $products_desc.", ".$order_detail->title;
                $products_desc = $order_detail->title;
                $total_quantity = $total_quantity + $order_detail->qty;
            }
            $products_desc = trim($products_desc, ', ');
            $orderArray['shipments'][0]['products_desc'] = $products_desc;

            # customer details #
            $orderArray['shipments'][0]['client'] = $this->client_name;
            $orderArray['shipments'][0]['name'] = $order->shipping_firstname . " " . $order->shipping_lastname;
            $orderArray['shipments'][0]['add'] = $order->shipping_address;
            $orderArray['shipments'][0]['city'] = $order->shipping_city;
            $orderArray['shipments'][0]['state'] = $order->shipping_state;
            $orderArray['shipments'][0]['country'] = 'India';
            $orderArray['shipments'][0]['pin'] = $order->shipping_pincode;
            $orderArray['shipments'][0]['phone'] = $order->shipping_phone;

            # return details #
            $orderArray['shipments'][0]['return_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['return_add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['shipments'][0]['return_city'] = 'New Delhi';
            $orderArray['shipments'][0]['return_state'] = 'Delhi';
            $orderArray['shipments'][0]['return_country'] = 'India';
            $orderArray['shipments'][0]['return_pin'] = '110015';
            $orderArray['shipments'][0]['return_phone'] = '011-40098888';
            $orderArray['shipments'][0]['supplier'] = 'Perfume Booth Pvt. Ltd.';

            # seller weight and dimensions #
            $orderArray['shipments'][0]['shipment_width'] = '17.4';
            $orderArray['shipments'][0]['shipment_height'] = '8.7';
            $orderArray['shipments'][0]['weight'] = '300';
            $orderArray['shipments'][0]['quantity'] = $total_quantity;

            # seller keys(optional) #
            $orderArray['shipments'][0]['seller_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['seller_inv'] = $invoice->id;
            $orderArray['shipments'][0]['seller_inv_date'] = date('Y-m-d H:m:s', strtotime($invoice->created));
            $orderArray['shipments'][0]['seller_add'] = 'Perfume Booth Pvt. Ltd, 70B/35A, 3rd Floor, Rama Road Industrial Area, New Delhi - 110015';
            $orderArray['shipments'][0]['seller_gst_tin'] = $this->seller_gst_tin;
            $orderArray['shipments'][0]['seller_cst'] = $this->seller_tin;
            $orderArray['shipments'][0]['seller_tin'] = $this->seller_tin;

            # pickup location #
            $orderArray['pickup_location']['name'] = $this->client_name;
            $orderArray['pickup_location']['add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['pickup_location']['city'] = 'New Delhi';
            $orderArray['pickup_location']['country'] = 'India';
            $orderArray['pickup_location']['pin'] = '110015';
            $orderArray['pickup_location']['phone'] = '011-40098888';
            // pr($orderArray);
            $input_string = 'format=json&data=' . json_encode($orderArray);

            $api_url = $this->api_base_url . '/api/cmu/create.json';
            $http = new Client();
            $result = $http->post($api_url, $input_string, ['headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            // pr($response);

            return $response;
        }
    }

    public function sendOrderNew($id_order)
    {
        $status = 0;
        if ($id_order > 0) {
            $orderTable = TableRegistry::get('Orders');
            $order = $orderTable->get($id_order);

            $orderArray = array();
            $orderArray['shipments'] = array();
            $orderArray['shipments'][0] = array();
            $orderArray['pickup_location'] = array();

            # order details #
            $orderArray['shipments'][0]['order'] = $id_order;
            $orderArray['shipments'][0]['order_date'] = date('Y-m-d H:m:s', strtotime($order->created));
            //$orderArray['shipments'][0]['waybill']            = '';
            $orderArray['shipments'][0]['total_amount'] = $order->payment_amount;
            if ($order->payment_mode == 'postpaid') {
                $orderArray['shipments'][0]['cod_amount'] = $order->payment_amount;
                $orderArray['shipments'][0]['payment_mode'] = 'COD';
            } else {
                $orderArray['shipments'][0]['cod_amount'] = 0;
                $orderArray['shipments'][0]['payment_mode'] = 'Pre­paid';
            }

            $invoiceTable = TableRegistry::get('Invoices');
            $invoiceData = $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
            $invoice = $invoiceData[0];
            $orderDetailTable = TableRegistry::get('OrderDetails');
            $orderDetailData = $orderDetailTable->find('all', ['conditions' => ['order_id' => $id_order]])->toArray();
            $products_desc = '';
            $total_quantity = 0;
            foreach ($orderDetailData as $order_detail) {
                // $products_desc    = $products_desc.", ".$order_detail->title;
                $products_desc = $order_detail->title;
                $total_quantity = $total_quantity + $order_detail->qty;
            }
            //$products_desc = trim($products_desc, ', ');
            $products_desc = preg_replace('/[^A-Za-z0-9, \-]/', ' ', $products_desc);
            $products_desc = preg_replace('/\s+/', ' ', $products_desc);
            $orderArray['shipments'][0]['products_desc'] = $products_desc;

            # customer details #
            $orderArray['shipments'][0]['client'] = $this->client_name;
            $orderArray['shipments'][0]['name'] = $order->shipping_firstname . " " . $order->shipping_lastname;
            $orderArray['shipments'][0]['add'] = $order->shipping_address;
            $orderArray['shipments'][0]['city'] = $order->shipping_city;
            $orderArray['shipments'][0]['state'] = $order->shipping_state;
            $orderArray['shipments'][0]['country'] = 'India';
            $orderArray['shipments'][0]['pin'] = $order->shipping_pincode;
            $orderArray['shipments'][0]['phone'] = $order->shipping_phone;

            # return details #
            $orderArray['shipments'][0]['return_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['return_add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['shipments'][0]['return_city'] = 'New Delhi';
            $orderArray['shipments'][0]['return_state'] = 'Delhi';
            $orderArray['shipments'][0]['return_country'] = 'India';
            $orderArray['shipments'][0]['return_pin'] = '110015';
            $orderArray['shipments'][0]['return_phone'] = '011-40098888';
            $orderArray['shipments'][0]['supplier'] = 'Perfume Booth Pvt. Ltd.';

            # seller weight and dimensions #
            $orderArray['shipments'][0]['shipment_width'] = '17.4';
            $orderArray['shipments'][0]['shipment_height'] = '8.7';
            $orderArray['shipments'][0]['weight'] = '300';
            $orderArray['shipments'][0]['quantity'] = $total_quantity;

            # seller keys(optional) #
            $orderArray['shipments'][0]['seller_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['seller_inv'] = $invoice->id;
            $orderArray['shipments'][0]['seller_inv_date'] = date('Y-m-d H:m:s', strtotime($invoice->created));
            $orderArray['shipments'][0]['seller_add'] = 'Perfume Booth Pvt. Ltd, 70B/35A, 3rd Floor, Rama Road Industrial Area, New Delhi - 110015';
            $orderArray['shipments'][0]['seller_gst_tin'] = $this->seller_gst_tin;
            $orderArray['shipments'][0]['seller_cst'] = $this->seller_tin;
            $orderArray['shipments'][0]['seller_tin'] = $this->seller_tin;

            # pickup location #
            $orderArray['pickup_location']['name'] = $this->client_name;
            $orderArray['pickup_location']['add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['pickup_location']['city'] = 'New Delhi';
            $orderArray['pickup_location']['country'] = 'India';
            $orderArray['pickup_location']['pin'] = '110015';
            $orderArray['pickup_location']['phone'] = '011-40098888';
            // pr($orderArray);
            $input_string = 'format=json&data=' . json_encode($orderArray);

            $api_url = $this->api_base_url . '/api/cmu/create.json';
            $http = new Client();
            $result = $http->post($api_url, $input_string, ['headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            if (isset($response['success']) && $response['success']) {
                $waybill = '';
                foreach ($response['packages'] as $package_row) {
                    $getRow = $package_row;
                    if ($getRow['status'] == 'Success') {
                        $waybill = $getRow['waybill'];
                    }
                }
                if (!empty($waybill)) {
                    $status = 1;
                    $packingSlip = $this->getPackingSlip($waybill);
                    $order->tracking_code = $waybill;
                    $order->delhivery_pickup_id = 3; //pickupId
                    $order->delhivery_response = serialize($response);
                    $order->packing_slip = serialize($packingSlip);
                    $orderTable->save($order);

                    $invoiceU = $invoiceTable->get($invoice->id);
                    $invoiceU->tracking_code = $waybill;
                    $invoiceU->pickup_id = 3; //pickupId
                    $invoiceU->delhivery_response = serialize($response);
                    $invoiceU->packing_slip = serialize($packingSlip);
                    $invoiceTable->save($invoiceU);
                }
            }
        }
        return $status;
    }

    public function sendOrderByAdmin($id_order)
    {
        $response = ''; $status = 0;
        if ($id_order > 0) {
            $orderTable = TableRegistry::get('Orders');
            $order = $orderTable->get($id_order);

            $orderArray = array();
            $orderArray['shipments'] = array();
            $orderArray['shipments'][0] = array();
            $orderArray['pickup_location'] = array();

            # order details #
            $orderArray['shipments'][0]['order'] = $id_order;
            $orderArray['shipments'][0]['order_date'] = date('Y-m-d H:m:s', strtotime($order->created));
            //$orderArray['shipments'][0]['waybill']            = '';
            $orderArray['shipments'][0]['total_amount'] = $order->payment_amount;
            if ($order->payment_mode == 'postpaid') {
                $orderArray['shipments'][0]['cod_amount'] = $order->payment_amount;
                $orderArray['shipments'][0]['payment_mode'] = 'COD';
            } else {
                $orderArray['shipments'][0]['cod_amount'] = 0;
                $orderArray['shipments'][0]['payment_mode'] = 'Pre­paid';
            }

            $invoiceTable = TableRegistry::get('Invoices');
            $invoiceData = $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
            $invoice = $invoiceData[0];
            $orderDetailTable = TableRegistry::get('OrderDetails');
            $orderDetailData = $orderDetailTable->find('all', ['conditions' => ['order_id' => $id_order]])->toArray();
            $products_desc = '';
            $total_quantity = 0;
            foreach ($orderDetailData as $order_detail) {
                // $products_desc    = $products_desc.", ".$order_detail->title;
                $products_desc = $order_detail->title;
                $total_quantity = $total_quantity + $order_detail->qty;
            }
            //$products_desc = trim($products_desc, ', ');
            $products_desc = preg_replace('/[^A-Za-z0-9, \-]/', ' ', $products_desc);
            $products_desc = preg_replace('/\s+/', ' ', $products_desc);
            $orderArray['shipments'][0]['products_desc'] = $products_desc;

            # customer details #
            $orderArray['shipments'][0]['client'] = $this->client_name;
            $orderArray['shipments'][0]['name'] = $order->shipping_firstname . " " . $order->shipping_lastname;
            $orderArray['shipments'][0]['add'] = $order->shipping_address;
            $orderArray['shipments'][0]['city'] = $order->shipping_city;
            $orderArray['shipments'][0]['state'] = $order->shipping_state;
            $orderArray['shipments'][0]['country'] = 'India';
            $orderArray['shipments'][0]['pin'] = $order->shipping_pincode;
            $orderArray['shipments'][0]['phone'] = $order->shipping_phone;

            # return details #
            $orderArray['shipments'][0]['return_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['return_add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['shipments'][0]['return_city'] = 'New Delhi';
            $orderArray['shipments'][0]['return_state'] = 'Delhi';
            $orderArray['shipments'][0]['return_country'] = 'India';
            $orderArray['shipments'][0]['return_pin'] = '110015';
            $orderArray['shipments'][0]['return_phone'] = '011-40098888';
            $orderArray['shipments'][0]['supplier'] = 'Perfume Booth Pvt. Ltd.';

            # seller weight and dimensions #
            $orderArray['shipments'][0]['shipment_width'] = '17.4';
            $orderArray['shipments'][0]['shipment_height'] = '8.7';
            $orderArray['shipments'][0]['weight'] = '300';
            $orderArray['shipments'][0]['quantity'] = $total_quantity;

            # seller keys(optional) #
            $orderArray['shipments'][0]['seller_name'] = 'Perfume Booth Pvt. Ltd.';
            $orderArray['shipments'][0]['seller_inv'] = $invoice->id;
            $orderArray['shipments'][0]['seller_inv_date'] = date('Y-m-d H:m:s', strtotime($invoice->created));
            $orderArray['shipments'][0]['seller_add'] = 'Perfume Booth Pvt. Ltd, 70B/35A, 3rd Floor, Rama Road Industrial Area, New Delhi - 110015';
            $orderArray['shipments'][0]['seller_gst_tin'] = $this->seller_gst_tin;
            $orderArray['shipments'][0]['seller_cst'] = $this->seller_tin;
            $orderArray['shipments'][0]['seller_tin'] = $this->seller_tin;

            # pickup location #
            $orderArray['pickup_location']['name'] = $this->client_name;
            $orderArray['pickup_location']['add'] = '70B/35A, 3rd Floor, Rama Road Industrial Area';
            $orderArray['pickup_location']['city'] = 'New Delhi';
            $orderArray['pickup_location']['country'] = 'India';
            $orderArray['pickup_location']['pin'] = '110015';
            $orderArray['pickup_location']['phone'] = '011-40098888';
            // pr($orderArray);
            $input_string = 'format=json&data=' . json_encode($orderArray);

            $api_url = $this->api_base_url . '/api/cmu/create.json';
            $http = new Client();
            $result = $http->post($api_url, $input_string, ['headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            //pr($response);
            if (isset($response['success']) && $response['success']) {
                $waybill = '';
                foreach ($response['packages'] as $package_row) {
                    $getRow = $package_row;
                    if ($getRow['status'] == 'Success') {
                        $waybill = $getRow['waybill'];
                    }
                }
                if (!empty($waybill)) {
                    $status = 1;
                    $packingSlip = $this->getPackingSlip($waybill);
                    $order->tracking_code = $waybill;
                    $order->delhivery_pickup_id = 3; //pickupId
                    $order->delhivery_response = serialize($response);
                    $order->packing_slip = serialize($packingSlip);
                    $orderTable->save($order);

                    $invoiceU = $invoiceTable->get($invoice->id);
                    $invoiceU->tracking_code = $waybill;
                    $invoiceU->pickup_id = 3; //pickupId
                    $invoiceU->delhivery_response = serialize($response);
                    $invoiceU->packing_slip = serialize($packingSlip);
                    $invoiceTable->save($invoiceU);
                }
            }
        }
        return ['status'=>$status,'response'=>$response];
    }

    public function getPackingSlip($waybill)
    {
        if ($waybill != '') {
            $api_url = $this->api_base_url . '/api/p/packing_slip/';
            $http = new Client();
            $result = $http->get($api_url, ['wbns' => $waybill], ['headers' => ['Accept' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            return $response;
        }
        return array();
    }

    public function getPackingSlipTest($waybill)
    {
        if ($waybill != '') {
            $api_url = $this->api_base_url . '/api/p/packing_slip/';
            $http = new Client();
            $result = $http->get($api_url, ['wbns' => $waybill], ['headers' => ['Accept' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            return $response;
        }
        return array();
    }

    public function sendPickupRequest($total_package, $date, $time)
    {
        $inputArray = array();
        $inputArray['pickup_location'] = $this->pickup_location;
        $inputArray['pickup_time'] = $time;
        $inputArray['pickup_date'] = $date;
        $inputArray['expected_package_count'] = $total_package;

        $api_url = $this->api_base_url . '/fm/request/new/';
        $http = new Client();
        $result = $http->post($api_url, json_encode($inputArray), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
        $response = $result->json;
        return $response;
    }

    public function cancelOrder($waybill)
    {
        if ($waybill != '') {
            $inputArray = array();
            $inputArray['waybill'] = $waybill;
            $inputArray['cancellation'] = 'true';

            $api_url = $this->api_base_url . '/api/p/edit';
            $http = new Client();
            $result = $http->post($api_url, json_encode($inputArray), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            return $response;
        }
        return array();
    }
    public function cancelOrderNew($orderId)
    {
        $status = 0;
        $orderTable = TableRegistry::get('Orders');
        $order = $orderTable->get($orderId);
        if (!empty($order->tracking_code)) {
            $inputArray = [
                'waybill' => $order->tracking_code,
                'cancellation' => 'true',
            ];
            $api_url = $this->api_base_url . '/api/p/edit';
            $http = new Client();
            $result = $http->post($api_url, json_encode($inputArray), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token ' . $this->token]]);
            $response = $result->json;
            if (isset($response['status']) && ($response['status'] == 1)) {
                $status = 1;
                $order->cancel_response = serialize($response);
                $orderTable->save($order);
            }
        }
        return $status;
    }
}
