<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class InvoiceDetail extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
