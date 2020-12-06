<?= $this->Html->docType() ?>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($this->fetch('title')) ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

	<link rel="stylesheet" href="<?= $this->Url->build('/admin/dist/css/AdminLTE.min.css') ?>">	
	<link rel="stylesheet" href="<?= $this->Url->build('/admin/dist/css/custom.css') ?>">

	<style>
.content .invoice{position:relative; left:50%; transform:translateX(-50%);}
.invoice_row{border-width:1px 1px 0 1px; border-style:solid; border-color:#ccc; padding:0;}
.invoice_row:last-child{border-width:1px 1px 1px 1px; border-style:solid; border-color:#ccc;}
.custom_barcode{padding:8px 15px 0;}
.custom_barcode:nth-child(2){border-width:0px 1px; border-style:solid; border-color:#ccc; padding:0;}
.custom_barcode p img{width:140px;}
.custom_barcode span{display:inline-block; line-height:20px; font-size:25px; color:rgba(0,0,0,0.7); text-transform:uppercase;}
.custom_barcode p.invoice_method{font-size:16px;}
.custom_barcode p.invoice_method b{font-size:20px;}
.custom_barcode span.pull-right{font-weight:bold;}
.custom_barcode p.D_logo{border-bottom:1px solid #ccc; padding:8px 15px;}
.custom_barcode .order_information{padding:8px 15px;}
.custom_barcode .order_information p{margin:0;}
.invoice_row .invoice-col{padding:8px 15px;}
.invoice_row .col-sm-6:first-child.invoice-col{border-right:1px solid #ccc;}
.invoice_row .table > tbody > tr > td, .invoice_row .table > tbody > tr > th, .invoice_row .table > tfoot > tr > td, .invoice_row .table > tfoot > tr > th, .invoice_row .table > thead > tr > td, .invoice_row .table > thead > tr > th{ padding:8px 15px;}
.invoice_signature{margin:0;}
.invoice_signature img{width:120px;}
.text_memo{font-size:20px; font-weight:bold; text-align:center; margin:0; color:rgba(0,0,0,0.7);}
.shipping_billing{width:50%; float:left;}
.signature_left{width:75%; float:left;}
.signature_right{width:25%; float:left;}	
.custom_barcode p span{font-size: 15px;letter-spacing: 3px;transform: rotateX(35deg); margin-top:-1px;}					
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
