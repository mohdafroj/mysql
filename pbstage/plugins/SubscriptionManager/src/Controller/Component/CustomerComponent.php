<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Admin component
 */
class CustomerComponent extends Component
{
    public $REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
    public $REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
    public $REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
    public $REG_PINCODE = '/^\d{6}$/';

    /***** This is SubscriptionManager Plugin *****/
    public function getOrdersDetails($customerId = 0, $orderId = 0)
    { //$userId = 44593; $orderNumber=100075942;
        $data = [];
        $query = TableRegistry::get('SubscriptionManager.Orders')->find('all', ['fields' => ['id', 'payment_mode', 'product_total', 'payment_amount', 'discount', 'ship_amount', 'mode_amount', 'coupon_code', 'tracking_code', 'status', 'created', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country', 'shipping_email', 'shipping_phone', 'debit_points', 'credit_points', 'debit_cash', 'credit_cash','debit_voucher', 'credit_voucher', 'created', 'transaction_ip'], 'conditions' => ['Orders.customer_id' => $customerId, 'Orders.id' => $orderId]])
        ->contain([
            'Locations' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['title','price_logo'=>'currency_logo']);
                }
            ],
            'Customers' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','firstname', 'lastname','email']);
                }
            ],
            'Couriers' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','title']);
                }
            ],
            'PaymentMethods' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['title']);
                }
            ],
            'OrderDetails' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['order_id', 'product_id', 'id', 'title', 'sku_code', 'size', 'price', 'quantity', 'discount']);
                }
            ],
            'OrderComments' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['order_id', 'id', 'given_by', 'status', 'comment']);
                }
            ],
            'OrderDetails.Products' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','url_key']);
                }
            ]
        ])
        ->hydrate(0)->toArray();
        $count = 0;
        foreach ($query as $value) {
            $orderDetails = array_map(function($v){
                $queryImg = TableRegistry::get('SubscriptionManager.ProductImages')->find('all', ['fields' => ['img_small'], 'conditions' => ['product_id' => $v['product_id'], 'is_small' => 1, 'is_active' => 'active']])->toArray();
                $v['product']['image'] = $queryImg[0]->img_small ?? '';
                //$v['product']['id'] = $v['product_id'];
                return array_slice($v, 1);
            }, $value['order_details']);
            $data = $value;
            $data['tracking_link'] = $this->getTrackUrl($value['courier']['id'] ?? 0, $value['tracking_code']);
            $data['payment_method'] = $value['payment_method']['title'] ?? '';
            $data['courier'] = $value['courier']['title'] ?? '';
            $data['location'] = $value['location']['title'] ?? '';
            $data['created'] = date("d F Y", strtotime($value['created']));
            $data['order_details'] = $orderDetails;
            $data['order_prefix'] = PC['ORDER_PREFIX'];
            $data['content'] = $this->productListInEmail($orderDetails);
            $data['order_comments'] = array_map( function($v){return array_slice($v,1); }, $value['order_comments']);
        }
        //pr($data);
        return $data;
    }
    public function productListInEmail ($productList) {
        $content = '';
        foreach ($productList as $value) {
            $content .= 
            '<table width="100%" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr>
                                <td>
                                    <table width="100" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="'.$value['product']['image'].'" alt="'.$value['title'].'" width="100%" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                    </table>
                                </td>
                                
                                <td style="border-left:1px solid #ccc;" width="15">&nbsp;</td>
                                
                                <td>
                                    
                                    <table width="250" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#3b4e76; margin:0; font-weight:700;">
                                                    '.$value['title'].'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <p style="font-size:13px; color:#363636; margin:0;">
                                                                Size '.$value['size'].'
                                                            </p>
                                                        </td>
                                                        <td align="right">
                                                            <p style="font-size:13px; color:#363636; margin:0; font-style:italic;">
                                                                '.$value['quantity'].' Qty
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#363636; margin:0; font-weight:700;">
                                                    '.number_format($value['price'], 2).'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            ';
        }
        return $content;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getTrackUrl($courierId, $trackCode){
        $res = '';
        if( !empty($courierId) && !empty($trackCode) ){
            if( $courierId == 3 ){
                $res = PC['DLYVERY']['track_package'].$trackCode;
            }else{
                $res = 'https://shiprocket.co/tracking/'.$trackCode;
            } 
        }
        return $res;
    }

}
