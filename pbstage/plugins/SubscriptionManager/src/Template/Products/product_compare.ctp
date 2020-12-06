<?php //pr($notesFamily);
?>
<section class="content-header col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12 inner_heading">
		<!-- start of inner_heading -->
		<h3>Similar Product Information </h3>

	</div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12">
		<!-- start of right_part -->
		<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table">
			<!-- start of tab -->
			<div id="myTabContent" class="tab-content tab_div_content">
				<!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12">
					<!-- start of content_1 -->
					<section class="content col-sm-12 col-xs-12">
						<div class="col-sm-12 col-xs-12 no-padding">
							<!-- start of pagination or buttons -->
							<div class="col-md-12 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div">
								<?= $this->Form->create(NULL, ['form-control']); ?>
								<div class="form-row">
									<div class="box-body">
										<div class="form-group col-sm-2 col-xs-12">
											<div class="row">
												<div class="col-sm-4">Gender:</div>
												<div class="col-sm-8"><?= $this->Form->select('gender', $this->Admin->siteGender, ['class' => 'form-control']) ?></div>
											</div>

										</div>
										<div class="form-group col-sm-2 col-xs-12" id="grpId">
											<div class="row">
												<div class="col-sm-5 col-xs-12">Family Name:</div>
												<div class="col-sm-7 col-xs-12 btnGroup"><?= $this->Form->select('family_name', $notesFamily, ['id' => 'family_name', 'onchange'=>"checkValidationClick('#grpId')", 'class' => 'form-control selectpicker', 'multiple' => true, 'data-live-search' => "true"], ['empty' => '(choose one)']) ?></div>
											</div>
										</div>
										<div class="form-group col-sm-3 col-xs-12">
											<div class="row">
												<div class="col-sm-5 col-xs-12">Brand:</div>
												<div class="col-sm-7 col-xs-12 btnGroup"><?= $this->Form->select('brand_name', $brandFamily, ['class' => 'form-control']) ?></div>
											</div>
										</div>
										<div class="form-group col-sm-5 col-xs-12" >
											<div class="form-group row">
												<label for="Description" class="col-sm-3 control-label">Product:</label>
												<div class="col-sm-7 btnGroup" id="prodId"><?= $this->Form->select('product_from', $productData, ['id' => 'product_from', 'class' => 'form-control', 'multiple' => true]) ?></div>
											</div>
										</div>
									</div>
									<div class="form-group col-sm-12 col-xs-12 text-center">
										<div class="col-sm-12">
											<?= $this->Form->button('Save', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']); ?>
										</div>
									</div>
								</div>
								<?= $this->Form->end(); ?>
							</div>
						</div>


					</section>
					<section class="content col-sm-12 col-xs-12">
						<div class="col-sm-12 col-xs-12">
							<!-- start of right_part -->
							<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table">
								<!-- start of tab -->
								<div id="myTabContent" class="tab-content tab_div_content">
									<!-- start of right_part -->
									<div class="tab-pane fade in active col-sm-12 col-xs-12">
										<!-- start of content_1 -->
										<section class="content col-sm-12 col-xs-12">
											<div class="col-sm-12 col-xs-12 no-padding">
												<!-- start of pagination or buttons -->
												<div class="col-md-12 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div">

													<table id="example" class="table table-striped table-bordered" style="width:100%">
														<thead>
															<tr>
																<th class="th-sm">Product Name </th>
																<th class="th-sm">Similar Product Total Value </th>
																<th>Total Match Notes</span></span></span></th>
																<th>Total Family Earn Value</th>
																<th>Notes Match Value</th>
																<th>Score</th>
																<th>Affinity Score</th>
															</tr>
														</thead>
														<tbody>
													<?php   foreach ( $resultFinal as $val ) { ?>
																<tr>
																	<td><?php echo $val['name'] ?></td>
																	<td><?php echo $val['value']; ?></td>
																	<td><?php echo $val['totalMatchNotes']; ?></td>
																	<td><?php echo $val['FamilyEarnValue']; ?></td>
																	<td><?php echo $val['notesMatchValue']; ?></td>
																	<td><?php echo $val['score']; ?></td>
																	<td><?php echo $val['affinityScore'] ?? 0; ?></td>
																</tr>
													<?php	} ?>

														</tbody>
													</table>
												</div>
											</div>
										</section>
									</div><!-- end of right_part -->
								</div><!-- end of tab -->


<?php //pr($familyTable);
echo $this->Html->css('SubscriptionManager.plugins/algo/css/bootstrap-multiselect.css');
echo $this->Html->script('SubscriptionManager.plugins/algo/js/bootstrap-multiselect.js');
?>
<script>
	$(document).ready(function() {
		$('#family_name').multiselect({
			enableFiltering: true
		});

		$('#product_from').multiselect({
			enableFiltering: true,
			maxHeight: 400,
			enableCaseInsensitiveFiltering: true,
			enableFullValueFiltering: true
		});

	});

// $(document).on('change','#prodId .multiselect-container label input[type=checkbox]',function(e){
// 	var s=$(this);
// alert('sss');
// console.log(s);
// 	var t=$('#prodId .multiselect-container label input[type="checkbox"]:checked').length;
// 	alert(t);
// 	if(t > 3){
// 		return false;
// 		s.prop('checked',false);
// 		s.click(false);
// 	}
// 	//alert(event.type);
// 	$('#prodId input[type="checkbox"]').on("click change", function(e){
// 	alert(e.type + " is fired");
// 	if(t > 3){
		//console.log($(this));
//		return false;
	//	e.preventDefault();
	//return false;
//}
//});
// 	$('#prodIdsss .multiselect-container label').click(function(){
// 		alert($(this).html());
// 	});
// var t=$('#prodIdsss .multiselect-container label input[type="checkbox"]:checked').length;
// if(t > 3){
// 	alert($(this).html());
// 	return false;
// }
//});
// $(document).ready(function(e){
// 	var len =	$('#prodId .multiselect-container label input[type="checkbox"]:checked').length;
// 	if(len > 3){
//     return false;
//    }
// //	$('#prodId .multiselect-container label input[type="checkbox"]:checked').each(function(i,v){
// 	//alert('ss');
// 	//console.log($(this));
// //});
// });
$("#prodId").mousedown(function() {
	var len = $("#prodId input[type='checkbox']:checked").length;
	//alert("mouse down"+len);
	if ( len > 2 ) {
    	return false;
   	} else {
		checkValidationClick('#prodId',len);
   	}
});
function checkValidationClick ( id, len ) {	
	//alert('length'+len);
	console.log('length',len);
	//var len = $("#prodId input[type='checkbox']:checked").length;
 	$(id+' input[type="checkbox"]').click(function(){	  
		if ( len > 3 ) {
		console.log(len);
			return false;
		}
  	});
}
									
</script>