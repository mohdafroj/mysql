<?php
$activeTab = ($this->request->getParam('action') != 'dashboard') ? $this->request->getParam('controller') : null;
?>
<?=$this->Html->docType()?>
<html>
<head>
<?=$this->Html->charset()?>
<?=$this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no'])?>
<title><?php echo $title ?? $this->fetch('title'); ?></title>
<?=$this->Html->meta('icon')?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<?php //$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css')?>
<?=$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css')?>
<?=$this->Html->css('/admin/emoji/lib/css/emoji.css')?>
<?=$this->Html->css('/admin/plugins/datepicker/datepicker3.css')?>
<?=$this->Html->css('/admin/plugins/iCheck/all.css')?>
<?=$this->Html->css('/admin/plugins/select2/select2.min.css')?>
<?=$this->Html->css('/admin/dist/css/AdminLTE.min.css')?>
<?=$this->Html->css('/admin/dist/css/skins/_all-skins.min.css')?>
<?=$this->Html->css('/admin/dist/css/responsive-table.css')?>
<?=$this->Html->css('/admin/dist/css/custom.css')?>
	<?=$this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js')?>
	<?=$this->Html->script('https://code.jquery.com/ui/1.12.1/jquery-ui.js');?>
	<?=$this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js')?>
	<?=$this->Html->css('/admin/plugins/datetimepicker/bootstrap-datetimepicker.min.css')?>
	<?=$this->Html->css('/admin/plugins/timepicker/bootstrap-timepicker.min.css')?>
</head>
<body class="hold-transition skin-blue sidebar-mini">

<?php //$this->assign('title', $title);?>
	<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">
    <a href="<?=$this->Url->build('/admin/dashboard');?>" class="logo">Dashboard</a>
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
            <li<?=(in_array($activeTab, ['Users'])) ? ' class="active"' : null;?> > <?=$this->Html->link(__('Users'), ['controller' => 'Users/']);?></li>
            <li<?=(in_array($activeTab, ['Customers'])) ? ' class="active"' : null;?> > <?=$this->Html->link(__('Customers'), ['controller' => 'Customers', 'action' => 'index']);?></li>
            <li class="dropdown <?=(in_array($activeTab, ['Attributes', 'Categories', 'Products', 'Reviews', 'Pbimages'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalog <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Attributes'), ['controller' => 'Attributes', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Categories'), ['controller' => 'Categories', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Products'), ['controller' => 'Products', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Reviews'), ['controller' => 'Reviews', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Category Brands'), ['controller' => 'Categories', 'action' => 'brand', 'view']);?></li>
                <li><?=$this->Html->link(__('Media'), ['controller' => 'Media']);?></li>
              </ul>
            </li>
            <li class="dropdown <?=(in_array($activeTab, ['Shopping', 'Markets'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Promotions <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Drift Markets'), ['controller' => 'Markets', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Cart Rule'), ['controller' => 'Shopping', 'action' => 'index']);?></li>
              </ul>
            </li>
            <li class="dropdown <?=(in_array($activeTab, ['Marketsss'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Finance <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
              </ul>
            </li>
            <li class="dropdown <?=(in_array($activeTab, ['Cms','Locations', 'Pgs', 'Shipvenders'])) ? 'active' : null;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
							  <li><?=$this->Html->link(__('Locations'), ['controller' => 'Locations', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('PG Methods'), ['controller' => 'Pgs', 'action' => 'index']);?></li>
		<li><?=$this->Html->link(__('Couriers Methods'), ['controller' => 'Pgs', 'action' => 'Couriers']);?></li>
                <li><?=$this->Html->link(__('Shipping Vendors'), ['controller' => 'Pgs', 'action' => 'shipvendors']);?></li>
                <li><?=$this->Html->link(__('CMS'), ['controller' => 'Cms', 'action' => 'index']);?></li>
                <li><?=$this->Html->link(__('Set Invalid Pincodes'), ['controller' => 'Cms', 'action' => 'setInvalidPincodes']);?></li>
		<li><?=$this->Html->link(__('System Logs'), ['controller' => 'Users', 'action' => 'syslog']);?></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Website <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('For PerfumeBooth'), ['prefix' => 'admin', 'plugin'=>null, 'controller' => 'Dashboard']);?></li>
                <!--li><a href="<?php //echo $this->Url->build(['prefix' => 'admin', 'plugin' => null, 'controller' => 'usd/dashboards', 'action' => 'index']);?>">For USA</a></li-->
                <li><a href="<?=$this->Url->build(['prefix' => 'admin', 'plugin' => null, 'controller' => 'subscription/dashboards', 'action' => 'index']);?>">For PerfumersClub</a></li>
              </ul>
            </li>
          </ul>
          <!--form class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search">
            </div>
          </form-->
        </div>
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"><i class="fa fa-bars"></i></button>
        <!-- /.navbar-collapse -->
		<!-- Navbar Right Menu -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- Messages: style can be found in dropdown.less-->
				<!--li class="dropdown messages-menu">
            <a href="#">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
        </li-->
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
						<ul class="dropdown-menu" role="menu" style="width:20%;">
								<li><a href="#">Out of Stock</a></li>
						</ul-->
          </li>

          <li class="dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!--img src="<?=$this->Url->build('/admin/')?>dist/img/user2-160x160.jpg" class="user-menu user-image" alt="User Image" -->
              <span class="hidden-xs"><?php if ($this->request->session()->check('userName')) {
    echo $this->request->session()->read('userName');
}
?></span> <span class="caret"></span>
            </a>
              <ul class="dropdown-menu" role="menu">
                <li><?=$this->Html->link(__('Profile'), ['controller' => 'Users', 'action' => 'profile'])?></li>
		<li> <?= $this->Html->link(__('Manage'), ['controller'=>'Users','action' => 'manage']) ?>
                <li><?=$this->Html->link(__('Logout'), ['controller' => 'Users', 'action' => 'logout'])?></li>
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
      For Perfumebooth Pvt. Ltd.
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2017 <a href="https://www.perfumebooth.com" target="_blank">Perfumebooth Pvt. Ltd</a>. </strong> All rights reserved.
  </footer>

    </div>
	<?=$this->Html->script('/admin/plugins/select2/select2.full.min.js')?>
	<?=$this->Html->script('/admin/plugins/input-mask/jquery.inputmask.js')?>
	<?=$this->Html->script('/admin/plugins/input-mask/jquery.inputmask.date.extensions.js')?>
	<?=$this->Html->script('/admin/plugins/input-mask/jquery.inputmask.extensions.js')?>
	<?=$this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js')?>
	<?=$this->Html->script('/admin/plugins/datetimepicker/bootstrap-datetimepicker.min.js')?>
	<?=$this->Html->script('/admin/plugins/daterangepicker/daterangepicker.js')?>
	<?php //$this->Html->script($this->Url->build('/admin/plugins/datepicker/bootstrap-datepicker.min.js', true)); ?>
	<?=$this->Html->script('/admin/dist/js/bootstrap-tabcollapse.js')?>
	<?=$this->Html->script($this->Url->build('/admin/plugins/iCheck/icheck.min.js', true))?>

	<?=$this->Html->script($this->Url->build('/admin/dist/js/app.min.js', true))?>
	<?=$this->Html->css('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');?>
	<?=$this->Html->script($this->Url->build('/admin/js/products.js', true))?>

<!-- Page script -->
<script>
  $(function () {

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

    //Colorpicker
    //$(".my-colorpicker1").colorpicker();
    //color picker with addon
    //$(".my-colorpicker2").colorpicker();

    //Timepicker
    //$(".timepicker").timepicker({
      //showInputs: false
    //});


	});

</script>

</body>
</html>