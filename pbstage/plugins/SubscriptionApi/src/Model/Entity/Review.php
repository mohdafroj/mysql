<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Review extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
