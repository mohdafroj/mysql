<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductPricesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('product_prices');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Locations', [
            'foreignKey' => 'location_id',
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
            ->notEmpty('name')
            ->add('name', [
                'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The name should be 3 to 500 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9()+,#& ]*$/i'], 'message' => 'The name contains only a-z, 0-1, (,+) #, & and space characters only!']
            ]);

        $validator
            ->notEmpty('title')
            ->add('title', [
                'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!'],
				'charNum' => ['rule' => ['custom', '/^[a-z0-9()+,#& ]*$/i'], 'message' => 'The title contains only a-z, 0-1, (,+) #, & and space characters only!']
            ]);

        $validator->notEmpty('price', 'Please enter price in decimal!')
            ->add('price', [
                'decimal' => ['rule' => 'decimal', 'message' => 'Please enter price in decimal!'],
            ]);
        $validator
            ->notEmpty('short_description', 'Please enter short description!')
            ->add('short_description', [
                'length' => ['rule' => ['lengthBetween', 50, 5000], 'message' => 'The Short Description should be 50 to 5000 character long!'],
            ]);

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['product_id'], 'Products'));
        $rules->add($rules->existsIn(['location_id'], 'Locations'));
        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
