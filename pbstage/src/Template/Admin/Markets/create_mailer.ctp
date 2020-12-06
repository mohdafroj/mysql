<?php echo $this->element('Admin/Markets/top_menu'); 
    $conditions = json_decode($mailer->conditions) ?? [];
?>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">
		
    <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
                   
        <?= $this->Form->create($mailer, ['enctype'=>'multipart/form-data', 'id'=>'submit_form_data', 'class' => 'form-horizontal', 'novalidate' => true]); ?>
            <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
                                
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                
                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Title <span class="text-red">*</span></label>                                    
                                    <div class="col-sm-9">
                                    <?= $this->Form->text('title', ['value'=>$mailer->title, 'class'=>'form-control', 'placeholder'=>'Enter mailer title']); ?>
                                        <span class="text-red">
										    <?php
												echo $errors['title']['_empty'] ?? NULL; 
												echo $errors['title']['length'] ?? NULL; 
												echo $errors['title']['charNum'] ?? NULL; 
											?>
										</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Mailer Subject <span class="text-red">*</span></label>                                    
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('subject', ['value'=>$mailer->subject, 'class'=>'form-control', 'placeholder'=>'Enter mailer subject']); ?>
                                        <span class="text-red">
										    <?php
												echo $errors['subject']['_empty'] ?? NULL; 
												echo $errors['subject']['length'] ?? NULL; 
												echo $errors['subject']['charNum'] ?? NULL; 
											?>
										</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Sender Name</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('sender_name', ['value'=>$mailer->sender_name, 'class'=>'form-control', 'placeholder'=>'set sender name']); ?>
                                    </div>
                                </div>
                                
                                
                                <div class="form-group">
                                    <label for="schedule" class="col-sm-3 control-label">Action Type <span class="text-red">*</span></label>
                                    <div class="col-sm-4">
                                        <?= $this->Form->hidden('bucket_id', ['value'=>$bucketId]); ?>
                                        <?= $this->Form->select('schedule_id', $this->Admin->mailerSchedule, ['value'=>$mailer->schedule_id, 'id'=>'schedule_id', 'onchange'=>'actionType(this.value);', 'style'=>'width:100%; cursor:pointer;','class'=>'form-control'])?>
                                        <span class="text-red">
										    <?php
												echo $errors['schedule_id']['_empty'] ?? NULL; 
												echo $errors['schedule_id']['schedule_id'] ?? NULL; 
											?>
										</span>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date">
                                            <div class="input-group-addon">Send at:</div>
                                            <div class="col-sm-12">
                                                <?= $this->Form->text('send_at', ['value'=>$mailer->send_at, 'class'=>'form-control']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>                              
                                
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->

                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                    <?php 
                            $i = 0;
                            foreach($this->Admin->mailerConditions as $key=>$value){ ?>
                                <div class="form-group">
                                    <div class="col-sm-4 no-padding">
                                        <label class="control-label">(<?php echo $i+1; ?>) <?= $value ?>:</label>
                                    </div>
                                    <div class="col-sm-8 no-padding">
                                        <div class="col-sm-4">
                                            <input type="hidden" name="keyword[]" value="<?= $key ?>" class="form-control" />
                                            <?= $this->Form->select('schedule_type[]', ['Select Type','Set Days','Set Hours'], ['value'=>($schedule_type[$key] ?? 0), 'onChange'=>'selectChangeType('. $i .', this.value)', 'style'=>'width:100%; cursor:pointer;', 'class'=>'form-control schedule_type'])?>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group date">
                                                <div class="input-group-addon">From:</div>
                                                <input type="number" name="start[]" min="0" onChange="rangChange(<?php echo $i; ?>);" value="<?= ($start[$key] ?? 0) ?>" class="form-control" />
                                                <div class="input-group-addon">To:</div>
                                                <input type="number" name="end[]" min="0" onChange="rangChange(<?php echo $i++; ?>);" value="<?= ($end[$key] ?? 0) ?>" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php   } ?>
                            </div>               
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
                <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <strong>Set HTML Template</strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <?= $this->Form->textarea('content', ['value'=>$mailer->content, 'id'=>'content', 'class'=>'form-control', 'placeholder'=>'Enter html code here']); ?>
                                        <span class="text-red">
										    <?php
												echo $errors['content']['_empty'] ?? NULL; 
												echo $errors['content']['length'] ?? NULL; 
											?>
										</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" id="saveData" class="btn btn-div-buy btn-1b btn-sm-long">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
                
            </div><!-- end of middle_content -->
        <?= $this->Form->end() ?>
            
    </div><!-- end of tab -->

</section>
    <!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="selectConditions" tabindex="-1" role="dialog" aria-labelledby="selectConditionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-primary text-center"><strong>Please enter following details</strong></h4>
            </div>
            <div class="modal-body" id="selectConditionsBody">
                Please set at least one condition!
            </div>
            <div class="modal-footer">
                <button type="button" id="closeConditions" class="col-sm-12 btn btn-default">OK</button>
            </div>
        </div>
    </div>
</div>


<?= $this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js') ?>
<script>
	CKEDITOR.replace( 'content', {
    fullPage: true,
    //extraPlugins: 'font,panelbutton,colorbutton,colordialog,justify,indentblock,aparat,buyLink',
    // You may want to disable content filtering because if you use full page mode, you probably
    // want to  freely enter any HTML content in source mode without any limitations.
    allowedContent: true,
    autoGrow_onStartup: true,
    enterMode: CKEDITOR.ENTER_BR
	});
    CKEDITOR.config.height = 500;
    CKEDITOR.config.uiColor = '#38B8BF';
    //CKEDITOR.config.extraPlugins = 'autogrow';
    //CKEDITOR.config.autoGrow_minHeight = 250;
    //CKEDITOR.config.autoGrow_maxHeight = 600;
    //CKEDITOR.config.skin = 'kama';
</script>


<?= $this->Html->script($this->Url->build('/admin/plugins/timepicker/bootstrap-timepicker.min.js', true)) ?>
<script>
$(document).ready(function(){
    $(".timepicker").timepicker({
        showInputs: false
    });
    
    $("#closeConditions").click(function(){
        $("#selectConditions").removeClass('in');
        $("#selectConditions").css({"display": "none", "padding-right": "17px"});
    });
    actionType($("#schedule_id option:selected").val());

    $("#saveData").click(function(event){
        event.preventDefault();
        var message = '';
        var sendError = 0;
        if($("input[name='title']").val() == ''){
            message += '<li>Please enter mailer title!</li>'; 
        }

        if($("input[name='subject']").val() == ''){
            message += '<li>Please enter mailer subject!</li>'; 
        }
        var sendStatus = 0;
        $("input[name='keyword[]']").map(function(k, v){
            if( parseInt($("select.schedule_type")[k].value) > 0 ){
                sendStatus = 1;                
            }            
        });
        
        if( sendStatus == 0){
            message += '<li>Please set at least one condition!</li>'; 
        }
        if( CKEDITOR.instances.content.getData().length < 200 ){
            message += '<li>Please enter template content!</li>'; 
        }

        if( message != '' ){
            $("#selectConditionsBody").html('<ol>'+message+'</ol>');
            $("#selectConditions").addClass('in');
            $("#selectConditions").css({"display": "block", "padding-right": "17px"});
        }else{
            $('#submit_form_data').submit();
        }
    });

});
    function actionType(value){
        if( value == 1 ){
            $("input[name='send_at']").parent().removeClass();
            $("input[name='send_at']").parent().addClass('col-sm-12 bootstrap-timepicker');
            $("input[name='send_at']").parent().html('<input type="text" name="send_at" value="" class="form-control timepicker" />');
            $(".timepicker").timepicker({
                showInputs: false
            });
        }else{
            $("input[name='send_at']").parent().removeClass();
            $("input[name='send_at']").parent().addClass('col-sm-12');
            $("input[name='send_at']").parent().html('<input type="number" name="send_at" min="1" max="23" value="1" class="form-control" />');
        }
    }

    function selectChangeType(k, value){
        var start = parseInt($("input[name='start[]']")[k].value);
        var end   = parseInt($("input[name='end[]']")[k].value);
        if( value == 0 ){
            $("input[name='start[]']")[k].value = 0;
            $("input[name='end[]']")[k].value = 0;
        }else{
            if( start >= end ){
                $("input[name='end[]']")[k].value = start + 1;
            }
        }
        return false;
    }

    function rangChange(k){
        var schedule = parseInt($("select.schedule_type")[k].value);
        var start = parseInt($("input[name='start[]']")[k].value);
        var end   = parseInt($("input[name='end[]']")[k].value);
        if( schedule == 0){
            $("input[name='start[]']")[k].value = 0;
            $("input[name='end[]']")[k].value = 0;
        }else{
            if( start >= end ){
                $("input[name='end[]']")[k].value = start + 1;
            }
        }
        return false;
    }

</script>