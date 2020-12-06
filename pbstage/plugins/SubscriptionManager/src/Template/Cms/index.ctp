<?php echo $this->element('Admin/Cms/top_menu');
?>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'get', 'id' => 'market_email_form'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->element('Admin/pagination'); ?>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Reset Filter', ['action' => 'index'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('name', 'Name')?></th>
                        <th><?=$this->Paginator->sort('title', 'Title')?></th>
                        <th><?=$this->Paginator->sort('url_key', 'Url Key')?></th>
                        <th><?=$this->Paginator->sort('created', 'Created')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('is_active', 'Status')?></th>
                        <th class="text-center"><?=__('Actions')?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="Name">
                        	<?=$this->Form->text('name', ['value' => $name, 'class' => 'form-control', 'placeholder' => 'Enter Name']);?>
                        </td>
                        <td data-title="Title">
                        	<?=$this->Form->text('title', ['value' => $cmsTitle, 'class' => 'form-control', 'placeholder' => 'Enter title']);?>
                        </td>
                        <td data-title="Url Key">
                            <?=$this->Form->text('url_key', ['value' => $url_key, 'class' => 'form-control', 'placeholder' => 'Enter url key']);?>
                        </td>
                        <td data-title="Created"></td>
                        <td data-title="Status">
                            <?=$this->Form->select('is_active', $this->Admin->siteStatus, ['value' => $status, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($cms as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$this->Number->format($value->id)?></td>
                        <td data-title="Name"><?=h($value->name)?></td>
                        <td data-title="Title"><?=h($value->title)?></td>
                        <td data-title="Url Key"><?=h($value->url_key)?></td>
                        <td class="text-center" data-title="Created"><?=h($this->Admin->emptyDate($value->created));?></td>
                        <td class="text-center" data-title="Status"><?=h($this->Admin->checkValue(ucfirst($value->is_active)))?></td>
                        <td class="text-right" data-title="Action">
                        <?=$this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'pages', $value->id, 'key', md5($value->id)], ['escape' => false, 'class' => 'btn btn-default btn-xs'])?>
                        &nbsp;&nbsp;&nbsp;
                        <?=$this->Form->postLink(__('<i class="fa fa-trash"></i>'), ['action' => 'delete', $value->id, 'key', md5($value->id)], ['block' => false, 'method'=>'delete', 'escape' => false, 'class' => 'btn btn-default btn-xs', 'confirm' => __('Are you sure you want to delete {0}?', '"'.$value->title.'" static page' )])?>
                        </td>
                    </tr>
            <?php endforeach;
if (!count($cms)):
?>
                    <tr>
                        <td colspan="8" class="text-center"><strong>Sorry, no record found!</strong></td>
                    </tr>
            <?php endif?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>
