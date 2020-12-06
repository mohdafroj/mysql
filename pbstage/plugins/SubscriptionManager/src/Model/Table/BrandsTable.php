<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class BrandsTable extends Table
{

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('brands');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('SubscriptionManager.Products', [
            'foreignKey' => 'brand_id',
            'className' => 'Products',
        ]);

        $this->hasMany('CategoryBrands', [
            'foreignKey' => 'brand_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.CategoryBrands',
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
            ->allowEmpty('image');

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }
}
