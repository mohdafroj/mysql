<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CartRule Model
 *
 * @method \App\Model\Entity\CartRule get($primaryKey, $options = [])
 * @method \App\Model\Entity\CartRule newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CartRule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CartRule|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CartRule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CartRule[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CartRule findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CartRulesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cart_rules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->hasMany('Coupons', [
            'foreignKey' => 'cart_rule_id',
        ]);
        $this->addBehavior('Timestamp');
        $this->addBehavior('Auditable');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
			->notEmpty('title', 'Please enter rule name!')
			->add('title',[
				'length' => ['rule' => ['lengthBetween', 5, 50], 'message' => 'Rule name should be 3 to 50 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Rule name contains only a-z, 0-1, and space characters only!']
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
				'code' => ['rule' =>['validateUnique'], 'provider'=>'table', 'message' =>'This coupon already exist!']
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
				'maxQty' => ['rule' => ['isInteger'], 'message' => 'Please enter valid number for maximum qty discount is applied to! ']
			])
            ->notEmpty('max_qty');

        $validator
            ->allowEmpty('valid_from')->date('ymd');

        $validator
            ->allowEmpty('valid_to')->date('ymd')
			->add('valid_to', 'validTo', [
				'rule' => function($value, $context){
					return strtotime($value) >= strtotime($context['data']['valid_from']);
				}, 
			'message' => 'Sorry, validity dates should not valid!'
			]);

        $validator
            ->allowEmpty('is_ship');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['coupon']));

        return $rules;
    }
}
