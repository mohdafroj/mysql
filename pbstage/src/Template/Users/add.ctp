<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user, ['novalidate'=>true]) ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?php
            echo $this->Form->control('firstname');
            echo $this->Form->control('lastname');
            echo $this->Form->control('email');
            echo $this->Form->control('username');
            echo $this->Form->control('password');
            echo $this->Form->control('logdate');
            echo $this->Form->control('lognum');
            echo $this->Form->control('reload_acl_flag');
            echo $this->Form->control('is_active');
            echo $this->Form->control('extra');
            echo $this->Form->control('rp_token');
            echo $this->Form->control('rp_token_created_at');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
