<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('products');
        //$this->setTable('products_categories');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ProductCategories', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.ProductCategories',
        ]);

        $this->hasMany('ProductImages', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.ProductImages',
        ]);

        $this->hasMany('Reviews', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.Reviews',
        ]);
        $this->hasMany('Carts', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.Carts',
        ]);
        $this->hasMany('ProductPrices', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.ProductPrices',
        ]);
        $this->hasMany('ProductNotes', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.ProductNotes',
        ]);
        $this->hasMany('UrlRewrite', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.UrlRewrite',
        ]);
        $this->hasMany('Wishlists', [
            'foreignKey' => 'product_id',
            'className' => 'SubscriptionManager.Wishlists',
        ]);
        $this->belongsTo('Brands', [
            'foreignKey' => 'brand_id',
            'className' => 'SubscriptionManager.Brands',
        ]);

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
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('sku_code')
            ->add('sku_code', [
                'length' => ['rule' => ['lengthBetween', 10, 20], 'message' => 'The SKU Code should be 10 to 20 character long!'],
                'charNum' => ['rule' => ['custom', '/^[a-z0-9]*$/i'], 'message' => 'SKU Code contains only a-z, 0-1 characters only!'],
                'skuCode' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => __('This sku code already exist!')],
            ]);

        $validator
            ->notEmpty('url_key')
            ->add('url_key', [
                'charNum' => ['rule' => ['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
                'urlKey' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')],
            ]);

        $validator
            ->notEmpty('quantity')
            ->integer('quanitty');

        $validator
            ->notEmpty('min_cart_qty')
            ->add('min_cart_qty', [
                'qtyMsg' => ['rule' => ['custom', '/^[1-9]*$/i'], 'message' => 'Please enter valid integer number for min cart quantity! '],
            ]);

        $validator
            ->requirePresence('min_cart_qty', 'create')
            ->notEmpty('max_cart_qty')
            ->add('max_cart_qty', 'qtyComp', [
                'rule' => function ($value, $context) {
                    return intval($value) >= intval($context['data']['min_cart_qty']);
                },
                'message' => 'Sorry, max cart qty should not less than min cart qty! ',
            ]);

        $validator
            ->integer('out_stock_qty');

        $validator
            ->integer('notify_stock_qty');

        $validator->allowEmpty('discount')->decimal('discount')
            ->add('discount', 'custom', [
                'rule' => function ($value) {
                    return $value < 99;
                },
                'message' => 'Please enter discount 1.00 to 99.00 decimal number!',
            ]);

        $validator->allowEmpty('discount_from')->date('discount_from', 'ymd');
        $validator->allowEmpty('discount_to')->date('discount_to', 'ymd');

        $validator
            ->requirePresence('discount_to', 'create')
            ->add('discount_to', 'discountTo', [
                'rule' => function ($value, $context) {
                    return strtotime($value) >= strtotime($context['data']['discount_from']);
                },
                'message' => 'Sorry, from date should not greater than to date! ',
            ]);

        $validator->notEmpty('size')->decimal('size');

        $validator
            ->requirePresence('sort_order', 'create')
            ->integer('sort_order')
            ->allowEmpty('sort_order');

        $validator
            ->allowEmpty('meta_title');

        $validator
            ->allowEmpty('meta_keyword');

        $validator
            ->allowEmpty('meta_description');

        $validator
            ->allowEmpty('is_active');

        return $validator;
    }

    public function validationAdminProductUpdate(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('sku_code')
            ->add('sku_code', [
                'length' => ['rule' => ['lengthBetween', 10, 20], 'message' => 'The SKU Code should be 10 to 20 character long!'],
                'charNum' => ['rule' => ['custom', '/^[a-z0-9]*$/i'], 'message' => 'SKU Code contains only a-z, 0-1 characters only!'],
            ]);

        $validator
            ->notEmpty('url_key')
            ->add('url_key', [
                'charNum' => ['rule' => ['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
                'urlKey' => ['rule' => ['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')],
            ]);

        $validator
            ->notEmpty('quantity')
            ->integer('quantity');

        $validator
            ->notEmpty('min_cart_qty')
            ->add('min_cart_qty', [
                'qtyMsg' => ['rule' => ['custom', '/^[1-9]*$/i'], 'message' => 'Please enter valid integer number for min cart quantity! '],
            ]);

        $validator
            ->requirePresence('min_cart_qty', 'create')
            ->notEmpty('max_cart_qty')
            ->add('max_cart_qty', 'qtyComp', [
                'rule' => function ($value, $context) {
                    return intval($value) >= intval($context['data']['min_cart_qty']);
                },
                'message' => 'Sorry, max cart qty should not less than min cart qty! ',
            ]);

        $validator
            ->integer('out_stock_qty');

        $validator
            ->integer('notify_stock_qty');

        $validator->allowEmpty('discount')->decimal('discount');
        $validator->allowEmpty('discount_from')->date('discount_from', 'ymd');
        $validator->allowEmpty('discount_to')->date('discount_to', 'ymd');

        $validator
            ->requirePresence('discount_to', 'create')
            ->add('discount_to', 'discountTo', [
                'rule' => function ($value, $context) {
                    return strtotime($value) >= strtotime($context['data']['discount_from']);
                },
                'message' => 'Sorry, from date should not greater than to date! ',
            ]);

        $validator->notEmpty('size')->decimal('size');

        $validator
            ->requirePresence('sort_order', 'create')
            ->integer('sort_order')
            ->allowEmpty('sort_order');

        $validator
            ->allowEmpty('meta_title');

        $validator
            ->allowEmpty('meta_keyword');

        $validator
            ->allowEmpty('meta_description');

        $validator
            ->allowEmpty('is_active');

        return $validator;
    }

    public function validationUploadImage($validator)
    {
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

        return $validator;
    }
}
