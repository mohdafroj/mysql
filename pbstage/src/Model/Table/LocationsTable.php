<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Locations Model
 *
 * @property \App\Model\Table\LocationsTable|\Cake\ORM\Association\BelongsTo $ParentLocations
 * @property \App\Model\Table\CustomersTable|\Cake\ORM\Association\HasMany $Customers
 * @property \App\Model\Table\LocationsTable|\Cake\ORM\Association\HasMany $ChildLocations
 *
 * @method \App\Model\Entity\Location get($primaryKey, $options = [])
 * @method \App\Model\Entity\Location newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Location[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Location|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Location patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Location[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Location findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class LocationsTable extends Table
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

        $this->setTable('locations');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');
        $this->addBehavior('Auditable');
        $this->belongsTo('ParentLocations', [
            'className' => 'Locations',
            'foreignKey' => 'parent_id'
        ]);
        $this->hasMany('Customers', [
            'foreignKey' => 'location_id'
        ]);
        $this->hasMany('ChildLocations', [
            'className' => 'Locations',
            'foreignKey' => 'parent_id'
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
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->allowEmpty('code');

        $validator
            ->boolean('is_active')
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

	public function validationAddLocations($validator){
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('title')
			->add('title', [
				'length' => ['rule' => ['lengthBetween', 5, 50], 'message' => 'The title should be 5 to 50 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Title contains only a-z, 0-1, and space characters only!']
			]);

        $validator
			->add('code', [
				'charNum' => ['rule' =>['custom', '/^[a-z]*$/i'], 'message' => 'Code contains only a-z characters only!'],
				'code' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This code key already exist!')]
			]);

        $validator->inList('is_active', ['active', 'inactive']);
        return $validator;
	}
	public function validationUpdateLocations($validator){
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('title')
			->add('title', [
				'length' => ['rule' => ['lengthBetween', 5, 50], 'message' => 'The title should be 5 to 50 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Title contains only a-z, 0-1, and space characters only!']
			]);

        $validator
			->add('code', [
				'charNum' => ['rule' =>['custom', '/^[a-z]*$/i'], 'message' => 'Code contains only a-z characters only!'],
				'code' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This code key already exist!')]
			]);

        $validator->inList('is_active', ['active', 'inactive']);
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
        $rules->add($rules->existsIn(['parent_id'], 'ParentLocations'));

        return $rules;
    }
}
