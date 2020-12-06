<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProductsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('products');
        //$this->setTable('products_categories');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ProductsCategories', [
            'foreignKey' => 'product_id'
        ]);
		
        $this->hasMany('ProductsImages', [
            'foreignKey' => 'product_id'
        ]);
		
        $this->hasMany('OrderDetails', [
            'foreignKey' => 'product_id'
        ]);
		
        $this->hasMany('Reviews', [
            'foreignKey' => 'product_id'
        ]);
        $this->hasMany('Carts', [
            'foreignKey' => 'product_id'
        ]);
        $this->hasMany('ProductsNotes', [
            'foreignKey' => 'product_id'
        ]);
        $this->hasMany('UrlRewrite', [
            'foreignKey' => 'product_id'
        ]);
        $this->hasMany('Wishlists', [
            'foreignKey' => 'product_id'
        ]);
        $this->belongsTo('Brands');
		$this->addBehavior('Auditable');
		$this->addBehavior('Josegonzalez/Upload.Upload', [
            'image' => [
                'fields' => [
                    'dir' => 'image_dir',
					'size' => 'photo_size',
                    'type' => 'photo_type'
                ],
                'nameCallback' => function ($data, $settings) {
                    return strtolower($data['name']);
                },
                'transformer' =>  function ($table, $entity, $data, $field, $settings) {
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
                        $path . 'thumbnail-' . $entity->{$field}
                    ];
                },
                'keepFilesOnDelete' => false
            ]
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('name')
			->add('name', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The name should be 3 to 500 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9()+,\'#& ]*$/i'], 'message' => 'The name contains only a-z, 0-1, (,+) & and space characters only!']
			]);	

        $validator
            ->notEmpty('title')
			->add('title', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9()+,\'#& ]*$/i'], 'message' => 'The title contains only a-z, 0-1, (,+) & and space characters only!']
			]);	

        $validator
            ->notEmpty('sku_code')
			->add('sku_code', [
				'length' => ['rule' => ['lengthBetween', 10, 20], 'message' => 'The SKU Code should be 10 to 20 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9]*$/i'], 'message' => 'SKU Code contains only a-z, 0-1 characters only!'],
				'skuCode' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This sku code already exist!')]
			]);	

        $validator
            ->notEmpty('url_key')
			->add('url_key', [
				'charNum' => ['rule' =>['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
				'urlKey' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')]
			]);
			
        $validator->notEmpty('price')->decimal('price');
		
        $validator
			->notEmpty('qty')
            ->integer('qty');
		
        $validator
			->notEmpty('min_cart_qty')
			->add('min_cart_qty', [
				'qtyMsg' => ['rule' => ['custom', '/^[1-9]*$/i'], 'message' => 'Please enter valid integer number for min cart quantity! ']
			]);
		
        $validator
			->requirePresence('min_cart_qty', 'create')
			->notEmpty('max_cart_qty')
			->add('max_cart_qty', 'qtyComp', [
				'rule' => function($value, $context){
					return intval($value) >= intval($context['data']['min_cart_qty']);
				}, 
				'message' => 'Sorry, max cart qty should not less than min cart qty! '
			]);
		
        $validator
            ->integer('out_stock_qty');
		
        $validator
			->integer('notify_stock_qty');
		
        $validator->allowEmpty('offer_price')->decimal('offer_price');
        $validator->allowEmpty('offer_from')->date('offer_from', 'ymd');
        $validator->allowEmpty('offer_to')->date('offer_to', 'ymd');

        $validator
			->requirePresence('offer_to', 'create')
			->add('offer_to', 'offerTo', [
				'rule' => function($value, $context){
					return strtotime($value) >= strtotime($context['data']['offer_from']);
				}, 
				'message' => 'Sorry, from date should not greater than to date! '
			]);
		
        $validator->notEmpty('size')->decimal('size');
	$validator->notEmpty('dead_weight')->decimal('dead_weight');

        $validator
            ->requirePresence('sort_order', 'create')
            ->integer('sort_order')
            ->allowEmpty('sort_order');

        $validator
            ->allowEmpty('short_description');

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
            ->notEmpty('title')
			->add('title', [
				'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The title should be 3 to 50 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'Title contains only a-z, 0-1, and space characters only!']
			]);	

        $validator
            ->notEmpty('sku_code')
			->add('sku_code', [
				'length' => ['rule' => ['lengthBetween', 10, 20], 'message' => 'The SKU Code should be 10 to 20 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9]*$/i'], 'message' => 'SKU Code contains only a-z, 0-1 characters only!']
			]);	

        $validator
            ->notEmpty('url_key')
			->add('url_key', [
				'charNum' => ['rule' =>['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
				'urlKey' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')]
			]);
			
        $validator->notEmpty('price')->decimal('price');
		
        $validator
			->notEmpty('qty')
            ->integer('qty');
		
        $validator
			->notEmpty('min_cart_qty')
			->add('min_cart_qty', [
				'qtyMsg' => ['rule' => ['custom', '/^[1-9]*$/i'], 'message' => 'Please enter valid integer number for min cart quantity! ']
			]);
		
        $validator
			->requirePresence('min_cart_qty', 'create')
			->notEmpty('max_cart_qty')
			->add('max_cart_qty', 'qtyComp', [
				'rule' => function($value, $context){
					return intval($value) >= intval($context['data']['min_cart_qty']);
				}, 
				'message' => 'Sorry, max cart qty should not less than min cart qty! '
			]);
		
        $validator
            ->integer('out_stock_qty');
		
        $validator
			->integer('notify_stock_qty');
		
        $validator->allowEmpty('offer_price')->decimal('offer_price');
        $validator->allowEmpty('offer_from')->date('offer_from', 'ymd');
        $validator->allowEmpty('offer_to')->date('offer_to', 'ymd');

        $validator
			->requirePresence('offer_to', 'create')
			->add('offer_to', 'offerTo', [
				'rule' => function($value, $context){
					return strtotime($value) >= strtotime($context['data']['offer_from']);
				}, 
				'message' => 'Sorry, from date should not greater than to date! '
			]);
		
        $validator->notEmpty('size')->decimal('size');
	$validator->notEmpty('dead_weight')->decimal('dead_weight');
        $validator
            ->requirePresence('sort_order', 'create')
            ->integer('sort_order')
            ->allowEmpty('sort_order');

        $validator
            ->allowEmpty('short_description');

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

	public function validationUploadImage($validator){
        $validator
            ->allowEmpty('image')
			->add('image', [
                'validSize' => [
                    'rule' => ['fileSize', '<=','2MB'],
                    'message' => __('File size should be less than 2MB!')
                ]
			])
			->add('image', [
                'validExtension' => [
                    'rule' => ['extension',['gif', 'jpeg', 'png', 'jpg']], // default  ['gif', 'jpeg', 'png', 'jpg']
                    'message' => __('Allowed only png, gif, jpeg, and jpg files!')
                ]
			]);

        return $validator;
	}
}
