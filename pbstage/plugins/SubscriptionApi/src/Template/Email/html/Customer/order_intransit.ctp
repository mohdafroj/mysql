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
                                                        Hi <?php 
                                                                echo $customer['firstname'] ?? '';
                                                                echo ' ';
                                                                echo $customer['lastname'] ?? '';
                                                            ?>,
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
                                                            Thanks for shopping with <?php echo PC['COMPANY']['tag']; ?>!
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                            Order ID : <b><?php echo PC['ORDER_PREFIX'];?><?php echo $id ?? ''; ?></b>
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
                                                        <?php echo PC['COMPANY']['tag']; ?> Team
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
                            <td align="center"><?php echo $content ?? ''; ?></td>
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
                                                                                                        <td width="100%" colspan="2" height="10" style="font-size:0;"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0;">
                                                                                                                Products Prices
                                                                                                            </p>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0; text-align:right;">
                                                                                                            <?php echo $price_logo ?? '';  echo $product_total ?? '0'; ?>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td width="100%" colspan="2" height="10" style="font-size:0;"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0;">
                                                                                                                Discounts
                                                                                                            </p>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0; text-align:right;">
                                                                                                            <?php echo $price_logo ?? '';  echo $discount ?? '0'; ?>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td width="100%" colspan="2" height="10" style="font-size:0;"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0;">
                                                                                                                <?php 
                                                                                                                    switch ( $payment_mode ) {
                                                                                                                        case 'prepaid' : 'Prepaid'; break;
                                                                                                                        case 'redeem' : 'Redeemed'; break;
                                                                                                                        case 'postpaid' : 'Cash On Delivery'; break;
                                                                                                                        default:
                                                                                                                    }
                                                                                                                ?>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0; text-align:right;">
                                                                                                            <?php echo $price_logo. ' ' .$mode_amount; ?>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td width="100%" colspan="2" height="10" style="font-size:0;"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0;">
                                                                                                                Shipping &amp; Handling Charges
                                                                                                            </p>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <p style="font-size:.85rem; color:#363636; margin:0; text-align:right;">
                                                                                                            <?php echo $price_logo. ' ' .$ship_amount; ?>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td width="100%" colspan="2" height="25" style="font-size:0;"></td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <p style="font-size:.95rem; color:#363636; margin:0;">
                                                                                                                <b>Grand Total</b>
                                                                                                            </p>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <p style="font-size:.95rem; color:#363636; margin:0; text-align:right;">
                                                                                                                <b><?php echo $price_logo. ' ' .$payment_amount; ?></b>
                                                                                                            </p>
                                                                                                        </td>
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
