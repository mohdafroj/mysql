<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoicesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('invoices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.Customers',
        ]);

        $this->belongsTo('Couriers', [
            'foreignKey' => 'courier_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.Couriers',
        ]);

        $this->belongsTo('SubscriptionApi.Locations');
        $this->belongsTo('PaymentMethods', [
            'foreignKey' => 'payment_method_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.PaymentMethods',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.Orders',
        ]);
        $this->hasMany('InvoiceDetails', [
            'foreignKey' => 'invoice_id',
            'className' => 'SubscriptionApi.InvoiceDetails',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('order_id', 'create')
            ->notEmpty('order_id');

        $validator
            ->allowEmpty('payment_mode');

        $validator
            ->numeric('product_total')
            ->requirePresence('product_total', 'create')
            ->notEmpty('product_total');

        $validator
            ->numeric('payment_amount')
            ->requirePresence('payment_amount', 'create')
            ->notEmpty('payment_amount');

        $validator
            ->numeric('discount')
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');

        $validator
            ->allowEmpty('ship_method');

        $validator
            ->numeric('ship_amount')
            ->requirePresence('ship_amount', 'create')
            ->notEmpty('ship_amount');

        $validator
            ->numeric('mode_amount')
            ->requirePresence('mode_amount', 'create')
            ->notEmpty('mode_amount');

        $validator
            ->allowEmpty('coupon_code');

        $validator
            ->allowEmpty('tracking_code');

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
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['location_id'], 'Locations'));
        $rules->add($rules->existsIn(['courier_id'], 'Couriers'));
        $rules->add($rules->existsIn(['payment_method_id'], 'PaymentMethods'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
