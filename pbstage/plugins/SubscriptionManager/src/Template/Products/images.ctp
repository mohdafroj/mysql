<?php echo $this->Element('Products/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
            <?php echo $this->Element('Products/sub_menu');?>
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
			<?= $this->Form->create(null, ['id'=>'updateForm', 'url'=>$this->Url->build(array('controller'=>'Products','action'=>'updateImages'), true), 'class' => 'form-horizontal', 'novalidate' => true]); ?>
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr><th>ID</th><th>Image</th><th>Order</th><th>Base</th><th>Small</th><th>Thumbnail</th><th>Large</th><th>Exclude</th><th>Remove</th><th style="text-align:center;">Status</th><th style="text-align:center;">Action</th></tr>
							</thead>                
							<tbody>
					<?php //pr($images);
						for($i=0; $i < count($images); $i++){
					?>		
								<tr>
									<td data-title="ID"><?php echo $i+1;?><input type="hidden" name="ids[]" value="<?php echo $images[$i]['id']; ?>" /><?php //echo $this->Form->hidden('ids[]', ['value'=>$images[$i]['id']]); ?></td>
									<td data-title="Image"><img src="<?php echo $images[$i]['imgBase']; ?>" alt="No Define" style="width:50px;height:50px;" /></td>
									<td data-title="Order"><?php echo $this->Form->text('order[]', ['value'=>$images[$i]['imgOrder']]); ?></td>
									<td data-title="Base"><?php echo $this->Form->radio('base[]', [$images[$i]['id']=>$images[$i]['isBase']], ['value'=>$images[$i]['isBase'],'label'=>false,'checked'=>$images[$i]['isBase']]); ?></td>
									<td data-title="Small"><?php echo $this->Form->radio('small[]', [$images[$i]['id']=>$images[$i]['isSmall']], ['label'=>false,'checked'=>$images[$i]['isSmall']]); ?></td>
									<td data-title="Thumbnail"><?php echo $this->Form->radio('thumbnail[]', [$images[$i]['id']=>$images[$i]['isThumbnail']], ['label'=>false,'checked'=>$images[$i]['isThumbnail']]); ?></td>
									<td data-title="Thumbnail"><?php echo $this->Form->radio('large[]', [$images[$i]['id']=>$images[$i]['isLarge']], ['label'=>false,'checked'=>$images[$i]['isLarge']]); ?></td>
									<td data-title="Exclude"><?php echo $this->Form->checkbox('exclude[]', ['value'=>$images[$i]['id'],'checked'=>$images[$i]['exclude']]); ?></td>
									<td data-title="Remove"><?php echo $this->Form->checkbox('remove[]', ['value'=>$images[$i]['id']]); ?></td>
									<td data-title="Status" align="center"><?= $this->Form->select('status[]', $this->Admin->siteStatus, ['value'=>$images[$i]['isActive']]);?></td>
									<td data-title="Action" align="center">
										<?= $this->Html->link('Edit', ['action'=>'images', $id, 'key', md5($id), $images[$i]['id']])?>
									</td>
								</tr>
					<?php }?>			
							</tbody>
						</table>
            <?= $this->Form->end(); ?>
					</div><!-- end of table -->
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
			<?= $this->Form->create(null, ['id'=>'linksForm','url'=>$this->Url->build(array('controller'=>'Products','action'=>'saveImages'), true),'class'=>'form-horizontal', 'novalidate' => true]); ?>
						<div class="box-body">
							<div class="form-group">
                                <label class="col-sm-2 control-label">Large Image Link:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('largeImage', ['class'=>'form-control', 'value'=>$image['largeImage'], 'placeholder'=>'Please enter a valid link!']); ?>
									<?php echo $this->Form->hidden('productId', ['value'=>$id]); ?>
									<?php echo $this->Form->hidden('id', ['value'=>$imgId]); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Base Image Link: <span class="text-red">*</span></label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('baseImage', ['class'=>'form-control', 'value'=>$image['baseImage'], 'placeholder'=>'Please enter a valid link!']); ?>
									<span class="text-red">
									
									</span>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Cart Image Link:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('smallImage', ['class'=>'form-control', 'value'=>$image['smallImage'], 'placeholder'=>'Please enter a valid link!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Thumbnail Image Link:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('thumbImage', ['class'=>'form-control', 'value'=>$image['thumbImage'], 'placeholder'=>'Please enter a valid link!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Popup Image Link:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('popupImage', ['class'=>'form-control', 'value'=>$image['popupImage'], 'placeholder'=>'Please enter a valid link!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Title:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('titleImage', ['class'=>'form-control', 'value'=>$image['titleImage'], 'placeholder'=>'Please enter title!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Alt Content:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->textarea('altContent', ['class'=>'form-control', 'value'=>$image['altContent'], 'placeholder'=>'Please enter title!']); ?>
                                </div>
                            </div>
							<div class="form-group" class="disabled">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
									<img id="loader" style="display:none;" src="/blog/img/loading.gif" alt="Loading..." width="50px" height="50px" />
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <button type="button" id="linksButton" class="btn btn-div-buy btn-1b" onClick="saveImages();">Save Links</button>
                                </div>
                            </div>
						</div>
            <?= $this->Form->end(); ?>
					</div><!-- end of table -->
				</section>
            </div><!-- end of profile -->			
        </div><!-- end of right_part -->            
    </div><!-- end of tab -->
</section>
<script type="text/javascript">
function updateImages(){
	//$('#updateForm').submit();
	var formData = $('#updateForm').serialize();
	var token = '<?php echo $token = $this->request->getParam('_csrfToken');?>';
            $.ajax({
                type:"POST",
                url:"<?php echo $this->Url->build(array('controller'=>'Products','action'=>'updateImages'));?>",
                dataType: 'text',
				data: formData,				
                async:false,
				beforeSend: function(xhr){
					xhr.setRequestHeader('X-CSRF-Token', token);
				},
                success: function(response){
					window.location.reload(true);
                },
                error: function (err) {
					window.location.reload(true);
                }
            });
	
}

function saveImages(){
	var formData = $('#linksForm').serialize();
	var token = '<?php echo $token = $this->request->getParam('_csrfToken');?>';
            $.ajax({
                type:"POST",
                url:"<?php echo $this->Url->build(array('controller'=>'Products','action'=>'saveImages'));?>",
                dataType: 'text',
				data: formData,				
                async:false,
				beforeSend: function(xhr){
					xhr.setRequestHeader('X-CSRF-Token', token);
				},
                success: function(response){
					var json = JSON.parse(response);
					if(json.status){
						window.location = '<?php echo $this->Url->build(array('controller'=>'Products','action'=>'images', $id, 'key',md5($id)));?>';
					}else{
						alert(json.message);
					}
                },
                error: function (err) {
                    alert(err);
                }
            });
}

</script>
