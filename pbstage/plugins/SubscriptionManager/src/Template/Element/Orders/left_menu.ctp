<?php $currentAction = $this->request->getParam('action');?>
<!-- start of left_part -->
<ul id="myTab" class="nav nav-tabs tab_div">
    <li <?php echo $currentAction == 'view' ? 'class="active"' : ''; ?> ><?=$this->Html->link(__('Information'), ['controller' => 'Orders', 'action' => 'view', $orderId, 'key', md5($orderId)])?></li>
    <li <?php echo $currentAction == 'invoice' ? 'class="active"' : ''; ?> ><?=$this->Html->link(__('Invoice'), ['controller' => 'Orders', 'action' => 'invoice', $orderId, 'key', md5($orderId)])?></li>
    <li <?php echo $currentAction == 'awbcode' ? 'class="active"' : ''; ?> ><?=$this->Html->link(__('Modify'), ['controller' => 'Orders', 'action' => 'awbcode', $orderId, 'key', md5($orderId)])?></li>
</ul>
<!-- end of left_part -->
