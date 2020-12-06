<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Couriers Methods') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('Reset'), ['controller'=>'Pgs', 'action' => 'Couriers', 'reset'], ['class'=>'btn btn-div-buy btn-1e']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?= $this->Form->create(null, ['type'=>'post']) ?>        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th class="text-center">Id</th>
                        <th><Title></Title></th>
                        <th>Code</th>
                        <th class="text-center">Default Prepaid</th>
                        <th class="text-center">Default Postpaid</th>
                        <th class="text-center">Logo</th>
                    </tr>
                </thead>                
                <tbody>                    
           <?php foreach ($couriers as $value):?>         
                    <tr>
                        <td class="text-center" data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="Title"><?= h($value->title) ?></td>
                        <td data-title="Code"><?= h($value->code) ?></td>
                        <td class="text-center" data-title="Default Prepaid"><input type="radio" name="prepaid" onchange="changeDefault();" value="<?php echo $value->id; ?>" style="cursor:pointer;" <?php echo ($value->prepaid > 0) ? 'checked':''; ?> ></td>
                        <td class="text-center" data-title="Default Postpaid"><input type="radio" name="postpaid" onchange="changeDefault();" value="<?php echo $value->id; ?>" style="cursor:pointer;" <?php echo ($value->postpaid > 0) ? 'checked':''; ?> ></td>
                        <td data-title="Logo"><img src="<?php empty($value->logo) ? '' : $value->logo; ?>" class="img-responsive" /></td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>

<script>
    function changeDefault(){
        var form = document.forms[0];
        form.submit();
    }
</script>

