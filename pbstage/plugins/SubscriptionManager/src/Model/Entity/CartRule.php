<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class CartRule extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
