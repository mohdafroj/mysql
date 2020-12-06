<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Wishlist extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
