<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of title -->
    <tbody>
    	<tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="20"></td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-size:30px;font-weight:300; text-align:center; color:#363636;">
                                Order Dispatched
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" height="8"></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="10%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                    	<td height="1" style="background:#000000; height:1px; font-size:0px;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" height="20"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    	<tr>
        	<td colspan="3" height="20"></td>
        </tr>
    </tbody>
</table><!-- end of title -->

<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of header_content -->
    <tbody>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="10"></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="100%" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                    	<td width="15"></td>
                                    	<td>
                                        	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:18px; color:#363636; margin:0;">
                                                            Hi <?php echo isset($shippingName) ? $shippingName : 'Customer'; ?>,
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Your order has been dispatched!
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Thanks for shopping with <?=PC['COMPANY']['tag']?>!
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                            Order ID : <?php echo isset($id) ? $id : ''; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="5"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                            Dispatched Date : <?php echo isset($currentDate) ? $currentDate : ''; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                            Thanks,
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="5"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                        <?=PC['COMPANY']['tag']?> Team
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="15"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
    </tbody>
</table><!-- end of header_content -->

<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of product_part -->
    <tbody>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="15"></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="100%" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                    	<td width="15"></td>
                                    	<td>
                                        	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
							<?php foreach ($details as $value) {?>
                                            	<tr>
                                                	<td>
                                                        <table width="150" border="0" cellspacing="0" cellpadding="0" align="left">
                                                        	<tr>
                                                            	<td>
                                                                	<img src="<?php echo !empty($value['image']) ? $value['image'] : PC['IMAGE']; ?>" alt="<?php echo $value['title']; ?>" width="100%" />
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>

                                        			<td width="15">&nbsp;</td>

                                                	<td>
                                                        <table width="370" border="0" cellspacing="0" cellpadding="0">
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:16px; color:#363636; margin:0;">
                                                                        <?php echo $value['title']; ?> (sku: <?php echo $value['skuCode']; ?>)
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="20"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                         Size <?php echo $value['size']; ?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                       <?php echo $value['qty']; ?> Qty
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                    <?php echo $this->Html->image('SubscriptionManager.rupees.png',['alt'=>'Rupees','width'=>'6px']);?> <?php echo $value['price']; ?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
							<?php }?>
                                            </table>
                                        </td>
                                        <td width="15"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" height="15"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    	<tr><!-- start of other_lines -->
        	<td width="20"></td>
            <td style="background:none;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
            	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                	<tbody>
                        <tr>
                            <td width="100%" height="5" colspan="2"></td>
                        </tr>
                    	<tr>
        					<td width="6"></td>
                        	<td>
                                <p style="font-size:14px; color:#363636; margin:0;">
                                    *VAT/CST and shipping charges may apply
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr><!-- end of other_lines -->
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
    </tbody>
</table><!-- end of product_part -->

<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of price_summary -->
    <tbody>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
    	<tr><!-- start of other_lines -->
        	<td width="20"></td>
            <td style="background:none;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
            	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                	<tbody>
                        <tr>
                            <td width="100%" height="5" colspan="2"></td>
                        </tr>
                    	<tr>
        					<td width="4"></td>
                        	<td>
                                <p style="font-size:20px; color:#363636; margin:0;">
                                    Price Summary
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr><!-- end of other_lines -->
    	<tr>
        	<td colspan="3" height="5"></td>
        </tr>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="15"></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="100%" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                    	<td width="15"></td>
                                    	<td>
                                        	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                            	<tr>
                                                    <td width="100%" colspan="2" height="10"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#666666; margin:0;">
                                                        <?php echo ($paymentMode == 'postpaid') ? 'Cash On Delivery' : 'Prepaid'; ?>
                                                        </p>
                                                    </td>
                                                	<td>
                                                        <p style="font-size:15px; color:#666666; margin:0; text-align:right;">
                                                        <?php echo $this->Html->image('SubscriptionManager.rupees.png',['alt'=>'Rupees','width'=>'6px']);?> <?php echo isset($modeAmount) ? $modeAmount : 0; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" colspan="2" height="10"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#666666; margin:0;">
                                                            Shipping &amp; Handling Charges
                                                        </p>
                                                    </td>
                                                	<td>
                                                        <p style="font-size:15px; color:#666666; margin:0; text-align:right;">
                                                        <?php echo $this->Html->image('SubscriptionManager.rupees.png',['alt'=>'Rupees','width'=>'6px']);?> <?php echo isset($shipAmount) ? $shipAmount : 0; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" colspan="2" height="25"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Grand Total
                                                        </p>
                                                    </td>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0; text-align:right;">
                                                        <?php echo $this->Html->image('SubscriptionManager.rupees.png',['alt'=>'Rupees','width'=>'6px']);?> <?php echo isset($paymentAmount) ? $paymentAmount : 0; ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" colspan="2" height="10"></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="15"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" height="15"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
    </tbody>
</table><!-- end of price_summary -->
