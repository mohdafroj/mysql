<?php
$activeTab = ($this->request->getParam('action') != 'dashboard') ? $this->request->getParam('controller') : null;
?>
<?=$this->Html->docType()?>
<html>
<head>
  <?=$this->Html->charset()?>
  <?=$this->Html->meta('favicon.ico', '/subscription_manager/favicon.ico', ['type'=>'icon'])?>
  <?=$this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no'])?>
  <title><?php echo $title ?? $this->fetch('title'); ?></title>
  <?php
echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css');
echo $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css');

?>
  <?=$this->Html->css('SubscriptionManager.plugins/datepicker/datepicker3')?>
  <?=$this->Html->css('SubscriptionManager.plugins/iCheck/all')?>
  <?=$this->Html->css('SubscriptionManager.plugins/select2/select2.min')?>
  <?=$this->Html->css('SubscriptionManager.dist/css/AdminLTE.min')?>
  <?=$this->Html->css('SubscriptionManager.dist/css/skins/_all-skins.min')?>
  <?=$this->Html->css('SubscriptionManager.dist/css/responsive-table')?>
  <?=$this->Html->css('SubscriptionManager.dist/css/custom')?>
  <?php
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js');
echo $this->Html->script('https://code.jquery.com/ui/1.12.1/jquery-ui.js');
echo $this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');

?>
	<?=$this->Html->css('SubscriptionManager.plugins/datetimepicker/bootstrap-datetimepicker.min.css')?>
	<?=$this->Html->css('SubscriptionManager.plugins/daterangepicker/bootstrap-daterangepicker.min.css')?>
	<?=$this->Html->css('SubscriptionManager.plugins/timepicker/bootstrap-timepicker.min.css')?>
	<?=$this->Html->css('SubscriptionManager.plugins/colorpicker/bootstrap-colorpicker.min')?>
</head>
<body class="hold-transition skin-yellow sidebar-mini">

<?php //$this->assign('title', $title);?>
	<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">
    <?=$this->Html->link(__('Dashboard'), ['controller' => 'Dashboards', 'action' => 'index'], ['class' => 'logo']);?>
	<!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle hide" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
	  <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown <?=(in_array($activeTab, ['Orders', 'Invoice'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Orders'), ['controller' => 'Orders', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Invoices'), ['controller' => 'Invoices', 'action' => 'index']);?></li>
              </ul>
            </li>
            <li <?=(in_array($activeTab, ['Customers'])) ? ' class="active"' : null;?> > <?=$this->Html->link(__('Customers'), ['controller' => 'Customers', 'action' => 'index']);?></li>
            <li class="dropdown <?=(in_array($activeTab, ['Attributes', 'Categories', 'Products', 'Reviews', 'Plans', 'Pbimages'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalog <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Categories'), ['controller' => 'Categories', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Products'), ['controller' => 'Products', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Product Compare'), ['controller' => 'Products', 'action' => 'productCompare']);?></li>
                <li><?=$this->Html->link(__('Reviews'), ['controller' => 'Reviews', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Category Brands'), ['controller' => 'Categories', 'action' => 'brand', 'view']);?></li>
                <li><?=$this->Html->link(__('Plans'), ['controller' => 'Plans', 'action' => 'index']);?></li>
              </ul>
            </li>
            <li class="dropdown <?=(in_array($activeTab, ['Shopping', 'Markets'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Promotions <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Drift Markets'), ['controller' => 'Markets', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Cart Rule'), ['controller' => 'Shopping', 'action' => 'index']);?></li>
              </ul>
            </li>
            <li class="dropdown <?=(in_array($activeTab, ['Locations', 'Pgs', 'Shipvenders'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
              <li><?=$this->Html->link(__('PG Methods'), ['controller' => 'Pgs', 'action' => 'index']);?></li>
              <li><?=$this->Html->link(__('Set Invalid Pincodes'), ['controller' => 'Cms', 'action' => 'setInvalidPincodes']);?></li>
                <li><?=$this->Html->link(__('Shipping Vendors'), ['controller' => 'Pgs', 'action' => 'shipvendors']);?></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Website <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('PerfumeBooth'), ['prefix' => 'admin', 'plugin'=>null, 'controller' => 'Dashboard']);?></li>
                <li><?=$this->Html->link(__(PC['COMPANY']['tag']), ['plugin' => 'SubscriptionManager', 'controller' => 'Dashboards', 'action' => 'index']);?></li>
              </ul>
            </li>
          </ul>
        </div>
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
        <!-- /.navbar-collapse -->
		<!-- Navbar Right Menu -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
          </li>

          <li class="dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php //echo $this->Html->Image('SubscriptionManager.dist/img/default-50x50.gif', ['width' => '20px', 'hieght' => '20px', 'class' => 'user-menu user-image', 'alt' => 'User Image'])?>
              <span class="hidden-xs"><?php if ($this->request->session()->check('userName')) {
    echo $this->request->session()->read('userName');
}
?></span> <span class="caret"></span>
            </a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?=$this->Url->build(['controller' => 'Users', 'action' => 'profile', 'prefix' => 'admin', 'plugin' => null])?>">Profile</a></li>
                <li><a href="<?=$this->Url->build(['controller' => 'Users', 'action' => 'logout', 'prefix' => 'admin', 'plugin' => null])?>">Logout</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="height:100%;">
  	<div class="loader_div hide">
    	<div class="loader"></div>
    </div>
<?php $adminSuccess = $this->Flash->render('adminSuccess', ['element' => 'Flash/admin_success']);
if (!empty($adminSuccess)):
    echo $adminSuccess;
endif;
?>
<?php $adminError = $this->Flash->render('adminError', ['element' => 'Flash/admin_error']);
if (!empty($adminError)):
    echo $adminError;
endif;
?>

    <!-- Content Header (Page header) -->
    <?=$this->fetch('content')?>
	</div>
  <!-- /.content-wrapper -->
  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      For <?php echo PC['COMPANY']['name']?>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2017 <a href="<?php echo PC['COMPANY']['website']?>" target="_blank"><?php echo PC['COMPANY']['name']?></a> </strong> All rights reserved.
  </footer>

    </div>
	<?=$this->Html->script('SubscriptionManager.plugins/select2/select2.full.min')?>
	<?=$this->Html->script('SubscriptionManager.plugins/input-mask/jquery.inputmask')?>
	<?=$this->Html->script('SubscriptionManager.plugins/input-mask/jquery.inputmask.date.extensions')?>
	<?=$this->Html->script('SubscriptionManager.plugins/input-mask/jquery.inputmask.extensions')?>
	<?=$this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js'); ?>
	<?=$this->Html->script('SubscriptionManager.plugins/datetimepicker/bootstrap-datetimepicker.min')?>
	<?=$this->Html->script('SubscriptionManager.plugins/daterangepicker/daterangepicker')?>
	<?=$this->Html->script('SubscriptionManager.dist/js/bootstrap-tabcollapse')?>
	<?=$this->Html->script('SubscriptionManager.plugins/iCheck/icheck.min')?>
	<?=$this->Html->script('SubscriptionManager.dist/js/app.min')?>
	<?=$this->Html->css('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css') ?>
	<?=$this->Html->script('SubscriptionManager.plugins/colorpicker/bootstrap-colorpicker.min')?>

<!-- Page script -->
<script>
  $(function () {

    $.ajax({url: "<?php echo $this->Url->build(['plugin' => 'SubscriptionManager', 'controller' => 'pgs', 'action' => 'notifications']) ?>", success: function(result){
        $(".notifications-menu").html(result);
    }});

	$('#myTab').tabCollapse({
		tabsClass: 'hidden-sm hidden-xs',
		accordionClass: 'visible-sm visible-xs'
	});

    //Initialize Select2 Elements
    $(".select2").select2();

    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    //Datemask2 mm/dd/yyyy
    $("#datemask2").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});
    //Money Euro
    $("[data-mask]").inputmask();

    //Date range picker
    $('#reservation').daterangepicker();
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD h:mm A'});
    //Date range as a button
    $('#daterange-btn').daterangepicker(
        {
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    );

    //Date picker
    $('#datepicker5, #datepicker4, #datepicker3, #datepicker2, #datepicker1').datepicker({
      autoclose: true,
      dateFormat: 'yy-mm-dd'
    });

    $('#datepickerForReviews').datetimepicker({
			autoclose: 1,
      format: 'yyyy-mm-dd hh:mm:ss'
    });

    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    $('#colorpicker').colorpicker();
	});

</script>

</body>
</html>