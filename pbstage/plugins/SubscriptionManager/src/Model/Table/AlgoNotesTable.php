<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlgoNotes Model
 *
 * @method \SubscriptionManager\Model\Entity\AlgoNote get($primaryKey, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote newEntity($data = null, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote[] newEntities(array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionManager\Model\Entity\AlgoNote findOrCreate($search, callable $callback = null, $options = [])
 */
class AlgoNotesTable extends Table
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

        $this->setTable('algo_notes');
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
            ->requirePresence('note_name', 'create')
            ->notEmpty('note_name');

        $validator
            ->requirePresence('group_name', 'create')
            ->notEmpty('group_name');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

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

    public function getAllNotes() {
        return $this->find('all', ['fields'=>['id','note_name','group_name', 'status'],'conditions' => ['status'=>'1'],'order'=>['note_name'=>'asc']])->hydrate(0)->toArray();
    }
}
