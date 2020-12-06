<?php $action = $this->request->getParam('action'); ?>
<!-- start of left_part -->
<ul id="myTab" class="nav nav-tabs tab_div">
    <li <?= ($action == 'view') ? 'class="active"':'' ?>><?= $this->Html->link(__('Manage Profile'), ['action' => 'view', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'addresses') ? 'class="active"':'' ?>><?= $this->Html->link(__('Manage Addresses'), ['action' => 'addresses', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'orders') ? 'class="active"':'' ?>><?= $this->Html->link(__('Orders'), ['action' => 'orders', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'cart') ? 'class="active"':'' ?>><?= $this->Html->link(__('My Cart'), ['action' => 'cart', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'wishlist') ? 'class="active"':'' ?>><?= $this->Html->link(__('My Wishlist'), ['action' => 'wishlist', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'wallet') ? 'class="active"':'' ?>><?= $this->Html->link(__('Wallets'), ['action' => 'wallet', $id, 'key', md5($id)]) ?></li>
    <li <?= ($action == 'reviews') ? 'class="active"':'' ?>><a href="#tab_6">Product Reviews</a></li>
    <!--li class=""><a href="#tab_7" data-toggle="tab">Newsletter</a></li-->
</ul><!-- end of left_part -->
