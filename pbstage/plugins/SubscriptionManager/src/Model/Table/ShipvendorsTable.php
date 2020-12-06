<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ShipvendorsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('shipvendors');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

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
