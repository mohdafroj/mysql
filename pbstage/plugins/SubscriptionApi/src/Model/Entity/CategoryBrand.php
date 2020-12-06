<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class CategoryBrand extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
