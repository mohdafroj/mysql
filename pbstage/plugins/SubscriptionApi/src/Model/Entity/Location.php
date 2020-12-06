<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Location extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
