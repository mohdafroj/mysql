<?php echo $this->Element('Customers/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
		<?php echo $this->Element('Customers/left_menu'); ?>

        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
			<div class="tab-pane fade col-sm-12 col-xs-12 active in" id="tab_5"><!-- Wallet -->
				<?=$this->Form->create(null, ['type' => 'get'])?>
					<section class="content col-sm-12 col-xs-12">
						<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
							<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
								<ul class="list-unstyled list-inline pagination_div">
									<li>
										Page
										<span class="span_1">
											<?=$this->Paginator->prev(__('Prev'))?>
											<input type="text" class="form-control" value="<?=$this->Paginator->counter(['format' => __('{{page}}')])?>">
											<?=$this->Paginator->next(__('Next'))?>
										</span>
										of <?=$this->Paginator->counter(['format' => __('{{pages}}')])?> pages
									</li>
									<li>
										View
										<span class="span_1 span_2">
											<?=$this->Form->select('limit', $this->Admin->selectMenuOptions, ['value' => $this->Paginator->param('perPage'), 'default' => 50, 'empty' => false, 'onChange' => 'this.form.submit();', 'class' => 'form-control']);?>
										</span>
										per page
									</li>
									<li>Total <?=$this->Paginator->counter(['format' => __('{{count}}')])?> records found</li>
								</ul>
							</div><!-- end of pagination -->

							<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
							</div><!-- end of buttons -->
						</div><!-- end of pagination or buttons -->

						<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
							<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
								<thead>
									<tr>
										<th>#Id</th>
										<th>SKU</th>
										<th>Title</th>
										<th>Price</th>
										<th>Added On</th>
									</tr>
								</thead>
								<tbody>
									<tr><!-- start of row_2 -->
										<?php $i = 1;foreach ($wishlist as $value): ?>
											<tr>
												<td data-title="Id"><?=$value->product->id;?></td>
												<td data-title="SKU"><?=$value->product->sku_code?></td>
												<td data-title="Title">
									<?php $productPrices = $value->product->product_prices;
foreach ($productPrices as $price) {
    echo '<p>' . $price->title . '</p>';
}
?>
												</td>
												<td data-title="Price">
									<?php
foreach ($productPrices as $price) {
    echo '<p>' . $price->location->currency_logo . ' ' . $price->price . '(' . $price->location->title . ')</p>';
}
?>
												</td>
												<td data-title="Action On">
													<?php
echo $this->Admin->emptyDate($value->created);
?>
												</td>
											</tr>
										<?php endforeach;?>
									</tr><!-- end of row_2 -->
								</tbody>
							</table><?php //pr($wishlist);?>
						</div><!-- end of table -->
					</section>
				<?=$this->Form->end()?>
			</div><!-- end of product review -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>