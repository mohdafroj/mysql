<?php
    $this->Paginator->setTemplates(['templates'=>'admin-list']);
?>

<ul class="list-unstyled list-inline pagination_div">
    <li>
        Page 
        <span class="span_1">
            <?= $this->Paginator->prev(__('Prev')) ?>
            <input type="text" class="form-control" value="<?= $this->Paginator->counter(['format'=>__('{{page}}')]) ?>">
            <?= $this->Paginator->next(__('Next')) ?>
        </span>
        of <?= $this->Paginator->counter(['format'=>__('{{pages}}')]) ?> pages
    </li>
    <li>
        View
        <span class="span_1 span_2">
            <?= $this->Form->select('perPage', $this->Admin->selectMenuOptions, ['value'=>$this->Paginator->param('perPage'),'empty' => FALSE,'onChange'=>'this.form.submit();','class'=>'form-control']);?>
        </span>
        per page
    </li>
    <li>Total <?= $this->Paginator->counter(['format'=>__('{{count}}')]) ?> records found</li>
</ul>
