<?php
namespace SubscriptionApi\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class MembershipComponent extends Component
{
    public function getPlanData($customerId, $orderId = 0)
    {
        $price = 9.95; $currency = '$';
        try {
            $product = TableRegistry::get('SubscriptionApi.ProductPrices')->find('all', ['fields' => ['id', 'price'], 'conditions' => ['product_id' => PC['PRIVE_PRODUCT_ID'], 'is_active' => 'active'], 'limit' => 1])->hydrate(0)->toArray();
            $price = $product[0]['price'] ?? $price;
        } catch (\Exception $ex) {}
        $customData = ['free_ship' => 'yes', 'discount_type' => 'percentage', 'discount' => 0, 'points_type' => 'percentage', 'points' => 10];
        return $data = ['customerId' => $customerId, 'orderId' => $orderId, 'validity' => '1 year', 'currency' => $currency, 'price' => $price, 'title' => 'Prive', 'status' => 'active', 'customData' => $customData];
    }

    public function add($data)
    {
        $responseCode = 0;
        $message = 'Please fill required details!';
        $customerId = $data['customerId'] ?? 0;
        if ($customerId > 0) {
            $customData = $data['customData'] ?? null;
            $validity = $data['validity'] ?? '1 year';
            $time = Time::now('Asia/Kolkata');
            $validTo = $time->format('Y-m-d H:i:s');
            $time->modify($validity);
            $validTo = $time->format('Y-m-d H:i:s');

            $dataTable = TableRegistry::get('SubscriptionApi.Memberships');
            $query = $dataTable->newEntity();
            $query->title = $data['title'] ?? '';
            $query->customer_id = $customerId;
            $query->order_id = $data['orderId'] ?? 0;
            $query->price = $data['price'] ?? 0;
            $query->custom_data = json_encode($customData);
            $query->valid = $validTo;
            $query->status = $data['status'] ?? 'inactive';
            if ($dataTable->save($query)) {
                $responseCode = 1;
                $message = 'One record addedd successfully!';
            } else {
                $message = 'Sorry, Some database error!';
            }
        }
        return ['status' => $responseCode, 'message' => $message];
    }

    public function updateStatus($data)
    {
        $responseStatus = 0;
        $whr = [];
        $id = $data['id'] ?? 0;
        $customerId = $data['customerId'] ?? 0;
        $orderId = $data['orderId'] ?? 0;
        $status = $data['status'] ?? 'inactive';

        if ($id > 0) {$whr['id'] = $id;}
        if ($customerId > 0) {$whr['customer_id'] = $customerId;}
        if ($orderId > 0) {$whr['order_id'] = $orderId;}

        if (!empty($whr) && !empty($status)) {
            $dataTable = TableRegistry::get('SubscriptionApi.Memberships');
            $q = $dataTable->query()->update()->set(['status' => $status])->where($whr)->execute();
            if ($q->rowCount()) {
                $responseStatus = 1;
            }
        }
        return $responseStatus;
    }

    //return an array with ['status'=>1/0,'freeShip'=>'yes/no','memPoints'=>decimal,'memDiscount'=>decimal,'msg'=>string]
    public function getMembership($customerId, $products = [])
    {
        $status = $memPoints = $memDiscount = 0;
        $error = $message = '';
        $freeShip = 'no';
        $price = 0;

        $tempTotal = 0;
        foreach ($products as $value) {
            //if( $value['id'] != PC['PRIVE_PRODUCT_ID'] ){
            $tempTotal += $value['price'] * $value['quantity'];
            //}
        }

        $dataTable = TableRegistry::get('SubscriptionApi.Memberships');
        $query = $dataTable->find('all', ['conditions' => ['customer_id' => $customerId, 'status' => 'active'], 'limit' => 1, 'order' => ['id' => 'desc']])
            ->toArray();
        if (empty($query)) {
            $error = 'Sorry, there are no any plan!';
        } else {
            $query = $query[0];
            if (($query->status == 'active')) {
                $status = 1;
                $error = 'Membership is active!';
                $crtDate = new Date();
                $crtDate = strtotime($crtDate);
                $validDate = empty($query->valid) ? 0 : strtotime($query->valid);
                if ($crtDate > $validDate) {
                    $status = 0;
                    $error = 'Your membership is expired!';
                }
                $validity = $query->valid;
            } else {
                $error = 'Sorry, there are no any active plan!!';
            }
        }
        //Final Calculation for discount
        //$status = 0;
        if ($status) {
            $cd = json_decode($query->custom_data, true);
            $freeShip = $cd['free_ship'] ?? 'no';

            $discountType = $cd['discount_type'] ?? null;
            $discount = $cd['discount'] ?? 0;

            $pointsType = $cd['points_type'] ?? null;
            $points = $cd['points'] ?? 0;

            if ($discountType == 'rupees') {
                $memDiscount = $discount;
            } else if ($discountType == 'percentage') {
                $memDiscount = (float) ($tempTotal * $discount) / 100;
            }

            if ($pointsType == 'flat') {
                $memPoints = $points;
            } else if ($pointsType == 'percentage') {
                $memPoints = (float) ($tempTotal * $points) / 100;
            }
            $message = '<span class="font-15 text-cgreen"><b>Privé Member:</b></span>
				You get free ship and 10% point Back!';
        } else {
            //get default membership data
            $memberData = $this->getPlanData($customerId);
            $price = $memberData['price'];
            $validity = $memberData['validity'];
            $freeShip = $memberData['customData']['free_ship'];
            $discountType = $memberData['customData']['discount_type'];
            $discount = $memberData['customData']['discount'];
            $pointsType = $memberData['customData']['points_type'];
            $points = $memberData['customData']['points'];
            if ($discountType == 'percentage') {
                $memDiscount = (float) ($tempTotal * $discount) / 100;
            } else {
                $memDiscount = (float) $discount;
            }
            if ($pointsType == 'percentage') {
                $memPoints = (float) ($tempTotal * $points) / 100;
            } else {
                $memPoints = (float) $points;
            }
            $message = '<span class="font-15 text-cgreen"><b>Privé Menber:</b></span>
				Add ' .$memberData['currency']. $price . ', get Free Ship and 10% Point Back for ' . $validity . '!';
        }
        return ['status' => $status, 'validity' => $validity, 'charge' => $price, 'freeShip' => $freeShip, 'points' => $memPoints, 'discount' => $memDiscount, 'error' => $error, 'message' => $message];
    }

    //return an array with ['memPoints'=>decimal,'memDiscount'=>decimal,'msg'=>string]
    public function walletDataPending($customerId, $orderId)
    {
        $status = $memPoints = $memDiscount = 0;
        $msg = '';

        $dataTable = TableRegistry::get('SubscriptionApi.Memberships');
        $query = $dataTable->find()
            ->select(['id', 'valid', 'custom_data'])
            ->where(['customer_id' => $customerId, 'status' => 'active'])
            ->order(['id' => 'desc'])
            ->limit(1)
            ->toArray();

        if (empty($query)) {
            $msg = 'Sorry, there are no any plan!';
        } else {
            $status = 1;
            $query = $query[0];
            $crtDate = new Date();
            $crtDate = strtotime($crtDate);
            $validDate = empty($query->valid) ? 0 : strtotime($query->valid);
            if ($crtDate > $validDate) {
                $status = 0;
            }
        }
        //Final Calculation for discount
        if ($status) {
            $cd = json_decode($query->custom_data, true);

            $discountType = $cd['discount_type'] ?? null;
            $discount = $cd['discount'] ?? 0;

            $pointsType = $cd['points_type'] ?? null;
            $points = $cd['points'] ?? 0;

            $dataTable = TableRegistry::get('SubscriptionApi.OrderDetails');
            $query = $dataTable->find();
            $query = $query->select(['order_id', 'total_price' => $query->func()->sum('price')])
                ->where(['order_id' => $orderId])
                ->toArray();

            $tempTotal = empty($query[0]->total_price) ? 0 : $query[0]->total_price;
            if ($discountType == 'rupees') {
                $memDiscount = $discount;
            } else if ($discountType == 'percentage') {
                $memDiscount = (float) ($tempTotal * $discount) / 100;
            }

            if ($pointsType == 'flat') {
                $memPoints = $points;
            } else if ($pointsType == 'percentage') {
                $memPoints = (float) ($tempTotal * $points) / 100;
            }
            if (($memDiscount > 0) || ($memPoints > 0)) {
                $transaction_type = 1;
                $id_referrered_customer = 0;
                $cash = $memDiscount;
                $points = $memPoints;
                $voucher = 0;
                $comments = "Wallet credit for placing Order #" . $orderId;
                //$this->Store->logPBWallet($customerId, $transaction_type, $id_referrered_customer, $orderId, $cash, $points, $voucher, $comments);
                $msg = 'Your wallets credited!';
            }
        }
        return $status;
    }

}
