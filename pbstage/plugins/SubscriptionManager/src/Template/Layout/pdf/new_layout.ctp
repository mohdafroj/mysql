<?= $this->Html->docType() ?>
<html>
<head>
    <?= $this->Html->charset() ?>
    <?=$this->Html->meta('favicon.ico', '/subscription_manager/favicon.ico', ['type'=>'icon'])?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($this->fetch('title')) ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <style>
		
/* start of invoice page */
.content .invoice{position:relative; transform:translateX(-50%); padding:0; margin:0;}
.invoice_row{border-width:1px 1px 0 1px; border-style:solid; border-color:#000000; padding:0;}
.invoice_row:last-child{border-width:1px 1px 1px 1px; border-style:solid; border-color:#000000;}
.custom_barcode{padding:8px 15px 0; border-width:0px 1px; border-style:solid; border-color:#000000; width:50%;}
.barcode_div{width:100%;}
.barcode_div img{width:310px;}
.custom_barcode:nth-child(2){padding:0; border-width:0px 0px;}
.custom_barcode span{display:inline-block; font-size:27px; color:rgba(0,0,0,0.7); text-transform:uppercase; line-height:20px;}
.custom_barcode p span{font-size:17px; letter-spacing:3px; transform:rotateX(35deg); margin-top:-1px;}
.custom_barcode p.invoice_method{font-size:20px; padding:0px 8px; width:100%; display:inline-block; float:left;}
.custom_barcode p.invoice_method b{font-size:22px;}
.custom_barcode span.pull-right{font-weight:bold;}
.custom_barcode p.D_logo{border-bottom:1px solid #000000; padding:0px 15px; margin-bottom:5px;}
.custom_barcode .order_information{padding:0px 8px; display:inline-block; float:left; width:100%; font-size:20px;}
.custom_barcode .order_information p{margin:0; line-height:20px;}
.invoice_row .invoice-col{padding:2px 8px; font-size:22px; line-height:20px; width:50%;}
.invoice_row .col-sm-6:first-child.invoice-col{border-right:1px solid #000000;}
.invoice_row .table > tbody > tr > td, .invoice_row .table > tbody > tr > th, .invoice_row .table > tfoot > tr > td, .invoice_row .table > tfoot > tr > th, .invoice_row .table > thead > tr > td, .invoice_row .table > thead > tr > th{ padding:2px 8px; font-size:22px;}
.invoice_row .b, .invoice_row address{font-size:22px;}

.invoice_signature{margin:0;}
.invoice_signature img{width:120px;}
.text_memo{font-size:22px; font-weight:bold; text-align:center; margin:0; color:rgba(0,0,0,0.7);}
.shipping_billing{width:50%; float:left;}
.signature_left{width:75%; float:left;}
.signature_left p{margin:0; line-height:22px;}
.signature_right{width:25%; float:left;}
    </style>
	</head>
<body>
<div class="wrapper">
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <?= $this->fetch('content') ?>
  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->
</body>
</html>
