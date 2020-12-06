<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OrderCommentsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('order_comments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Orders',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('given_by', 'create')
            ->notEmpty('given_by');

        $validator
            ->allowEmpty('status');

        $validator
            ->allowEmpty('comment');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
