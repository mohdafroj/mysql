<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CustomerWalletsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('customer_wallets');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'className' => 'SubscriptionManager.Customers',
        ]);
        $this->belongsTo('Locations', [
            'foreignKey' => 'customer_id',
            'className' => 'SubscriptionManager.Locations',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->numeric('customer_id')
            ->notEmpty('customer_id');

        $validator
            ->numeric('location_id')
            ->notEmpty('location_id');

        $validator
            ->numeric('vouchers')
            ->allowEmpty('vouchers');

        $validator
            ->numeric('cash')
            ->requirePresence('cash', 'create')
            ->notEmpty('cash');

        $validator
            ->numeric('points')
            ->requirePresence('points', 'create')
            ->notEmpty('points');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['location_id'], 'Locations'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
