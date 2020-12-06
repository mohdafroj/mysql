<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CustomerPlans Model
 *
 * @property \SubscriptionManager\Model\Table\CustomersTable|\Cake\ORM\Association\BelongsTo $Customers
 *
 * @method \SubscriptionManager\Model\Entity\CustomerPlan get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\CustomerPlan findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CustomerPlansTable extends Table
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

        $this->setTable('customer_plans');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Customers'
        ]);
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
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->requirePresence('sku', 'create')
            ->notEmpty('sku')
            ->add('sku', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('duration')
            ->requirePresence('duration', 'create')
            ->notEmpty('duration');

        $validator
            ->numeric('price')
            ->requirePresence('price', 'create')
            ->notEmpty('price');

        $validator
            ->allowEmpty('currency');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('image');

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
        $rules->add($rules->isUnique(['sku']));
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
