<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CouponsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('coupons');
        $this->belongsTo('SubscriptionApi.CartRules', [
            'foreignKey' => 'cart_rule_id',
        ]);

        $this->addBehavior('Timestamp');
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id');

        $validator
            ->requirePresence('coupon', 'create')
            ->notEmpty('coupon')
            ->add('coupon', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->boolean('used')
            ->requirePresence('used', 'create')
            ->notEmpty('used');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['coupon']));
        $rules->add($rules->existsIn(['cart_rule_id'], 'CartRules'));
        return $rules;
    }
}
