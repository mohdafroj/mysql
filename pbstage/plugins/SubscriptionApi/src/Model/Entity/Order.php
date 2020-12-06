<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Order extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
