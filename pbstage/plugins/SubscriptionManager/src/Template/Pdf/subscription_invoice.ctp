<section class="content col-sm-12 col-xs-12 no-padding">
	<?php
if (count($invoice) > 0) {
    ?>
        <div class="col-sm-6 col-xs-12 invoice">
            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <p class="text_memo">Tax Invoice / Cash Memo</p>
            </div><!-- end of upper_part -->
            <!-- /.col -->

            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <div class="col-sm-4 col-xs-12" style="float:left; width:36%;"><!-- start of custom scan -->
					<?php
    $oid_barcode = $sort_code = '';
    if ($invoice->packing_slip != '') {
        $packing_slip = json_decode($invoice->packing_slip, true);
        if (isset($packing_slip['packages']) && count($packing_slip['packages']) > 0) {
            $sort_code = $packing_slip['packages'][0]['sort_code'];
            $oid_barcode = $packing_slip['packages'][0]['oid_barcode'];
        }
    }
    if ( !empty($barcode) ) {
        ?>
						<p class="text-center margin-top">
						<img src="data:image/png;base64,<?php echo $barcode; ?>" width="250px" height="120px">
						</p>
                        <p class="text-center"><?php echo $invoice->tracking_code; ?></p>
						<?php
}
    ?>
                </div><!-- end of custom scan -->

                <div class="col-sm-4 col-xs-12 custom_barcode" style="float:left; width:28%;"><!-- start of Logo_part -->
                    <p class="D_logo">
                        <?php echo $this->Html->image(PC['COMPANY']['website'].'/pb/subscription_manager/img/delhivery.jpg', ['alt' => 'Delhivery', 'class' => 'img-responsive center-block']); ?>
                    </p>
                    <div class="order_information">
                        <p>
                            <b>Order Id :</b> PC<?=h($invoice->order_id)?>
                        </p>
                        <p>
                            <b>Invoice No. :</b> PC<?=h($invoice->id)?>
                        </p>
                        <p>
                            <b>Invoice Date :</b> <?= ($invoice->created == "") ? 'N/A' : date('Y-m-d h:i:s A', strtotime($invoice->created)); ?>
                        </p>
                    </div>
                </div><!-- end of Logo_part -->

                <div class="col-sm-4 col-xs-12 custom_barcode" style="float:left; width:36%;"><!-- start of delhivery scan -->
                    <?php
if ($oid_barcode != '') {
        ?>
						<p class="text-center">
							<img src="<?php echo $oid_barcode; ?>" alt="" class="img-responsive center-block"style="width:90%; max-width:175px;">
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
                <div class="col-sm-4 col-xs-12 invoice-col" style="float:left; width:33%;">
                    <b>Shipped By</b><br>
                    <address class="no-margin">
                    	<?php echo $this->Html->image(PC['COMPANY']['website'].'/pb/subscription_manager/img/logo.svg', ['alt' => PC['COMPANY']['name'], 'width'=>'60%']); ?>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 col-xs-12 invoice-col" style="float:left; width:33%;">
                    <b><?php echo PC['COMPANY']['name']?></b><br>
                    <address class="no-margin">
                        <?php echo PC['COMPANY']['add'].',<br>'.PC['COMPANY']['city'].'-'.PC['COMPANY']['pin']; ?>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 col-xs-12 invoice-col" style="float:left; width:33%;">
                    <b>GST No.:</b> <?php echo PC['SELLER_GST']?><br>
                    <b>Phone No.:</b> <?php echo PC['COMPANY']['phone']?><br>
                    <b>Email:</b> <?php echo PC['COMPANY']['email']?><br>
                    <b>Website:</b> <?php echo PC['COMPANY']['website']?>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- info row -->
            <div class="col-sm-12 col-xs-12 invoice_row">
                <div class="col-sm-12 col-xs-12 invoice-col shipping_billing">
                    <b>Shipping Address</b>
                    <address class="no-margin">
                    	<b><?=h($invoice->shipping_firstname)?> <?=h($invoice->shipping_lastname)?></b><br>
                    	<?=h($invoice->shipping_address)?>,<br>
                        <?=h($invoice->shipping_city)?>, <?=h($invoice->shipping_state)?><br>
						<?=h($invoice->shipping_country)?>-<?=h($invoice->shipping_pincode)?>.<br>
						Mobile No.: <?=h($invoice->shipping_phone)?>
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
        <?php $counter = $totalQty = $subTotal = $discountTotal = $rowTotal = 0;
    $logo = $invoice->location->currency_logo ?? '';
    foreach ($invoice->invoice_details as $value):
        $counter++;
        $totalQty += $value->quantity;
        $subTotal += $value->price * $value->quantity;
        $discountTotal += $value->discount;
        $rowTotal += $value->price * $value->quantity - $value->discount;
        ?>
											                        <tr>
											                            <td><?php echo $value->title; ?></td>
											                            <td><?php echo $logo . ' ' . number_format($value->price, 2); ?></td>
											                            <td class="text-center"><?php echo $value->quantity; ?></td>
											                            <td class="text-right"><?php echo $logo . ' ' . number_format($value->price * $value->quantity - $value->discount, 2); ?></td>
											                        </tr>
											        <?php endforeach;?>
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
                            <td class="text-right"><b><?php echo $logo . ' ' . number_format($subTotal, 2); ?></b></td>
                        </tr>
                        <tr>
                            <td>Discount (-)</td>
                            <td class="text-right"><?php echo $logo . ' ' . number_format($invoice->discount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Payment Mode (+)</td>
                            <td class="text-right"><?php echo $logo . ' ' . number_format($invoice->mode_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Shipping (+)</td>
                            <td class="text-right"><?php echo $logo . ' ' . number_format($invoice->ship_amount, 2); ?></td>
                        </tr>
                        <tr>
                            <td><b>Grand Total</b></td>
                            <td class="text-right"><b><?php echo $logo . ' ' . number_format($invoice->payment_amount, 2); ?></b></td>
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
                    <p><b>GST No.:</b> <?php echo PC['SELLER_GST']?><br></p>
                </div>

                <div class="col-sm-3 col-xs-12 invoice-col signature_right">
                	<p class="invoice_signature">
                    	<?php echo $this->Html->image(PC['COMPANY']['website'].'/pb/subscription_manager/img/signature_b.jpg', ['alt' => 'Signature', 'class' => 'img-responsive center-block']); ?>
                    </p>
                	<p class="text-right no-margin">(Authorized Signatory)</p>
                </div>
            </div>

        </div>
		<?php
}
?>
</section>