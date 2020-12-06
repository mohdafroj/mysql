<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CategoryBrandsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('category_brands');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Categories', [
            'foreignKey' => 'category_id',  
            'className' => 'SubscriptionManager.Categories'
        ]);
        $this->hasMany('Brands', [
            'foreignKey' => 'brand_id',
            'className' => 'SubscriptionManager.Brands'
        ]);
    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['category_id'], 'Categories'));
        $rules->add($rules->existsIn(['brand_id'], 'Brands'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
