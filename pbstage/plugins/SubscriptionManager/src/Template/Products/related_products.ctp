<?php echo $this->Element('Products/top_menu'); ?>
		<?=$this->Form->create($products, ['type' => 'get', 'id' => 'relatedIdsForm', 'class' => 'form-horizontal', 'novalidate' => true]);?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<?php echo $this->Element('Products/sub_menu'); ?>
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
						<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
							<?php echo $this->Element('pagination'); ?>
						</div><!-- end of pagination -->

						<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
							<?=$this->Html->link('Reset Filter', ['controller' => 'Products', 'action' => 'related-products', $id, 'key', md5($id)], ['class' => 'btn btn-div-cart btn-1e']);?>
							<?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
							<?=$this->Form->button('Save', ['type' => 'button', 'class' => 'btn btn-div-buy btn-1b', 'id' => 'saveRelatedProducts', 'data-id' => $id, 'data-url' => $this->Url->build(['controller' => 'Products', 'action' => 'saverelated'])]);?>
						</div><!-- end of buttons -->
					</div><!-- end of pagination or buttons -->

					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr>
									<th></th>
									<th><?=$this->Paginator->sort('sku_code', 'SKU Code')?></th>
									<th><?=$this->Paginator->sort('is_active', 'Status')?></th>
								</tr>
							</thead>
							<tbody>
								<tr><!-- start of row_1 -->
									<td data-title="">
										<?php //echo $this->Form->select("status", ['any'=>'Any','yes'=>'Yes','no'=>'No'], ['empty'=> false,'style'=>'width:100%;','class'=>'form-control']);?>
									</td>
									<td data-title="SKU Code">
										<?=$this->Form->text('sku_code', ['value' => $skuCode, 'class' => 'form-control', 'placeholder' => 'Enter sku code']);?>
									</td>

									<td data-title="Status">
										<?=$this->Form->select('is_active', $this->Admin->siteStatus, ['value' => $isActive, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
									</td>
								</tr><!-- end of row_1 -->
                    <?php foreach ($products as $value): ?>
								<tr>
									<td data-title="">
										<div class="form-group">
											<div class="col-sm-offset-3 col-sm-10">
												<div class="checkbox">
													<label class="no-padding">
														<?=$this->Form->checkbox(null, ['class' => 'minimal', 'value' => $value->id, 'checked' => in_array($value->id, $relatedIds)]);?>
													</label>
												</div>
											</div>
										</div>
									</td>
									<td data-title="SKU Code"><?=h($value->sku_code)?></td>
									<td data-title="Status"><?=h($this->Admin->checkValue(ucfirst($value->is_active)))?></td>
								</tr>
					<?php endforeach;?>
							</tbody>
						</table>
					</div><!-- end of table -->
				</section>
                </div><!-- end of right_part -->
            </div><!-- end of profile -->

        </div><!-- end of right_part -->

    </div><!-- end of tab -->
</section>
            <?=$this->Form->end();?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript">
	$(document).ready(function (){
		$("#saveRelatedProducts").on("click", function(){
			//document.getElementById("relatedIdsForm").method = "post";
			var currentId = $(this).data('id');
			var targeturl = $(this).data('url');

			var alldIds = $("input:checkbox").map(function(){
				return $(this).val();
			}).get();

			var chkdIds = $("input:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			//alert(Cookies.get('csrfToken'));
			//var formData = $('#relatedIdsForm').serialize();
			//$("#chkdIds").val(chkdIds);
			//alert($("input[type=hidden][name=_csrfToken]").val());
			//alert(currentId);
			//var myFormData = new FormData();
			//myFormData.append("currentId", currentId);
			//myFormData.append("currentAllIds", alldIds);
			//myFormData.append("currentChkIds", chkdIds);

			$.ajax({
				url: targeturl,
				type: "POST",
				cache:false,
				processData:true,
				beforeSend: function(xhr){
					//xhr.setRequestHeader('Content-Type', 'application/json');
					xhr.setRequestHeader('X-CSRF-Token', $("input[type=hidden][name=_csrfToken]").val());
				},
				//data:myFormData,
				data:{"currentId":currentId,"currentChkIds":chkdIds,"currentAllIds":alldIds},
				dataType:'HTML',
				success: function(response){
					//var data = JSON.stringify(data);
					window.location.reload();
					//alert(request.getAllResponseHeaders());
				},
				error:function (XMLHttpRequest, textStatus, errorThrown) {
                    alert(textStatus+": "+errorThrown);
                }
			});
		});
	});
</script>