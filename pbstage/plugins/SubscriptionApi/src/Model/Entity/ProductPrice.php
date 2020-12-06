<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class ProductPrice extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
