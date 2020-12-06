<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * DriftMailerLists Model
 *
 * @property \App\Model\Table\DriftMailersTable|\Cake\ORM\Association\BelongsTo $DriftMailers
 *
 * @method \App\Model\Entity\DriftMailerList get($primaryKey, $options = [])
 * @method \App\Model\Entity\DriftMailerList newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\DriftMailerList[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\DriftMailerList|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\DriftMailerList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\DriftMailerList[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\DriftMailerList findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DriftMailerListsTable extends Table
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

        $this->setTable('drift_mailer_lists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Auditable');
        $this->addBehavior('Timestamp');

        $this->belongsTo('DriftMailers', [
            'foreignKey' => 'drift_mailer_id',
            'joinType' => 'INNER'
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
            ->requirePresence('content', 'create')
            ->notEmpty('content');

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
        $rules->add($rules->existsIn(['drift_mailer_id'], 'DriftMailers'));

        return $rules;
    }
}
