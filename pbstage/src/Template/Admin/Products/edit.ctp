<?php echo $this->Element('Admin/Products/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
		    <?php echo $this->Element('Admin/Products/sub_menu');?>
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
			<?= $this->Form->create($product, ['enctype'=>'multipart/form-data','class' => 'form-horizontal', 'novalidate' => true]); ?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-6 col-xs-6 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Name <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Products.name', ['class'=>'form-control', 'placeholder'=>'Enter name']); ?>
											<span class="text-red">
												<?php
													echo $error['name']['_empty'] ?? NULL; 
													echo $error['name']['length'] ?? NULL; 
													echo $error['name']['charNum'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Product Title <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Products.title', ['class'=>'form-control', 'placeholder'=>'Enter title']); ?>
											<span class="text-red">
												<?php
													echo $error['title']['_empty'] ?? NULL; 
													echo $error['title']['length'] ?? NULL; 
													echo $error['title']['charNum'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">SKU Code <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('Products.sku_code', ['class'=>'form-control', 'placeholder'=>'Enter sku code']); ?>
											<span class="text-red">
												<?php
													echo $error['sku_code']['_empty'] ?? NULL; 
													echo $error['sku_code']['length'] ?? NULL;
													echo $error['sku_code']['charNum'] ?? NULL; 
												?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">URL Key <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('Products.url_key', ['class'=>'form-control', 'placeholder'=>'Enter url key']); ?>
											<span class="text-red">
												<?php
													echo $error['url_key']['_empty'] ?? NULL; 
													echo $error['url_key']['urlKey'] ?? NULL;
													echo $error['url_key']['charNum'] ?? NULL; 
												?>
											</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Price <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
												<?= $this->Form->text('Products.price', ['class'=>'form-control', 'style'=>'width:100%', 'placeholder'=>'Enter price']); ?>
												<span class="text-red">
													<?php
														echo $error['price']['_empty'] ?? NULL; 
														echo $error['price']['decimal'] ?? NULL;
													?>
												</span>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label">Cost Price</label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
												<?= $this->Form->text('Products.cost_price', ['class'=>'form-control', 'style'=>'width:100%', 'placeholder'=>'Enter cost price']); ?>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label">Quantity <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->number('Products.qty', ['class'=>'form-control', 'style'=>'width:100%', 'placeholder'=>'Enter quantity']); ?>
											<span class="text-red">
												<?php
													echo $error['qty']['_empty'] ?? NULL; 
													echo $error['qty']['qtyMsg'] ?? NULL;
												?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Cart Qty <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->number('Products.min_cart_qty', ['class'=>'form-control', 'style'=>'width:48%;float:left;margin-right:2%;', 'placeholder'=>'Min cart qty']); ?>
											<?= $this->Form->number('Products.max_cart_qty', ['class'=>'form-control', 'style'=>'width:50%', 'placeholder'=>'Max cart qty']); ?>
											<span class="text-red">
												<?php
													echo $error['max_cart_qty']['_empty'] ?? NULL; 
													echo $error['max_cart_qty']['qtyMsg'] ?? NULL;
													echo $error['max_cart_qty']['qtyComp'] ?? NULL;
												?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Qty Notify <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->number('Products.out_stock_qty', ['class'=>'form-control', 'style'=>'width:48%;float:left;margin-right:2%;', 'placeholder'=>'Out stock qty']); ?>
											<?= $this->Form->number('Products.notify_stock_qty', ['class'=>'form-control', 'style'=>'width:50%', 'placeholder'=>'Notify stock qty']); ?>
											<span class="text-red">
												<?php
													echo $error['out_stock_qty']['_empty'] ?? NULL; 
													echo $error['out_stock_qty']['qtyMsg'] ?? NULL;
													echo $error['notify_stock_qty']['_empty'] ?? NULL; 
													echo $error['notify_stock_qty']['qtyMsg'] ?? NULL;
												?>
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Stock Availability <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.is_stock', $this->Admin->productStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['is_stock']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Goods Tax <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.goods_tax', $this->Admin->productTax, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['goods_tax']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Cross Price </label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
												<?= $this->Form->text('Products.offer_price', ['class'=>'form-control', 'style'=>'width:100%', 'placeholder'=>'Cross price']); ?>
												<span class="text-red">
													<?php
														echo $error['offer_price']['decimal'] ?? NULL;													
													?>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Validity </label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:50%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('Products.offer_from', ['id'=>'datepicker1','class'=>'form-control','style'=>'width:100%;','placeholder'=>'Valid from']); ?>
												<span class="text-red">
												<?php
													echo $error['offer_from']['dateTime'] ?? NULL;
												?>
												</span>
											</div>
											<div class="input-group date" style="width:49%;">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('Products.offer_to', ['id'=>'datepicker2','class'=>'form-control','style'=>'width:100%;','placeholder'=>'Valid to']); ?>
												<span class="text-red">
												<?php
													echo $error['offer_to']['dateTime'] ?? NULL;
												?>
												</span>
											</div>
											<span class="text-red">
												<?php
													echo $error['offer_to']['offerTo'] ?? NULL;													
												?>
											</span>											
										</div>
									</div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tag Line </label>
                                        <div class="col-sm-9">
											<?= $this->Form->select('Products.tag_line', $this->Admin->productTags, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Link Codes</label>
										<div class="col-sm-9">
											<?= $this->Form->text('Products.combo_code', ['class'=>'form-control', 'style'=>'width:48%;float:left;margin-right:2%;', 'placeholder'=>'Enter code for combo']); ?>
											<?= $this->Form->text('Products.refill_code', ['class'=>'form-control', 'style'=>'width:50%', 'placeholder'=>'Enter code for refill']); ?>
										</div>
									</div>
                                                                        
                                </div>
                            </div><!-- end of box_content -->
                            <div class="col-sm-6 col-xs-6 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Size <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Products.size', ['class'=>'form-control', 'style'=>'width:50%;float:left;margin-right:2%;', 'placeholder'=>'Size']); ?>
                                        	<?= $this->Form->select('Products.size_unit', $this->Admin->productSize, ['empty'=>false,'style'=>'width:48%;','class'=>'form-control'])?>
											<span class="text-red">
												<?php
													echo $error['size']['_empty'] ?? NULL; 
													echo $error['size']['decimal'] ?? NULL;
												?>
											</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Dead Weight <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Products.dead_weight', ['class'=>'form-control', 'style'=>'width:50%;float:left;margin-right:2%;', 'placeholder'=>'Enter in grms']); ?>
                                        	<?= $this->Form->select('Products.box_weight', $this->Admin->productBoxWeight, ['empty'=>false,'style'=>'width:48%;','class'=>'form-control'])?>
											<span class="text-red">
												<?php
													echo $error['dead_weight']['_empty'] ?? NULL; 
													echo $error['dead_weight']['decimal'] ?? NULL;
												?>
											</span>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Gender <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.gender', $this->Admin->siteGender, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['gender']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Product Type <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.product_type', $this->Admin->productType, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['product_type']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Brand</label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.brand_id', $brands, ['empty'=>true,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['brand_id']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.is_active', $this->Admin->siteStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['is_active']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Perfume Type </label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.product_perfume_type', $this->Admin->productPerfumeType, ['empty'=>true,'style'=>'width:100%;float:left;','class'=>'form-control'])?>
											(Incase of Perfume Category)
											<span class="text-red"><?= $error['product_perfume_type']['inList'] ?? NULL;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Families</label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Products.family_ids', $families, ['multiple'=>true,'empty'=>true,'style'=>'width:100%;height:150px;','class'=>'form-control'])?>
											<span class="text-red"><?= $error['family_id']['inList'] ?? NULL;?></span>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Sort Order</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->number('Products.sort_order', ['class'=>'form-control', 'style'=>'width:100%;', 'placeholder'=>'Sort order number']); ?>
											<span class="text-red">
												<?php
													echo $error['sort_order']['_empty'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Best Seller</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->number('Products.best_seller', ['class'=>'form-control', 'style'=>'width:100%;', 'placeholder'=>'Best seller number']); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Combo:</label>
                                        <div class="col-sm-3 radio">
                                        	<?= $this->Form->radio('Products.is_combo', [['value'=>'0','text'=>'NO','id'=>'is_combo_no']],['hiddenField'=>false]); ?>
                                        </div>
                                        <div class="col-sm-6 radio">
                                        	<?= $this->Form->radio('Products.is_combo', [['value'=>'1','text'=>'YES','id'=>'is_combo_yes']],['hiddenField'=>false]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Short Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Products.short_description', ['id'=>'short_description', 'class'=>'form-control', 'placeholder'=>'Enter short description']); ?>
                                        </div>
                                    </div>
									
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Products.description', ['id'=>'description', 'class'=>'form-control', 'placeholder'=>'Enter description']); ?>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-2 control-label">Meta Title</label>
										<div class="col-sm-10">
											<?= $this->Form->text('Products.meta_title', ['class'=>'form-control', 'placeholder'=>'Enter meta title']); ?>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Keyword</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Products.meta_keyword', ['class'=>'form-control', 'placeholder'=>'Enter meta keywords']); ?>
                                        </div>
                                    </div>
									
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Products.meta_description', ['class'=>'form-control', 'placeholder'=>'Enter meta description']); ?>
                                        </div>
                                    </div>									
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Search Keywords</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Products.search_keyword', ['class'=>'form-control', 'placeholder'=>'Enter search keywords']); ?>
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
            <?= $this->Form->end(); ?>
            </div><!-- end of tab -->
        </div><!-- end of right_part -->            
    </div><!-- end of tab -->
</section>
<?= $this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js') ?>
<script>
	CKEDITOR.replace('short_description');
	CKEDITOR.replace('description');
</script>