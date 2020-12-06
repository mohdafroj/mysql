<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ReviewMeta Model
 *
 * @property \SubscriptionManager\Model\Table\ReviewsTable|\Cake\ORM\Association\BelongsTo $Reviews
 *
 * @method \SubscriptionManager\Model\Entity\ReviewMetum get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\ReviewMetum findOrCreate($search, callable $callback = null, $options = [])
 */
class ReviewMetaTable extends Table
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

        $this->setTable('review_meta');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Reviews', [
            'foreignKey' => 'review_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Reviews'
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
            ->requirePresence('meta_type', 'create')
            ->notEmpty('meta_type');

        $validator
            ->requirePresence('meta_value', 'create')
            ->notEmpty('meta_value');

        $validator
            ->allowEmpty('flag');

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
        $rules->add($rules->existsIn(['review_id'], 'Reviews'));

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
