<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3>
                Order # <?= h($order->order_id) ?> | <?= date('Y-m-d H:m:s',strtotime($order->created)) ?>
            </h3>
            <ul class="list-inline list-unstyled">
                <li>
                    <button type="button" class="btn btn-div-cart btn-1e">Back</button>
                </li>
                <li>
                    <button type="button" class="btn btn-div-buy btn-1b">Ship</button>
                </li>
                <li>
                    <button type="button" class="btn btn-div-buy btn-1b">Reorder</button>
                </li>
                <li>
                    <button type="button" class="btn btn-div-buy btn-1b">Reorder</button>
                </li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">
		
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
                    
            <form class="form-horizontal">
                
            <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
                <div class="col-sm-12 col-xs-12 flex_box no-padding responsive-mobile-table"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price + Tax</th>
                                    <th>Qty</th>
                                    <th>Tax Percentage</th>
                                    <th>Tax Amount</th>
                                    <th>Subtotal</th>
                                    <th>Discount</th>
                                    <th>Row Subtotal</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                <?php 	$counter = $totalQty = $subTotal = $discountTotal = $rowTotal = $totalTax = 0;
						foreach ($order->order_details as $value):
							$counter++;
							$totalQty += $value->qty;
							$subTotal += $value->price*$value->qty;
							$discountTotal += $value->discount;
							$rowTotal += $value->price*$value->qty - $value->discount;
							$totalTax += $value->tax_amount;
				?>                
                                <tr>
                                    <td data-title="Product"><span class="text-bold">SKU: <?= h($value->sku_code) ?></span> <?php echo $value->title.' ('.$value->size.')'; ?></td>
                                    <td data-title="Price + Tax"><?php echo $this->Admin->priceLogo.number_format($value->price,2); ?></td>
                                    <td data-title="Qty"><?php echo $value->qty;?></td>
                                    <td data-title="Tax Percentage"><?php echo $value->goods_tax;?></td>
                                    <td data-title="Tax Amount"><?php echo $this->Admin->priceLogo.number_format($value->tax_amount,2);?></td>
                                    <td data-title="Subtotal"><?php echo $this->Admin->priceLogo.number_format($value->price*$value->qty,2); ?></td>
                                    <td data-title="Discount"><?php echo $this->Admin->priceLogo.number_format($value->discount,2); ?></td>
                                    <td data-title="Row Subtotal"><?php echo $this->Admin->priceLogo.number_format($value->price*$value->qty - $value->discount, 2); ?></td>
                                </tr>
                <?php endforeach; ?>                
                                <tr class="total_div">
                                    <td data-title="Product">Total <?= $counter ?> products with <?= $totalQty ?></td>
                                    <td data-title="Price + Tax" class="hidden-xs"></td>
                                    <td data-title="Qty" class="hidden-xs"></td>
                                    <td data-title="Tax Percentage" class="hidden-xs">Subtotal</td>
                                    <td data-title="Tax Amount" class="text-bold"><?php echo $this->Admin->priceLogo.number_format($totalTax, 2); ?></td>
                                    <td data-title="Subtotal" class="text-bold"><?php echo $this->Admin->priceLogo.number_format($subTotal, 2); ?></td>
                                    <td data-title="Discount" class="text-bold"><?php echo $this->Admin->priceLogo.number_format($discountTotal,2); ?></td>
                                    <td data-title="Row Subtotal" class="text-bold"><?php echo $this->Admin->priceLogo.number_format($rowTotal,2); ?></td>
                                </tr>                                
                            </tbody>
                        </table>
                        
                        
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border"><!-- start of box_heading -->
                            <h3 class="box-title">Apply Coupon Code</h3>
                        </div><!-- end of box_heading -->
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group col-sm-8 col-xs-12">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Coupon Code</label>
                                    
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input type="text" value="<?= h($order->coupon_code) ?>" class="form-control" placeholder="" disabled />                                            
                                        </div><!-- /input-group -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Account Information</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group col-sm-8 col-xs-12">
                                    <label for="Emailid" class="col-sm-4 control-label">Email Id</label>
                                    
                                    <div class="col-sm-8">
                                        <input class="form-control" id="Emailid" name="Emailid" value="<?= h($order->email) ?>" placeholder="" type="email" disabled />
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Shipping Address</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="First Name" class="col-sm-3 control-label">First Name</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_firstname) ?>" placeholder="First Name" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Last Name" class="col-sm-3 control-label">Last Name</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_lastname) ?>" placeholder="Last Name" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Address" class="col-sm-3 control-label">Address</label>
                                    
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="3" placeholder="Enter ..." disabled><?= h($order->shipping_address) ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="City" class="col-sm-3 control-label">City</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_city) ?>" placeholder="City" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="State" class="col-sm-3 control-label">State</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_state) ?>" placeholder="state" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Country" class="col-sm-3 control-label">Country</label>       
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_country) ?>" placeholder="Country" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-3 control-label">Email</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_email) ?>" placeholder="Email" type="email" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Pincode" class="col-sm-3 control-label">Pincode</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_pincode) ?>" placeholder="Pincode" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Mobile" class="col-sm-3 control-label">Mobile</label>       
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->shipping_phone) ?>" placeholder="Mobile" type="text" disabled />
                                    </div>
                                </div>                                                                
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Billing Address</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="First Name" class="col-sm-3 control-label">First Name</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_firstname) ?>" placeholder="First Name" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Last Name" class="col-sm-3 control-label">Last Name</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_lastname) ?>" placeholder="Last Name" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Address" class="col-sm-3 control-label">Address</label>
                                    
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="3" placeholder="Enter ..." disabled><?= h($order->billing_address) ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="City" class="col-sm-3 control-label">City</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_city) ?>" placeholder="City" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="State" class="col-sm-3 control-label">State</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_state) ?>" placeholder="state" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Country" class="col-sm-3 control-label">Country</label>       
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_country) ?>" placeholder="Country" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Email" class="col-sm-3 control-label">Email</label>                                    
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_email) ?>" placeholder="Email" type="email" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Pincode" class="col-sm-3 control-label">Pincode</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_pincode) ?>" placeholder="Pincode" type="text" disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Mobile" class="col-sm-3 control-label">Mobile</label>       
                                    <div class="col-sm-9">
                                        <input class="form-control" value="<?= h($order->billing_phone) ?>" placeholder="Mobile" type="text" disabled />
                                    </div>
                                </div>                                                                
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Payment Mode</h3>
                        </div>                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-4 col-xs-12">
                                    	<label class="col-xs-12 no-padding"><?= h(ucfirst($order->payment_mode)) ?></label>
                                        <label class="no-padding col-xs-12">
                                            <i class="fa fa-rupee"></i><?= h($order->mode_amount) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Shipping Method</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-4 col-xs-12">
                                    	<label class="col-xs-12 no-padding"><?= h($order->ship_method) ?></label>
                                        <label class="no-padding col-xs-12">
                                            <i class="fa fa-rupee"></i><?= h($order->ship_amount) ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Order History</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Order Comments</label>
                                    
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of price_detail -->
                    <div class="box box-success"><!-- start of box_div -->
                        <div class="box-header with-border">
                            <h3 class="box-title">Order Totals</h3>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Subtotal</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $this->Admin->priceLogo.number_format($subTotal, 2); ?></p>
                            </div><!-- end of row_1 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Total Tax</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $this->Admin->priceLogo.number_format($totalTax, 2); ?></p>
                            </div><!-- end of row_1 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Discount(<?= $order->coupon_code ?>)</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $this->Admin->priceLogo.number_format($order->discount, 2); ?></p>
                            </div><!-- end of row_2 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Payment Mode Fee</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $this->Admin->priceLogo.number_format($order->mode_amount, 2); ?></p>
                            </div><!-- end of row_2 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding">Shipping Fee</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><?php echo $this->Admin->priceLogo.number_format($order->ship_amount, 2); ?></p>
                            </div><!-- end of row_2 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
                                <p class="col-sm-7 col-xs-7 no-padding-left xs-no-padding text-bold">Grand Total (Excl. Tax)</p>
                                <p class="col-sm-5 col-xs-5 no-padding-right xs-no-padding"><span class="text-cgreen"><?php echo $this->Admin->priceLogo.number_format($order->payment_amount, 2); ?></span></p>
                            </div><!-- end of row_3 -->
                            
                            <div class="col-sm-12 col-xs-12 no-padding text-right margin-top"><!-- start of row_3 -->
                                <button type="submit" class="btn btn-div-buy btn-1b">
                                	Submit Order
                                </button>
                            </div><!-- end of row_3 -->
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of price_detail -->
            </div><!-- end of middle_content -->
            </form>            
        </div><!-- end of tab -->
    </section>
    <!-- /.content -->