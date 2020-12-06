<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

/**
 * Admin component
 */
class CouponComponent extends Component
{
    public function generateUniqueId($length = null, $prefix = '', $suffix = '')
    {
        $rndId = crypt(uniqid(rand(), 1), 'pb');
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"), "", $rndId);
        $rndId = strrev(str_replace("/", "", $rndId));
        if (!is_null($rndId)) {
            return strtoupper($prefix . substr($rndId, 0, $length) . $suffix);
        }
        return strtoupper($prefix . $rndId . $suffix);
    }

    //Check coupon code for given product to applicable
    //return an array with ['status'=>1/0,'freeShip'=>'yes/no','couponDiscount'=>decimal,'msg'=>string]
    public function getRulesByCoupon($coupon, $email, $products)
    {
        $rulesData = [];
        $dataTable = TableRegistry::get('SubscriptionManager.CartRules');
        $query = $dataTable->find('all')
            ->matching("Coupons", function ($q) use ($coupon) {
                return $q->select(['id', 'cart_rule_id', 'coupon', 'status'])->where(['Coupons.coupon' => $coupon]);
            })
            ->toArray();
        $rulesData['status'] = 0;
        if (empty($query)) {
            $rulesData['msg'] = 'Sorry, this coupon code not exists!';
        } else {
            $discountProducts = [];
            $query = $query[0];
            $discountType = $query->discount_type;
            $discountValue = $query->discount_value;
            if (($query->status == 'active') && ($query->_matchingData['Coupons']->status == 'active')) {
                $rulesData['status'] = 1;
                $rulesData['msg'] = 'Coupon code is active!';
                $crtDate = new Date();
                $crtDate = strtotime($crtDate);
                $fromDate = empty($query->valid_from) ? 0 : strtotime($query->valid_from);
                $toDate = empty($query->valid_to) ? 0 : strtotime($query->valid_to);
                if (($crtDate > $toDate) && $toDate) {
                    $rulesData['status'] = 0;
                    $rulesData['msg'] = 'This coupon code is expired!';
                } else if (($crtDate < $fromDate) && $fromDate) {
                    $rulesData['status'] = 0;
                    $rulesData['msg'] = 'This code is applicable from "' . date("d, M Y", $fromDate) . '"!';
                } else {
                    $cd = json_decode($query->custom_data, true);
                    $rulesData['freeShip'] = $cd['free_ship'];

                    //$rulesData['minQty']= $cd['min_qty'];
                    //$rulesData['categories']= $cd['categories'];
                    //$rulesData['usages']= $cd['usages'];
                    //$rulesData['validCustomers']= $cd['valid_customers'];
                    //$rulesData['usedCustomers']= $cd['used_customers'];

                    //check customer already used or not
                    $usedStatus = 1;
                    foreach ($cd['used_customers'] as $v) {
                        if (($v['email'] == $email) && ($v['used'] == $cd['usages'])) {
                            $usedStatus = 0;
                            break;
                        }
                    }
                    //echo !in_array($email, $cd['valid_customers']);
                    if ($usedStatus) {
                        if (count($cd['valid_customers']) > 0) {
                            //Only for assigned email ids
                            if (!in_array($email, $cd['valid_customers'])) {
                                $rulesData['status'] = 0;
                                $rulesData['msg'] = 'Sorry, this coupon code is not applicable to your account!';
                            }
                        }
                    } else {
                        $rulesData['status'] = 0;
                        $rulesData['msg'] = 'Sorry, this coupon code already applied to your account!';
                    }

                    //$products = [['id'=>2,'qty'=>2],['id'=>4,'qty'=>3],['id'=>11,'qty'=>1]];
                    $ids = [];
                    $qty = 0;
                    $ids = array_column($products, 'id');
                    $qtyArr = array_column($products, 'quantity');
                    $qty = array_sum($qtyArr);

                    //Check minimum quantity in your cart
                    if ($qty < $cd['min_qty']) {
                        $rulesData['status'] = 0;
                        $rulesData['msg'] = 'Please add at least ' . $cd['min_qty'] . ' quantity in your cart!';
                    }

                    //Check products in your cart
                    if (count($cd['categories'])) {
                        $query = TableRegistry::get('SubscriptionManager.ProductCategories')->find('all', ['fields' => ['product_id', 'category_id'], 'conditions' => ['product_id IN' => $ids]])->toArray();
                        if (!empty($query)) {
                            $usedStatus = 1;
                            foreach ($query as $v) {
                                if (in_array($v->category_id, $cd['categories'])) {
                                    $discountProducts[] = $v->product_id;
                                    $usedStatus = 0;
                                }
                            }
                            if ($usedStatus) {
                                $rulesData['status'] = 0;
                                $rulesData['msg'] = 'Sorry, this coupon code not applicable on these products!';
                            }
                        }
                    }

                    $minPrice = $cd['min_price'] ?? 1;
                    $mimDiscountProducts = [];
                    if (count($discountProducts) > 0) {
                        foreach ($products as $value) {
                            if (($value['price'] >= $minPrice) && in_array($value['id'], $discountProducts)) {
                                $mimDiscountProducts[] = $value['id'];
                            }
                        }
                    } else {
                        foreach ($products as $value) {
                            if ($value['price'] >= $minPrice) {
                                $mimDiscountProducts[] = $value['id'];
                            }
                        }
                    }
                    if (count($mimDiscountProducts) > 0) {
                        $discountProducts = $mimDiscountProducts;
                    } else {
                        $rulesData['status'] = 0;
                        $rulesData['msg'] = "Sorry, this coupon applicable on minimun price of product is $minPrice";
                    }

                }
                //Final Calculation for discount
                $couponDiscount = $tempTotal = $a = 0;
                if ($rulesData['status']) {
                    if ($discountType == 'rupees') {
                        $couponDiscount = $discountValue;
                    } else {
                        foreach ($products as $value) {
                            if (in_array($value['id'], $discountProducts)) {
                                $tempTotal += $value['price'] * $value['quantity'];
                            }
                        } //echo $tempTotal; die;
                        $couponDiscount = (float) ($tempTotal * $discountValue) / 100;
                    }
                }
                $rulesData['couponDiscount'] = $couponDiscount;
            } else {
                $rulesData['msg'] = 'Sorry, coupon code is inactive!';
            }
        }
        return $rulesData;
    }

    public function inOrderStatus($coupon, $email)
    {
        $dataTable = TableRegistry::get('SubscriptionManager.CartRules');
        $query = $dataTable->find('all')
            ->matching("Coupons", function ($q) use ($coupon) {
                return $q->select(['id', 'cart_rule_id', 'coupon', 'status'])->where(['Coupons.coupon' => $coupon]);
            })
            ->toArray();
        if (!empty($query)) {
            $query = $query[0];
            $cd = json_decode($query->custom_data, true);
            $status = 1;
            if (count($cd['used_customers']) > 0) {
                for ($i = 0; $i < count($cd['used_customers']); $i++) {
                    if ($cd['used_customers'][$i]['email'] == $email) {
                        $cd['used_customers'][$i]['used'] += 1;
                        $status = 0;
                        break;
                    }
                }
            }
            if ($status) {
                array_push($cd['used_customers'], ['email' => $email, 'used' => 1]);
            }

            $rule = $dataTable->get($query->id);
            $rule->custom_data = json_encode($cd);
            if ($dataTable->save($rule)) {
                $dataTable = TableRegistry::get('SubscriptionManager.Coupons');
                $rule = $dataTable->get($query->_matchingData['Coupons']->id);
                $rule->used += 1;
                $dataTable->save($rule);
            }
        }
        return true;
    }

    public function outOrderStatus($coupon, $email)
    {
        $dataTable = TableRegistry::get('SubscriptionManager.CartRules');
        $query = $dataTable->find('all')
            ->matching("Coupons", function ($q) use ($coupon) {
                return $q->select(['id', 'cart_rule_id', 'coupon', 'status'])->where(['Coupons.coupon' => $coupon]);
            })
            ->toArray();
        if (!empty($query)) {
            $query = $query[0];
            $cd = json_decode($query->custom_data, true);
            $status = 1;
            if (count($cd['used_customers']) > 0) {
                for ($i = 0; $i < count($cd['used_customers']); $i++) {
                    if ($cd['used_customers'][$i]['email'] == $email) {
                        $cd['used_customers'][$i]['used'] -= 1;
                        $status = 0;
                        break;
                    }
                }
            }

            $rule = $dataTable->get($query->id);
            $rule->custom_data = json_encode($cd);
            if ($dataTable->save($rule)) {
                $dataTable = TableRegistry::get('SubscriptionManager.Coupons');
                $rule = $dataTable->get($query->_matchingData['Coupons']->id);
                $rule->used -= 1;
                $dataTable->save($rule);
            }
        }
        return true;
    }

    public function orderedData($coupon, $orderId, $email)
    {
        $resData = [];
        $dataTable = TableRegistry::get('SubscriptionManager.Orders');
        $query = $dataTable->find('all', ['contain' => ['OrderDetails'], 'fields' => ['id'], 'conditions' => ['id' => $orderId, 'coupon_code' => $coupon]])
            ->toArray();
        if (!empty($query)) {
            $query = $query[0];
            $products = [];
            foreach ($query['order_details'] as $value) {
                $products[] = ['id' => $value->product_id, 'qty' => $value->qty, 'price' => $value->price];
            }

            $dataTable = TableRegistry::get('SubscriptionManager.CartRules');
            $query = $dataTable->find('all')
                ->matching("Coupons", function ($q) use ($coupon) {
                    return $q->select(['id', 'cart_rule_id', 'coupon', 'status'])->where(['Coupons.coupon' => $coupon]);
                })
                ->toArray();
            $resData['coupon'] = $coupon;
            $resData['status'] = 0;
            if (empty($query)) {
                $resData['msg'] = 'Sorry, this coupon code not exists!';
            } else {
                $discountProducts = [];
                $query = $query[0];
                $discountType = $query->discount_type;
                $discountValue = $query->discount_value;
                if (($query->status == 'active') && ($query->_matchingData['Coupons']->status == 'active')) {
                    $resData['status'] = 1;
                    $resData['msg'] = 'Coupon code is active!';
                    $crtDate = new Date();
                    $crtDate = strtotime($crtDate);
                    $fromDate = empty($query->valid_from) ? 0 : strtotime($query->valid_from);
                    $toDate = empty($query->valid_to) ? 0 : strtotime($query->valid_to);
                    if (($crtDate > $toDate) && $toDate) {
                        $resData['status'] = 0;
                        $resData['msg'] = 'This coupon code is expired!';
                    } else if (($crtDate < $fromDate) && $fromDate) {
                        $resData['status'] = 0;
                        $resData['msg'] = 'This code is applicable from "' . date("d, M Y", $fromDate) . '"!';
                    } else {
                        $cd = json_decode($query->custom_data, true);
                        $resData['freeShip'] = $cd['free_ship'];

                        $usedStatus = 1;
                        foreach ($cd['used_customers'] as $v) {
                            if (($v['email'] == $email) && ($v['used'] == $cd['usages'])) {
                                $usedStatus = 0;
                                break;
                            }
                        }
                        //echo !in_array($email, $cd['valid_customers']);
                        if ($usedStatus) {
                            if (count($cd['valid_customers']) > 0) {
                                //Only for assigned email ids
                                if (!in_array($email, $cd['valid_customers'])) {
                                    $rulesData['status'] = 0;
                                    $rulesData['msg'] = 'Sorry, this coupon code is not applicable to your account!';
                                }
                            }
                        } else {
                            $rulesData['status'] = 0;
                            $rulesData['msg'] = 'Sorry, this coupon code already applied to your account!';
                        }

                        //$products = [['id'=>2,'qty'=>2],['id'=>4,'qty'=>3],['id'=>11,'qty'=>1]];
                        $ids = [];
                        $qty = 0;
                        $ids = array_column($products, 'id');
                        $qtyArr = array_column($products, 'qty');
                        $qty = array_sum($qtyArr);

                        //Check minimum quantity in your cart
                        if ($qty < $cd['min_qty']) {
                            $rulesData['status'] = 0;
                            $rulesData['msg'] = 'Please add at least ' . $cd['min_qty'] . ' qty in your cart!';
                        }
                        //Check products in your cart

                        if (count($cd['categories'])) {
                            $query = TableRegistry::get('SubscriptionManager.ProductCategories')->find('all', ['fields' => ['product_id', 'category_id'], 'conditions' => ['product_id IN' => $ids]])->distinct(['category_id'])->toArray();
                            if (!empty($query)) {
                                $usedStatus = 1;
                                foreach ($query as $v) {
                                    if (in_array($v->category_id, $cd['categories'])) {
                                        $discountProducts[] = $v->product_id;
                                        $usedStatus = 0;
                                    }
                                }
                                if ($usedStatus) {
                                    $resData['status'] = 0;
                                    $resData['msg'] = 'Sorry, this coupon code not applicable on these products!';
                                }
                            }
                        }
                    }
                    //Final Calculation for discount
                    $couponDiscount = $tempTotal = 0;
                    if ($resData['status']) {
                        if ($discountType == 'rupees') {
                            $couponDiscount = $discountValue;
                        } else {
                            foreach ($products as $value) {
                                if (count($discountProducts) > 0) {
                                    if (in_array($value['id'], $discountProducts)) {
                                        $tempTotal += $value['price'] * $value['qty'];
                                    }
                                } else {
                                    $tempTotal += $value['price'] * $value['qty'];
                                }
                            }
                            $couponDiscount = (float) ($tempTotal * $discountValue) / 100;
                        }
                    }
                    $resData['couponDiscount'] = $couponDiscount;
                } else {
                    $resData['msg'] = 'Sorry, coupon code is inactive!';
                }
            }
        }
        //pr($resData);
        return $resData;
    }

}
