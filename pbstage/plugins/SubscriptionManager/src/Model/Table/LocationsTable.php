<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class LocationsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('locations');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Tree');

        $this->hasMany('SubscriptionManager.CustomerWallets', [
            'foreignKey' => 'location_id',
        ]);

        $this->hasMany('SubscriptionManager.Orders', [
            'foreignKey' => 'location_id',
        ]);
        $this->hasMany('SubscriptionManager.CustomerLogs', [
            'foreignKey' => 'location_id',
        ]);
        $this->hasMany('SubscriptionManager.ProductPrices', [
            'foreignKey' => 'location_id',
            'className' => 'ProductPrices',
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
            ->allowEmpty('code');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentLocations'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
