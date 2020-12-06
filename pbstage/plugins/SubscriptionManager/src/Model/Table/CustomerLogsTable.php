<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CustomerLogsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('customer_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'className' => 'SubscriptionManager.Customers',
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'className' => 'SubscriptionManager.Orders',
        ]);
        $this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
            'className' => 'SubscriptionManager.Locations',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->integer('id_referrered_customer')
            ->requirePresence('id_referrered_customer', 'create')
            ->notEmpty('id_referrered_customer');

        $validator
            ->numeric('cash')
            ->requirePresence('cash', 'create')
            ->notEmpty('cash');

        $validator
            ->numeric('points')
            ->requirePresence('points', 'create')
            ->notEmpty('points');

        $validator
            ->numeric('voucher')
            ->allowEmpty('voucher');

        $validator
            ->requirePresence('transaction_type', 'create')
            ->notEmpty('transaction_type');

        $validator
            ->requirePresence('comments', 'create')
            ->notEmpty('comments');

        $validator
            ->requirePresence('transaction_ip', 'create')
            ->notEmpty('transaction_ip');

        $validator
            ->dateTime('transaction_date')
            ->requirePresence('transaction_date', 'create')
            ->notEmpty('transaction_date');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
