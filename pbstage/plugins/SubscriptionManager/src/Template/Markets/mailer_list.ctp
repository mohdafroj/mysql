<?php echo $this->element('Markets/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding">
                <table width="95%" class="table-bordered table-hover table-condensed no-padding no-border" align="center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th class="text-center" width="18%">Created</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php foreach ($mailerList as $value): ?>
                        <tr><!-- start of row_2 -->
                            <td data-title="Id"><?=$value['id']?></td>
                            <td data-title="Title"><?=$value['drift_mailer']['title']?></td>
                            <td data-title="Subject"><?=$value['drift_mailer']['subject']?></td>
                            <td data-title="Created" class="text-center"><?=$this->Admin->emptyDate($value['created'])?></td>
                        </tr><!-- end of row_2 -->
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>

        </div><!-- end of table -->

</section>