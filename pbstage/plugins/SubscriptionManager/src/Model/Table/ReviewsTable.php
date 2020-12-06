<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ReviewsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('reviews');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Customers',
        ]);
        $this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Locations',
        ]);
        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Products',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('customer_name');

        $validator
            ->allowEmpty('title');

        $validator
            ->notEmpty('description', 'Please enter review!');

        $validator
            ->integer('rating')
            ->requirePresence('rating', 'create')
            ->notEmpty('rating');
        $validator
            ->integer('customer_id')
            ->notEmpty('customer_id', 'Please enter customer email and select from filtered list!');
        $validator
            ->integer('product_id')
            ->notEmpty('product_id', 'Please enter product name and select from filtered list!');

        $validator
            ->allowEmpty('location_ip');

        $validator
            ->integer('offer')
            ->allowEmpty('offer');

        $validator
            ->allowEmpty('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['product_id'], 'Products'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
