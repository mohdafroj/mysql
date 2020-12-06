<?php
echo $this->Element('Pgs/top_menu');
?>

<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'post'])?>
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th class="text-center"><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('title', 'Title')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('code', 'Code')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('fees', 'Fees')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('active_default', 'Default')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('sort_order', 'Sort Order')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('created', 'Created')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('modified', 'Modified')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('status', 'Status')?></th>
                        <th class="text-center"><?=__('Actions')?></th>
                    </tr>
                </thead>
                <tbody>
           <?php foreach ($pgs as $value): ?>
                    <tr>
                        <td class="text-center" data-title="Id"><?=$this->Number->format($value->id)?></td>
                        <td data-title="Title" title="<?=h($value->message)?>"><?=h($value->title)?></td>
                        <td class="text-center" data-title="Code"><?= $value->code?></td>
                        <td class="text-center" data-title="Fees"><?=$this->SubscriptionManager->priceLogo . $this->Number->format($value->fees)?></td>
                        <td class="text-center" data-title="Default"><input type="radio" name="id" onchange="changeDefault();" value="<?php echo $value->id; ?>" style="cursor:pointer;" <?php echo $value->active_default ? 'checked' : ''; ?> ></td>
                        <td class="text-center" data-title="Sort Order"><?=h($value->sort_order)?></td>
                        <td class="text-center" data-title="Created"><?=$this->SubscriptionManager->emptyDate($value->created);?></td>
                        <td class="text-center" data-title="Modified"><?=$this->SubscriptionManager->emptyDate($value->modified);?></td>
                        <td class="text-center" data-title="Status"><?php echo ($value->status) ? 'Active' : 'Inactive'; ?></td>
                        <td class="text-center" data-title="Action">
                            <?=$this->Html->link(__('<i class="fa fa-pencil"></i>'), ['action' => 'edit', $value->id, 'key', md5($value->id)], ['title' => 'Edit Method', 'escape' => false])?>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>

<script>
    function changeDefault(){
        var form = document.forms[0];
        form.submit();
    }
</script>

