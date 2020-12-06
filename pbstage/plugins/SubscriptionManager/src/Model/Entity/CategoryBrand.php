<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class CategoryBrand extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
