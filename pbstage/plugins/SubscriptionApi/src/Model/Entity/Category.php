<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Category extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
