<?php echo $this->Element('Shopping/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
			<?=$this->Form->create($rule, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3"></label>
                                        <div class="col-sm-9 text-red">
											<?php //pr($error) ?>
										</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Rule Name <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->text('title', ['class' => 'form-control', 'placeholder' => 'Enter title']);?>
											<div class="text-red">
												<?php echo $error['title']['_empty'] ?? null; ?>
												<?php echo $error['title']['length'] ?? null; ?>
												<?php echo $error['title']['charNum'] ?? null; ?>
											</div>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Description </label>
										<div class="col-sm-9">
											<?=$this->Form->textarea('description', ['class' => 'form-control', 'placeholder' => 'Enter content ...']);?>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Discount Type <span class="text-red">*</span></div>
													<?=$this->Form->select('discount_type', $this->SubscriptionManager->discountType, ['style' => 'width:100%;', 'class' => 'form-control'])?>
												</div>
												<div class="text-red">
													<?php echo $error['discount_type']['_empty'] ?? null; ?>
												</div>
											</div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Discount Amount <span class="text-red">*</span></div>
													<?=$this->Form->text('discount_value', ['class' => 'form-control', 'style' => 'width:100%;']);?>
												</div>
												<div class="text-red">
													<?php echo $error['discount_value']['discountValue'] ?? null; ?>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Valid From <i class="fa fa-calendar"></i></div>
													<?=$this->Form->text('valid_from', ['id' => 'datepicker1', 'class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Valid from']);?>
												</div>
												<div class="text-red">
												<?php echo $error['valid_from']['validFrom'] ?? null; ?>
												<?php echo $error['valid_from']['date'] ?? null; ?>
												</div>
											</div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Valid To <i class="fa fa-calendar"></i></div>
													<?=$this->Form->text('valid_to', ['id' => 'datepicker2', 'class' => 'form-control', 'style' => 'width:100%;', 'placeholder' => 'Valid to']);?>
												</div>
												<div class="text-red">
												<?php echo $error['valid_to']['validTo'] ?? null; ?>
												<?php echo $error['valid_to']['date'] ?? null; ?>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Usages <span class="text-red">*</span>(0 for multiple)</div>
													<?=$this->Form->text('usages', ['value' => 0, 'class' => 'form-control', 'placeholder' => 'Enter number for usages times!']);?>
													<div class="input-group-addon">Times</div>
												</div>
												<div class="text-red">
													<?php echo $error['usages']['integer'] ?? null; ?>
												</div>
											</div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Status <span class="text-red">*</span></div>
													<?=$this->Form->select('status', $this->Admin->siteStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
												</div>
												<div class="text-red">
													<?php echo $error['status']['_empty'] ?? null; ?>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Free Shipping </div>
													<?=$this->Form->select('free_ship', ['no' => 'No', 'yes' => 'Yes'], ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
												</div>
											</div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Mini Qty for discount to applied</div>
													<?=$this->Form->text('min_qty', ['class' => 'form-control', 'style' => 'width:100%;', 'value' => 1]);?>
												</div>
												<div class="text-red">
													<?php echo $error['min_qty']['decimal'] ?? null; ?>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
												<div class="input-group date">
													<div class="input-group-addon">Minimum Amount of Product(<?php echo $this->SubscriptionManager->priceLogo; ?>)<span class="text-red">*</span></div>
													<?=$this->Form->text('min_price', ['value' => 1, 'class' => 'form-control', 'style' => 'width:100%;']);?>
												</div>
												<div class="text-red">
												<?php echo $error['min_price']['_empty'] ?? null; ?>
												<?php echo $error['min_price']['minPrice'] ?? null; ?>
												</div>
											</div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Categories </label>
										<div class="col-sm-5">
											<div class="form-group sidebar tree_div">
											<?php
//pr($cateTree)
function cateFun($cateTreeItem, $categories, $data = '')
{
    if (count($cateTreeItem)) {
        foreach ($cateTreeItem as $chilName) {
            $child = count($chilName->children);
            $checked = in_array($chilName->id, $categories) ? 'checked="checked"' : null;
            if ($child > 0) {
                echo '<li class="treeview active">
						<!--label><input type="checkbox" name="categories[]" value="' . $chilName->id . '" class="minimal" ' . $checked . ' /></label-->
						<label style="width:90%;">' . $chilName->name . ' (' . $child . ')</label>
						<ul class="treeview-menu menu-open">';
                cateFun($chilName->children, $categories, $data);
                echo '</ul>
				  	  </li>';
            } else {
                echo '<li><!--input type="checkbox" name="categories[]" value="' . $chilName->id . '" class="minimal" ' . $checked . ' /-->
				<table class="table-striped table-bordered" style="width:100%" id="table-' . $chilName->id . '">
				<caption class="bg-danger text-center ">Category - ' . $chilName->name . '</caption>
				<thead>
					<tr>
						<th>Brand Name</th>
						<th>Discount</th>
						<th></th>
					</tr>
				</thead>
				<tbody></tbody>
				<table class="table table-striped table-bordered" style="width:100%">
				<tbody>
					<tr><td colspan="3" class="text-center text-danger" id="brandError-' . $chilName->id . '">&nbsp;</td></tr>
					<tr>
						<td width="30%"><select id="brand-' . $chilName->id . '" class="brand-all"></select></td>
						<td class="text-center"><select id="type-' . $chilName->id . '" ><option value="percentage">Percentage</option><option value="rupees">Rupees</option></select>&nbsp;&nbsp;<input type="text" id="discount-' . $chilName->id . '" style="width:20%;" value="0"></td>
						<td width="10%" class="text-center"><label onClick="addBrands(' . $chilName->id . ');" class="button btn-primary btn-xs">Add</label></td>
					</tr>
				</tbody>
				</table>
				</li>';
            }
        }
    }
}
?>
												<ul class="sidebar-menu">
													<?php cateFun($cateTree, $categoriesIds = []);?>
												</ul>
												<input type="hidden" name="categories" value=""/>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Customers (enter commas seperated emails)</label>
												<?=$this->Form->textarea('emails', ['class' => 'form-control', 'rows' => 10, 'style' => 'width:100%;', 'placeholder' => 'Please enter valid email id!']);?>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:50%;float:left;margin-right:1%;">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
											</div>
										</div>
									</div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            <?=$this->Form->end();?>
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>

<style>
	.skin-blue .tree_div .sidebar-menu > li:hover > a, .skin-blue .tree_div .sidebar-menu > li.active > a{background:none; color:#363636; padding:5px; display:inline-block; width:90%;}
	.skin-blue .tree_div .sidebar-menu > li > .treeview-menu{padding-left:15px;background: #ffffff !important;}
	.tree_div ul.sidebar-menu li a{display:inline-block; padding-left:5px; width:90%;}
</style>
<script>
	var brands = <?php echo json_encode($brands); ?>;
	var selectedCatBrand = [];
	setCategoryBrands(selectedCatBrand); //set selected category brands

	$(document).ready(function() {
		var brandAll = '<option value="">Select Brand</option><option value="0">All</option>';
		$.each(brands, function(k, v){
			brandAll = brandAll + '<option value="'+v.id+'">'+v.title+'</option>';
		});
		$(".brand-all").html(brandAll);
	});

	function addBrands(catId){
		$("#brandError-"+catId).html('&nbsp;');
		var brandId = $("#brand-"+catId).val();
		var discountType = $("#type-"+catId).val();
		var discount = $("#discount-"+catId).val();

		if( brandId == '' ){
			$("#brandError-"+catId).html('Please select brand!');
		}else if( $.isNumeric(discount) == false ) {
			$("#brandError-"+catId).html('Please enter discount in numeric format!');
		}else{
			var addStatus = true;
			discount = Number(discount).toString();
			var discountLabel = ( discountType == 'percentage' ) ? discount + '%' : 'Rs. '+discount;
			$.each(selectedCatBrand,function(key, value){
				if( value.category ){
					if( (value.category == catId) && (value.brand == brandId || 0 == brandId) ){ addStatus = false; }
				}else{
					if( value == catId ){ addStatus = false; }
				}
			});
			if( addStatus ){
				selectedCatBrand.push({"category":catId,"brand":brandId,"type":discountType, "discount":discount});
				var discountLabel = ( discountType == 'percentage' ) ? discount + '%' : 'Rs. '+discount;
				var markup = '<tr id="rowId-'+(catId+"-"+brandId)+'"><td>'+getBrandName(brandId)+'</td><td>' + discountLabel + '</td><td class="text-center"><i onClick="deleteBrand('+catId+', '+brandId+');" class="fa fa-trash"></i></td></tr>';
				$("#table-"+catId).append(markup);
				$("[name='categories']").val(JSON.stringify(selectedCatBrand));
				$("#brand-"+catId).val('');
				$("#type-"+catId).val('percentage');
				$("#discount-"+catId).val('0');
			}else{
				if( brandId == 0 ){
					$("#brandError-"+catId).html('"All" brand not added because some brand already added!');
				}else{
					$("#brandError-"+catId).html('The "'+getBrandName(brandId)+'" brand already added!');
				}
			}
		}
		return false;
	}

	function setCategoryBrands(arr){
		$.each(arr,function(key, value){
			var discountLabel = '';
			var markup = '';
			if( value.category ) {
				discountLabel = ( value.type == 'percentage' ) ? value.discount + '%' : 'Rs. '+value.discount;
				markup = '<tr id="rowId-'+(value.category+"-"+value.brand)+'"><td>'+getBrandName(value.brand)+'</td><td>' + discountLabel + '</td><td class="text-center"><i onClick="deleteBrand('+value.category+', '+value.brand+');" class="fa fa-trash"></i></td></tr>';
				$("#table-"+value.category).append(markup);
			}else if( value != undefined && $.isNumeric(value) ) {
				value = Number(value).toString();
				discountLabel = ( $("[name='discount_type']").children("option:selected").val() == 'percentage' ) ? $("[name='discount_value']").val() + '%' : 'Rs. '+$("[name='discount_value']").val();
				markup = '<tr id="rowId-'+value+'-0"><td>All</td><td>' + discountLabel + '</td><td class="text-center"><i onClick="deleteBrand('+value+', 0);" class="fa fa-trash"></i></td></tr>';
				$("#table-"+value).append(markup);
			}
		});
		$("[name='categories']").val(JSON.stringify(arr));
		return false;
	}

	function deleteBrand(catId, brandId){
		$.each(selectedCatBrand, function(k,v){
			//console.log(k, v);
			if( $.isNumeric(v) ){
				if( v == catId ){
					selectedCatBrand.splice(k,1);
					$("#rowId-"+catId+"-"+brandId).remove();
					$("[name='categories']").val(JSON.stringify(selectedCatBrand));
					$("#brandError-"+catId).html('One item deleted!');
				}
			}else if( v.category ){
				if((v.category == catId) && (v.brand == brandId)){
					selectedCatBrand.splice(k,1);
					$("#rowId-"+catId+"-"+brandId).remove();
					$("[name='categories']").val(JSON.stringify(selectedCatBrand));
					$("#brandError-"+catId).html('One item deleted!');
				}
			}
		});
		console.log(selectedCatBrand);
		return false;
	}

	function getBrandName(id){
		let name = '';
		if( id != 0){
			$.each(brands, function(k, v){
				if( v.id == id ) { name = v.title; }
			});
		}else{
			name = 'All';
		}
		return name;
	}
</script>
