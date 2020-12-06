<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CartRulesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cart_rules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->hasMany('SubscriptionApi.Coupons', [
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title', 'Please enter rule name!')
            ->add('title', [
                'length' => ['rule' => ['lengthBetween', 5, 50], 'message' => 'Rule name should be 3 to 50 character long!'],
                'charNum' => ['rule' => ['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Rule name contains only a-z, 0-1, and space characters only!'],
            ]);

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('discount_type');

        $validator
            ->allowEmpty('coupon')
            ->add('coupon', [
                'length' => ['rule' => ['lengthBetween', 8, 20], 'message' => 'Coupon should be 8 to 20 character long!'],
                //'charNum' => ['rule' =>['custom', '/^[a-z0-9]*$/i'], 'message' => 'Coupon contains only a-z, 0-1 characters only!'],
                'code' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => 'This coupon already exist!'],
            ]);

        $validator
            ->numeric('discount_amount')
            ->requirePresence('discount_amount', 'create')
            ->add('discount_amount', [
                'discountAmount' => ['rule' => ['decimal'], 'message' => 'Discount amount should be decimal or number!'],
            ])
            ->notEmpty('discount_amount');

        $validator
            ->integer('max_qty')
            ->requirePresence('max_qty', 'create')
            ->add('max_qty', [
                'maxQty' => ['rule' => ['isInteger'], 'message' => 'Please enter valid number for maximum qty discount is applied to! '],
            ])
            ->notEmpty('max_qty');

        $validator
            ->allowEmpty('valid_from')->date('ymd');

        $validator
            ->allowEmpty('valid_to')->date('ymd')
            ->add('valid_to', 'validTo', [
                'rule' => function ($value, $context) {
                    return strtotime($value) >= strtotime($context['data']['valid_from']);
                },
                'message' => 'Sorry, validity dates should not valid!',
            ]);

        $validator
            ->allowEmpty('is_ship');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['coupon']));

        return $rules;
    }
}
