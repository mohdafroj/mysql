<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class OrderComment extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
