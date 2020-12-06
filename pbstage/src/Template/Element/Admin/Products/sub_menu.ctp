<?php 
    $crtAction = $this->request->getParam('action');
    $id        = isset($id) ? $id : 0;
?>
<ul class="nav nav-tabs tab_div">
    <li <?php echo ($crtAction == 'edit') ? 'class="active"':''; ?> ><?= $this->Html->link('General', ['action'=>'edit', $id,'key', md5($id)])?></li>
    <li <?php echo ($crtAction == 'categories') ? 'class="active"':''; ?> ><?= $this->Html->link('Categories', ['action'=>'categories', $id,'key', md5($id)])?></li>
    <li <?php echo ($crtAction == 'images') ? 'class="active"':''; ?> ><?= $this->Html->link('Images', ['action'=>'images', $id,'key', md5($id)])?></li>
    <li <?php echo ($crtAction == 'relatedProducts') ? 'class="active"':''; ?> ><?= $this->Html->link('Related Products', ['action'=>'related-products', $id,'key', md5($id)])?></li>
    <li <?php echo ($crtAction == 'productReviews') ? 'class="active"':''; ?> ><?= $this->Html->link('Product Rviews', ['action'=>'product-reviews', $id,'key', md5($id)])?></li>
    <li <?php echo ($crtAction == 'productNotes') ? 'class="active"':''; ?> ><?= $this->Html->link('Product Notes', ['action'=>'product-notes', $id,'key', md5($id)])?></li>
</ul>
