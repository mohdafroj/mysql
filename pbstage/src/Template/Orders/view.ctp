<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Order $order
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Order'), ['action' => 'edit', $order->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Order'), ['action' => 'delete', $order->id], ['confirm' => __('Are you sure you want to delete # {0}?', $order->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Orders'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Order'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customers'), ['controller' => 'Customers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customer'), ['controller' => 'Customers', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Order Details'), ['controller' => 'OrderDetails', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Order Detail'), ['controller' => 'OrderDetails', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="orders view large-9 medium-8 columns content">
    <h3><?= h($order->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Customer') ?></th>
            <td><?= $order->has('customer') ? $this->Html->link($order->customer->id, ['controller' => 'Customers', 'action' => 'view', $order->customer->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Mode') ?></th>
            <td><?= h($order->order_mode) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Coupon') ?></th>
            <td><?= h($order->order_coupon) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Tracking Number') ?></th>
            <td><?= h($order->order_tracking_number) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Email') ?></th>
            <td><?= h($order->order_email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= h($order->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Firstname') ?></th>
            <td><?= h($order->shipping_firstname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Lastname') ?></th>
            <td><?= h($order->shipping_lastname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Address') ?></th>
            <td><?= h($order->shipping_address) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping City') ?></th>
            <td><?= h($order->shipping_city) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping State') ?></th>
            <td><?= h($order->shipping_state) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Country') ?></th>
            <td><?= h($order->shipping_country) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Pincode') ?></th>
            <td><?= h($order->shipping_pincode) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Email') ?></th>
            <td><?= h($order->shipping_email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Shipping Phone') ?></th>
            <td><?= h($order->shipping_phone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Firstname') ?></th>
            <td><?= h($order->billing_firstname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Lastname') ?></th>
            <td><?= h($order->billing_lastname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Address') ?></th>
            <td><?= h($order->billing_address) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing City') ?></th>
            <td><?= h($order->billing_city) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing State') ?></th>
            <td><?= h($order->billing_state) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Country') ?></th>
            <td><?= h($order->billing_country) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Pincode') ?></th>
            <td><?= h($order->billing_pincode) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Email') ?></th>
            <td><?= h($order->billing_email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Billing Phone') ?></th>
            <td><?= h($order->billing_phone) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($order->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Amount') ?></th>
            <td><?= $this->Number->format($order->order_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Discount') ?></th>
            <td><?= $this->Number->format($order->order_discount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Shipping Amount') ?></th>
            <td><?= $this->Number->format($order->order_shipping_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Mode Amount') ?></th>
            <td><?= $this->Number->format($order->order_mode_amount) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Tax') ?></th>
            <td><?= $this->Number->format($order->order_tax) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Order Date') ?></th>
            <td><?= h($order->order_date) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Order Details') ?></h4>
        <?php if (!empty($order->order_details)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Order Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Sku Code') ?></th>
                <th scope="col"><?= __('Size') ?></th>
                <th scope="col"><?= __('Price') ?></th>
                <th scope="col"><?= __('Qty') ?></th>
                <th scope="col"><?= __('Goods Tax') ?></th>
                <th scope="col"><?= __('Offer Price') ?></th>
                <th scope="col"><?= __('Short Description') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($order->order_details as $orderDetails): ?>
            <tr>
                <td><?= h($orderDetails->id) ?></td>
                <td><?= h($orderDetails->order_id) ?></td>
                <td><?= h($orderDetails->title) ?></td>
                <td><?= h($orderDetails->sku_code) ?></td>
                <td><?= h($orderDetails->size) ?></td>
                <td><?= h($orderDetails->price) ?></td>
                <td><?= h($orderDetails->qty) ?></td>
                <td><?= h($orderDetails->goods_tax) ?></td>
                <td><?= h($orderDetails->offer_price) ?></td>
                <td><?= h($orderDetails->short_description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'OrderDetails', 'action' => 'view', $orderDetails->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'OrderDetails', 'action' => 'edit', $orderDetails->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'OrderDetails', 'action' => 'delete', $orderDetails->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orderDetails->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
