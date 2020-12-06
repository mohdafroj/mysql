<section style="width:100%; padding:15px; position:relative;">

        <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%" style="border:1px solid #000000;">
        	<tbody>

                <tr><!-- start of heading text -->
                    <td>
                        <p style="color:rgba(0,0,0,0.7); font-size:30px; font-weight:bold; text-align:center; margin:0;">Logistics Manifest</p>
                        <p style="color:rgba(0,0,0,0.7); font-size:18px; font-weight:300; text-align:center; margin:0; letter-spacing:1px;">Generated on <?php echo $createdAt; ?></p>
                    </td>
                </tr><!-- end of heading text -->

                <tr><!-- start of heading content -->
                	<td>
                    	<table cellpadding="0" cellspacing="0" width="100%">
                        	<tr>
                                <td style="font-size:20px; color:rgba(0, 0, 0, 0.7); width:30%; padding:0 15px;">
                                	<p style="margin:0 0 2px 0; font-size:32px; font-weight:bold; color:rgba(0, 0, 0, 0.9);">
<?php echo $dataList['title'] ?? '';if (!empty($dataList['logo'])) { ?><span style="padding-left:6px; display:inline-block;"><img src="<?=$dataList['logo']?>" alt="<?php echo $dataList['title'] ?>" width="30"></span><?php }?>
                                    </p>
                                    <b>Perfumebooth</b>
                                    <address style="font-size:18px;">
                                        Perfume Booth Pvt Ltd, 3rd Floor, 70B/35A,3rd Floor, Rama Road Industrial Area, Near Kirti Nagar Metro Station.<br> New Delhi-110015.
                                    </address>
                                </td>
                                <td style="width:30%;">&nbsp;</td>
                                <td style="font-size:20px; color:rgba(0, 0, 0, 0.7); width:30%; padding:0 15px; letter-spacing:0.5px; text-align:right;">
                                    <p style="margin:0 0 2px 0;">
                                        Document No. : <?php echo $docmentNumber; ?>
                                    </p>
                                    <p style="margin:0 0 2px 0;">
                                        Total Shipment to Dispatch : <b><?php echo count($dataList['invoices']); ?></b>
                                    </p>
                                    <p style="margin:0 0 2px 0;">
                                        Total Shipments to Check : <b>0</b>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr><!-- end of heading content -->

                <tr><!-- start of product_part -->
                	<td>
                    	<table cellpadding="0" cellspacing="0" width="100%">
                            <thead>
                                <tr style="page-break-after:always; padding-bottom:5px;">
                                    <th style="padding:2px 8px 2px 15px; font-size:24px;">S. No.</th>
                                    <th style="padding:2px 8px; font-size:24px; text-align:center;">Tracking Id</th>
                                    <th style="padding:2px 8px; font-size:24px; text-align:center;">Mode</th>
                                    <th style="padding:2px 15px 2px 8px; font-size:24px; text-align:center;">Order Number</th>
                                </tr>
                            </thead>
                            <tbody>
                    <?php
$i = 1;
foreach ($dataList['invoices'] as $value) {
    ?>
                                <tr style="page-break-after:always; padding-bottom:5px;">
                                    <td style="padding:2px 8px 2px 15px; font-size:20px;"><?=$i++?></td>
                                    <td style="padding:2px 8px; font-size:20px; text-align:center;"><?php echo $value['tracking_code'] ?? ''; ?></td>
                                    <td style="padding:2px 8px; font-size:20px; text-align:center;"><?php echo ucfirst($value['payment_mode']); ?></td>
                                    <td style="padding:2px 15px 2px 8px; font-size:20px; text-align:center;"><?php echo $value['order_number'] ?? ''; ?></td>
                                </tr>
<?php }
?>
                            </tbody>
                        </table>
                    </td>
                </tr><!-- end of product_part -->

                <tr><!-- start of middle_space -->
                    <td width="100%" height="20" style="font-size:0;"></td>
                </tr><!-- end of middle_space -->

                <tr><!-- start of Logistics form heading text -->
                	<td>
                        <p style="color:rgba(0,0,0,0.7); font-size:24px; font-weight:bold; text-align:center; margin:0 15px; text-transform:uppercase; border-width:1px 0; border-style:dashed; border-color:rgba(0,0,0,0.6); padding:5px 0;">
                            To Be Filled By <?php echo $dataList['title'] ?? ''; ?> Logistics Executive
                        </p>
                    </td>
                </tr><!-- end of Logistics form heading text -->

                <tr><!-- start of middle_space -->
                    <td width="100%" height="20" style="font-size:0;"></td>
                </tr><!-- end of middle_space -->

                <tr><!-- start of Logistics_form -->
                	<td>
                    	<table cellpadding="0" cellspacing="0" border="0" width="40%" align="left">
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 15px;">
                                    	Pickup In Time :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="10" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 15px;">
                                    	Pickup Out Time :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="25" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 15px;">
                                    	FE Name :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="10" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 15px;">
                                    	FE Signature :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                        </table>

                    	<table cellpadding="0" cellspacing="0" border="0" width="10%" align="left">
                        	<tr>
                            	<td width="100%">&nbsp;</td>
                            </tr>
                        </table>

                        <table cellpadding="0" cellspacing="0" border="0" width="40%" align="left">
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 7px;">
                                    	Total Items Picked :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="15" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 7px;">
                                    	All Shipment Have Flipkart Packaging :
                                    </p>
                                </td>
                            	<td width="60%" valign="top">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; display:inline-block; width:48%;">
                                    	<span style="border:1px solid #363636; width:15px; height:15px; margin-right:10px; display:inline-block; background:none;"></span>Yes
                                    </p>
                                    <p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; display:inline-block; width:48%;">
                                    	<span style="border:1px solid #363636; width:15px; height:15px; margin-right:10px; display:inline-block; background:none;"></span>No
                                    </p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="6" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 7px;">
                                    	Seller Name :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">PERFUMEBOOTH</p>
                                </td>
                            </tr>
                            <tr><!-- start of middle_space -->
                                <td width="100%" height="10" colspan="2" style="font-size:0;"></td>
                            </tr><!-- end of middle_space -->
                        	<tr>
                            	<td width="40%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; padding:0 0 0 7px;">
                                    	Seller Signature :
                                    </p>
                                </td>
                            	<td width="60%">
                                	<p style="font-size:24px; color:rgba(0, 0, 0, 0.7); margin:0; border-bottom:1px solid #363636;">&nbsp;</p>
                                </td>
                            </tr>
                        </table>

                    	<table cellpadding="0" cellspacing="0" border="0" width="10%" align="left">
                        	<tr>
                            	<td width="100%">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%" height="20" style="font-size:0;"></td>
                </tr>

                <tr>
                    <td>
                        <p style="color:rgba(0,0,0,0.7); font-size:22px; text-align:center; margin:0;">
                        	This is a system generated document
                        </p>
                    </td>
                </tr>

                <tr>
                    <td width="100%" height="10" style="font-size:0;"></td>
                </tr>

            </tbody>
        </table>

    </section>
