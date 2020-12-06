<?php echo $this->Element('Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Customers/left_menu');?>            
        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
			<div class="tab-pane fade col-sm-12 col-xs-12 active in" id="tab_3"><!-- Orders -->
				<?= $this->Form->create(null, ['type'=>'get']) ?>
					<section class="content col-sm-12 col-xs-12">		
						<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
							<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
								<?php echo $this->Element('pagination');?>
							</div><!-- end of pagination -->
				
							<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
								<?= $this->Html->link(__('Reset Filter'), ['action' => 'Plans', $customerId, 'key', md5($customerId)], ['class'=>'btn btn-div-cart btn-1e']) ?>
								<?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
							</div><!-- end of buttons -->
						</div><!-- end of pagination or buttons -->
			
						<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
							<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
								<thead>
									<tr>
										<th>#ID</th>
										<th>SKU</th>
										<th>Name</th>
										<th>Price</th>
										<th>Duration</th>
										<th>Validity</th>
										<th>Created</th>
										<th class="text-center">Action</th>
									</tr>
								</thead>                
								<tbody>                    
									<tr><!-- start of row_1 -->
										<td data-title="#ID"></td>
										<td data-title="SKU">
											<?= $this->Form->text('sku', ['value' => $sku, 'class' => 'form-control', 'placeholder' => 'Search by SKU']); ?>
										</td>										
										<td data-title="Name">
											<?= $this->Form->text('name', ['value' => $name, 'class' => 'form-control', 'placeholder' => 'Search by Name']); ?>
										</td>										
										<td data-title="Price"></td>										
										<td data-title="Duration"></td>
										<td data-title="Validity"></td>
										<td data-title="Created"></td>										
										<td data-title="Action">
											<?=$this->Form->select('status', $this->SubscriptionManager->siteStatus, ['empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
										</td>
									</tr><!-- end of row_1 -->
									
									<?php foreach ($plans as $value):?>
										<tr><!-- start of row_2 -->
											<td data-title="#Id">
												<?= $value->id ?>
											</td>
											<td data-title="SKU"><?= $value->sku?></td>
											<td data-title="Name"><?= $value->name?></td>
											<td data-title="Price" class="text-right">
												<?= $this->Number->format($value->price, array('places' => 2)).' '.$value->currency ?>
											</td>
											<td data-title="Duration">
												<?= $this->SubscriptionManager->planDuration[$value->duration] ?? 'N/A';?>
											</td>
											<td data-title="Validity">
												<?= $this->SubscriptionManager->emptyDate($value->created);?>
											</td>
											<td data-title="Created">
												<?= $this->SubscriptionManager->emptyDate($value->created);?>
											</td>
											<td data-title="Action" class="text-center">
								<?php if( strtolower($value->is_active) == 'active' ) {?>
												<button data-toggle="modal" data-target="#selectedPlan" data-object='<?= $value ?>' type="button" title ="View Active Plan" class="btn btn-success btn-xs"><i class="fa fa-eye"></i></button>
								<?php } else {?>
												<button data-toggle="modal" data-target="#selectedPlan" data-object='<?= $value ?>' type="button" title ="View Inactive Plan" class="btn btn-warning btn-xs"><i class="fa fa-eye"></i></button>
								<?php }?>
											</td>
										</tr><!-- end of row_2 -->
									<?php endforeach; ?>
								</tbody>
							</table>
						</div><!-- end of table -->
					</section>
				<?= $this->Form->end() ?>
			</div><!-- end of orders -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>

<!-- Modal -->
<div class="modal fade" id="selectedPlan" tabindex="-1" role="dialog" aria-labelledby="selectedPlanModalLabel" aria-hidden="true">
<?= $this->Form->create($customerPlan, ['type'=>'post']) ?>
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
	            <h4 class="modal-title text-primary text-center"><strong>Please enter following details</strong></h4>
            </div>
            <div class="modal-body no-padding-bottom no-padding-top" id="selectedPlanBody">
				<div class="flex_box_content price_detail"><!-- start of box_content -->
					<div class="form-group row">
						<label class="control-label text-center">Validity for </label> "<span class="selected-plan"></span>"
					</div>
					<div class="form-group row">
						<label class="control-label text-center text-red error-message"></label>
					</div>
					<div class="form-group row">
						<label class="control-label">From <span class="text-red">*</span></label>
						<div>
							<?=$this->Form->text('valid_from', ['id'=>'datepicker11', 'class' => 'form-control', 'placeholder' => 'Enter date', 'readonly'=>true])?>
						</div>
					</div>
					<div class="form-group row">
						<label class="control-label">To <span class="text-red">*</span></label>
						<div>
							<?=$this->Form->text('valid_to', ['id'=>'datepicker22', 'class' => 'form-control', 'placeholder' => 'Enter date', 'readonly'=>true])?>
							<?=$this->Form->hidden('id', ['id'=>'planId'])?>
						</div>
					</div>
					<div class="form-group row no-padding-bottom">
						<div class="text-center">
							<?=$this->Form->select('is_active', $this->SubscriptionManager->siteStatus, ['id'=>'is_active', 'class' => 'form-control'])?>
						</div>
					</div>
				</div>
			</div>	
            <div class="modal-footer">
				<div class="form-group row no-padding-bottom no-margin-bottom">
					<div class="col-sm-6 col-xs-12 text-center no-padding-right"><button class="btn btn-success btn-sm" type="submit" id="updatePlan">Save</button></div>
					<div class="col-sm-6 col-xs-12 text-center no-padding-left"><button class="btn btn-warning btn-sm" data-dismiss="modal">Close</button></div>
				</div>
            </div>
        </div>
    </div>
<?= $this->Form->end() ?>
</div>

<script>
	var dataObject;
	$("#datepicker11").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function () {
			var durationDays = dataObject.duration || 0;
			switch (durationDays) {
				case 1: // For one year
					durationDays = 365;
					break;
				case 2: // For 6 month
					durationDays = 182;
					break;
				case 3: // For 3 month
					durationDays = 90;
					break;
				default: 	
					durationDays = 30;
			}
            var fromDate = $(this).datepicker('getDate');
            var toDate = $('#datepicker22').datepicker('getDate');
            //difference in days. 86400 seconds in day, 1000 ms in second
            var dateDiff = (toDate - fromDate)/(86400 * 1000);
            if (dateDiff > durationDays){
                $(".error-message").text("Sorry, you can not exceed plan duration!");
            } else if ( (dateDiff > 0) && ( dateDiff <= durationDays ) ){
                $('#datepicker22').datepicker('setDate', toDate);
            }
        }
	});
	
	$("#datepicker22").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function () {
			var durationDays = dataObject.duration || 0;
			switch ( durationDays ) {
				case 1: // For one year
					durationDays = 365;
					break;
				case 2: // For 6 month
					durationDays = 182;
					break;
				case 3: // For 3 month
					durationDays = 90;
					break;
				default: 	
					durationDays = 30;
			}
            var toDate = $(this).datepicker('getDate');
            var fromDate = $('#datepicker11').datepicker('getDate');
            //difference in days. 86400 seconds in day, 1000 ms in second
            var dateDiff = (toDate - fromDate)/(86400 * 1000);
            if (dateDiff > durationDays){
                $(".error-message").text("Sorry, you can not exceed plan duration!");
			} else if ( (dateDiff > 0) && (dateDiff <= durationDays) ){
                $('#datepicker22').datepicker('setDate', toDate);
            }
        }
	});
	
	$('#selectedPlan').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) // Button that triggered the modal
		dataObject = button.data('object') // Extract info from data-* attributes
		var modal = $(this);
		modal.find('.selected-plan').text(dataObject.name);
		var valid_from = ( dataObject.valid_from != null ) ? (dataObject.valid_from).substr(0, 10) : '';
		modal.find('#datepicker11').val(valid_from);
		var valid_to = ( dataObject.valid_to != null ) ? (dataObject.valid_to).substr(0, 10) : '';
		modal.find('#datepicker22').val(valid_to);
		modal.find('#is_active').val(dataObject.is_active);
		modal.find('#planId').val(dataObject.id);
	});

	$("#updatePlan").click(function(){
		console.log(dataObject);
		return true;
	});
</script>