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
                            Abandoned Cart
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
            <td style="background:#ffffff;color:#000000;display:block;font-weight:500;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
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
                                                        <p style="font-size:18px; color:#363636; margin:0; text-align:center;">
                                                        Hi <?php
                                                                $username = '';
                                                                $username = (isset($customer['firstname']) && !empty($customer['firstname'])) ? $customer['firstname'].' ':'';
                                                                $username += (isset($customer['lastname']) && !empty($customer['lastname'])) ? $customer['lastname']:'';
                                                                echo !empty($username) ? $username:'Customer';
                                                            ?>,
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0; text-align:center;">
                                                            Psst… you’ve left something behind.
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;font-weight: bold; text-align:center;">
                                                            To help make up your mind, enjoy
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="5"></td>
                                                </tr>
                                                <tr>
                                                	<td>
                                                        <p style="font-size:50px; color:#38b8bf; margin:0;line-height: 40px;padding: 5px 0 2px 0px; text-align:center;">
                                                           <b>10% off</b>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="1"></td>
                                                </tr>
                                                <tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;font-weight: bold; text-align:center;">
                                                            on your purchase.
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0; text-align:center;">
                                                            Enter code
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="5"></td>
                                                </tr>
                                                <tr>
                                                	<td>
                                                        <p style=" color:#363636; margin:0; text-align:center;">
                                                            <span style="border: 1px dashed #38b8bf;display: inline-block;padding: 5px 8px 2px;font-size: 30px;line-height: 28px;font-weight: bold;">PBSAVE10</span>
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="100%" height="8"></td>
                                                </tr>
                                                <tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0; text-align:center;">
                                                            at checkout. Hurry up, this offer is valid for limited time!
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="30"></td>
                                                </tr>
                                            	<tr>
                                                	<td style="padding:0; text-align:center;">
                                                        <a href="<?=PC['COMPANY']['website']?>/checkout/cart" target="_blank" style="background-color:#38b8bf; text-decoration:none; padding:8px 5px; font-size:20px; font-weight:bold; color:#fff; white-space:nowrap; text-transform:uppercase; display:inline-block; width:150px; text-align:center;">
                                                            Shop Now
                                                        </a>
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


<?php if( count($cart['cart']) > 0){ ?>	

<?php foreach($cart['cart'] as $value){?>	
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
										
                                            	<tr>
                                                	<td>
                                                        <table width="150" border="0" cellspacing="0" cellpadding="0" align="left">
                                                        	<tr>
                                                            	<td>
                                                                	<img src="<?php echo !empty($value['images'][0]['url']) ? $value['images'][0]['url'] :PC['IMAGE'];?>" alt="<?php echo $value['title'];?>" width="100%" />
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
                                                                        <?php echo $value['title'];?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="20"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                         Size <?php echo $value['size'];?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                       <?php echo $value['cart_qty'];?> Qty
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10"></td>
                                                            </tr>
                                                        	<tr>
                                                            	<td>
                                                                    <p style="font-size:15px; color:#666666; margin:0;">
                                                                        <?php 
                                                                        echo $this->Html->image('admin/rupees.png',['fullBase' => true, 'alt'=>'Rupees', 'width'=>'6']);
                                                                        echo $value['price']*$value['cart_qty'];
                                                                        ?>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
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
    </tbody>
</table><!-- end of product_part -->
<?php }?>

<?php } ?>		 
               