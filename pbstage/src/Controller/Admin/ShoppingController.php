<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ShoppingController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Coupon');
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
    }

    public function index()
    {
        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];
        $filterData = [];

        $title = $this->request->getQuery('title', '');
        $this->set('title', $title);
        if (!empty($title)) {$filterData['CartRules.title'] = $title;}

        $coupon = $this->request->getQuery('coupon', '');
        $this->set('coupon', $coupon);

        $discountType = $this->request->getQuery('discount_type', '');
        $this->set('discountType', $discountType);
        if (!empty($discountType)) {$filterData['CartRules.discount_type'] = $discountType;}

        $discountValue = $this->request->getQuery('discount_value', '');
        $this->set('discountValue', $discountValue);
        if (!empty($discountValue)) {$filterData['CartRules.discount_value'] = $discountValue;}

        $validFrom = $this->request->getQuery('valid_from', '');
        $this->set('validFrom', $validFrom);
        if (!empty($validFrom)) {
            $date = new Date($validFrom);
            $validFrom = $date->format('Y-m-d');
            $filterData['CartRules.valid_from LIKE'] = "$validFrom%";
        }

        $validTo = $this->request->getQuery('valid_to', '');
        $this->set('validTo', $validTo);
        if (!empty($validTo)) {
            $date = new Date($validTo);
            $validTo = $date->format('Y-m-d');
            $filterData['CartRules.valid_to LIKE'] = "$validTo%";
        }

        $status = $this->request->getQuery('status', '');
        $this->set('status', $status);
        if (!empty($status)) { $filterData['CartRules.status'] = $status; }else{ $filterData['CartRules.status IN'] = ['active','inactive']; }

        $dataTable = TableRegistry::get('CartRules');
        $query = $dataTable->find('all', ['conditions' => $filterData]);
        if (empty($coupon)) {
            $query = $query->contain([
                'Coupons' => [
                    'queryBuilder' => function ($q) use ($coupon) {
                        return $q->select(['id', 'cart_rule_id', 'coupon']);
                    },
                ],
            ]);
        } else {
            $query = $query->matching("Coupons", function ($q) use ($coupon) {
                return $q->select(['id', 'cart_rule_id', 'coupon'])->where(['Coupons.coupon' => $coupon]);
            });
        }
        $query = $query->order(['CartRules.created' => 'DESC']);
        //pr($query->toArray());
        $cartRule = $this->paginate($query);

        $this->set(compact('cartRule'));
        $this->set('_serialize', ['cartRule']);
    }

    public function addRule($key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5('shopping'))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $dataTable = TableRegistry::get('CartRules');
        $rule = $dataTable->newEntity();
        if ($this->request->is('post')) {
            $validator = new Validator();
            $validator
                ->notEmpty('title', 'Please enter rule title!')
                ->add('title', [
                    'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!'],
                    'charNum' => ['rule' => ['custom', '/^[a-z0-9()+, ]*$/i'], 'message' => 'Title contains only a-z, 0-1, (,+) and space characters only!'],
                ]);

            $validator
                ->notEmpty('discount_type', 'Please select discount type!')
                ->inList('discount_type', ['percentage', 'rupees']);

            $validator
                ->notEmpty('discount_value', 'Please enter discount value!')
                ->add('discount_value', 'discountValue', [
                    'rule' => function ($value) {
                        return ($value > -1);
                    },
                    'message' => 'Sorry, Discount Value should be greater than "-1"!',
                ]);

            $validator
                ->notEmpty('discount_value', 'Please enter discount value!')
                ->add('discount_value', 'discountValue', [
                    'rule' => function ($value) {
                        return ($value > -1);
                    },
                    'message' => 'Sorry, Discount Value should be greater than "-1"!',
                ]);

            $validator->allowEmpty('valid_from')->date('valid_from', 'ymd');
            $validator->allowEmpty('valid_to')->date('valid_to', 'ymd');
            $validator
                ->add('valid_from', 'validFrom', [
                    'rule' => function ($value) {
                        return strtotime($value) > strtotime(new Date('-1 day'));
                    },
                    'message' => 'Sorry, date should not be past!',
                ]);

            $validator
                ->add('valid_to', 'validTo', [
                    'rule' => function ($value, $context) {
                        return strtotime($value) > strtotime($context['data']['valid_from']);
                    },
                    'message' => 'Sorry, "To Date" should be greater than "From Date"!',
                ]);

            $validator->integer('usages', 'Please enter valid positve number!');

            $validator
                ->notEmpty('status', 'Please select status of rule!')
                ->inList('status', ['active', 'inactive','hidden']);

            $validator->inList('free_ship', ['yes', 'no']);
            $validator->integer('min_qty', 'Please enter a valid positive number!');

            $error = $validator->errors($this->request->getData());
            //pr($this->request->getData());
            if (empty($error)) {
                $rule->title = $this->request->getData('title');
                $rule->description = $this->request->getData('description');
                $rule->discount_type = $this->request->getData('discount_type');
                $rule->discount_value = $this->request->getData('discount_value');
                $rule->valid_from = empty($this->request->getData('valid_from')) ? null : $this->request->getData('valid_from');
                $rule->valid_to = empty($this->request->getData('valid_to')) ? null : $this->request->getData('valid_to');
                $rule->status = $this->request->getData('status');

                $customers = [];
                foreach (explode(',', $this->request->getData('emails')) as $value) {
                    $value = preg_replace("/\r|\n/", "", $value);
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $customers[] = $value;
                    }
                }

                $categories = $this->request->getData('categories', '{}');
                $categories = json_decode($categories);

                $customData = [
                    'usages' => $this->request->getData('usages'),
                    'free_ship' => $this->request->getData('free_ship'),
                    'min_qty' => $this->request->getData('min_qty'),
                    'min_price' => $this->request->getData('min_price'),
                    'categories' => $categories,
                    'valid_customers' => $customers,
                    'used_customers' => [],
                ];
                $rule->custom_data = json_encode($customData);
                if ($dataTable->save($rule)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                    return $this->redirect(['action' => 'editRule', $rule->id, 'key', md5($rule->id)]);
                }
                $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }

        $cateTree = TableRegistry::get('Categories')->find('threaded', ['order' => 'Categories.lft'])->where(['is_active' => 'active'])->toArray();
        $brands = TableRegistry::get('Brands')->find('all', ['fields' => ['id', 'title'], 'order' => ['title' => 'asc']])->where(['is_active' => 'active'])->toArray();

        $this->set(compact('rule', 'cateTree', 'brands', 'error'));
        $this->set('_serialize', ['rule', 'cateTree', 'brands', 'error']);
    }

    public function editRule($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }

        $error = [];
        $dataTable = TableRegistry::get('CartRules');
        $rule = $dataTable->get($id, [
            'contain' => [],
        ]);
        if (!empty($rule->valid_from)) {
            $date = new Date($rule->valid_from);
            $rule->valid_from = $date->format('Y-m-d');
        }
        if (!empty($rule->valid_to)) {
            $date = new Date($rule->valid_to);
            $rule->valid_to = $date->format('Y-m-d');
        }
        $customData = json_decode($rule->custom_data, true);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $validator = new Validator();
            $validator
                ->notEmpty('title', 'Please enter rule title!')
                ->add('title', [
                    'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!'],
                    'charNum' => ['rule' => ['custom', '/^[a-z0-9()+, ]*$/i'], 'message' => 'Title contains only a-z, 0-1, (,+) and space characters only!'],
                ]);

            $validator
                ->notEmpty('discount_type', 'Please select discount type!')
                ->inList('discount_type', ['percentage', 'rupees']);

            $validator
                ->notEmpty('discount_value', 'Please enter discount value!')
                ->add('discount_value', 'discountValue', [
                    'rule' => function ($value) {
                        return ($value > -1);
                    },
                    'message' => 'Sorry, Discount Value should be greater than "-1"!',
                ]);

            $validator
                ->notEmpty('min_price', 'Please enter mini price of product!')
                ->add('min_price', 'minPrice', [
                    'rule' => function ($value) {
                        return ($value > 0);
                    },
                    'message' => 'Mini price should be greater than "0"!',
                ]);

            $validator->allowEmpty('valid_from')->date('valid_from', 'ymd');
            $validator->allowEmpty('valid_to')->date('valid_to', 'ymd');
            /*$validator
            ->add('valid_from', 'validFrom', [
            'rule' => function($value){
            return strtotime($value) > strtotime(new Date('-1 day'));
            },
            'message' => 'Sorry, date should not be past!'
            ]);*/

            $validator
                ->add('valid_to', 'validTo', [
                    'rule' => function ($value, $context) {
                        return strtotime($value) > strtotime($context['data']['valid_from']);
                    },
                    'message' => 'Sorry, "To Date" should be greater than "From Date"!',
                ]);

            $validator->integer('usages', 'Please enter valid positve number!');

            $validator
                ->notEmpty('status', 'Please select status of rule!')
                ->inList('status', ['active', 'inactive','hidden']);

            $validator->inList('free_ship', ['yes', 'no']);
            $validator->integer('min_qty', 'Please enter a valid positive number!');

            $error = $validator->errors($this->request->getData());

            if (empty($error)) {
                $rule->title = $this->request->getData('title');
                $rule->description = $this->request->getData('description');
                $rule->discount_type = $this->request->getData('discount_type');
                $rule->discount_value = $this->request->getData('discount_value');
                $rule->valid_from = empty($this->request->getData('valid_from')) ? null : $this->request->getData('valid_from');
                $rule->valid_to = empty($this->request->getData('valid_to')) ? null : $this->request->getData('valid_to');
                $rule->status = $this->request->getData('status');

                $customers = [];
                foreach (explode(',', $this->request->getData('emails')) as $value) {
                    $value = preg_replace("/\r|\n/", "", $value);
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $customers[] = $value;
                    }
                }
                $categories = $this->request->getData('categories', '{}');
                $categories = json_decode($categories);
                $customData = [
                    'usages' => $this->request->getData('usages'),
                    'free_ship' => $this->request->getData('free_ship'),
                    'min_qty' => $this->request->getData('min_qty'),
                    'min_price' => $this->request->getData('min_price'),
                    'categories' => $categories,
                    'valid_customers' => $customers,
                    'used_customers' => [],
                ];
                $rule->custom_data = json_encode($customData);
                if ($dataTable->save($rule)) {
                    $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                } else {
                    $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
                }
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }

        $cateTree = TableRegistry::get('Categories')->find('threaded', ['order' => 'Categories.lft'])->where(['id >' => 1])->toArray();
        $brands = TableRegistry::get('Brands')->find('all', ['fields' => ['id', 'title'], 'order' => ['title' => 'asc']])->where(['is_active' => 'active'])->toArray();
        //pr($error);
        $this->set(compact('rule', 'cateTree', 'id', 'error', 'categoriesIds', 'brands', 'customData'));
        $this->set('_serialize', ['rule', 'cateTree', 'id', 'error', 'categoriesIds', 'brands', 'customData']);
    }

    public function addCoupons($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }
        $dataTable = TableRegistry::get('CartRules');
        $rule = $dataTable->get($id, [
            'contain' => [],
        ]);
        $customData = json_decode($rule->custom_data, true);
        $error = [];
        if ($this->request->is(['post'])) {
            $dataTable = TableRegistry::get('Coupons');
            $newCode = $this->request->getData('newCode');
            if (empty($newCode)) {
                $validator = new Validator();
                $validator
                    ->notEmpty('couponQty', 'Please enter require quantity!')
                    ->integer('couponQty', 'Quantity should be positve number!');
                $validator
                    ->notEmpty('couponLength', 'Please enter coupon length!')
                    ->integer('couponLength', 'Coupon length should be positve number!');
                $validator
                    ->allowEmpty('codePrefix')
                    ->add('codePrefix', [
                        'length' => ['rule' => ['lengthBetween', 1, 3], 'message' => 'The code prefix should be 1 to 3 character long!'],
                        'charNum' => ['rule' => ['custom', '/^[a-z0-9]*$/i'], 'message' => 'Code prefix contains only a-z and 0-1 characters only!'],
                    ]);

                $validator
                    ->allowEmpty('codeSuffix')
                    ->add('codeSuffix', [
                        'length' => ['rule' => ['lengthBetween', 1, 3], 'message' => 'The code suffix should be 1 to 3 character long!'],
                        'charNum' => ['rule' => ['custom', '/^[a-z0-9]*$/i'], 'message' => 'Code suffix contains only a-z and 0-1 characters only!'],
                    ]);

                $error = $validator->errors($this->request->getData());
                if (empty($error)) {
                    $insertRecords = $uniqueCodes = [];

                    $couponQty = $this->request->getData('couponQty');
                    $couponLength = $this->request->getData('couponLength');
                    $codePrefix = $this->request->getData('codePrefix');
                    $codeSuffix = $this->request->getData('codeSuffix');

                    for ($i = 0; $i < $couponQty; $i++) {
                        $value = $this->Coupon->generateUniqueId($couponLength, $codePrefix, $codeSuffix);
                        $insertRecords[] = [
                            'id' => null,
                            'cart_rule_id' => $id,
                            'coupon' => $value,
                            'used' => 0,
                            'status' => 'active',
                        ];
                    }
                    if (count($insertRecords) > 0) {
                        $insertRecords = $dataTable->newEntities($insertRecords);
                    }
                    if ($couponQty == count($insertRecords)) {
                        //pr($insertRecords);die;
                        if ($dataTable->saveMany($insertRecords)) {
                            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
                        } else {
                            $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
                        }
                    } else {
                        $this->Flash->error(__('Sorry, codes are not generate as given quantity, please manage quantity or length!'), ['key' => 'adminError']);
                    }
                } else {
                    $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
                }
            } else {
                //This is for single coupon code generate
                $newCode = strtoupper($newCode);
                $chkCoupons = $dataTable->find('all', ['conditions' => ['coupon' => $newCode]])->toArray();
                //pr($chkCoupons);
                if (empty($chkCoupons)) {
                    $insertRecords[] = [
                        'id' => null,
                        'cart_rule_id' => $id,
                        'coupon' => $newCode,
                        'used' => 0,
                        'status' => 'active',
                    ];
                    $insertRecords = $dataTable->newEntities($insertRecords);
                    if ($dataTable->saveMany($insertRecords)) {
                        $this->Flash->success(__('The "' . $newCode . '" has been saved!'), ['key' => 'adminSuccess']);
                    } else {
                        $this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
                    }
                } else {
                    $this->Flash->error(__('Sorry, the "' . $newCode . '" already exists!'), ['key' => 'adminError']);
                }
            }
        }

        $this->set('queryString', $this->request->getQueryParams());
        $limit = $this->request->getQuery('limit', 50);
        $filterData['cart_rule_id'] = $id;

        $coupon = $this->request->getQuery('coupon', '');
        $this->set('coupon', $coupon);
        if (!empty($coupon)) {$filterData['coupon'] = $coupon;}

        $createdFrom = $this->request->getQuery('createdFrom', '');
        $this->set('createdFrom', $createdFrom);
        $createdTo = $this->request->getQuery('createdTo', '');
        $this->set('createdTo', $createdTo);

        if (!empty($createdFrom) && !empty($createdTo)) {
            $date = new Date($createdFrom . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
            $date = new Date($createdTo . ' 23:59:59 +1 day');
            $createdTo = $date->format('Y-m-d');
        } else if (empty($createdFrom) && empty($createdTo)) {
            $createdFrom = $createdTo = '';
        } else if (!empty($createdFrom)) {
            $date = new Date($createdFrom . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
            $date = new Date($createdFrom . ' 23:59:59 +1 day');
            $createdTo = $date->format('Y-m-d');
        } else if (!empty($createdTo)) {
            $date = new Date($createdTo . ' 23:59:59');
            $createdTo = $date->format('Y-m-d');
            $date = new Date($createdTo . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
        }

        $status = $this->request->getQuery('status', '');
        $this->set('status', $status);
        if ($status !== '') {$filterData['status'] = $status;}
        $this->paginate = [
            'limit' => $limit, // limits the rows per page
            'maxLimit' => 2000,
        ];
        $coupons = TableRegistry::get('Coupons')->find('all', ['conditions' => $filterData]);
        if (!empty($createdFrom) && !empty($createdTo)) {
            $coupons = $coupons->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            });
        }
        $coupons = $coupons->order(['created' => 'DESC']);
        $coupons = $this->paginate($coupons);

        $csvName = new Time();
        $csvName = 'coupons_' . $csvName->format('Y-m-d');
        $this->set(compact('id', 'rule', 'coupons', 'error', 'customData', 'csvName'));
        $this->set('_serialize', ['id', 'rule', 'coupons', 'error', 'customData', 'csvName']);
    }

    public function exports($id = null, $key = null, $md5 = null)
    {
        if (($key != 'key') || ($md5 != md5($id))) {
            return $this->redirect(['action' => 'index']);
        }
        $this->response->withDownload('exports.csv');

        $filterData['cart_rule_id'] = $id;
        $coupon = $this->request->getQuery('coupon', '');
        $this->set('coupon', $coupon);
        if (!empty($coupon)) {$filterData['coupon'] = $coupon;}

        $createdFrom = $this->request->getQuery('createdFrom', '');
        $this->set('createdFrom', $createdFrom);
        $createdTo = $this->request->getQuery('createdTo', '');
        $this->set('createdTo', $createdTo);

        if (!empty($createdFrom) && !empty($createdTo)) {
            $date = new Date($createdFrom . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
            $date = new Date($createdTo . ' 23:59:59 +1 day');
            $createdTo = $date->format('Y-m-d');
        } else if (empty($createdFrom) && empty($createdTo)) {
            $createdFrom = $createdTo = '';
        } else if (!empty($createdFrom)) {
            $date = new Date($createdFrom . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
            $date = new Date($createdFrom . ' 23:59:59 +1 day');
            $createdTo = $date->format('Y-m-d');
        } else if (!empty($createdTo)) {
            $date = new Date($createdTo . ' 23:59:59');
            $createdTo = $date->format('Y-m-d');
            $date = new Date($createdTo . ' 00:00:01');
            $createdFrom = $date->format('Y-m-d');
        }

        $status = $this->request->getQuery('status', '');
        $this->set('status', $status);
        if ($status !== '') {$filterData['status'] = $status;}

        $coupons = TableRegistry::get('Coupons')->find('all', ['conditions' => $filterData]);
        if (!empty($createdFrom) && !empty($createdTo)) {
            $coupons = $coupons->where(function ($exp, $q) use ($createdFrom, $createdTo) {
                return $exp->between('created', $createdFrom, $createdTo);
            });
        }
        $coupons = $coupons->order(['created' => 'DESC']);
        $_serialize = 'coupons';
        $_header = ['ID', 'CartRFuleId', 'Coupon', 'Used', 'Created', 'Status'];
        $_extract = ['id', 'cart_rule_id', 'coupon', 'used', 'created', 'status'];
        $this->set(compact('coupons', '_serialize', '_header', '_extract'));
        $this->viewBuilder()->setClassName('CsvView.Csv');
        return;
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        $dataTable = TableRegistry::get('CartRules');
        $rule = $dataTable->get($id);
        if ($dataTable->delete($rule)) {
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        return true;
    }

    public function manageCoupons()
    {
        $id = $this->request->getData('id');
        $dataTable = TableRegistry::get('Coupons');
        $coupons = $dataTable->get($id);
        if ($this->request->is(['put'])) {
            $newStatus = ($coupons->status == 'active') ? 'inactive' : 'active';
            $coupons->status = $newStatus;
            if ($dataTable->save($coupons)) {
                $this->Flash->success(__('The record has been successfully saved!'), ['key' => 'adminSuccess']);
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        } else if ($this->request->is(['delete'])) {
            if ($dataTable->delete($coupons)) {
                $this->Flash->success(__('The record has been successfully deleted!'), ['key' => 'adminSuccess']);
            } else {
                $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
            }
        }
        die;
    }

    public function beforeFilter(Event $event)
    {
        $fields = ['categories'];
        $this->Security->config('unlockedFields', $fields);
        $actions = ['manageCoupons', 'updateImages'];

        if (in_array($this->request->params['action'], $actions)) {
            // for csrf
            $this->eventManager()->off($this->Csrf);
            // for security component
            $this->Security->config('unlockedActions', $actions);
        }
    }
}
