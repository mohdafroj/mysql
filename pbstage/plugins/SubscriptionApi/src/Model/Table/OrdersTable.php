<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OrdersTable extends Table
{

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('SubscriptionApi.Locations');
        $this->belongsTo('SubscriptionApi.Customers');
        $this->belongsTo('SubscriptionApi.Couriers');
        $this->belongsTo('SubscriptionApi.PaymentMethods');
        $this->hasMany('SubscriptionApi.OrderDetails');
        $this->hasMany('SubscriptionApi.OrderComments');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('order_mode');

        $validator
            ->numeric('order_amount')
            ->requirePresence('order_amount', 'create')
            ->notEmpty('order_amount');

        $validator
            ->numeric('order_discount')
            ->requirePresence('order_discount', 'create')
            ->notEmpty('order_discount');

        $validator
            ->numeric('order_shipping_amount')
            ->requirePresence('order_shipping_amount', 'create')
            ->notEmpty('order_shipping_amount');

        $validator
            ->numeric('order_mode_amount')
            ->requirePresence('order_mode_amount', 'create')
            ->notEmpty('order_mode_amount');

        $validator
            ->allowEmpty('order_coupon');

        $validator
            ->allowEmpty('tracking_number');

        $validator
            ->allowEmpty('order_email');

        $validator
            ->dateTime('order_date')
            ->requirePresence('order_date', 'create')
            ->notEmpty('order_date');

        $validator
            ->allowEmpty('status');

        $validator
            ->allowEmpty('shipping_firstname');

        $validator
            ->allowEmpty('shipping_lastname');

        $validator
            ->allowEmpty('shipping_address');

        $validator
            ->allowEmpty('shipping_city');

        $validator
            ->allowEmpty('shipping_state');

        $validator
            ->allowEmpty('shipping_country');

        $validator
            ->allowEmpty('shipping_pincode');

        $validator
            ->allowEmpty('shipping_email');

        $validator
            ->allowEmpty('shipping_phone');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['location_id'], 'Locations'));
        $rules->add($rules->existsIn(['courier_id'], 'Couriers'));
        $rules->add($rules->existsIn(['payment_method_id'], 'PaymentMethods'));
        return $rules;
    }

    public function createNew ($item) {
        $orderLog = $item['order_log'] ?? '';
        $order = $this->newEntity();
        $order->customer_id = $item['customer_id'];
        $order->location_id = $item['location_id'] ?? 1;
        $order->courier_id = $item['courier_id'] ?? 100;
        $order->credit_mailer = $item['credit_mailer'] ?? 0;
        $order->payment_method_id = $item['payment_method_id'];
        $order->product_total = $item['product_total'];
        $order->payment_amount = $item['payment_amount'];
        $order->discount = $item['discount'] ?? 0;
        $order->ship_amount = $item['ship_amount'] ?? 0;
        $order->ship_discount = $item['ship_discount'] ?? 0;
        $order->payment_mode = $item['payment_mode'] ?? 'postpaid';
        $order->mode_amount = $item['mode_amount'] ?? 0;
        $order->coupon_code = $item['coupon_code'] ?? '';
        $order->mobile = $item['mobile'];
        $order->email = $item['email'];
        $order->shipping_firstname = $item['shipping_firstname'];
        $order->shipping_lastname = $item['shipping_lastname'];
        $order->shipping_address = $item['shipping_address'];
        $order->shipping_city = $item['shipping_city'];
        $order->shipping_state = $item['shipping_state'];
        $order->shipping_country = $item['shipping_country'] ?? 'India';
        $order->shipping_pincode = $item['shipping_pincode'];
        $order->shipping_email = $item['shipping_email'];
        $order->shipping_phone = $item['shipping_phone'];
        $order->debit_points = $item['debit_points'] ?? 0;
        $order->debit_cash = $item['debit_cash'] ?? 0;
        $order->debit_voucher = $item['debit_voucher'] ?? 0;
        $order->credit_points = $item['credit_points'] ?? 0;
        $order->credit_cash = $item['credit_cash'] ?? 0;
        $order->credit_voucher = $item['credit_voucher'] ?? 0;
        $order->order_log = json_encode($orderLog);
        $order->transaction_ip = $_SERVER['REMOTE_ADDR'];
        return $this->save($order)->toArray();
    }

    public function setEarnedToHold ($orderId, $ids=[]) {
        if ( count($ids) > 0 ) {
            $query = $this->find('all', ['fields' => ['id', 'order_log'], 'conditions' => ['id'=>$orderId]])->hydrate(0)->toArray();
            foreach ($query as $value) {
                $order_log = json_decode($value['order_log'], true);                
                $order_log['earnedToHold'] = $ids;
                $this->query()->update()->set(['order_log'=>json_encode($order_log)])->where(['id'=>$orderId])->execute();
            }
        }
        return 1;
    }
    
    public function getEarnedToHold ($orderId) {
        $ids = [];
        $query = $this->find('all', ['fields' => ['id', 'order_log'], 'conditions' => ['id'=>$orderId]])->hydrate(0)->toArray();
        foreach ($query as $value) {
            $order_log = json_decode($value['order_log'], true);                
            $ids = $order_log['earnedToHold'];
        }
        return $ids;
    }

}
