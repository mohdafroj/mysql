<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Family extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
