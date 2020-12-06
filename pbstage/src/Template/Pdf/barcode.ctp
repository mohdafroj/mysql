<section class="content col-sm-12 col-xs-12 no-padding">    	
        <div class="col-sm-12 col-xs-12 invoice">
            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <p class="text_memo">Barcode</p>
            </div><!-- end of upper_part -->
            <!-- /.col -->
            
            <!-- title row -->
            <div class="col-sm-12 col-xs-12 invoice_row"><!-- start of upper_part -->
                <div class="col-sm-12 col-xs-12"><!-- start of custom scan -->
                    <p class="text-center">&nbsp;</p>
                    <p class="text-center">
                        <img src="data:image/png;base64,<?php echo $barcode['code']; ?>" width="250" height="120" />
                        <span ></span>
                    </p>
                    <p class="text-center"><?php echo $barcode['tracking_code']; ?></p>
	            </div><!-- end of custom scan -->
                
            </div><!-- end of upper_part -->
        </div>
    </section>