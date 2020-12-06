<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Shipvendor extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
