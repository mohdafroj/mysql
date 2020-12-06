<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Plans Model
 *
 * @method \SubscriptionManager\Model\Entity\Plan get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\Plan findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PlansTable extends Table
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

        $this->setTable('plans');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->integer('duration')
            ->requirePresence('duration', 'create')
            ->notEmpty('duration');

        $validator
            ->numeric('amount')
            ->requirePresence('amount', 'create')
            ->notEmpty('amount');

        $validator
            ->allowEmpty('currency');

        $validator
            ->allowEmpty('description');

        return $validator;
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
