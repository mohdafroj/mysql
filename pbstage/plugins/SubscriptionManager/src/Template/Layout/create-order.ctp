<?php 
$activeTab	= ($this->request->getParam('action') != 'dashboard') ? $this->request->getParam('controller') : NULL;
?>
<?= $this->Html->docType() ?>
<html>
<head>
<?= $this->Html->charset() ?>
<?= $this->Html->meta(['name'=>'viewport','content'=>'width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no']) ?>
<?= __('<title>') ?><?= h($this->fetch('title')) ?><?= __('</title>') ?>
<?= $this->Html->meta('icon') ?>
<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css') ?>
<?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css') ?>
<?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css') ?>
<?= $this->Html->css($this->Url->build('/admin/plugins/datepicker/datepicker3.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/plugins/iCheck/all.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/plugins/select2/select2.min.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/dist/css/AdminLTE.min.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/dist/css/skins/_all-skins.min.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/dist/css/responsive-table.css', true)) ?>
<?= $this->Html->css($this->Url->build('/admin/dist/css/custom.css', true)) ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">

<?php //$this->assign('title', $title);?> 
	<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">
    <a href="<?= $this->Url->build('/admin/dashboard');?>" class="logo">Dashboard</a>
	<!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle hide" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
	  <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown <?= (in_array($activeTab, ['Orders','Invoice'])) ? 'active':NULL;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sales <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?= $this->Html->link(__('Orders'), ['controller' => 'Orders', 'action' => 'index']); ?></li>
                <li><?= $this->Html->link(__('Invoices'), ['controller' => 'Invoices', 'action' => 'index']); ?></li>
              </ul>
            </li>
            <li<?= (in_array($activeTab, ['Users'])) ? ' class="active"':NULL;?> > <?= $this->Html->link(__('Users'), ['controller' => 'Users/']); ?></li>
            <li<?= (in_array($activeTab, ['Customers'])) ? ' class="active"':NULL;?> > <?= $this->Html->link(__('Customers'), ['controller' => 'Customers', 'action' => 'index']); ?></li>
            <li class="dropdown <?= (in_array($activeTab, ['Attributes','Categories','Products','Reviews'])) ? 'active':NULL;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catalog <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?= $this->Html->link(__('Attributes'), ['controller' => 'Attributes', 'action' => 'index']); ?></li>
                <li><?= $this->Html->link(__('Categories'), ['controller' => 'Categories', 'action' => 'index']); ?></li>
                <li><?= $this->Html->link(__('Products'), ['controller' => 'Products', 'action' => 'index']); ?></li>
                <li><?= $this->Html->link(__('Reviews'), ['controller' => 'Reviews', 'action' => 'index']); ?></li>
              </ul>
            </li>
            <li class="dropdown <?= (in_array($activeTab, ['Shopping'])) ? 'active':NULL;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Promotions <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?= $this->Html->link(__('Shopping Cart Rule'), ['controller' => 'Shopping', 'action' => 'index']); ?></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Finance <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
              </ul>
            </li>
            <li class="dropdown <?= (in_array($activeTab, ['Locations'])) ? 'active':NULL;?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><?= $this->Html->link(__('Locations'), ['controller' => 'Locations', 'action' => 'index']); ?></li>
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
				<li class="dropdown messages-menu">
				<!-- Menu toggle button -->
            <a href="#">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
          </li>
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
          </li>
		  
          <li class="dropdown">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!--img src="<?= $this->Url->build('/admin/') ?>dist/img/user2-160x160.jpg" class="user-menu user-image" alt="User Image" -->
              <span class="hidden-xs"><?php  if($this->request->session()->check('userName')) echo $this->request->session()->read('userName');?></span> <span class="caret"></span>
            </a>
              <ul class="dropdown-menu" role="menu">
                <li><?= $this->Html->link(__('Profile'), ['controller'=>'Users','action' => 'profile']) ?></li>
                <li><?= $this->Html->link(__('Logout'), ['controller'=>'Users','action' => 'logout']) ?></li>
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
<?php  	$adminSuccess = $this->Flash->render('adminSuccess', ['element' => 'Flash/admin_success']);
	   	if( !empty($adminSuccess) ):
	   		echo $adminSuccess;
	   	endif;
?>
<?php  	$adminError= $this->Flash->render('adminError', ['element' => 'Flash/admin_error']);
		if( !empty($adminError) ):
			echo $adminError;
		endif;
?>
		
    <!-- Content Header (Page header) -->
    <?= $this->fetch('content') ?>
	</div>
  <!-- /.content-wrapper -->
  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      For <?=PC['COMPANY']['name']?>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2017 <a href="<?=PC['COMPANY']['website']?>" target="_blank"><?=PC['COMPANY']['name']?></a>. </strong> All rights reserved.
  </footer>

    </div>
	<?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js') ?>
	<?= $this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js') ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/select2/select2.full.min.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/input-mask/jquery.inputmask.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/input-mask/jquery.inputmask.date.extensions.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/input-mask/jquery.inputmask.extensions.js', true)) ?>
	<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js') ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/daterangepicker/daterangepicker.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/datepicker/bootstrap-datepicker.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/dist/js/bootstrap-tabcollapse.js', true)) ?>
	<?= $this->Html->script($this->Url->build('/admin/plugins/iCheck/icheck.min.js', true)) ?>

	<?= $this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js') ?>
	<?= $this->Html->script($this->Url->build('/admin/dist/js/app.min.js', true)) ?>
	<?php echo $this->Html->script('https://code.jquery.com/ui/1.12.1/jquery-ui.js'); ?>
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
    $('#datepicker2, #datepicker1').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });

	CKEDITOR.replace('short_description');
    	CKEDITOR.replace('description');
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
    $(".my-colorpicker1").colorpicker();
    //color picker with addon
    $(".my-colorpicker2").colorpicker();

    //Timepicker
    $(".timepicker").timepicker({
      showInputs: false
    });
	  
 	
  });
</script>
   
<script>
	$(document).ready(function()
	{
		if($('#select_all').length > 0)
		{
			$('#select_all').change(function()
			{
				if($(this).is(':checked'))
				{
					$('.select_checkbox').prop('checked', 'checked');
				}
				else
				{
					$('.select_checkbox').prop('checked', false);
				}
			});
			
			$('.select_checkbox').change(function()
			{
				$('#select_all').prop('checked', false);
			});
			
			$('#download_invoice_button').click(function()
			{
				if($('.select_checkbox').filter(':checked').length > 0)
				{
					$('#download_invoice').val(1);
					$('#invoice_form').submit();
				}
				else
				{
					alert('Please select atleast 1 invoice to download.');
				}
			});
		}
		
		if($('#create_order').length > 0)
		{
			$('#shipping_state').find('option').eq(0).remove();
			$('#shipping_state').find('option').eq(0).remove();
			$('#billing_state').find('option').eq(0).remove();
			$('#billing_state').find('option').eq(0).remove();
			
			var billing_state	= $('#current_billing_state').val();
			$('#billing_state option').each(function()
			{
				if($(this).html() == billing_state)
				{
					$(this).attr('selected', 'selected');
				}
			});
			
			var shipping_state	= $('#current_shipping_state').val();
			$('#shipping_state option').each(function()
			{
				if($(this).html() == billing_state)
				{
					$(this).attr('selected', 'selected');
				}
			});
			
			$('#same_address').change(function()
			{
				if($(this).is(':checked'))
				{
					$('.billing_field').attr('readonly', 'readonly');
					$('#billing_state').attr('disabled', 'disabled');
				}
				else
				{
					$('.billing_field').removeAttr('readonly');
					$('#billing_state').removeAttr('disabled');
				}
			});
			
			$( ".product_sku" ).autocomplete({
				source: skuArray,
				select: function( event, ui )
				{
					var element		= event.target.id;
					var getValue	= ui.item.value;
					setItemDetail(element, getValue);
				}
			});
			
			$('#add_more').click(function()
			{
				$('#order_item_table').append('<tr class="order_row order_row_product_'+total_counter+'" data-row="'+total_counter+'"><td><input type="hidden" id="product_id_'+total_counter+'" name="product_id['+total_counter+']" value="" /><input type="text" id="product_'+total_counter+'" name="product_sku['+ total_counter +']" value="" class="product_sku" style="width:125px;" /></td><td class="title_product_'+total_counter+'"></td><td class="align_center"><input type="number" id="quantity_product_'+total_counter+'" name="product_quantity['+total_counter+']" value="1" class="align_center cart_change" style="width:75px;" /></td><td class="align_center"><input type="text" id="price_product_'+total_counter+'" name="product_price['+total_counter+']" value="" class="align_center cart_change" style="width:75px;" /></td><td class="subtotal_product_'+total_counter+'"></td><td class="align_center delete"><i class="fa fa-trash"></i></td></tr>');
				
				$("#product_" + total_counter).autocomplete({
					source: skuArray,
					select: function( event, ui )
					{
						var element		= event.target.id;
						var getValue	= ui.item.value;
						setItemDetail(element, getValue);
					}
				});
				total_counter++;
			});
			
			calculateTotalAmount();
			
			$("body").delegate(".cart_change, #shipping_amount", "keyup", function(e)
			{
				calculateTotalAmount();
			});
			
			$("body").delegate(".delete i", "click", function(e)
			{
				if(confirm('Are you sure that you want to remove this item?'))
				{
					var position	= $(this).parent().parent().attr('data-row');
					$('.order_row_product_' + position).remove();
					calculateTotalAmount();
				}
			});
			
			$("body").delegate(".product_sku", "blur", function(e)
			{
				var element		= $(this).attr('id');
				var getValue	= $(this).val();
				setItemDetail(element, getValue);
			});
		}
	});
	
	function calculateTotalAmount()
	{
		var total_amount	= 0;
		$('.order_row').each(function()
		{
			var position		= $(this).attr('data-row');
			var product_id		= $('#product_' + position).val();
			var quantity		= parseInt($('#quantity_product_' + position).val());
			var price			= parseFloat($('#price_product_' + position).val());
			if(product_id != '')
			{
				sub_amount		= quantity * price;
				$('.subtotal_product_' + position).html(sub_amount.toFixed(2));
				total_amount	+= sub_amount;
			}
		});
		
		$('#sub_total').val(total_amount.toFixed(2));
		var shipping_amount		= parseFloat($('#shipping_amount').val());
		var grand_total			= total_amount + shipping_amount;
		$('#grand_total').val(grand_total.toFixed(2));
	}
	
	function setItemDetail(element, getValue)
	{
		var selected	= -1;
		for(var i in skuArray)
		{
			if(skuArray[i] == getValue)
			{
				selected	= i;
				break;
			}
		}
		
		if(selected > -1)
		{
			$('#product_id_' + element).val(productArray[selected].id);
			$('.title_' + element).html(productArray[selected].title);
			$('#quantity_' + element).val(1);
			$('#price_' + element).val(parseFloat(productArray[selected].price).toFixed(2));
			$('.subtotal_' + element).html(parseFloat(productArray[selected].price).toFixed(2));
		}
		else
		{
			$('#' + element).val('');
		}
		calculateTotalAmount();
	}
</script>
</body>
</html>