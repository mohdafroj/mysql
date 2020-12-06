<?php echo $this->Element('Products/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
		    <?php echo $this->Element('Products/sub_menu'); ?>
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
			<?=$this->Form->create($product, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
							<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <table class="col-xs-12 table table-striped table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>SKU Code</th>
                        <th>Title</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Created</th>
                        <th>Modified</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
           <?php foreach ($productList as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$value->product->id?></td>
                        <td data-title="SKU Code"><?=h($value->product->sku_code)?></td>
                        <td data-title="Title"><?php echo $value->title ?? 'NA'; ?></td>
                        <td data-title="Name"><?php echo $value->name ?? 'NA'; ?></td>
                        <td data-title="Price">
<?php
echo '<p>' . $value->location->currency_logo . ' ' . number_format($value->price, 2) . ' (' . $value->location->title . ')</p>';
?>
<span class="tooltiptext">
<?php
echo 'C-'. $value->location->currency_logo . ' ' . number_format($value->cross, 2) 
. ', T1-' .$value->location->currency_logo . ' ' . number_format($value->price1, 2)
. ', T2-' .$value->location->currency_logo . ' ' . number_format($value->price2, 2)
. ', T3-' .$value->location->currency_logo . ' ' . number_format($value->price3, 2)
;
?>
</span>

                        </td>
                        <td data-title="Created"><?=$this->Admin->emptyDate($value->created)?></td>
                        <td data-title="Modified"><?=$this->Admin->emptyDate($value->modified)?></td>
                        <td data-title="Status"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->is_active)))?></td>
                        <td data-title="Action" class="text-center">
                            <?=$this->Html->link('<i class="fa fa-pencil"></i>', ['action' => 'prices', $id, 'key', md5($id), $value->id], ['title' => 'Edit this record', 'escape' => false])?>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href="#" onClick="deletePriceRow(<?=$value->id?>, '<?=$value->title?>')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
                            </div><!-- end of box_content -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="col-sm-3 control-label">Name <span class="text-red">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=$this->Form->text('name', ['class' => 'form-control', 'placeholder' => 'Enter name']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['name']['_empty'] ?? null;
                                                            echo $error['name']['length'] ?? null;
                                                            echo $error['name']['charNum'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="col-sm-3 control-label">Title <span class="text-red">*</span></label>
                                                <div class="col-sm-9">
                                                    <?=$this->Form->text('title', ['class' => 'form-control', 'placeholder' => 'Enter title']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['title']['_empty'] ?? null;
                                                            echo $error['title']['length'] ?? null;
                                                            echo $error['title']['charNum'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="col-sm-3 control-label">Cross Price</label>
                                                <div class="col-sm-3">
                                                    <?=$this->Form->text('cross', ['class' => 'form-control', 'placeholder' => 'Enter cross price']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['cross']['numeric'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                                <label class="col-sm-3 control-label">Tier1 Price</label>
                                                <div class="col-sm-3">
                                                    <?=$this->Form->text('price1', ['class' => 'form-control', 'placeholder' => 'Enter tier 1 price']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['price1']['numeric'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="col-sm-3 control-label">Tier2 Price</label>
                                                <div class="col-sm-3">
                                                    <?=$this->Form->text('price2', ['class' => 'form-control', 'placeholder' => 'Enter tier 2 price']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['price2']['numeric'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                                <label class="col-sm-3 control-label">Tier3 Price</label>
                                                <div class="col-sm-3">
                                                    <?=$this->Form->text('price3', ['class' => 'form-control', 'placeholder' => 'Enter tier 3 price']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['price3']['numeric'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
									<div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="col-sm-3 control-label">Price <span class="text-red">*</span></label>
                                                <div class="col-sm-4">
                                                    <?=$this->Form->text('price', ['class' => 'form-control', 'style' => 'width:100%', 'placeholder' => 'Enter price']);?>
                                                    <span class="text-red">
                                                        <?php
                                                            echo $error['price']['_empty'] ?? null;
                                                            echo $error['price']['decimal'] ?? null;
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="col-sm-5">
                                                    <?=$this->Form->hidden('product_id', ['value' => $id]);?>
                                                    <?=$this->Form->select('location_id', $locations, ['class' => 'form-control']);?>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="col-sm-6">
                                                    <?=$this->Form->select('is_active', ['active' => 'Active', 'in_active' => 'In Active'], ['class' => 'form-control']);?>
                                                </div>
                                            </div>
                                        </div>
									</div>
                                </div>
                            </div><!-- end of box_content -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Short Description</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('short_description', ['id' => 'short_description', 'class' => 'form-control', 'placeholder' => 'Enter short description']);?>
                                            <span class="text-red">
													<?php
echo $error['short_description']['_empty'] ?? null;
echo $error['short_description']['length'] ?? null;
?>
												</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('description', ['id' => 'description', 'class' => 'form-control', 'placeholder' => 'Enter description']);?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-3">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=$this->Html->link('Reset', ['action' => 'prices', $id, 'key', md5($id)], ['title' => 'Reset', 'class' => 'btn btn-div-buy btn-1b'])?>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            <?=$this->Form->end();?>
            <?=$this->Form->create($product, ['id'=>'priceForm', 'method'=>'delete','enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
                <?=$this->Form->hidden('price_id', ['id'=>'priceId','value' =>0]);?>
            <?=$this->Form->end();?>
            
            </div><!-- end of tab -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>
<?=$this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js')?>
<script>
	CKEDITOR.replace('short_description');
	CKEDITOR.replace('description');

    function deletePriceRow(priceId, priceTitle){
        if( confirm("Sure you want to delete "+priceTitle+ " record!") ){
            $("#priceId").val(priceId);
            $("#priceForm").submit();
        }
        return false;
    }
</script>