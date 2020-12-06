<?php echo $this->element('Admin/Markets/top_menu');
$sno = 1;
?>
<section class="content col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding">
                <table width="95%" class="table-bordered table-hover table-condensed no-padding no-border" align="center">
                    <thead>
                        <tr>
                            <th>S No</th>
                            <th>Mailer ID</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th class="text-center">List Size</th>
                            <th class="text-center" width="18%">Created</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php foreach ($mailerList as $value): ?>
                        <tr><!-- start of row_2 -->
                            <td data-title="S NO"><?=$sno++?></td>
                            <td data-title="Mailer Id"><?=$value['id']?></td>
                            <td data-title="Title"><?=$value['drift_mailer']['title']?></td>
                            <td data-title="Subject"><?=$value['drift_mailer']['subject']?></td>
                            <td data-title="List size" class="text-right"><?=$value['size_of_list']?>&nbsp;</td>
                            <td data-title="Created" class="text-center"><?=$this->Admin->emptyDate($value['created'])?></td>
                            <td data-title="Action" class="text-center">
                            <?=$this->Html->link(__('<i class="fa fa-eye"></i>'), '#', ['data-toggle' => 'modal', 'data-id' => $value['id'], 'data-subject' => $value['drift_mailer']['subject'], 'data-target' => '#mailerDetail', 'escape' => false, 'class' => 'btn btn-danger mailerDetail btn-xs']);?>&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=$this->Form->postLink(__('<i class="fa fa-trash"></i>'), ['action' => 'mailerList', $value['id']], ['escape' => false, 'block' => false, 'method' => 'delete', 'class' => 'btn btn-info btn-xs', 'confirm' => __('Are you sure you want to delete # {0}?', $value['drift_mailer']['subject'])])?>
                            </td>
                        </tr><!-- end of row_2 -->
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>

        </div><!-- end of table -->
</section>
<!-- Modal -->
<div class="modal fade" id="mailerDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" id="mailerDetailSubject"></h4>
            </div>
            <div id="mailerContent" class="modal-body" style="height:auto; max-height:450px; overflow-y:scroll; overflow-x:hidden;"></div>
            <div class="modal-footer">&nbsp;</div>
        </div>
    </div>
</div>
<div class="modal fade" id="mailerDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center mailerDeleteSubject"></h4>
            </div>
            <div class="modal-body mailerDeleteContent" style="height:auto;"></div>
            <div class="modal-footer">
                <input type="hidden" id="mailerListId" name="mailerListId" value="0" />
                <button type="button" onClick="ConfirmDeleteMailer();" class="btn btn-danger btn-sm active">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
        $(document).ready(function() {
            $(".mailerDetail").on("click", function () {
                var mailerId = $(this).attr('data-id');
                var subject  = $(this).attr('data-subject'); console.log("content"+mailerId);
                $("#mailerDetailSubject").html('<strong>'+subject+'</strong>');
                $("#mailerContent").html('<div class="row"><div class="col-sm-12 text-center"><strong>Mailer detail are fetching, please wait...</strong></div></div>');
                var csrfToken = <?=json_encode($this->request->getParam('_csrfToken'))?>;
                $.ajax({
                    url: "<?php echo $this->Url->build(['action' => 'mailerList']) ?>",
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    data:{"id":mailerId},
                    success: function( res )
                    {
                        $("#mailerContent").html(res);
                    }
                });
            });
        });
    </script>