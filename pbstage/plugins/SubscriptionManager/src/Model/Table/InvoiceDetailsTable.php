<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InvoiceDetailsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('invoice_details');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Invoices', [
            'foreignKey' => 'invoice_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Invoices'
        ]);
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
            ->integer('qty')
            ->requirePresence('qty', 'create')
            ->notEmpty('qty');

        $validator
            ->numeric('discount')
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');

        $validator
            ->allowEmpty('goods_tax');

        $validator
            ->numeric('tax_amount')
            ->requirePresence('tax_amount', 'create')
            ->notEmpty('tax_amount');

        $validator
            ->allowEmpty('short_description');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['invoice_id'], 'Invoices'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
