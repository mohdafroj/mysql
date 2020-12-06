<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductImagesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('product_images');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('SubscriptionApi.Products');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('alt_text');

        $validator
            ->integer('img_order')
            ->requirePresence('img_order', 'create')
            ->notEmpty('img_order');

        $validator
            ->allowEmpty('img_thumbnail');

        $validator
            ->allowEmpty('img_small');

        $validator
            ->allowEmpty('img_base');

        $validator
            ->allowEmpty('img_large');

        $validator
            ->allowEmpty('img_popup');

        $validator
            ->boolean('is_thumbnail')
            ->requirePresence('is_thumbnail', 'create')
            ->notEmpty('is_thumbnail');

        $validator
            ->boolean('is_small')
            ->requirePresence('is_small', 'create')
            ->notEmpty('is_small');

        $validator
            ->boolean('is_base')
            ->requirePresence('is_base', 'create')
            ->notEmpty('is_base');

        $validator
            ->boolean('is_large')
            ->requirePresence('is_large', 'create')
            ->notEmpty('is_large');

        $validator
            ->boolean('exclude')
            ->requirePresence('exclude', 'create')
            ->notEmpty('exclude');

        $validator
            ->allowEmpty('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['product_id'], 'Products'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
