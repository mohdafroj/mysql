<?php echo $this->Element('Products/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'get'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->Element('pagination'); ?>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'Products', '?' => $queryString], ['class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link('Reset Filter', ['controller' => 'Products'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table table-striped table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('sku_code', 'SKU Code')?></th>
                        <th><?=$this->Paginator->sort('brand_id', 'Brand')?></th>
                        <th>Category</th>
                        <th><?=$this->Paginator->sort('url_key', 'Url Key')?></th>
                        <th><?=$this->Paginator->sort('size', 'Size')?></th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Discount(%)</th>
                        <th>Quantity</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id">
                        	<?=$this->Form->text('id', ['value' => $id, 'class' => 'form-control', 'placeholder' => 'Enter Id']);?>
						</td>
                        <td data-title="SKU Code">
                        	<?=$this->Form->text('sku_code', ['value' => $skuCode, 'class' => 'form-control', 'placeholder' => 'Enter sku code']);?>
                        </td>
                        <td data-title="Brand">
                            <?=$this->Form->select('brand_id', $brandsList, ['value' => $brandId, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Category">
                            <?=$this->Form->select('category_id', $cateList, ['value' => $categoryId, 'empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Url">
                        	<?=$this->Form->text('url_key', ['value' => $url, 'class' => 'form-control', 'placeholder' => 'Enter url key']);?>
                        </td>
                        <td data-title="Size">
                        	<?=$this->Form->text('size', ['value' => $size, 'class' => 'form-control', 'placeholder' => 'Enter size']);?>
                            <?=$this->Form->select('unit', $this->SubscriptionManager->productSize, ['value' => $sizeUnit, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Name">
                        	<?=$this->Form->text('name', ['value' => $name, 'class' => 'form-control', 'placeholder' => 'Enter name']);?>
                        </td>
                        <td data-title="Price">
                            <?=$this->Form->text('price', ['value' => $price, 'class' => 'form-control', 'placeholder' => 'Enter price']);?>
                        </td>
                        <td data-title="Discount">
                            <?=$this->Form->text('discount', ['value' => $discount, 'class' => 'form-control', 'placeholder' => 'Enter discount']);?>
                        </td>
                        <td data-title="Qty">
                        	<?=$this->Form->text('quantity', ['value' => $quantity, 'class' => 'form-control', 'placeholder' => 'Enter quantity']);?>
                        </td>
                        <td data-title="Gender">
                            <?=$this->Form->select('gender', $this->SubscriptionManager->siteGender, ['value' => $gender, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('is_active', $this->SubscriptionManager->siteStatus, ['value' => $isActive, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($products as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$value->id?></td>
                        <td data-title="SKU Code"><?=h($value->sku_code)?></td>
                        <td data-title="Brand"><?php echo $value->brand->title ?? 'NA'; ?></td>
                        <td data-title="Category">
							<?php
foreach ($value->product_categories as $v) {
    echo isset($cateList[$v->category_id]) ? $cateList[$v->category_id] . '<br />' : '';
}
?>
						</td>
                        <td data-title="Url"><?=h($this->SubscriptionManager->checkValue($value->url_key))?></td>
                        <td data-title="Size"><?=h($this->SubscriptionManager->checkValue($value->size) . " " . strtoupper($value->unit))?></td>
                        <td data-title="Name">
<?php
foreach ($value->product_prices as $n) {
    echo '<p>' . $n->name . '</p>';
}?>
                        </td>
                        <td data-title="Price" class="text-right">
                        <?php
foreach ($value->product_prices as $n) {
    echo '<p>' . $n->location->currency_logo . ' ' . number_format($n->price, 2) . ' (' . $n->location->code . ')</p>';
}?>
                        </td>
                        <td data-title="Discount" class="text-right"><?=$value->discount?>&nbsp;%&nbsp;</td>
                        <td data-title="Quantity" class="text-right"><?=$value->quantity;?>&nbsp;&nbsp;</td>
                        <td data-title="Gender"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->gender)));?></td>
                        <td data-title="Status"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->is_active)))?></td>
                        <td data-title="Action" class="text-center">
                            <?=$this->Html->link('<i class="fa fa-pencil"></i>', ['action' => 'edit', $value->id, 'key', md5($value->id)], ['title' => 'Edit Products', 'escape' => false])?>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <div class="col-sm-12 col-xs-12 no-padding">
            <div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->Element('pagination'); ?>
            </div><!-- end of pagination -->
        </div>
        <?=$this->Form->end()?>
</section>

