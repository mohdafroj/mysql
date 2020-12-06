<?php echo $this->element('Markets/top_menu');?>
<section class="content col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <div class="col-sm-8 col-xs-8 flex_box no-padding-left xs-no-padding">
                <table width="95%" class="table-bordered table-hover table-condensed no-padding no-border" align="center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th width="60%">Campaign Name</th>
                            <th class="text-center">Mailers</th>
                            <th class="text-center" width="18%"><?= __('Actions') ?></th>
                        </tr>
                    </thead>                
                    <tbody>
            <?php $c= 1;foreach ($mailerList as $value):?>
                        <tr><!-- start of row_2 -->
                            <td data-title="Id"><?= $c++ ?></td>
                            <td data-title="Campaign Name"><?= $this->Html->link($value['title'], ['action' => 'view', $value['id'], 'key', md5($value['id'])]) ?></td>
                            <td data-title="No of Mailers" class="text-right">
                                <?php
                                    echo count($value['drift_mailers']); 
                                ?>
                            </td>
                            <td data-title="Actions" class="text-center">
                                
                                <?php 
                                    if( !in_array($value['id'], [2,3]) ){
                                        echo $this->Html->link(__('<i class="fa fa-pencil"></i>'), ['action' => 'index', $value['id'], 'key', md5($value['id'])],['escape'=>false,'class'=>'btn btn-default btn-xs']).'&nbsp;|&nbsp;';
                                        echo $this->Form->postLink(__('<i class="fa fa-trash"></i>'), ['action' => 'index', $value['id'], 'key', md5($value['id'])], ['block' => false, 'method'=>'delete', 'escape'=>false, 'class' =>'btn btn-default btn-xs', 'confirm' => __('Are you sure you want to delete {0}?', '"'.$value['title'].'" Campaign' )]);
                                    }
                                ?>                                
                            </td>
                        </tr><!-- end of row_2 -->
                    <?php endforeach; ?>    
                    </tbody>
                </table>
            </div>

            <div class="col-sm-4 col-xs-4 table_view responsive-mobile-table"><!-- start of table -->
                <?= $this->Form->create($mailer, ['id'=>'market_email_form']) ?>
                    <table width="99%" class="table-bordered table-hover table-condensed no-padding no-border" align="center">
                        <tr>
                            <td data-title="Campaign Name">Campaign Name:</td>
                            <td data-title="Box"><?= $this->Form->text('title', ['value'=>$mailer->title, 'class'=>'form-control', 'placeholder'=>'Enter Campaign name']); ?></td>
                        </tr>
                        <tr>
                            <td data-title="Campaign Name"></td>
                            <td data-title="Box">
                                <button type="submit" class="btn btn-div-buy btn-1b btn-sm-long">Save</button>&nbsp;
                                <?= $this->Html->link(__('Clear'), ['action' => 'index'], ['class'=>'btn btn-div-buy btn-1e btn-sm-long']) ?>
                            </td>
                        </tr>
                    </table>
                <?= $this->Form->end() ?>
            </div><!-- end of table -->
        </div><!-- end of table -->

</section>