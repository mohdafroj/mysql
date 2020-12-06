<style>
.content .invoice{position:relative; left:50%; transform:translateX(-50%);}
.invoice_row{border-width:1px 1px 0 1px; border-style:solid; border-color:#ccc; padding:0;}
.invoice_row:last-child{border-width:1px 1px 1px 1px; border-style:solid; border-color:#ccc;}
.custom_barcode{padding:8px 15px 0;}
.custom_barcode:nth-child(2){border-width:0px 1px; border-style:solid; border-color:#ccc; padding:0;}
.custom_barcode p img{width:140px;}
.custom_barcode span{display:inline-block; font-size:25px; color:rgba(0,0,0,0.7); text-transform:uppercase;}
.custom_barcode p.invoice_method{font-size:16px;}
.custom_barcode p.invoice_method b{font-size:20px;}
.custom_barcode span.pull-right{font-weight:bold;}
.custom_barcode p.D_logo{border-bottom:1px solid #ccc; padding:8px 15px;}
.custom_barcode .order_information{padding:8px 15px;}
.custom_barcode .order_information p{margin:0;}
.invoice_row .invoice-col{padding:8px 15px;}
.invoice_row .col-sm-6:first-child.invoice-col{border-right:1px solid #ccc;}
.invoice_row .table > tbody > tr > td, .invoice_row .table > tbody > tr > th, .invoice_row .table > tfoot > tr > td, .invoice_row .table > tfoot > tr > th, .invoice_row .table > thead > tr > td, .invoice_row .table > thead > tr > th{ padding:8px 15px;}
.invoice_signature{margin:0;}
.invoice_signature img{width:120px;}
.text_memo{font-size:20px; font-weight:bold; text-align:center; margin:0; color:rgba(0,0,0,0.7);}
.shipping_billing{width:50%; float:left;}
.signature_left{width:75%; float:left;}
.signature_right{width:25%; float:left;}						
				</style>											
<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3>
                Order # <?= h($order->id) ?> | <?= date('M d, Y',strtotime($order->created)) ?>
            </h3>
			<ul class="list-inline list-unstyled">
				<?php
				if( count($invoice) > 0 )
				{
					?>
					<li>
						<?= $this->Html->link(__('Get PDF'), ['controller' => 'Invoices', 'action' => 'getPdf', $invoice->id, 'pdf', md5($invoice->id)],['class' => 'btn btn-div-buy btn-1b']) ?>
					</li>
					<?php
				}
				?>
                <li>
					<?= $this->Html->link(__('Back'), ['action' => 'index'],['class' => 'btn btn-div-cart btn-1b']) ?>
                </li>
				<?php
				/*if( count($invoice) == 0 )
				{
					?>			
					<li>
						<button type="button" class="btn btn-div-buy btn-1b">Create</button>
					</li>
					<?php
				}
				else
				{
					?>			
					<li>
						<button type="button" class="btn btn-div-buy btn-1b">Delete</button>
					</li>
					<?php
				}*/
				?>			
            </ul>
        </div><!-- end of inner_heading -->
</section>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">		
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->element('Admin/Orders/left_menu');?>                  
		<div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->                
				<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_2 -->
					<!-- Main content -->
					<section class="content col-sm-12 col-xs-12 no-padding">
						<?php
						if( count($invoice) > 0 )
						{
							?>
							<div class="col-sm-8 col-xs-12 invoice">
								<!-- title row -->
								<div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
									<p class="text_memo">Tax Invoice / Cash Memo</p>
								</div><!-- end of upper_part -->
								<!-- /.col -->
								
								<!-- title row -->
								<div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
									<div class="col-sm-4 col-xs-12 custom_barcode"><!-- start of custom scan -->
										<?php
										$barcode		= '';
										$oid_barcode	= '';
										if($invoice->packing_slip != '')
										{
											$packing_slip	= unserialize($invoice->packing_slip);
											if(isset($packing_slip['packages']) && count($packing_slip['packages']) > 0)
											{
												$sort_code		= $packing_slip['packages'][0]['sort_code'];
												$barcode		= $packing_slip['packages'][0]['barcode'];
												$oid_barcode	= $packing_slip['packages'][0]['oid_barcode'];
											}
										}
										if($barcode != '')
										{
											?>
											<p class="text-center">
												<img src="<?php echo $barcode;?>" alt="" class="img-responsive center-block" style="width:90%; max-width:100px;">
											</p>
											<span class="pull-right"><?php echo $sort_code;?></span>
											<?php
										}
										?>
									</div><!-- end of custom scan -->
									
									<div class="col-sm-4 col-xs-12 custom_barcode"><!-- start of Logo_part -->
										<p class="D_logo">
											<?php echo $this->Html->image('invoice/fzsd.jpg', ['alt'=>'Delhivery', 'class'=>'img-responsive center-block']);?>
										</p>
										<div class="order_information">
											<p>
												<b>Order Id :</b> <?= h($order->id) ?>
											</p>
											<p>
												<b>Invoice No. :</b> <?= h($invoice->invoice_number) ?>
											</p>
											<p>
												<b>Invoice Date :</b> <?= date('M d, Y',strtotime($invoice->created)) ?>
											</p>
										</div>
									</div><!-- end of Logo_part -->
									
									<div class="col-sm-4 col-xs-12 custom_barcode"><!-- start of delhivery scan -->
										<?php
										if($oid_barcode != '')
										{
											?>
											<p class="text-center">
												<img src="<?php echo $oid_barcode;?>" alt="" class="img-responsive center-block">
											</p>
											<?php
										}
										?>
										<p class="invoice_method">
											Pay Mode : <b><?php echo ucfirst($invoice->payment_mode); ?></b>
										</p>
									</div><!-- end of delhivery scan -->
								</div><!-- end of upper_part -->
								<!-- /.col -->
								
								<!-- info row -->
								<div class="col-sm-12 col-xs-12 invoice_row">
									<div class="col-sm-4 col-xs-12 invoice-col">
										<b>Shipped By</b>
										<address class="no-margin">
											<?php echo $this->Html->image('invoice/pb_logo.jpg', ['alt'=>'PB Logo', 'class'=>'img-responsive center-block']);?>
										</address>
									</div>
									<!-- /.col -->
									<div class="col-sm-4 col-xs-12 invoice-col">
										<b>Perfume Booth Private Limited</b><br>
										<address class="no-margin">
											70B/35A,3rd Floor,<br>
											Rama Road Industrial Area,<br>
											New Delhi-110015
										</address>
									</div>
									<!-- /.col -->
									<div class="col-sm-4 col-xs-12 invoice-col">
										<b>GST No.:</b> 07AAICP6302R1ZX<br>
										<b>Phone No.:</b> +91-11-8865421452<br>
										<b>Email:</b> admin@perfumebooth.com<br>
										<b>Website:</b> www.perfumebooth.com
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->
								
								<!-- info row -->
								<div class="col-sm-12 col-xs-12 invoice_row">
									<div class="col-xs-12">
										<b>Shipping Address:</b>
										<address class="no-margin">
											<b><?= h($invoice->shipping_firstname) ?> <?= h($invoice->shipping_lastname) ?></b><br>
											<?= h($invoice->shipping_address) ?>,
											<?= h($invoice->shipping_city) ?>, <?= h($invoice->shipping_state) ?>,
											<?= h($invoice->shipping_country) ?>-<?= h($invoice->shipping_pincode) ?>.<br>
											Email: <?= h($invoice->shipping_email) ?><br>
											Mobile No.: <?= h($invoice->shipping_phone) ?><br>
										</address>
									</div>
								</div>
								<!-- /.row -->
								
								<!-- Table row -->
								<div class="col-xs-12 table-responsive invoice_row">
									<table class="table table-striped no-margin">
										<thead>
											<tr>
												<th>Products</th>
												<th>Price</th>
												<th class="text-center">Qty</th>
												<th class="text-right">Total</th>
											</tr>
										</thead>
										<tbody>
						<?php 	$counter = $totalQty = $subTotal = $discountTotal = $rowTotal = $totalTax = 0;
									foreach ($invoiceDetails as $value):
										$counter++;
										$totalQty += $value->qty;
										$subTotal += $value->price*$value->qty;
										$discountTotal += $value->discount;
										$rowTotal += $value->price*$value->qty - $value->discount;
										$totalTax += $value->tax_amount;
						?> 				
											<tr>
												<td><?php echo $value->title; ?></td>
												<td><?php echo $this->Admin->priceLogo.number_format($value->price,2); ?></td>
												<td class="text-center"><?php echo $value->qty;?></td>
												<td class="text-right"><?php echo $this->Admin->priceLogo.number_format($value->price*$value->qty - $value->discount, 2); ?></td>
											</tr>
						<?php endforeach; ?> 											
										</tbody>
									</table>
								</div>
								<!-- /.col -->
								
								<div class="col-sm-12 col-xs-12 invoice_row">
									<!-- /.col -->
									<div class="col-sm-12 col-xs-12 no-padding table-responsive">
										<table class="table no-margin">
											<tr>
												<td><b>Subtotal</b></td>
												<td class="text-right"><b><?php echo $this->Admin->priceLogo.number_format($subTotal, 2); ?></b></td>
											</tr>
											<tr>
												<td>Discount (-)</td>
												<td class="text-right"><?php echo $this->Admin->priceLogo.number_format($order->discount, 2); ?></td>
											</tr>
											<tr>
												<td>Payment Mode Fee (+)</td>
												<td class="text-right"><?php echo $this->Admin->priceLogo.number_format($order->mode_amount, 2); ?></td>
											</tr>
											<tr>
												<td>Shipping Fee (+)</td>
												<td class="text-right"><?php echo $this->Admin->priceLogo.number_format($order->ship_amount, 2); ?></td>
											</tr>
											<tr>
												<td><b>Grand Total</b></td>
												<td class="text-right"><b><?php echo $this->Admin->priceLogo.number_format($order->payment_amount, 2); ?></b></td>
											</tr>
											<tr>
												<td>
													<?php if( strtolower($invoice->shipping_state) == 'delhi' ):?>
														SGST + CGST (9% + 9%)
													<?php else: ?>
														IGST (18%)
													<?php endif?>
													 <b>(Inclusive)</b>
												</td>
												<?php
												$principal_amount	= ($order->payment_amount * 100)/118;
												$total_tax			= $order->payment_amount - $principal_amount;
												?>
												<td class="text-right"><?php echo $this->Admin->priceLogo.number_format($total_tax, 2); ?></td>
											</tr>
										</table>
									</div>
									<!-- /.col -->
								</div>
								<!-- /.row -->
								
								<!-- this row will not appear when printing -->
								<div class="col-sm-12 col-xs-12 invoice_row">
									<div class="col-sm-9 col-xs-12 invoice-col signature_left">
										<p>This is a computer generated invoice</p>
										<p>Declaration: The goods sold are intendedfor end user consumption and not for sale.</p>
									</div>
									
									<div class="col-sm-3 col-xs-12 invoice-col signature_right">
										<p class="invoice_signature">
											<?php echo $this->Html->image('invoice/signature_b.jpg', ['alt'=>'Signature', 'class'=>'img-responsive center-block']);?>
										</p>
										<p class="text-right no-margin">(Authorized Signatory)</p>
									</div>
								</div>
								
							</div>
							<?php
						}
						else
						{
							?>
							<div class="col-sm-8 col-xs-12 invoice">
								<!-- title row -->
								<div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
									<p class="text_memo">No invoice generated for this order.</p>
								</div><!-- end of upper_part -->
							</div>
							<?php
						}
						?>
					</section>
					<!-- /.content -->
				</div><!-- end of content_2 -->
            </div><!-- end of right_part -->
        </div><!-- end of tab -->
    </section>