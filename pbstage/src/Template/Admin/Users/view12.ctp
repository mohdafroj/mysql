<div class="users view large-9 medium-8 columns content">
    <h3><?= __('Admin User Detail') ?></h3>
    <table class="vertical-table users view large-9 medium-8 columns content">
        <tr>
            <th scope="row"><?= __('Firstname') ?></th>
            <td><?= h($user->firstname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lastname') ?></th>
            <td><?= h($user->lastname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Username') ?></th>
            <td><?= h($user->username) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lognum') ?></th>
            <td><?= $this->Number->format($user->lognum) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Reload Acl Flag') ?></th>
            <td><?= $this->Number->format($user->reload_acl_flag) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Active') ?></th>
            <td><?= $this->Number->format($user->is_active) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Logdate') ?></th>
            <td><?= h($user->logdate) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rp Token Created At') ?></th>
            <td><?= h($user->rp_token_created_at) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Extra') ?></th>
            <td><?= h($user->extra) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rp Token') ?></th>
            <td><?= h($user->rp_token) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?> | <?= $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?></td>
        </tr>
    </table>
</div>
