<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Cart extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
