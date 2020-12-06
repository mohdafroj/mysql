<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CouriersTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('couriers');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
        $this->hasMany('SubscriptionManager.Orders', [
            'foreignKey' => 'courier_id',
            'className' => 'SubscriptionManager.Orders',
        ]);

    }

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

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
