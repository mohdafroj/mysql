<?php 
//	echo "$name | $email | $mobile";
?>
<?php 
//	echo "$subject";
?>
<?php 
//	echo "$comment";
?>

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
                                Contact Us
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
                                                            Comment: <?php echo $comment;?>
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Mobile: <?php echo $mobile;?>
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            Thank you for contacting <?=PC['COMPANY']['tag']?>.
                                                        </p>
                                                    </td>
                                                </tr>
												
                                            	<tr>
                                                    <td width="100%" height="15"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:16px; color:#363636; margin:0;">
                                                            This is just a quick note to let you know that we have received your query. We will contact you as soon as we can!
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
