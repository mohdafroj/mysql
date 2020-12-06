<?php $currentAction = $this->request->getParam('action'); ?>
<!-- start of left_part -->
<ul id="myTab" class="nav nav-tabs tab_div">
    <li <?php echo $currentAction == 'view'    ? 'class="active"':''; ?> ><?= $this->Html->link(__('Information'), ['controller'=>'Orders', 'action' => 'view', $order->id, 'key', md5($order->id)]) ?></li>
    <li <?php echo $currentAction == 'invoice' ? 'class="active"':''; ?> ><?= $this->Html->link(__('Invoice'), ['controller'=>'Orders', 'action' => 'invoice', $order->id, 'key', md5($order->id)]) ?></li>
    <li <?php echo $currentAction == 'awbcode' ? 'class="active"':''; ?> ><?= $this->Html->link(__('Modify'), ['controller'=>'Orders', 'action' => 'awbcode', $order->id, 'key', md5($order->id)]) ?></li>
</ul>
<!-- end of left_part -->
