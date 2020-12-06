<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3>
                Order # <?=h($order->id)?> | <?=date('M d, Y a', strtotime($order->created))?>
            </h3>
            <ul class="list-inline list-unstyled">
                <li>
                    <button type="button" onClick="history.go(-1);" class="btn btn-div-cart btn-1e">Back</button>
                </li>
				<?php
if ($order->status == 'accepted') {
    ?>
					<li>
						<?=$this->Html->link(__('Cancel Order'), ['action' => 'cancel', $order->id, 'key', md5($order->id)], ['onclick' => "return confirm('Are you sure that you want to cancel this order?')", 'class' => 'btn btn-div-buy btn-1b'])?>
					</li>
					<?php
}
?>
				<?php
if ($order->status == 'accepted') {
    ?>
					<li>
						<?=$this->Html->link(__('Delivered'), ['action' => 'delivered', $order->id, 'key', md5($order->id)], ['onclick' => "return confirm('Are you sure that you want to set the status of this order to Delivered?')", 'class' => 'btn btn-div-buy btn-1b'])?>
					</li>
					<?php
}
?>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
            <?php echo $this->element('Orders/left_menu'); ?>
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
                    <div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
                        <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->

							<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Customer Name
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
												<?php echo $order->customer->firstname . ' ' . $order->customer->lastname; ?>
                                            </p>
                                        </div><!-- end of row_1 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Email Id
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <a href="mailto:<?=$order->customer->email?>" class="text-cgreen"><?=$order->customer->email?></a>
                                            </p>
                                        </div><!-- end of row_2 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Customer Group
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                 <?php echo ucfirst($order->customer->is_group); ?>
                                            </p>
                                        </div><!-- end of row_3 -->
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Order Number
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                PC<?=$order->id?>
                                            </p>
                                        </div><!-- end of row_2 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Created At:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?=h($this->Admin->emptyDate($order->created));?>
                                            </p>
                                        </div><!-- end of row_1 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Modified At:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?=h($this->Admin->emptyDate($order->modified));?>
                                            </p>
                                        </div><!-- end of row_1 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Order Status
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?=ucfirst($order->status)?>
                                            </p>
                                        </div><!-- end of row_2 -->

                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                IP
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?=$order->transaction_ip?>
                                            </p>
                                        </div><!-- end of row_2 -->

                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Ship to Name:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-left xs-no-padding">
                                                <?=h($order->shipping_firstname . ' ' . $order->shipping_lastname)?><br />
                                            </p>
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Address:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-left xs-no-padding">
												<?=h($order->shipping_address)?>, <?=h($order->shipping_city)?>, <?=h($order->shipping_state)?><br />
												<?=h($order->shipping_country)?> (<?=h($order->shipping_pincode)?>)<br />
                                            </p>
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Email:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-left xs-no-padding">
												<?=h($order->shipping_email)?><br />
                                            </p>
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Mobile:
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-left xs-no-padding">
												<?=h($order->shipping_phone)?>
                                            </p>
                                        </div><!-- end of row_1 -->

                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-12 col-xs-12 text-center">
                                                <img src="data:image/png;base64,<?php echo $barcode['code']; ?>" width="250" height="120" />
                                            </p>
                                            <p class="col-sm-12 col-xs-12 text-center">
                                            <?php
                                                if ( $order->courier_id ==  3 ) {
                                                    echo '<a href="'.PC['DLYVERY']['track_package']. $barcode['tracking_code'] . '" target="_blank">' . $barcode['tracking_code'] . '</a>';
                                                } else {
                                                    echo $barcode['tracking_code'];
                                                }                                                
                                            ?>
                                            </p>
                                            <p class="col-sm-12 col-xs-12 text-center">
                                                <?php
if ($barcode['tracking_code'] > 0) {
    //echo $this->Html->link(__('<i class="fa fa-download"></i>'), ['action' => 'barcode', $barcode['tracking_code'], 'pdf', md5($barcode['tracking_code'])],['title'=>'Download Barcode as PDF','escape'=>false]);
}
?>
                                            </p>

                                        </div><!-- end of row_1 -->

                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-12 col-xs-12 flex_box no-padding responsive-mobile-table"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
										<thead>
											<tr>
												<th>Product</th>
												<th>Price</th>
												<th>Qty</th>
												<!--<th>Tax Percentage</th>
												<th>Tax Amount</th>
												<th>Subtotal</th>
												<th>Discount</th>-->
												<th>Row Subtotal</th>
											</tr>
										</thead>

										<tbody>
											<?php
$counter = $totalQty = $subTotal = $discountTotal = $rowTotal = 0;
$logo = $order->location->currency_logo ?? '';
foreach ($order->order_details as $value):
    $counter++;
    $totalQty += $value->quantity;
    $subTotal += $value->price * $value->quantity;
    $discountTotal += $value->discount;
    $rowTotal += $value->price * $value->quantity - $value->discount;
    ?>
																																																								<tr>
																																																									<td data-title="Product"><span class="text-bold">SKU: <?=h($value->sku_code)?></span> <?php echo $value->title . ' (' . $value->size . ')'; ?></td>
																																																									<td data-title="Price"><?php echo $logo . ' ' . number_format($value->price, 2); ?></td>
																																																									<td data-title="Quantity"><?php echo $value->quantity; ?></td>
																																																									<td data-title="Row Subtotal" class="text-right"><?php echo $logo . ' ' . number_format($value->price * $value->quantity - $value->discount, 2); ?></td>
																																																								</tr>
																																																						<?php endforeach;?>
											<tr class="total_div">
												<td data-title="Product" colspan="2">Total <?=$counter?> products with <?=$totalQty?></td>
												<td data-title="Sub Total" class="text-bold">Subtotal</td>
												<td data-title="Row Subtotal" class="text-bold text-right"><?php echo '<strong>' . $logo . '</strong> ' . number_format($rowTotal, 2); ?></td>
											</tr>
										</tbody>
									</table>
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Comments History</h3>
                                    </div>
                                    <?php //pr($order); ?>
                                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                        <?=$this->Form->create(null, ['class' => 'form-horizontal', 'method' => 'post', 'id' => 'comment_order_form', 'autocomplete' => 'off']);?>
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label for="inputEmail3" class="col-sm-3 control-label">Status</label>

                                                    <div class="col-sm-9">
                                                        <?=$this->Form->select('status', $this->SubscriptionManager->orderStatus, ['value' => $order->status, 'default' => '', 'empty' => true, 'class' => 'form-control'])?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="inputPassword3" class="col-sm-3 control-label">Comment</label>

                                                    <div class="col-sm-9">
                                                        <textarea name="comment" class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-offset-3 col-sm-10">
                                                        <button type="submit" class="btn btn-div-buy btn-1b">Submit</button>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="GivenBy" class="col-sm-2 control-label">GivenBy</label>
                                                    <label for="Status" class="col-sm-2 control-label">Status</label>
                                                    <label for="Comment" class="col-sm-8">Comment</label>
                                                </div>
                                <?php //pr($order->order_comments);
foreach ($order->order_comments as $value) {
    ?>
                                                <div class="form-group">
                                                    <span for="Given" class="col-sm-2"><?php echo $value->given_by; ?></span>
                                                    <span for="Status" class="col-sm-2"><?php echo $value->status; ?></span>
                                                    <span for="Comment" class="col-sm-8">:<?php echo $value->comment; ?></span>
                                                </div>
                                                <hr />
                                <?php }?>
                                            </div>
                                        <?=$this->Form->end();?>
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-success"><!-- start of box_div -->
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Order Totals</h3>
                                    </div>

                                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Subtotal</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $logo . ' ' . number_format($subTotal, 2); ?></p>
										</div><!-- end of row_1 -->

										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Discount</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $logo . ' ' . number_format($order->discount, 2); ?></p>
										</div><!-- end of row_2 -->

										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Payment Mode</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $order->payment_method->title; ?></p>
										</div><!-- end of row_2 -->

										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Payment Mode Fee</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $logo . ' ' . number_format($order->mode_amount, 2); ?></p>
										</div><!-- end of row_2 -->

										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Shipping Fee</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $logo . ' ' . number_format($order->ship_amount, 2); ?></p>
										</div><!-- end of row_2 -->

										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Grand Total</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><span class="text-cgreen"><?php echo $logo . ' ' . number_format($order->payment_amount, 2); ?></span></p>
							</div><!-- end of row_3 -->
                                        <?php if (!empty($order->tracking_code)) {?>
										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Track Order</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><a href="https://www.delhivery.com/track/package/<?php echo $order->tracking_code; ?>" target="_blank"></a><?php echo $order->tracking_code; ?></p>
										</div><!-- end of row_3 -->
							<?php }?>
							<?php if (count($couponData)) {?>
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Coupon Code:</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $couponData['coupon']; ?></p>
										</div><!-- end of row_3 -->
										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Free Shipping:</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo strtoupper($couponData['freeShip']); ?></p>
										</div><!-- end of row_3 -->
										<div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
											<p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Discount:</p>
											<p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $logo . ' ' . number_format($couponData['couponDiscount'], 2); ?></p>
										</div><!-- end of row_3 -->
							<?php }?>
									</div><!-- end of box_content -->

                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->
                        </div><!-- end of middle_content -->

                    </div><!-- end of content_1 -->
            </div><!-- end of right_part -->
        </div><!-- end of tab -->

    </section>
