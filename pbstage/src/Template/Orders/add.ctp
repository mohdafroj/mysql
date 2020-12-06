<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Orders'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Customers'), ['controller' => 'Customers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Customer'), ['controller' => 'Customers', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Order Details'), ['controller' => 'OrderDetails', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Order Detail'), ['controller' => 'OrderDetails', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="orders form large-9 medium-8 columns content">
    <?= $this->Form->create($order) ?>
    <fieldset>
        <legend><?= __('Add Order') ?></legend>
        <?php
            echo $this->Form->control('customer_id', ['options' => $customers]);
            echo $this->Form->control('order_mode');
            echo $this->Form->control('order_amount');
            echo $this->Form->control('order_discount');
            echo $this->Form->control('order_shipping_amount');
            echo $this->Form->control('order_mode_amount');
            echo $this->Form->control('order_tax');
            echo $this->Form->control('order_coupon');
            echo $this->Form->control('order_tracking_number');
            echo $this->Form->control('order_email');
            echo $this->Form->control('order_date');
            echo $this->Form->control('status');
            echo $this->Form->control('shipping_firstname');
            echo $this->Form->control('shipping_lastname');
            echo $this->Form->control('shipping_address');
            echo $this->Form->control('shipping_city');
            echo $this->Form->control('shipping_state');
            echo $this->Form->control('shipping_country');
            echo $this->Form->control('shipping_pincode');
            echo $this->Form->control('shipping_email');
            echo $this->Form->control('shipping_phone');
            echo $this->Form->control('billing_firstname');
            echo $this->Form->control('billing_lastname');
            echo $this->Form->control('billing_address');
            echo $this->Form->control('billing_city');
            echo $this->Form->control('billing_state');
            echo $this->Form->control('billing_country');
            echo $this->Form->control('billing_pincode');
            echo $this->Form->control('billing_email');
            echo $this->Form->control('billing_phone');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
