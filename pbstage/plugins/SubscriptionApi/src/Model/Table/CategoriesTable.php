<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CategoriesTable extends Table
{
    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');

        $this->belongsTo('SubscriptionApi.ParentCategories', [
            'className' => 'Categories',
            'foreignKey' => 'parent_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('name');

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('image');

        $validator
            ->requirePresence('title', 'create')
            ->allowEmpty('title');

        $validator
            ->allowEmpty('meta_keyword');

        $validator
            ->allowEmpty('meta_description');

        $validator
            ->notEmpty('is_active');

        return $validator;
    }

    public function validationAddCategories($validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('name')
            ->add('name', [
                'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The name should be 3 to 50 character long!'],
                'charNum' => ['rule' => ['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Name contains only a-z, 0-1, and space characters only!'],
            ]);

        $validator
            ->add('url_key', [
                'charNum' => ['rule' => ['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
                'urlKey' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')],
            ]);

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('image')
            ->add('image', [
                'validSize' => [
                    'rule' => ['fileSize', '<=', '2MB'],
                    'message' => __('File size should be less than 2MB!'),
                ],
            ])
            ->add('image', [
                'validExtension' => [
                    'rule' => ['extension', ['gif', 'jpeg', 'png', 'jpg']], // default  ['gif', 'jpeg', 'png', 'jpg']
                    'message' => __('Allowed only png, gif, jpeg, and jpg files!'),
                ],
            ]);

        $validator
            ->requirePresence('title', 'create')
            ->allowEmpty('title');

        $validator
            ->allowEmpty('meta_keyword');

        $validator
            ->allowEmpty('meta_description');

        $validator->inList('is_active', ['active', 'inactive']);

        return $validator;
    }

    public function validationUpdateCategories(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('name')
            ->add('name', [
                'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The name should be 3 to 50 character long!'],
                'charNum' => ['rule' => ['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Name contains only a-z, 0-1, and space characters only!'],
            ]);

        $validator
            ->add('url_key', [
                'charNum' => ['rule' => ['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
                'urlKey' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')],
            ]);

        $validator
            ->allowEmpty('description');

        $validator
            ->allowEmpty('image')
            ->add('image', [
                'validSize' => [
                    'rule' => ['fileSize', '<=', '2MB'],
                    'message' => __('File size should be less than 2MB!'),
                ],
            ])
            ->add('image', [
                'validExtension' => [
                    'rule' => ['extension', ['gif', 'jpeg', 'png', 'jpg']], // default  ['gif', 'jpeg', 'png', 'jpg']
                    'message' => __('Allowed only png, gif, jpeg, and jpg files!'),
                ],
            ]);

        $validator
            ->requirePresence('title', 'create')
            ->allowEmpty('title');

        $validator
            ->allowEmpty('meta_keyword');

        $validator
            ->allowEmpty('meta_description');

        $validator->inList('is_active', ['active', 'inactive']);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategories'));

        return $rules;
    }
}
