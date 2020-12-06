<?php
$this->Paginator->setTemplates(['templates' => 'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
    <div class="col-sm-12 col-xs-12 inner_heading">
        <!-- start of inner_heading -->
        <h3><?= h('Manage Logs') ?></h3>
        
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
    <?= $this->Form->create(null, ['type' => 'get']) ?>
    <div class="col-sm-12 col-xs-12 no-padding">
        <!-- start of pagination or buttons -->
        <div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding">
            <!-- start of pagination -->
            <ul class="list-unstyled list-inline pagination_div">
                <li>
                    Page
                    <span class="span_1">
                        <?= $this->Paginator->prev(__('Prev')) ?>
                        <input type="text" class="form-control" value="<?= $this->Paginator->counter(['format' => __('{{page}}')]) ?>">
                        <?= $this->Paginator->next(__('Next')) ?>
                    </span>
                    of <?= $this->Paginator->counter(['format' => __('{{pages}}')]) ?> pages
                </li>
                <li>
                    View
                    <span class="span_1 span_2">
                        <?= $this->Form->select('limit', $this->Admin->selectMenuOptions, ['value' => $this->Paginator->param('perPage'), 'default' => 50, 'empty' => FALSE, 'onChange' => 'this.form.submit();', 'class' => 'form-control']); ?>
                    </span>
                    per page
                </li>
                <li>Total <?= $this->Paginator->counter(['format' => __('{{count}}')]) ?> records found</li>
            </ul>
        </div><!-- end of pagination -->

        <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div">
            <!-- start of buttons -->

            <?= $this->Html->link('Reset Filter', ['controller' => 'Users/syslog'], ['class' => 'btn btn-div-cart btn-1e']); ?>
            <?= $this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']); ?>
        </div><!-- end of buttons -->
    </div><!-- end of pagination or buttons -->

    <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table">
        <!-- start of table -->
        <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id', 'Id') ?></th>
                    <th><?= $this->Paginator->sort('username', 'User Name') ?></th>
                    <th><?= $this->Paginator->sort('entity_name', 'Entity Name') ?></th>
                    <th><?= $this->Paginator->sort('machine_ip', 'Client Ip Address ') ?></th>
                    <th><?= $this->Paginator->sort('action_type', 'Action') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Created Date') ?></th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- start of row_1 -->
                    <td data-title="Id"></td>
                    <td data-title="User Name">
                            
                        <?= $this->Form->select('username',$userList, ['value'=>$username??'','empty' => 'Select User Name','class' => 'form-control']); ?>
                    </td>
                    
                    <td data-title="Entity Name">
                            
                        <?= $this->Form->select('entity_name',$entityName, ['value'=>$entity_name,'empty' => 'Select Entity','class' => 'form-control']); ?>
                    </td> 
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>

                    <td data-title="Created Date">
                        <div class="input-group date">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <?= $this->Form->text('created', ['id' => 'datepicker1', 'class' => 'form-control']); ?>
                        </div>
                    </td>



                </tr><!-- end of row_1 -->
                <?php               
                foreach ($sysLogs as $value) :
                    ?>
                    <tr>
                        <!-- start of row_2 -->
                        <td data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="User Name"><?= h($this->Admin->checkValue($value->user->firstname.' '.$value->user->lastname.'( '.$value->user->username.' )')) ?></td>
                        <td data-title="Module Name"><?= h($this->Admin->checkValue($value->entity_name)) ?></td>
                        <td data-title="Client Ip Address"><?= h($this->Admin->checkValue($value->machine_ip)) ?></td>
                        <td data-title="Action"><?= h($this->Admin->checkValue($value->action_type)); ?></td>
                        <td data-title="Created Date"><?= h($this->Admin->emptyDate($value->created)); ?></td>

                    </tr><!-- end of row_2 -->
                <?php
                endforeach;
                ?>
            </tbody>
        </table>

    </div><!-- end of table -->
    <?= $this->Form->end() ?>
</section>