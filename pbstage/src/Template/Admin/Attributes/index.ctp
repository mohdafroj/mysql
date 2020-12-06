<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Manage Attributes') ?></h3>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?= __('S No') ?></th>
                        <th><?= __('Attribute Title') ?></th>
                        <th><?= __('Action') ?></th>
                    </tr>
                </thead>                
                <tbody>                                        
                    <tr>
                        <td data-title="Id"><?= __(1) ?></td>
                        <td data-title="Title"><?= __('Brands') ?></td>
                        <td data-title="Action" class="text-center"><?= $this->Html->link(__('View'), ['controller'=>'Attributes', 'action'=>'brands', 'key', md5('brands')]) ?></td>
                    </tr>
                    <tr>
                        <td data-title="Id"><?= __(2) ?></td>
                        <td data-title="Title"><?= __('Families') ?></td>
                        <td data-title="Action" class="text-center"><?= $this->Html->link(__('View'), ['controller'=>'Attributes', 'action'=>'families', 'key', md5('families')]) ?></td>
                    </tr>
                </tbody>
            </table>           
        </div><!-- end of table -->
</section>    