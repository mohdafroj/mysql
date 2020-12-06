<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class Address extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
