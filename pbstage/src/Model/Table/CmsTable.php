<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CmsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('cms');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Auditable');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->notEmpty('title')
			->add('title', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!'],
				//'charNum' => ['rule' =>['custom', '/^[a-z0-9()+, ]*$/i'], 'message' => 'The title contains only a-z, 0-1, (,+) and space characters only!']
			]);	

        $validator
            ->notEmpty('url_key')
			->add('url_key', [
				'charNum' => ['rule' =>['custom', '/^[a-z0-9-]*$/i'], 'message' => 'Url key contains only a-z, 0-1, and - characters only!'],
				'urlKey' => ['rule' =>['validateUnique'], 'provider' => 'table', 'message' => __('This url key already exist!')]
			]);
					
            $validator
            ->notEmpty('content')
			->add('content', [
                'length' => ['rule' => ['lengthBetween', 200, 50000], 'message' => 'The Template should be 200 to 50000 character long!']
        ]);
					
        $validator
            ->allowEmpty('image')
			->add('image', [
                'length' => ['rule' => ['lengthBetween', 0, 1000], 'message' => 'The image link should not be greater than 1000 characters!']
        ]);
					
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
	
}
