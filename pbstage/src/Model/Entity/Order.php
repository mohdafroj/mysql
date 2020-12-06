<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property string $order_mode
 * @property float $order_amount
 * @property float $order_discount
 * @property float $order_shipping_amount
 * @property float $order_mode_amount
 * @property float $order_tax
 * @property string $order_coupon
 * @property string $order_tracking_number
 * @property string $order_email
 * @property \Cake\I18n\FrozenTime $order_date
 * @property string $status
 * @property string $shipping_firstname
 * @property string $shipping_lastname
 * @property string $shipping_address
 * @property string $shipping_city
 * @property string $shipping_state
 * @property string $shipping_country
 * @property string $shipping_pincode
 * @property string $shipping_email
 * @property string $shipping_phone
 * @property string $billing_firstname
 * @property string $billing_lastname
 * @property string $billing_address
 * @property string $billing_city
 * @property string $billing_state
 * @property string $billing_country
 * @property string $billing_pincode
 * @property string $billing_email
 * @property string $billing_phone
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\OrderDetail[] $order_details
 */
class Order extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
