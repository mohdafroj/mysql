<?php
$this->Paginator->setTemplates(['templates' => 'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?=h('Manage Orders')?> <?php //pr($this->Paginator->params())?></h3>
        <ul class="list-inline list-unstyled">
            <!--li><?=$this->Html->link(__('Sync to Shiprocket'), ['controller' => 'Orders', 'action' => 'shiprocket'], ['class' => 'btn btn-div-buy btn-1b'])?></li-->
            <li><?=$this->Html->link(__('New Order'), ['controller' => 'Orders', 'action' => 'add'], ['class' => 'btn btn-div-buy btn-1b'])?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<?=$this->Form->create(null, ['type' => 'get'])?>
		<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->Element('pagination'); ?>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'orders', '?' => $queryString], ['class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link('Reset Filter', ['controller' => 'Orders'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><a href="#">S No</a></th>
                        <th><?=$this->Paginator->sort('id', 'Order No')?></th>
                        <th><?=$this->Paginator->sort('tracking_code', 'Tracking Code')?></th>
                        <th><?=$this->Paginator->sort('email', 'Email')?></th>
                        <th><?=$this->Paginator->sort('mobile', 'Mobile')?></th>
                        <th><?=$this->Paginator->sort('payment_amount', 'Amount')?></th>
                        <th width="10%">Courier Name</th>
                        <th>Country</th>
                        <th><?=$this->Paginator->sort('created', 'Created')?></th>
                        <th><?=$this->Paginator->sort('payment_mode', 'Mode')?></th>
                        <th><?=$this->Paginator->sort('status', 'Status')?></th>
                        <th><a href="#">Actions</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="S No"></td>
                        <td data-title="Order No">
                            <div class="input-group date">
								<div class="input-group-addon">PC</div>
                                <?=$this->Form->text('id', ['value' => $id, 'class' => 'form-control', 'placeholder' => 'Order number']);?>
							</div>
                        </td>
                        <td data-title="Tracking Code">
                        	<?=$this->Form->text('tracking_code', ['value' => $trackingCode, 'class' => 'form-control', 'placeholder' => 'Tracking Code']);?>
                        </td>
                        <td data-title="Email">
                        	<?=$this->Form->text('email', ['value' => $email, 'class' => 'form-control', 'placeholder' => 'Email id']);?>
                        </td>
                        <td data-title="Mobile">
                        	<?=$this->Form->text('mobile', ['value' => $mobile, 'class' => 'form-control', 'placeholder' => 'Mobile']);?>
                        </td>
                        <td data-title="Amount">
							<div class="input-group date">
								<div class="input-group-addon">From:</div>
								<?=$this->Form->text('fromAmount', ['value' => $fromAmount, 'class' => 'form-control']);?>
							</div>
                        	<div class="input-group date">
                        		<div class="input-group-addon">To: </div>
                        		<?=$this->Form->text('toAmount', ['value' => $toAmount, 'class' => 'form-control']);?>
                        	</div>
                        </td>
                        <td data-title="Courier Name">
                            <?=$this->Form->select('courierId', $couriers, ['empty' => true, 'value' => $courierId, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Courier Name">
                            <?=$this->Form->select('locationId', $locations, ['empty' => true, 'value' => $locationId, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon">From: <i class="fa fa-calendar"></i></div>
                        		<?=$this->Form->text('created_from', ['value' => $createdFrom, 'id' => 'datepicker1', 'class' => 'form-control']);?>
                        	</div>
                        	<div class="input-group date">
                        		<div class="input-group-addon">To: <i class="fa fa-calendar"></i></div>
                        		<?=$this->Form->text('created_to', ['value' => $createdTo, 'id' => 'datepicker2', 'class' => 'form-control']);?>
                        	</div>
                        </td>
                        <td data-title="Mode">
                            <?=$this->Form->select('payment_mode', ['prepaid' => 'Prepaid', 'postpaid' => 'Postpaid'], ['value' => $mode, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('status', $this->SubscriptionManager->orderStatus, ['value' => $status, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->

           <?php
$page = $this->Paginator->param('page');
$perPage = $this->Paginator->param('perPage');
$i = ($page == 1) ? 1 : (($page - 1) * $perPage) + 1;
foreach ($orders as $value): ?>
                    <tr>
                        <td data-title="s No"><?=$i++?></td>
                        <td data-title="Order No">PC<?=h($value->id)?></td>
                        <td data-title="Tracking Code">
                            <?php
if (!empty($value->tracking_code)) {
    switch ($value->courier_id) {
        case 3: 
            echo '<a href="'.PC['DLYVERY']['track_package']. $value->tracking_code . '" target="_blank">' . $value->tracking_code . '</a>';
            break;
        case 4: 
            echo '<a href="https://shiprocket.co/tracking/' . $value->tracking_code . '" target="_blank">' . $value->tracking_code . '</a>';
            break;
        default: 
            echo $value->tracking_code;
    }
} else {
    if ($value->status != 'pending') {
        echo $this->Html->link(__('Create'), ['action' => 'generate', $value->id, 'key', md5($value->id)], ['title' => 'Click to create AWB number']);
    } else {
        echo 'N/A';
    }
}
?>
						</td>
                        <td data-title="Email"><?=h($this->SubscriptionManager->checkValue($value->customer->email))?></td>
                        <td data-title="Mobile"><?=h($this->SubscriptionManager->checkValue($value->customer->mobile))?></td>
                        <td data-title="Amount" class="text-right">
<?php
$currency_logo = $value->location->currency_logo ?? '';
$currency_code = $value->location->code ?? 'IND';
echo $currency_logo . ' ' . number_format($value->payment_amount, 2) . '(' . $currency_code . ')'

?>
                        </td>
                        <td data-title="Courier Name">
<?php
echo $value->courier->title ?? 'N/A';
?>
                        </td>
                        <td data-title="Country">
<?php
echo $value->location->title ?? 'N/A';
?>
                        </td>
                        <td data-title="Created"><?=h($this->SubscriptionManager->emptyDate($value->created));?></td>
                        <td data-title="Mode"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->payment_mode)))?></td>
                        <td data-title="Status"><?php echo isset($this->SubscriptionManager->orderStatus[$value->status]) ? $this->SubscriptionManager->orderStatus[$value->status] : $value->status; ?></td>
                        <td data-title="Action" class="text-center">
                            <?=$this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view', $value->id, 'key', md5($value->id)], ['title' => 'View Order', 'escape' => false])?> |
                            <?=$this->Html->link(__('<i class="fa fa-eyedropper"></i>'), ['action' => 'add', $value->id, 'key', md5($value->id)], ['title' => 'Re-Order', 'escape' => false])?>
							<?php
if ($value->status == 'accepted' || $value->status == 'proccessing') {
    ?> |
								<?=$this->Html->link(__('<i class="fa fa-close"></i>'), ['action' => 'cancel', $value->id, 'key', md5($value->id)], ['title' => 'Cancel Order', 'escape' => false, 'onclick' => "return confirm('Are you sure that you want to cancel this order?')"])?>
								<?php
}
?>
                        </td>
                    </tr>
            <?php endforeach;
if (!empty($orders)):
?>
            <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center"><strong>Sorry, no record found!</strong></td>
                    </tr>
            <?php endif;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <div class="col-sm-12 col-xs-12 no-padding">
            <div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php //echo $this->Element('pagination'); ?>
            </div><!-- end of pagination -->
        </div>
        <?=$this->Form->end()?>
</section>