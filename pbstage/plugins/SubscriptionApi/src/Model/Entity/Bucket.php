<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Bucket extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
