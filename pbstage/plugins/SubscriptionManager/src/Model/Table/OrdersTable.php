<?php
namespace SubscriptionManager\Model\Table;

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

        $this->belongsTo('SubscriptionManager.Locations');
        $this->belongsTo('SubscriptionManager.Customers');
        $this->belongsTo('SubscriptionManager.Couriers');
        $this->belongsTo('SubscriptionManager.PaymentMethods');
        $this->hasMany('SubscriptionManager.OrderDetails', [
            'foreignKey' => 'order_id',
        ]);
        $this->hasMany('SubscriptionManager.OrderComments', [
            'foreignKey' => 'order_id',
        ]);
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
            ->numeric('order_tax')
            ->requirePresence('order_tax', 'create')
            ->notEmpty('order_tax');

        $validator
            ->allowEmpty('order_coupon');

        $validator
            ->allowEmpty('order_tracking_number');

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

        $validator
            ->allowEmpty('billing_firstname');

        $validator
            ->allowEmpty('billing_lastname');

        $validator
            ->allowEmpty('billing_address');

        $validator
            ->allowEmpty('billing_city');

        $validator
            ->allowEmpty('billing_state');

        $validator
            ->allowEmpty('billing_country');

        $validator
            ->allowEmpty('billing_pincode');

        $validator
            ->allowEmpty('billing_email');

        $validator
            ->allowEmpty('billing_phone');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }
}
