<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
    <div class="col-sm-12 col-xs-12 inner_heading">
        <!-- start of inner_heading -->
        <h3><?= h('Permission Management') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('Back'), ['controller' => 'Users/'], ['class' => 'btn btn-div-cart btn-1e']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<?= $this->Form->create(null, ['context' => ['validator' => 'adminUserAdd'], 'class' => 'form-horizontal', 'novalidate' => true]) ?>
<!-- Main content -->
<style>
    .form-horizontal .checkbox {
        min-height: 0px !important;
    }

    .sidebar-menu .treeview-menu .treeview-menu {
        padding-left: 50px;
    }
</style>
<section class="content col-sm-12 col-xs-12">
    <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div">
        <!-- start of tab -->
        <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top">
            <!-- start of middle_content -->
            <div class="col-sm-3 col-xs-12 flex_box no-padding-left xs-no-padding"></div><!-- start of col_div -->
            <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding">
                <!-- start of col_div -->
                <div class="box box-default">
                    <!-- start of box_div -->
                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail">
                        <!-- start of box_content -->
                        <div class="box-body">

                            <div class="col-sm-12 col-xs-12 flex_box no-
                             responsive-mobile-table">
                                <!-- start of col_div -->
                                <div class="box box-default">
                                    <!-- start of box_div -->
                                    <div class="box-header with-border" style="margin-bottom:25px;">
                                        <h3 class="box-title"><?= $this->request->session()->read('userName'); ?> of Permission Management</h3>
                                    </div>
                                    <?php
                                    if (isset($finalPermission) && !empty($finalPermission)) {
                                        foreach ($finalPermission as $contrl => $method) {
                                            ?>
                                            <div class="col-sm-3 col-xs-12 sidebar tree_div" style="height:500px;">

                                                <ul class="sidebar-menu">

                                                    <li class="treeview active">

                                                        <ul class="treeview-menu">

                                                            <li class="treeview active">
                                                                <a href="#">
                                                                    <label style="cursor:pointer;"><i class="fa fa-folder-o"></i> <?php echo $contrl; ?></label><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                                                                </a>
                                                                <ul class="treeview-menu custom-control custom-checkbox">
                                                                    <?php foreach ($method as $key => $methodName) {
                                                                                ?>
                                                                        <li class="treeview"> <?= $this->Form->input($contrl . '[' . $key . ']', ['checked' => $methodName, 'disabled' => ($parentId ? 1 : 0), 'class' => 'custom-control-input', 'type' => 'checkbox', 'label' => false]); ?><label class="custom-control-label" for="defaultUnchecked"><?php echo $key; ?></label></li>
                                                                    <?php

                                                                            } ?>
                                                                </ul>
                                                            </li>

                                                        </ul>
                                                    </li>
                                                </ul>

                                            </div> <?php
                                                        }

                                                        ?>

                                </div><!-- end of box_div -->
                            </div>
                            <?php if (empty($parentId)) { ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->button('Save', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']); ?>&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div><?php }
                                        } else {
                                            echo "<span style='color:red;'>Sorry!!! There is no permission... Please contact to own senior </span>";
                                        } ?>
                        </div>
                    </div><!-- end of box_content -->
                </div><!-- end of box_div -->
            </div><!-- end of col_div -->
        </div><!-- end of middle_content -->
    </div><!-- end of tab -->
</section>
<?= $this->Form->end() ?>