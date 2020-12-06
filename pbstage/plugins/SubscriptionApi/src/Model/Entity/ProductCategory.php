<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class ProductCategory extends Entity
{
    protected $_accessible = [
        '*' => true,
        'product_id' => false,
        'category_id' => false,
    ];
}
