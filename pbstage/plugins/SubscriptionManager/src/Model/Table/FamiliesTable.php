<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class FamiliesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('families');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
    }

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

        return $validator;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
