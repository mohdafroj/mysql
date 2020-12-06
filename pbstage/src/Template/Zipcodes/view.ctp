<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Zipcode $zipcode
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Zipcode'), ['action' => 'edit', $zipcode->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Zipcode'), ['action' => 'delete', $zipcode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $zipcode->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Zipcodes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Zipcode'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="zipcodes view large-9 medium-8 columns content">
    <h3><?= h($zipcode->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Zipcode') ?></th>
            <td><?= h($zipcode->zipcode) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Prepaid') ?></th>
            <td><?= h($zipcode->prepaid) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Cod') ?></th>
            <td><?= h($zipcode->cod) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('City') ?></th>
            <td><?= h($zipcode->city) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('State') ?></th>
            <td><?= h($zipcode->state) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($zipcode->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($zipcode->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($zipcode->modified) ?></td>
        </tr>
    </table>
</div>
