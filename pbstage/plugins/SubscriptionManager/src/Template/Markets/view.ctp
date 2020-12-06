<?php echo $this->element('Markets/top_menu');
$mailerSchedule = $this->Admin->mailerSchedule;
array_push($mailerSchedule, 'Select Action');
?>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'get', 'id' => 'market_email_form'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->element('pagination'); ?>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Reset Filter', ['action' => 'view', $bucketId, 'key', md5($bucketId)], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('title', 'Title')?></th>
                        <th><?=$this->Paginator->sort('subject', 'Mailer Subject')?></th>
                        <th><?=$this->Paginator->sort('schedule_id', 'Action Type')?></th>
                        <th><?=$this->Paginator->sort('created', 'Created')?></th>
                        <th class="text-center"><?=$this->Paginator->sort('status', 'Status')?></th>
                        <th class="text-center"><?=__('Actions')?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="Title">
                        	<?=$this->Form->text('title', ['value' => $mailerTitle, 'class' => 'form-control', 'placeholder' => 'Enter title']);?>
                        </td>
                        <td data-title="Mailer Subject">
                            <?=$this->Form->text('subject', ['value' => $subject, 'class' => 'form-control', 'placeholder' => 'Enter subject']);?>
                        </td>
                        <td data-title="Action Type">
                            <?=$this->Form->select('schedule_id', $mailerSchedule, ['value' => $schedule_id, 'default' => '', 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Created">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?=$this->Form->text('created', ['value' => $created, 'id' => 'datepicker1', 'class' => 'form-control', 'placeholder' => 'Enter created date']);?>
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('status', $this->Admin->siteStatus, ['value' => $status, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($mailers as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$this->Number->format($value->id)?></td>
                        <td data-title="Title"><?=h($value->title)?></td>
                        <td data-title="Subject"><?=h($value->subject)?></td>
                        <td data-title="Action Type"><?=$mailerSchedule[$value->schedule_id]?></td>
                        <td class="text-center" data-title="Created"><?=h($this->Admin->emptyDate($value->created));?></td>
                        <td class="text-center" data-title="Status"><?=h($this->Admin->checkValue(ucfirst($value->status)))?></td>
                        <td class="text-right" data-title="Action">
                        <?php
if ($value->status == 'active') {
    echo $this->Html->link(__('<i class="fa fa-send"></i> Send'), '#ConfirmDelete', ['data-toggle' => 'modal',
        'data-action' => $value->id, 'escape' => false, 'class' => 'btn btn-primary btn-xs btn-confirm']);
}
?>
                        &nbsp;&nbsp;
                        <?=$this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'mailer', $bucketId, 'key', md5($bucketId), $value->id, 'ref', md5($value->id)], ['escape' => false, 'class' => 'btn btn-default btn-xs'])?>
                        &nbsp;&nbsp;
                        <?=$this->Html->link(__('Stats'), '#dataForStats', ['data-toggle' => 'modal', 'data-id' => $value->id, 'data-subject' => $value->subject, 'escape' => false, 'class' => 'btn btn-success btn-xs btn-stats'])?>
                        </td>
                    </tr>
            <?php endforeach;
if (!count($mailers)):
?>
                    <tr>
                        <td colspan="8" class="text-center"><strong>Sorry, no record found!</strong></td>
                    </tr>
            <?php endif?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>



<!-- Modal -->
<div class="modal fade" id="ConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <?=$this->Form->create(null, ['url' => ['action' => 'send', $bucketId, 'key', md5($bucketId)], 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true])?>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center" id="myModalLabel"><strong>Send mailer to Customers</strong></h4>
                </div>
                <div class="modal-body">
                    Do you want really to send this mailer!
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="mailerId" name="id" value="0" />
                    <button type="submit" class="btn btn-danger btn-sm active">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    <?=$this->Form->end()?>
</div>

<!-- Modal -->
<div class="modal fade" id="dataForStats" tabindex="-1" role="dialog" aria-labelledby="statsModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" id="statsModal"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-body" style="height:auto; max-height:300px; overflow-y:scroll;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Date</th>
                                            <th style="width: 40px">Processed</th>
                                            <th style="width: 40px">Delivered</th>
                                            <th style="width: 40px">Opens</th>
                                            <th style="width: 40px">Clicks</th>
                                            <th style="width: 40px">Bounces</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-stats"></tbody>
                                </table>
                            </div>
                            <div class="box-footer clearfix">
                                <div class="col-md-5">
                                    <input type="hidden" id="catId" name="catId" value="0" />
                                    <strong>Start:&nbsp;&nbsp;</strong><input type="text" id="datepicker2" value="<?php echo date('Y-m-d'); ?>" />
                                </div>
                                <div class="col-md-5">
                                    <strong>End:&nbsp;&nbsp;</strong><input type="text" id="datepicker3" value="<?php echo date('Y-m-d'); ?>" />
                                </div>
                                <div class="col-md-2 text-right">
                                    <button type="submit" class="btn btn-danger btn-sm btnstatssearch">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    <script>
        $(document).ready(function() {
            $(".btn-confirm").on("click", function () {
                var mailerId = $(this).attr('data-action');
                $("#mailerId").val(mailerId);
            });
            $(".btn-stats").on("click", function () {
                var catId = $(this).attr('data-id');
                $("#catId").val(catId);
                var subject = $(this).attr('data-subject');
                $("#statsModal").html('<strong>Stats for mailer "'+subject+'"</strong>');
                var currentDate = "<?php echo date('Y-m-d'); ?>";
                $("#datepicker2").val(currentDate);
                $("#datepicker3").val(currentDate);
                $(".body-stats").html('<tr><td colspan="7">Please click on search button for stats from <strong>"'+currentDate+' to '+currentDate+'"</strong>!</td></tr>');
            });
            $(".btnstatssearch").on("click", function () {
                searchStats();
            });

        });

        function searchStats(){
            $(".body-stats").html("Wait....");
            var catId = $("#catId").val();
            var startDate = $("#datepicker2").val();
            var endDate   = $("#datepicker3").val();
            var csrfToken = <?=json_encode($this->request->getParam('_csrfToken'))?>;
            $.ajax({
                url: "<?php echo $this->Url->build(['action' => 'getStats']) ?>",
                method: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                data:{"catId":catId,"startDate":startDate,"endDate":endDate},
                success: function( res )
                {
                    $(".body-stats").html(res);
                    return false;
                }
            });
        }
    </script>