<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Schedule extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
