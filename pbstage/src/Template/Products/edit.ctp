<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $product->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $product->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Products'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Product Categories'), ['controller' => 'ProductCategories', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Product Category'), ['controller' => 'ProductCategories', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Reviews'), ['controller' => 'Reviews', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Review'), ['controller' => 'Reviews', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Url Rewrite'), ['controller' => 'UrlRewrite', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Url Rewrite'), ['controller' => 'UrlRewrite', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Wishlists'), ['controller' => 'Wishlists', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Wishlist'), ['controller' => 'Wishlists', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="products form large-9 medium-8 columns content">
    <?= $this->Form->create($product) ?>
    <fieldset>
        <legend><?= __('Edit Product') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('sku_code');
            echo $this->Form->control('sort_order');
            echo $this->Form->control('short_description');
            echo $this->Form->control('meta_title');
            echo $this->Form->control('meta_keyword');
            echo $this->Form->control('meta_description');
            echo $this->Form->control('is_active');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
