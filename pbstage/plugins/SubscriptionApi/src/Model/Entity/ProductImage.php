<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class ProductImage extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
