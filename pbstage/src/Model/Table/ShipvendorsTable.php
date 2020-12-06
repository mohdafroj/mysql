<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Shipvendors Model
 *
 * @property \App\Model\Table\ShipvendorPincodesTable|\Cake\ORM\Association\HasMany $ShipvendorPincodes
 *
 * @method \App\Model\Entity\Shipvendor get($primaryKey, $options = [])
 * @method \App\Model\Entity\Shipvendor newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Shipvendor[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Shipvendor|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Shipvendor patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Shipvendor[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Shipvendor findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ShipvendorsTable extends Table
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

        $this->setTable('shipvendors');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Auditable');
        $this->hasMany('ShipvendorPincodes', [
            'foreignKey' => 'shipvendor_id'
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
            ->allowEmpty('title');

        $validator
            ->boolean('set_default')
            ->requirePresence('set_default', 'create')
            ->notEmpty('set_default');

        return $validator;
    }
}
