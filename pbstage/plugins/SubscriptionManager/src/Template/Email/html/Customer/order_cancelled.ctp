<table width="100%" cellpadding="0" cellspacing="0" border="0"><!-- start of bg_content -->
	<tbody>
    	<tr>
        	<td style="color:#000000; border:0; display:block; max-width:600px; margin:0 auto; clear:both; background:#ffffff; overflow:hidden;" bgcolor="#ffffff">
            	<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center"><!-- start of white_part -->
                	<tr>
                        <td width="100%" height="30" style="font-size:0;"></td>
                    </tr>
                    
                	<tr><!-- start of content -->
                    	<td style="color:#000000;display:block;font-weight:500;max-width:100%;margin:0 auto;clear:both;">
                        	
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tbody>
                                    <tr>
                                        <td align="center"> 
                                            <table width="96%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="15"></td>
                                                    <td>
                                                        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                                            <tr>
                                                                <td width="100%" height="15" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:1rem; color:#363636; margin:0; font-weight:700;">
                                                                        Hi <?php 
                                                                                echo $customer['firstname'] ?? '';
                                                                                echo ' ';
                                                                                echo $customer['lastname'] ?? '';
                                                                            ?>,
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:.85rem; color:#363636; margin:0; line-height:1.7;">
                                                                        We are sad that you want to cancel…
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:.85rem; color:#363636; margin:0; line-height:1.7;">
                                                                        Your recently placed order has been cancelled.
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:.85rem; color:#363636; margin:0; line-height:1.7;">
                                                                        We would like to tell you that you can place order in future by clicking
Re-Order Button.
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:.85rem; color:#363636; margin:0; line-height:1.7;">
                                                                        Order ID : <b><?php echo PC['ORDER_PREFIX']; ?><?php echo $id ?? ''; ?></b>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p style="font-size:.85rem; color:#363636; margin:0; line-height:1.7;">
                                                                        Order Date : <b><?php echo $created ?? ''; ?></b>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr><!-- start of shopping bag heading tr -->
                                                                <td align="center">
                                                                	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                                    	<tr>
                                                                        	<td style="background:#3b4e76; padding:.55rem .9rem; border:.13rem solid #ee9591;" align="center">
                                                                            	<span style="text-decoration:none; font-size:1rem; font-weight:bold; color:#ffffff; white-space:nowrap; text-transform:uppercase; letter-spacing:0.5px; display:inline-block;">
                                                                                    Your Order Detail
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of shopping bag heading tr -->
                                                            
                                                            <tr><!-- start of product for loop tr -->
                                                                <td>
                                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td width="100%" height="15" style="font-size:0;"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center"> 
                                                                                    <?php echo $content ?? ''; ?>
                                                                                </td> 
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="100%" height="15" style="font-size:0;"></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of product for loop tr -->
                                                            
                                                            <tr><!-- start of border_line -->
                                                                <td align="center">
                                                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td height="1" style="background:#3b4e76; height:1px; font-size:0px;"></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of border_line -->
                                                            
                                                            <!------------- start of price detail ------------------->
                                                            
                                                            <tr>
                                                                <td width="100%" height="25" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr><!-- start of price detail heading tr -->
                                                                <td align="center">
                                                                	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                                    	<tr>
                                                                        	<td style="background:#363636; padding:.55rem .9rem;" align="center">
                                                                            	<span style="text-decoration:none; font-size:1rem; font-weight:bold; color:#ffffff; white-space:nowrap; text-transform:uppercase; letter-spacing:0.5px; display:inline-block;">
                                                                                    Price Summary
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of price detail heading tr -->
                                                            
                                                            <tr><!-- start of price detail tr -->
                                                                <td>
                                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td width="100%" height="15" style="font-size:0;"></td>
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
                                                                                <td width="100%" height="15" style="font-size:0;"></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of price detail tr -->
                                                            
                                                            <tr><!-- start of border_line -->
                                                                <td align="center">
                                                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                                        <tr>
                                                                            <td height="1" style="background:#363636; height:1px; font-size:0px;"></td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr><!-- end of border_line -->
                                                            
                                                            <!------------- end of price detail ------------------->
                                                            
                                                            <tr>
                                                                <td width="100%" height="30" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%">
                                                                    <p style="font-size:.85rem; color:#3b4e76; margin:0; font-weight:700; font-style:italic;">
                                                                        Love,
                                                                    </p>
                                                                </td> 
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%">
                                                                    <p style="font-size:.85rem; color:#3b4e76; margin:0; font-weight:700; font-style:italic;">
                                                                    <?=PC['COMPANY']['tag']?> team
                                                                    </p>
                                                                </td> 
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="30" style="font-size:0;"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center">
                                                                	<table width="100" border="0" align="center" cellpadding="0" cellspacing="0">
                                                                    	<tr>
                                                                        	<td style="background:#3b4e76; padding:.55rem .9rem; border:.13rem solid #ee9591;">
                                                                            	<a href="<?php echo PC['COMPANY']['website']; ?>" target="_blank" style="text-decoration:none; font-size:1rem; font-weight:bold; color:#ffffff; white-space:nowrap; text-transform:uppercase; letter-spacing:0.5px; display:inline-block;">
                                                                                    Continue Shopping
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="30" style="font-size:0;"></td>
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
                    </tr><!-- end of content -->
                    
                	<tr>
                        <td width="100%" height="30" style="font-size:0;"></td>
                    </tr>
                </table><!-- end of white_part -->
            	
            </td>
        </tr>
    </tbody>
</table><!-- end of bg_content -->
