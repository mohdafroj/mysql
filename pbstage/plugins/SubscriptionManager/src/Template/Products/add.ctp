<?php echo $this->Element('Products/top_menu'); ?>

<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
			<?=$this->Form->create($product, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-6 col-xs-6 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-3 control-label">SKU Code <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->text('Products.sku_code', ['class' => 'form-control', 'placeholder' => 'Enter sku code']);?>
											<span class="text-red">
												<?php
echo $error['sku_code']['_empty'] ?? '';
echo $error['sku_code']['length'] ?? '';
echo $error['sku_code']['charNum'] ?? '';
echo $error['sku_code']['skuCode'] ?? '';
?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">URL Key <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->text('Products.url_key', ['class' => 'form-control', 'placeholder' => 'Enter url key']);?>
											<span class="text-red">
												<?php
echo $error['url_key']['_empty'] ?? '';
echo $error['url_key']['urlKey'] ?? '';
echo $error['url_key']['charNum'] ?? '';
?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Quantity <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->number('Products.quantity', ['class' => 'form-control', 'style' => 'width:100%', 'placeholder' => 'Enter quantity']);?>
											<span class="text-red">
												<?php
echo $error['quantity']['_empty'] ?? '';
echo $error['quantity']['qtyMsg'] ?? '';
?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Cart Qty <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->number('Products.min_cart_qty', ['class' => 'form-control', 'style' => 'width:48%;float:left;margin-right:2%;', 'placeholder' => 'Min cart qty']);?>
											<?=$this->Form->number('Products.max_cart_qty', ['class' => 'form-control', 'style' => 'width:50%', 'placeholder' => 'Max cart qty']);?>
											<span class="text-red">
												<?php
echo $error['min_cart_qty']['_empty'] ?? '';
echo $error['min_cart_qty']['qtyMsg'] ?? '';
echo $error['max_cart_qty']['_empty'] ?? '';
echo $error['max_cart_qty']['qtyMsg'] ?? '';
echo $error['max_cart_qty']['qtyComp'] ?? '';
?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Qty Notify <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->number('Products.out_stock_qty', ['class' => 'form-control', 'style' => 'width:48%;float:left;margin-right:2%;', 'placeholder' => 'Out stock qty']);?>
											<?=$this->Form->number('Products.notify_stock_qty', ['class' => 'form-control', 'style' => 'width:50%', 'placeholder' => 'Notify stock qty']);?>
											<span class="text-red">
												<?php
echo $error['out_stock_qty']['_empty'] ?? '';
echo $error['out_stock_qty']['qtyMsg'] ?? '';
echo $error['notify_stock_qty']['_empty'] ?? '';
echo $error['notify_stock_qty']['qtyMsg'] ?? '';
?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Stock Availability <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.is_stock', $this->SubscriptionManager->productStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['is_stock']['inList']) ? $error['is_stock']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Discount(%)</label>
										<div class="col-sm-9">
										<?=$this->Form->text('Products.discount', ['class' => 'form-control', 'maxlength' => 5, 'style' => 'width:20%;text-align:right;', 'placeholder' => 'Enter discount']);?>
												<span class="text-red">
													<?php
echo $error['discount']['decimal'] ?? '';
echo $error['discount']['custom'] ?? '';
?>
												</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Validity </label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:50%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<?=$this->Form->text('Products.discount_from', ['id' => 'datepicker1', 'class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Valid from']);?>
												<span class="text-red">
												<?php
echo $error['discount_from']['dateTime'] ?? '';
?>
												</span>
											</div>
											<div class="input-group date" style="width:49%;">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<?=$this->Form->text('Products.discount_to', ['id' => 'datepicker2', 'class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Valid to']);?>
												<span class="text-red">
												<?php
echo $error['discount_to']['dateTime'] ?? '';
?>
												</span>
											</div>
											<span class="text-red">
												<?php
echo $error['discount_to']['discountTo'] ?? '';
?>
											</span>
										</div>
									</div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tag Line </label>
                                        <div class="col-sm-9">
											<div class="input-group date" style="width:38%;float:left;margin-right:1%;">
												<?=$this->Form->select('Products.tag_line', $this->SubscriptionManager->productTags, ['empty' => false, 'style' => 'cursor:pointer;', 'class' => 'form-control'])?>
											</div>
											<div class="input-group date" style="width:61%;">
												<label class="col-sm-4 control-label" style="padding-right:1px;">Title colour</label>
												<div class="col-sm-8" style="padding: 0 0 0 4px;">
													<?=$this->Form->text('Products.title_color', ['id' => 'colorpicker', 'class' => 'form-control', 'minlength'=>'7', 'maxlength'=>'7', 'placeholder' => 'choose color for title']);?>
												</div>
											</div>
										</div>
                                    </div>

                                </div>
                            </div><!-- end of box_content -->
                            <div class="col-sm-6 col-xs-6 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Size <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->text('Products.size', ['class' => 'form-control', 'style' => 'width:50%;float:left;margin-right:2%;', 'placeholder' => 'Size']);?>
                                        	<?=$this->Form->select('Products.unit', $this->SubscriptionManager->productSize, ['empty' => false, 'style' => 'width:48%;', 'class' => 'form-control'])?>
											<span class="text-red">
												<?php
echo $error['size']['_empty'] ?? '';
echo $error['size']['decimal'] ?? '';
?>
											</span>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Gender <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.gender', $this->SubscriptionManager->siteGender, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['gender']['inList']) ? $error['gender']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Product Type <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.product_type', $this->SubscriptionManager->productType, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['product_type']['inList']) ? $error['product_type']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Brand</label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.brand_id', $brands, ['empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['brand_id']['inList']) ? $error['brand_id']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.is_active', $this->SubscriptionManager->siteStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['is_active']['inList']) ? $error['is_active']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Perfume Type </label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.product_perfume_type', $this->SubscriptionManager->productPerfumeType, ['empty' => true, 'style' => 'width:100%;float:left;', 'class' => 'form-control'])?>
											(Incase of Perfume Category)
											<span class="text-red"><?=isset($error['product_perfume_type']['inList']) ? $error['product_perfume_type']['inList'] : null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Families</label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('Products.family_ids', $families, ['multiple' => true, 'empty' => true, 'style' => 'width:100%;height:150px;', 'class' => 'form-control'])?>
											<span class="text-red"><?=isset($error['family_id']['inList']) ? $error['family_id']['inList'] : null;?></span>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Sort Order</label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->number('Products.sort_order', ['class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Sort order number']);?>
											<span class="text-red">
												<?php
echo $error['sort_order']['_empty'] ?? '';
?>
											</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Sold</label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->number('Products.sold', ['class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Enter number of sold']);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Combo:</label>
                                        <div class="col-sm-3 radio">
                                        	<?=$this->Form->radio('Products.is_combo', [['value' => '0', 'text' => 'NO', 'id' => 'is_combo_no']], ['hiddenField' => false]);?>
                                        </div>
                                        <div class="col-sm-6 radio">
                                        	<?=$this->Form->radio('Products.is_combo', [['value' => '1', 'text' => 'YES', 'id' => 'is_combo_yes']], ['hiddenField' => false]);?>
                                        </div>
                                    </div>

                                </div>
                            </div><!-- end of box_content -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-2 control-label">Meta Title</label>
										<div class="col-sm-10">
											<?=$this->Form->text('Products.meta_title', ['class' => 'form-control', 'placeholder' => 'Enter meta title']);?>
										</div>
									</div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Keyword</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('Products.meta_keyword', ['class' => 'form-control', 'placeholder' => 'Enter meta keywords']);?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Description</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('Products.meta_description', ['class' => 'form-control', 'placeholder' => 'Enter meta description']);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Search Keywords</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('Products.search_keyword', ['class' => 'form-control', 'placeholder' => 'Enter search keywords']);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            <?=$this->Form->end();?>
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>
<?=$this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js')?>
<script>
	CKEDITOR.replace('short_description');
	CKEDITOR.replace('description');
</script>