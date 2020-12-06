<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?=h('Manage Plans')?></h3>
        <ul class="list-inline list-unstyled">
            <li><?=$this->Html->link(__('New Product'), ['controller' => 'Plans', 'action' => 'add', 'key', md5('products')], ['class' => 'btn btn-div-buy btn-1b'])?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'get'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
            <div class="no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'Plans', '?' => $queryString], ['class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link('Reset Filter', ['controller' => 'Plans'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table table-striped table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">ID</th>
                        <th>Name</th>
                        <th>SKU Code</th>
                        <th class="text-right">Price &nbsp;</th>
                        <th class="text-center">Duration</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id">
                        	<?=$this->Form->text('id', ['value' => $id, 'class' => 'form-control', 'placeholder' => 'Enter Id']);?>
						</td>
                        <td data-title="Name">
                        	<?=$this->Form->text('name', ['value' => $name, 'class' => 'form-control', 'placeholder' => 'Enter name']);?>
                        </td>
                        <td data-title="SKU Code">
                        	<?=$this->Form->text('sku', ['value' => $sku, 'class' => 'form-control', 'placeholder' => 'Enter sku code']);?>
                        </td>
                        <td data-title="Price">
                            <?=$this->Form->text('price', ['value' => $price, 'class' => 'form-control', 'placeholder' => 'Enter price']);?>
                        </td>
                        <td data-title="Duration" class="text-center">
                            <?=$this->Form->select('duration', $this->SubscriptionManager->planDuration, ['value' => $duration, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('is_active', $this->SubscriptionManager->siteStatus, ['value' => $isActive, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($plans as $value): ?>
                    <tr>
                        <td data-title="Id" class="text-center"><?=$value->id?></td>
                        <td data-title="Name"><?=h($value->name)?></td>
                        <td data-title="SKU Code"><?=h($value->sku)?></td>
                        <td data-title="Price" class="text-right">
                            <?=h($value->price.' '.$value->currency)?>
                        </td>
                        <td data-title="Duration" class="text-center"><?php echo $this->SubscriptionManager->planDuration[$value->duration] ?? 'N/A'; ?></td>
                        <td data-title="Status" class="text-center"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->is_active)))?></td>
                        <td data-title="Action" class="text-center">
                            <?=$this->Html->link('<i class="fa fa-pencil"></i>', ['action' => 'edit', $value->id, 'key', md5($value->id)], ['title' => 'Edit Products', 'escape' => false])?>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>

