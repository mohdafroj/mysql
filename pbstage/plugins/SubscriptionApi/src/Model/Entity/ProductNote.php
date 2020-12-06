<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class ProductNote extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
