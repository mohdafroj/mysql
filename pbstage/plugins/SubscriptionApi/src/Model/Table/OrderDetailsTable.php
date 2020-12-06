<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OrderDetailsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('order_details');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->belongsTo('SubscriptionApi.Orders');
        $this->belongsTo('SubscriptionApi.Products');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('sku_code');

        $validator
            ->allowEmpty('size');

        $validator
            ->decimal('price')
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->integer('quantity')
            ->notEmpty('quantity');

        $validator
            ->numeric('discount')
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');

        $validator
            ->allowEmpty('short_description');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['order_id'], 'Orders'));
        $rules->add($rules->existsIn(['product_id'], 'Products'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
