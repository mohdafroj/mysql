<?php
namespace SubscriptionManager\Model\Table;

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
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'image' => [
                'fields' => [
                    'dir' => 'image_dir',
                    'size' => 'photo_size',
                    'type' => 'photo_type',
                ],
                'nameCallback' => function ($data, $settings) {
                    return strtolower($data['name']);
                },
                'transformer' => function ($table, $entity, $data, $field, $settings) {
                    $extension = pathinfo($data['name'], PATHINFO_EXTENSION);

                    // Store the thumbnail in a temporary file
                    $tmp = tempnam(sys_get_temp_dir(), 'upload') . '.' . $extension;

                    // Use the Imagine library to DO THE THING
                    $size = new \Imagine\Image\Box(40, 40);
                    $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
                    $imagine = new \Imagine\Gd\Imagine();

                    // Save that modified file to our temp file
                    $imagine->open($data['tmp_name'])
                        ->thumbnail($size, $mode)
                        ->save($tmp);

                    // Now return the original *and* the thumbnail
                    return [
                        $data['tmp_name'] => $data['name'],
                        $tmp => 'thumbnail-' . $data['name'],
                    ];
                },
                'deleteCallback' => function ($path, $entity, $field, $settings) {
                    // When deleting the entity, both the original and the thumbnail will be removed
                    // when keepFilesOnDelete is set to false
                    return [
                        $path . $entity->{$field},
                        $path . 'thumbnail-' . $entity->{$field},
                    ];
                },
                'keepFilesOnDelete' => false,
            ],
        ]);

        $this->belongsTo('SubscriptionManager.ParentCategories', [
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
    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategories'));

        return $rules;
    }
}
