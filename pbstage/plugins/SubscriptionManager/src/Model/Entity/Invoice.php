<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Invoice Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $payment_method_id
 * @property int $order_id
 * @property string $payment_mode
 * @property float $product_total
 * @property float $payment_amount
 * @property float $discount
 * @property string $ship_method
 * @property float $ship_amount
 * @property float $mode_amount
 * @property string $coupon_code
 * @property string $tracking_code
 * @property string $mobile
 * @property string $email
 * @property \Cake\I18n\FrozenTime $created
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
 * @property string $billing_lastname
 * @property string $billing_address
 * @property string $billing_city
 * @property string $billing_country
 * @property string $billing_pincode
 * @property string $billing_email
 * @property string $billing_phone
 * @property float $gift_voucher_amount
 * @property float $points_amount
 * @property float $cash_amount
 * @property float $credit_gift_amount
 * @property float $credit_points_amount
 * @property float $credit_cash_amount
 * @property string $transaction_ip
 * @property string $delhivery_response
 * @property int $pickup_id
 * @property string $packing_slip
 *
 * @property \SubscriptionManager\Model\Entity\Customer $customer
 * @property \SubscriptionManager\Model\Entity\PaymentMethod $payment_method
 * @property \SubscriptionManager\Model\Entity\Pickup $pickup
 * @property \SubscriptionManager\Model\Entity\InvoiceDetail[] $invoice_details
 */
class Invoice extends Entity
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
