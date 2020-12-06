<section class="content col-sm-12 col-xs-12 no-padding">
    	
        <div class="col-sm-6 col-xs-12 invoice">
            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <p class="text_memo">Tax Invoice / Cash Memo</p>
            </div><!-- end of upper_part -->
            <!-- /.col -->
            
            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <div class="col-sm-4 col-xs-12 custom_barcode"><!-- start of custom scan -->
                    <p>
						<?php echo $this->Html->image('invoice/custom_code.jpg', ['alt'=>'', 'class'=>'img-responsive center-block']);?>
                    </p>
                    <span>560035</span>
                    <span class="pull-right">BLR/HDP</span>	
                </div><!-- end of custom scan -->
                
                <div class="col-sm-4 col-xs-12 custom_barcode"><!-- start of Logo_part -->
                    <p class="D_logo">
                        <?php echo $this->Html->image('invoice/fzsd.jpg', ['alt'=>'Delhivery', 'class'=>'img-responsive center-block']);?>
                    </p>
                    <div class="order_information">
                        <p>
                            <b>Order Id :</b> <?= h($invoice->order_number) ?>
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
                    <p>
                        <?php echo $this->Html->image('invoice/delhivery_code.jpg', ['alt'=>'Delhivery', 'class'=>'img-responsive center-block']);?>
                    </p>
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
                    <b>Phone No.:</b> 011-40098888<br>
                    <b>Email:</b> admin@perfumebooth.com<br>
                    <b>Website:</b> www.perfumebooth.com
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            
            <!-- info row -->
            <div class="col-sm-12 col-xs-12 invoice_row">
                <div class="col-sm-6 col-xs-12 invoice-col shipping_billing">
                    <b>Shipping Address</b>
                    <address class="no-margin">
                    	<b><?= h($invoice->shipping_firstname) ?> <?= h($invoice->shipping_lastname) ?></b><br>
                    	<?= h($invoice->shipping_address) ?>,<br>
                        <?= h($invoice->shipping_city) ?>, <?= h($invoice->shipping_state) ?><br>
						<?= h($invoice->shipping_country) ?>-<?= h($invoice->shipping_pincode) ?>.<br>
						Mobile No.: <?= h($invoice->shipping_phone) ?>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-6 col-xs-12 invoice-col shipping_billing">
                    <b>Billing Address</b><br>
                    <address class="no-margin">
                    	<b><?= h($invoice->billing_firstname) ?> <?= h($invoice->billing_lastname) ?></b><br>
                        <?= h($invoice->billing_address) ?>,<br>
						<?= h($invoice->billing_city) ?>, <?= h($invoice->billing_state) ?><br>
						<?= h($invoice->billing_country) ?>-<?= h($invoice->billing_pincode) ?>.<br>
						Mobile No.: <?= h($invoice->billing_phone) ?>
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
				foreach ($invoice->invoice_details as $value):
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
                            <td class="text-right"><?php echo $this->Admin->priceLogo.number_format($invoice->discount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Payment Mode (+)</td>
                            <td class="text-right"><?php echo $this->Admin->priceLogo.number_format($invoice->mode_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Shipping (+)</td>
                            <td class="text-right"><?php echo $this->Admin->priceLogo.number_format($invoice->ship_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td><b>Grand Total</b></td>
                            <td class="text-right"><b><?php echo $this->Admin->priceLogo.number_format($invoice->payment_amount, 2); ?></b></td>
                        </tr>
                        <tr>
                            <td>
			<?php 
				  
				  if( strtolower($invoice->shipping_state) == 'delhi' ):?>
									SGST + CGST (14% + 14%)
			<?php else: ?>
									IGST (28%)
			<?php endif?>
								<b>(Inclusive)</b>
							
							</td>
                            <td class="text-right"><?php echo $this->Admin->priceLogo.number_format($totalTax, 2); ?></td>
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

    </section>