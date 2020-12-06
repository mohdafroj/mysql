<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class OrderDetail extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
