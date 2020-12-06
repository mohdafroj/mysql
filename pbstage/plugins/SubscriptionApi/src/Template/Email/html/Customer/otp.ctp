<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of title -->
    <tbody>
    	<tr>
        	<td colspan="3" height="20" style="font-size:0;"></td>
        </tr>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#000000;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="20" style="font-size:0;"></td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-size:30px;font-weight:300; text-align:center; color:#363636;"> 
                                One Time Password
                            </td> 
                        </tr>
                        <tr>
                            <td width="100%" height="8" style="font-size:0;"></td>
                        </tr>
                        <tr>
                            <td align="center">
                                <table width="20%" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                    	<td height="1" style="background:#000000; height:1px; font-size:0px;"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" height="20" style="font-size:0;"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    	<tr>
        	<td colspan="3" height="20" style="font-size:0;"></td>
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
                                                            Hi <?php echo $name; ?>,
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            <strong><?php echo $otp; ?></strong> is the One Time Password (OTP) for your cash on delivery (COD) on <?php echo PC['COMPANY']['name']?> for Rs. <?php echo $amount; ?> on your Mobile ending no. <?php echo substr($mobile, -4, 4); ?>
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            This OTP is valid for 5 minutes or 1 successful attempt whichever is earlier. Please note this OTP is valid only for this order and cannot be used for any other order.
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Please do not share this One Time Password with anyone.
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            In case you have not requested for OTP, please contact the <?php echo PC['COMPANY']['tag']?> helpline at <?php echo PC['COMPANY']['phone']; ?>.
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#666666; margin:0;">
                                                            Warm Regards<br />
															<?php echo PC['COMPANY']['tag']; ?> Team<br />
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="5"></td>
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
