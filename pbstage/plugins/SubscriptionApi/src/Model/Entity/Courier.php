<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Courier extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
