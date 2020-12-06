<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Couriers Model
 *
 * @method \App\Model\Entity\Courier get($primaryKey, $options = [])
 * @method \App\Model\Entity\Courier newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Courier[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Courier|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Courier patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Courier[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Courier findOrCreate($search, callable $callback = null, $options = [])
 */
class CouriersTable extends Table
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

        $this->setTable('couriers');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->addBehavior('Auditable');
        $this->hasMany('Orders', [
            'foreignKey' => 'delhivery_pickup_id',
        ]);
        $this->hasMany('Invoices', [
            'foreignKey' => 'pickup_id',
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
            ->allowEmpty('logo');

        $validator
            ->allowEmpty('code');

        return $validator;
    }
}
