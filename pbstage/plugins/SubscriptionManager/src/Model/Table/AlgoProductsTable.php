<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlgoProducts Model
 *
 * @method \SubscriptionManager\Model\Entity\AlgoProduct get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoProduct findOrCreate($search, callable $callback = null, $options = [])
 */
class AlgoProductsTable extends Table
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

        $this->setTable('algo_products');
        $this->setDisplayField('id');
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
            ->requirePresence('product_name', 'create')
            ->notEmpty('product_name');

        $validator
            ->allowEmpty('fragrantica_brands');

        $validator
            ->allowEmpty('url');

        $validator
            ->allowEmpty('group_name');

        $validator
            ->allowEmpty('image');

        $validator
            ->requirePresence('main_accords', 'create')
            ->notEmpty('main_accords');

        $validator
            ->requirePresence('user_votes_notes', 'create')
            ->notEmpty('user_votes_notes');

        $validator
            ->requirePresence('perfume_pyramid', 'create')
            ->notEmpty('perfume_pyramid');

        $validator
            ->allowEmpty('main_notes_according_votes');

        $validator
            ->dateTime('created_date')
            ->requirePresence('created_date', 'create')
            ->notEmpty('created_date');

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
