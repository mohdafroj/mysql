<html>
	<head>
		<title><?= $this->fetch('title') ?></title>
        <link href="https://fonts.googleapis.com/css?family=Heebo:300,400,700" rel="stylesheet">
		<style type="text/css"> body { margin: 0; font-family: 'Heebo', sans-serif; } </style>
	</head>
	<body style="font-family: 'Heebo', sans-serif; margin: 0px; padding: 0px; background:#ffffff;">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" style=" background:#ffffff;">
			<tr><td style="background:#ffffff; height:20px;"></td></tr>
			<tr>
				<td style="background:#fafafa;color:#ffffff; border:0px solid #ccc; display:block;font-weight:600;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of bootom_line -->
    <tbody>
        <tr>
            <td style="background:none;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >                    
                    <tr>
                        <td align="center">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tbody>
                                    <tr>
                                    	<td style="background:#38b8bf; height:5px; font-size:0px;" height="1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table><!-- end of bootom_line -->
                                
<table  width="600" cellspacing="0" cellpadding="0" border="0"><!-- start of blank part -->
    <tbody>
        <tr>
            <td width="20"></td>
            <td style="background:none;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table style="font-size:12px;font-weight:300;border-bottom-width:1px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="20"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of blank part -->

<table  width="600" cellspacing="0" cellpadding="0" border="0"><!-- start of banner_logo -->
    <tbody>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table style="font-size:12px;font-weight:300;border-bottom-width:1px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" style="font-size:0;"> 
                                <a style="width:100%; font-size:0; border:none" href="#" target="_blank">
                                    <?php echo $this->Html->image('admin/header.jpg',['fullBase' => true, 'alt'=>'Perfumebooth', 'width'=>'100%', 'style'=>'background-color:#f6f2e9;border:none;color:#818181;display:block;font-size:9px;max-width:100%']); ?>
                                </a>
                            </td> 
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of banner_logo -->
                    

    <?= $this->fetch('content') ?>
	
	
<?php if( isset($customerId) && ($customerId < 0) ){ ?>	
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of Share_Earn -->
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
        					<td width="3"></td>
                        	<td>
                                <p style="font-size:20px; color:#363636; margin:0;">
                                    Share &amp; Earn
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
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Refer your friends to get 5% PB cash & 10% PB points on every subsequent transaction made by your friend, until 10 transactions in the first year of membership, once the product is delivered.
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="12"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Your Referral link is : <a href="https://www.perfumebooth.com/customer/registration?ref=<?php echo $customerId; ?>" target="_blank" style="color:#38b8bf; text-decoration:none; border:1px dashed #38b8bf; padding:5px; display:inline-block;">https://www.perfumebooth.com/customer/registration?ref=<?php echo $customerId; ?></a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="15"></td>
                                    </tr>
                                </table>
                            </td> 
                        </tr>
                        <tr>
                            <td width="100%" height="10"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of Share_Earn -->
<?php } ?>
	
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of contact_us -->
    <tbody>
    	<tr>
        	<td colspan="3" height="10"></td>
        </tr>
    	<tr><!-- start of other_lines -->
        	<td width="20"></td>
            <td style="background:none;color:#000000;display:block;font-weight:500;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
            	<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                	<tbody>
                        <tr>
                            <td width="100%" height="5" colspan="2"></td>
                        </tr>
                    	<tr>
        					<td width="3"></td>
                        	<td>
                                <p style="font-size:20px; color:#363636; margin:0;">
                                    Contact Us
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
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            For any query please contact us
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="8"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Website: <a href="https://www.perfumebooth.com/" style="color:#38b8bf; text-decoration:none;" target="_blank">perfumebooth.com</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="8"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Address: 70B/35A, 3rd Floor, Rama Road Industrial Area, New Delhi – 110015
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="8"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Call us: <a href="tel:+91-11-40098888" style="color:#38b8bf; text-decoration:none;">+91-11-40098888</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="8"></td>
                                                </tr>
                                            	<tr>
                                                	<td>
                                                        <p style="font-size:15px; color:#363636; margin:0;">
                                                            Email id: <a href="mailto:connect@perfumebooth.com" style="color:#38b8bf; text-decoration:none;">connect@perfumebooth.com</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            	<tr>
                                                    <td width="100%" height="10"></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="15"></td>
                                    </tr>
                                </table>
                            </td> 
                        </tr>
                        <tr>
                            <td width="100%" height="10"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of contact_us -->
            
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of free_border -->
    <tbody>
        <tr>
            <td width="20"></td>
            <td style="background:#ffffff;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table style="font-size:12px;font-weight:300;border-bottom-width:1px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        
                        <tr>
                            <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                                    <tr>
                                        <td width="40%" align="left" style="padding:0; font-size:0;">
                                            <?php echo $this->Html->image('admin/sample.jpg', ['fullBase'=>true]);?>
                                        </td>
                                        <td width="25%" align="center" style="font-size:0;">
                                            <?php echo $this->Html->image('admin/sample_2.jpg', ['fullBase'=>true]);?>
                                        </td>
                                        <td width="30%" align="right" style="padding:0; font-size:0;">
                                            <?php echo $this->Html->image('admin/sample.jpg', ['fullBase'=>true]);?>
                                        </td>
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
</table><!-- end of free_border -->
           
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of footer -->
    <tbody>
        <tr>
            <td width="20"></td>
            
            <td style="background:none;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >                    
                    <tr>
                    	<td height="20"></td>
                    </tr>

                    <tr>
                        <td align="center">
                        	<table width="60%" class="responsivewidth-mobile" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" width="25%">
                                        <a href="https://twitter.com/perfumebooth" target="_blank">
                                            <?php echo $this->Html->image('admin/twitter.png',['fullBase' => true, 'alt'=>'Twitter']); ?>
                                        </a>
                                    </td>
                                    <td align="center" width="25%">
                                        <a href="https://www.instagram.com/perfumeboothindia/" target="_blank">
                                            <?php echo $this->Html->image('admin/insta.png',['fullBase' => true, 'alt'=>'Instagram']); ?>
                                        </a>
                                    </td>
                                    <td align="center" width="25%">
                                        <a href="https://www.youtube.com/c/perfumebooth" target="_blank">
                                            <?php echo $this->Html->image('admin/youtube.png',['fullBase' => true, 'alt'=>'Youtube']); ?>
                                        </a>
                                    </td>
                                    <td align="center" width="25%">
                                        <a href="https://www.facebook.com/perfumeboothin" target="_blank">
                                            <?php echo $this->Html->image('admin/facebook.png',['fullBase' => true, 'alt'=>'Facebook']); ?>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                    	<td height="22"></td>
                    </tr>
                    
                    <tr>
                        <td style="font-size:14px; color:#8a8a8a; text-align: center;">
                        <a href="https://www.perfumebooth.com/terms-of-use" target="_blank" style="color:#8a8a8a; text-decoration:none;">Terms &amp; Conditions </a> <span style="padding:0px 5px;">|</span> 
						<a href="https://www.perfumebooth.com/privacy-policy" target="_blank" style="color:#8a8a8a; text-decoration:none;">Privacy Policy</a> <span style="padding:0px 5px;">|</span> 
						<a href="https://www.perfumebooth.com/contact-us" target="_blank" style="color:#8a8a8a; text-decoration:none;">Contact Us</a>
                        </td>
                    </tr>
                    
                    <tr>
                    	<td height="15"></td>
                    </tr>
                </table>
            </td>
            
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of footer -->

<table  width="600" cellspacing="0" cellpadding="0" border="0"><!-- start of blank part -->
    <tbody>
        <tr>
            <td width="20"></td>
            <td style="background:none;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table style="font-size:12px;font-weight:300;border-bottom-width:1px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                    <tbody>
                        <tr>
                            <td width="100%" height="20"></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td width="20"></td>
        </tr>
    </tbody>
</table><!-- end of blank part -->
           
<table  width="100%" cellspacing="0" cellpadding="0" border="0"><!-- start of bootom_line -->
    <tbody>
        <tr>
            <td style="background:none;color:#ffffff;display:block;font-weight:300;max-width:600px;margin:0 auto;clear:both" bgcolor="#ffffff">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >                    
                    <tr>
                        <td align="center">
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                <tbody>
                                    <tr>
                                    	<td style="background:#38b8bf; height:5px; font-size:0px;" height="1"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table><!-- end of bootom_line -->
	
	
				</td>
			</tr>
			<tr><td style="background:#ffffff; height:20px;"></td></tr>
		</table>
	</body>
</html>