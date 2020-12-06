<?php
namespace SubscriptionApi\Model\Table;

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

        $this->hasMany('SubscriptionApi.Products', [
            'foreignKey' => 'brand_id',
            'className' => 'Products',
        ]);

        $this->hasMany('CategoryBrands', [
            'foreignKey' => 'brand_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.CategoryBrands',
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
    public function getBrands()
    {
        return $this->find('all', ['conditions' => ['is_active' => 'active']])->order(['title'=>'ASC'])->hydrate(0)->toArray();
    }

    public function getWebBrands()
    {
        return $this->find('all', ['fields'=>['id','title'],'conditions' => ['Brands.is_active' => 'active','Products.is_active' => 'active']])->innerJoinWith('Products')->order(['title'=>'ASC'])->group(['Brands.id'])->hydrate(0)->toArray();
    }

}
