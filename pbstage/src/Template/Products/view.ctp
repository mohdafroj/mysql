<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Product $product
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Product'), ['action' => 'edit', $product->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Product'), ['action' => 'delete', $product->id], ['confirm' => __('Are you sure you want to delete # {0}?', $product->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Products'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Product Categories'), ['controller' => 'ProductCategories', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Product Category'), ['controller' => 'ProductCategories', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Reviews'), ['controller' => 'Reviews', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Review'), ['controller' => 'Reviews', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Url Rewrite'), ['controller' => 'UrlRewrite', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Url Rewrite'), ['controller' => 'UrlRewrite', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Wishlists'), ['controller' => 'Wishlists', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Wishlist'), ['controller' => 'Wishlists', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="products view large-9 medium-8 columns content">
    <h3><?= h($product->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($product->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sku Code') ?></th>
            <td><?= h($product->sku_code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Meta Title') ?></th>
            <td><?= h($product->meta_title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Active') ?></th>
            <td><?= h($product->is_active) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($product->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sort Order') ?></th>
            <td><?= $this->Number->format($product->sort_order) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($product->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($product->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Short Description') ?></h4>
        <?= $this->Text->autoParagraph(h($product->short_description)); ?>
    </div>
    <div class="row">
        <h4><?= __('Meta Keyword') ?></h4>
        <?= $this->Text->autoParagraph(h($product->meta_keyword)); ?>
    </div>
    <div class="row">
        <h4><?= __('Meta Description') ?></h4>
        <?= $this->Text->autoParagraph(h($product->meta_description)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Product Categories') ?></h4>
        <?php if (!empty($product->product_categories)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($product->product_categories as $productCategories): ?>
            <tr>
                <td><?= h($productCategories->product_id) ?></td>
                <td><?= h($productCategories->category_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'ProductCategories', 'action' => 'view', $productCategories->]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'ProductCategories', 'action' => 'edit', $productCategories->]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'ProductCategories', 'action' => 'delete', $productCategories->], ['confirm' => __('Are you sure you want to delete # {0}?', $productCategories->)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Reviews') ?></h4>
        <?php if (!empty($product->reviews)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Customer Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Description') ?></th>
                <th scope="col"><?= __('Rating') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Location Ip') ?></th>
                <th scope="col"><?= __('Is Active') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($product->reviews as $reviews): ?>
            <tr>
                <td><?= h($reviews->id) ?></td>
                <td><?= h($reviews->customer_id) ?></td>
                <td><?= h($reviews->product_id) ?></td>
                <td><?= h($reviews->title) ?></td>
                <td><?= h($reviews->description) ?></td>
                <td><?= h($reviews->rating) ?></td>
                <td><?= h($reviews->created) ?></td>
                <td><?= h($reviews->modified) ?></td>
                <td><?= h($reviews->location_ip) ?></td>
                <td><?= h($reviews->is_active) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Reviews', 'action' => 'view', $reviews->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Reviews', 'action' => 'edit', $reviews->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Reviews', 'action' => 'delete', $reviews->id], ['confirm' => __('Are you sure you want to delete # {0}?', $reviews->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Url Rewrite') ?></h4>
        <?php if (!empty($product->url_rewrite)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Id Path') ?></th>
                <th scope="col"><?= __('Request Path') ?></th>
                <th scope="col"><?= __('Target Path') ?></th>
                <th scope="col"><?= __('Is System') ?></th>
                <th scope="col"><?= __('Options') ?></th>
                <th scope="col"><?= __('Category Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($product->url_rewrite as $urlRewrite): ?>
            <tr>
                <td><?= h($urlRewrite->id) ?></td>
                <td><?= h($urlRewrite->id_path) ?></td>
                <td><?= h($urlRewrite->request_path) ?></td>
                <td><?= h($urlRewrite->target_path) ?></td>
                <td><?= h($urlRewrite->is_system) ?></td>
                <td><?= h($urlRewrite->options) ?></td>
                <td><?= h($urlRewrite->category_id) ?></td>
                <td><?= h($urlRewrite->product_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'UrlRewrite', 'action' => 'view', $urlRewrite->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'UrlRewrite', 'action' => 'edit', $urlRewrite->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'UrlRewrite', 'action' => 'delete', $urlRewrite->id], ['confirm' => __('Are you sure you want to delete # {0}?', $urlRewrite->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Wishlists') ?></h4>
        <?php if (!empty($product->wishlists)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Customer Id') ?></th>
                <th scope="col"><?= __('Product Id') ?></th>
                <th scope="col"><?= __('Location Ip') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($product->wishlists as $wishlists): ?>
            <tr>
                <td><?= h($wishlists->id) ?></td>
                <td><?= h($wishlists->customer_id) ?></td>
                <td><?= h($wishlists->product_id) ?></td>
                <td><?= h($wishlists->location_ip) ?></td>
                <td><?= h($wishlists->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Wishlists', 'action' => 'view', $wishlists->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Wishlists', 'action' => 'edit', $wishlists->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Wishlists', 'action' => 'delete', $wishlists->id], ['confirm' => __('Are you sure you want to delete # {0}?', $wishlists->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
