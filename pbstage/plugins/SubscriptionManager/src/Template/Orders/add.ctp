<?php echo $this->Html->css('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'); ?>
<style>
	.align_center{text-align:center;}
	.delete i{cursor:pointer;}
</style>
<script>
	var skuArray			= new Array();
	var productArray		= new Array();
	<?php

$counter = 0;
foreach ($products as $temp_product) {
    ?>
		skuArray[<?php echo $counter; ?>]		= "<?php echo $temp_product['title']; ?>";
		productArray[<?php echo $counter; ?>]	= {"id":"<?php echo $temp_product['product']['id']; ?>", "title":"<?php echo $temp_product['title']; ?>", "sku":"<?php echo $temp_product['product']['sku_code']; ?>", "price":"<?php echo $temp_product['price']; ?>"};
		<?php
$counter++;
}
?>
</script>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
	<div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
		<h3><?=h('Create Order')?></h3>
		<ul class="list-inline list-unstyled">
			<li><?=$this->Html->link(__('Back'), ['action' => 'index'], ['class' => 'btn btn-div-cart btn-1e'])?></li>
		</ul>
	</div><!-- end of inner_heading -->
</section>
<section id="create_order" class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
    	<ul id="myTab" class="nav nav-tabs tab_div"><!-- start of left_part -->
            <li class=""><a href="#tab_1" data-toggle="tab">Order Details</a></li>
        </ul><!-- end of left_part -->

        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
            <div class="in active tab-pane fade col-sm-12 col-xs-12" id="tab_2"><!-- Addresses  -->
                <?=$this->Form->create($order, ['class' => 'form-horizontal', 'id' => 'create_order_form', 'autocomplete' => 'off']);?>

					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->

					<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
							<div class="box box-default"><!-- start of box_div -->
								<div class="box-header with-border"><h3 class="box-title">Customer Information</h3></div>
								<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
									<div class="box-body">
							<?php if (isset($order->customer->id)) {
    echo $this->Form->hidden('id_customer_new', ['id' => 'id_customer_new', 'value' => $order->customer->id]);
} else {?>
										<div class="form-group">
											<label class="col-sm-4">Customer: <span class="text-red">*</span></label>
											<div class="col-sm-8">
												<?=$this->Form->hidden('id_customer_new', ['id' => 'id_customer_new']);?>
												<?=$this->Form->text('customer_name_new', ['class' => 'form-control', 'placeholder' => 'Select Customer', 'value' => '', 'id' => 'customer_name_new']);?>
											</div>
										</div>
							<?php }?>
										<div id="customer_id" class="form-group">
											<b>Customer ID:</b> <span><?php echo $order->customer->id ?? null; ?></span>
										</div>
										<div id="customer_name" class="form-group">
											<b>Name:</b> <span><?php echo $order->customer->firstname ?? null; ?></span>
										</div>
										<div id="customer_email" class="form-group">
											<b>Email:</b> <span><?php echo $order->customer->email ?? null; ?></span>
										</div>
										<div id="customer_mobile" class="form-group">
											<b>Mobile:</b> <span><?php echo $order->customer->mobile ?? null; ?></span>
										</div>
										<div class="form-group">
											<div>
												<select id="addresses" class="form-control" onchange="setCustomerAddress(this.value);">
													<option value="0">Please Select an Address</option>
												</select>
											</div>
										</div>
									</div>
								</div><!-- end of box_content -->
							</div><!-- end of box_div -->

						</div><!-- end of col_div -->

						<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
							<div class="box box-default"><!-- start of box_div -->
								<div class="box-header with-border"><h3 class="box-title">Shipping Details</h3></div>
								<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
									<div class="box-body">
										<div class="form-group">
											<label class="col-sm-3 control-label">First Name <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_firstname', ['class' => 'form-control', 'placeholder' => 'Enter firstname', 'id' => 'shipping_firstname', 'value' => $order->shipping_firstname]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Last Name <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_lastname', ['class' => 'form-control', 'placeholder' => 'Enter lastname', 'id' => 'shipping_lastname', 'value' => $order->shipping_lastname]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Address <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->textarea('shipping_address', ['rows' => 1, 'class' => 'form-control', 'placeholder' => 'Enter address', 'id' => 'shipping_address', 'value' => $order->shipping_address]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">City <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_city', ['class' => 'form-control', 'placeholder' => 'Enter city', 'id' => 'shipping_city', 'value' => $order->shipping_city]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Pincode <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_pincode', ['class' => 'form-control', 'placeholder' => 'Enter pincode', 'id' => 'shipping_pincode', 'value' => $order->shipping_pincode]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Mobile <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_phone', ['class' => 'form-control', 'placeholder' => 'Enter mobile number', 'id' => 'shipping_phone', 'value' => $order->shipping_phone]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Email <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_email', ['class' => 'form-control', 'placeholder' => 'Enter email', 'id' => 'shipping_email', 'value' => $order->shipping_email]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">State <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?=$this->Form->text('shipping_state', ['class' => 'form-control', 'placeholder' => 'Enter state', 'id' => 'shipping_state', 'value' => $order->shipping_state]);?>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-3 control-label">Country <span class="text-red">*</span></label>
											<div class="col-sm-9">
											<?=$this->Form->select('shipping_country', $country, ['class' => 'form-control', 'id' => 'shipping_country', 'value' => $order->shipping_country]);?>
											</div>
										</div>
									</div>
								</div><!-- end of box_content -->
							</div><!-- end of box_div -->

						</div><!-- end of col_div -->

					</div><!-- end of middle_content -->


					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
						<div class="box box-default">
							<div class="box-header with-border"><h3 class="box-title">Order Details</h3></div>
						</div>
					</div><!-- end of middle_content -->

					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr>
									<th>Product Title</th>
									<th>SKU</th>
									<th class="align_center">Quantity</th>
									<th class="align_center">Price</th>
									<th>Sub Total</th>
									<th class="align_center">Action</th>
								</tr>
							</thead>
							<tbody id="order_item_table">
								<?php
$counter = 0;
if (isset($order->order_details) && count($order->order_details) > 0) {
    foreach ($order->order_details as $item_row) {
        ?>
										<tr class="order_row order_row_product_<?php echo $counter; ?>" data-row="<?php echo $counter; ?>"><!-- start of row_2 -->
											<td>
												<input type="hidden" id="id_product_<?php echo $counter; ?>" name="product_id[<?php echo $counter; ?>]" value="<?php echo $item_row->product_id; ?>" />
												<input type="text" id="product_<?php echo $counter; ?>" name="product_sku[<?php echo $counter; ?>]" value="<?php echo $item_row->title; ?>" class="product_sku" style="width:250px;" />
											</td>
											<td class="title_product_<?php echo $counter; ?>">
												<?php echo $item_row->sku_code; ?>
											</td>
											<td class="align_center">
												<input type="number" id="quantity_product_<?php echo $counter; ?>" name="product_quantity[<?php echo $counter; ?>]" value="<?php echo $item_row->quantity; ?>" class="align_center cart_change" style="width:75px;" />
											</td>
											<td class="align_center">
												<input type="text" id="price_product_<?php echo $counter; ?>" name="product_price[<?php echo $counter; ?>]" value="<?php echo number_format($item_row->price, 2, '.', ''); ?>" class="align_center cart_change" style="width:75px;" />
											</td>
											<td class="subtotal_product_<?php echo $counter; ?>">
												<?php echo number_format(($item_row->quantity * $item_row->price), 2); ?>
											</td>
											<td class="align_center delete">
												<i class="fa fa-trash"></i>
											</td>
										</tr><!-- end of row_2 -->
										<?php
$counter++;
    }
}
?>
							</tbody>
						</table>
						<script>
							var total_counter	= <?php echo $counter; ?>;
						</script>
					</div><!-- end of table -->

					<div class="form-group align_center" style="clear:both; padding-top:25px;">
						<button id="add_more" type="button" class="btn btn-div-buy btn-1b">Add More</button>
					</div>


					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
						<div class="box box-default">
							<div class="box-header with-border"><h3 class="box-title">Payment Details</h3></div>
						</div>
						<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
							<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
								<div class="box-body">
									<div class="form-group">
										<label class="col-sm-4 control-label">Sub Total <span class="text-red">*</span></label>
										<div class="col-sm-8">
											<?=$this->Form->text('sub_total', ['class' => 'form-control', 'placeholder' => '', 'value' => '0.00', 'id' => 'sub_total', 'readonly' => 'readonly']);?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Payment Method <span class="text-red">*</span></label>
										<div class="col-sm-8">
										<?=$this->Form->select('payment_method', $pgMethods, ['class' => 'form-control', 'id' => 'payment_method', 'value' => $order->payment_method_id]);?>
										</div>
									</div>
								</div>
							</div><!-- end of box_content -->
						</div><!-- end of col_div -->
						<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
							<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
								<div class="box-body">
									<div class="form-group">
										<label class="col-sm-4 control-label">Shipping Amount <span class="text-red">*</span></label>
										<div class="col-sm-8">
											<?=$this->Form->text('shipping_amount', ['class' => 'form-control', 'placeholder' => '', 'id' => 'shipping_amount', 'value' => '0.00']);?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">Grand Total <span class="text-red">*</span></label>
										<div class="col-sm-8">
											<?=$this->Form->text('grand_total', ['class' => 'form-control', 'placeholder' => '', 'id' => 'grand_total', 'value' => '0.00']);?>
										</div>
									</div>
								</div>
							</div><!-- end of box_content -->
						</div><!-- end of col_div -->
						<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
							<div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
								<div class="box-body">
									<div class="form-group">
										<label class="col-sm-4 control-label">Coupon Code:</label>
										<div class="col-sm-5">
											<?=$this->Form->text('coupon_code', ['class' => 'form-control', 'placeholder' => '', 'id' => 'couponCode', 'value' => '']);?>
										</div>
										<div class="col-sm-3">
											<button id="checkCouponCode" type="button" class="btn btn-div-buy btn-1b">Apply</button>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label"></label>
										<div class="col-sm-8" id="couponMsg">
											shipping free
										</div>
									</div>
								</div>
							</div><!-- end of box_content -->
						</div><!-- end of col_div -->
					</div><!-- end of middle_content -->

					<div class="form-group align_center">
						<button id="create_order_btn" type="submit" class="btn btn-div-buy btn-1b">Submit</button>
					</div>
				<?=$this->Form->end();?>
            </div><!-- end of address -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>


<!-- Modal -->
<?php if ($locationId == 0) {?>
<div class="modal fade in" style="display:block;">
    <?=$this->Form->create(null, ['type' => 'get', 'class' => 'form-horizontal', 'novalidate' => true])?>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center"><strong>Please choose Shipping Country</strong></h4>
                </div>
                <div class="modal-body">
					<select name="location-id" class="form-control">
					<?php foreach ($locations as $loc) {?>
						<option value="<?php echo $loc['id']; ?>"><?php echo $loc['title']; ?></option>
					<?php }?>
					</select>
                </div>
                <div class="modal-footer pb-1">
                    <button type="submit" class="btn btn-danger btn-sm">Submit</button>
                </div>
            </div>
        </div>
    <?=$this->Form->end()?>
</div>
<?php }?>

<script type="text/javascript">

	$(document).ready(function(){
		$("#checkCouponCode").click(function(){
			var code = $("#couponCode").val();
			var email = $("#customer_email span").html();
			//var productIds = document.getElementsByName('product_price[]').value; //$("input[name='product_id[]']").val();
			console.log(productIds);
			if( code != "" ){
				$("#couponMsg").html(code+" | "+email);
			}else{
				$("#couponMsg").html("Please enter coupon!");
			}
		});
	});

	function manageCoupon(id, reqType){
		var msg = '';
		if( reqType == 'put'){
			msg = 'Sure, you want to update status!';
		}else if( reqType == 'delete'){
			msg = 'Sure, you want to delete coupon code!';
		}
		if( confirm(msg) ){
			$.ajax({
				type: reqType,
				data: {"id":id},
				url: "<?php echo $this->Url->build(['action' => 'checkCoupon']); ?>",
				success: function(result){
					//location.reload();
				}
			});
		}
		return false;
	}
</script>


<script type="text/javascript">
	$(document).ready(function()
	{
		if($('#create_order').length > 0)
		{
			$( ".product_sku" ).autocomplete({
				source: skuArray,
				select: function( event, ui )
				{
					var element		= event.target.id;
					var getValue	= ui.item.value;
					setItemDetail(element, getValue);
				}
			});

			if($( "#customer_name_new" ).length > 0)
			{
				$( "#customer_name_new" ).autocomplete({
					source: "<?php echo $this->Url->build(['plugin' => 'SubscriptionManager', 'controller' => 'customers', 'action' => 'search']); ?>",
					minLength: 2,
					select: function( event, ui )
					{
						setCustomerDetail(ui.item);
					}
				});
			}

			$('#add_more').click(function()
			{
				$('#order_item_table').append('<tr class="order_row order_row_product_'+total_counter+'" data-row="'+total_counter+'"><td><input type="hidden" id="id_product_'+total_counter+'" name="product_id['+total_counter+']" value="" /><input type="text" id="product_'+total_counter+'" name="product_sku['+ total_counter +']" value="" class="product_sku" style="width:250px;" /></td><td class="title_product_'+total_counter+'"></td><td class="align_center"><input type="number" id="quantity_product_'+total_counter+'" name="product_quantity['+total_counter+']" value="1" class="align_center cart_change" style="width:75px;" /></td><td class="align_center"><input type="text" id="price_product_'+total_counter+'" name="product_price['+total_counter+']" value="" class="align_center cart_change" style="width:75px;" /></td><td class="subtotal_product_'+total_counter+'"></td><td class="align_center delete"><i class="fa fa-trash"></i></td></tr>');

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

			$('#create_order_btn').click(function(event)
			{
				event.preventDefault();
				var same_address	= 0;
				if($('#same_address').is(':checked'))
				{
					same_address	= 1;
				}
				if($('#id_customer_new').val() == '')
				{
					alert('Please select a customer');
				}
				else if($('#shipping_firstname').val() == '')
				{
					alert('Please enter Shipping First Name.');
				}
				else if($('#shipping_lastname').val() == '')
				{
					alert('Please enter Shipping Last Name.');
				}
				else if($('#shipping_address').val() == '')
				{
					alert('Please enter Shipping Address.');
				}
				else if($('#shipping_city').val() == '')
				{
					alert('Please enter Shipping City.');
				}
				else if($('#shipping_pincode').val() == '')
				{
					alert('Please enter Shipping Pincode.');
				}
				else if($('#shipping_state').val() == '' )
				{
					alert('Please select Shipping State.');
				}
				else if($('#shipping_phone').val() == '')
				{
					alert('Please enter Shipping Phone.');
				}
				else if($('#shipping_email').val() == '')
				{
					alert('Please enter Shipping Email.');
				}
				else if($('#payment_method').val() == '')
				{
					alert('Please select a Payment Method.');
				}
				else
				{
					if($('.order_row').length > 0)
					{
						var is_found	= true;
						$('.order_row').each(function()
						{
							var temp_position	= $(this).attr('data-row');
							if($('#id_product_' + temp_position).val() == '' || $('#id_product_' + temp_position).val() == '0')
							{
								is_found	= false;
							}
							else if($('#quantity_product_' + temp_position).val() == '' || $('#quantity_product_' + temp_position).val() == '0')
							{
								is_found	= false;
							}
							else if($('#product_' + temp_position).val() == '')
							{
								is_found	= false;
							}
						});
						if(is_found)
						{
							$('#create_order_form').submit();
						}
						else
						{
							alert('Please complete your cart.');
						}
					}
					else
					{
						alert('Please add atleast one product into cart.');
					}
				}
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
			console.log(productArray[selected].id);
			$('#id_' + element).val(productArray[selected].id);
			$('.title_' + element).html(productArray[selected].sku);
			$('#quantity_' + element).val(1);
			$('#price_' + element).val(parseFloat(productArray[selected].price).toFixed(2));
			$('.subtotal_' + element).html(parseFloat(productArray[selected].price).toFixed(2));
		}
		else
		{
			$('#' + element).val('');
			$('#id_' + element).val(0);
			$('.title_' + element).html('');
			$('#quantity_' + element).val(1);
			$('#price_' + element).val(0.00);
			$('.subtotal_' + element).html(0.00);
		}
		calculateTotalAmount();
	}

	function setCustomerDetail(customer)
	{
		$('#id_customer_new').val(customer.id);
		$('#customer_id span').html(customer.id);
		$('#customer_email span').html(customer.email);
		$('#customer_name span').html(customer.firstname + " " + customer.lastname);
		$('#customer_mobile span').html(customer.mobile);
		getAddresses();

	}

	var addresses = [];
	getAddresses();
	function getAddresses(){
		var customerId = $("#id_customer_new").val(); //118119; //
		if( customerId > 0 ){
			$.ajax({
				url: "<?php echo $this->Url->build(['plugin' => 'SubscriptionManager', 'controller' => 'customers', 'action' => 'getAddresses', '?' => ['customer-id' => '']]); ?>"+customerId,
				method: 'GET',
				data:[],
				success: function( data )
				{
					addresses = JSON.parse(data); console.log(addresses);
					if( data.length > 0 ){
						var options = '<option value="0">Please Select an Address</option>';
						for(var i=0; i < addresses.length; i++){
							options += '<option value="'+addresses[i]['id']+'">'+addresses[i]['firstname']+' '+addresses[i]['lastname']+' ('+addresses[i]['mobile']+')'+addresses[i]['address']+'</option>';
						}
						$("#addresses").html(options);
					}
				}
			});
		}
	}

    function setCustomerAddress(id){

		if( id == 0 ){
			$('#shipping_firstname').val('');
			$('#shipping_lastname').val('');
			$('#shipping_address').val('');
			$('#shipping_city').val('');
			$('#shipping_pincode').val('');
			$('#shipping_phone').val('');
			$('#shipping_email').val('');
			$('#shipping_state').val('');
		}else{
			for(var i=0; i < addresses.length; i++){
				if( id == addresses[i]['id'] ){
					$('#shipping_firstname').val(addresses[i]['firstname']);
					$('#shipping_lastname').val(addresses[i]['lastname']);
					$('#shipping_address').val(addresses[i]['address']);
					$('#shipping_city').val(addresses[i]['city']);
					$('#shipping_pincode').val(addresses[i]['pincode']);
					$('#shipping_phone').val(addresses[i]['mobile']);
					$('#shipping_email').val(addresses[i]['email']);
					$('#shipping_state').val(addresses[i]['state']);
				}
			}
		}
	}


</script>
