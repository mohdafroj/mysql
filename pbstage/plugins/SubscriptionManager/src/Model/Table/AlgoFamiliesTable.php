<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlgoFamilies Model
 *
 * @method \SubscriptionManager\Model\Entity\AlgoFamily get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoFamily findOrCreate($search, callable $callback = null, $options = [])
 */
class AlgoFamiliesTable extends Table
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

        $this->setTable('algo_families');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
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
            ->allowEmpty('title');

        $validator
            ->allowEmpty('image');

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        $validator
            ->integer('fscore')
            ->requirePresence('fscore', 'create')
            ->notEmpty('fscore');

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
