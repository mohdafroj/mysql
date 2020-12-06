<?php
$this->Paginator->setTemplates(['templates' => 'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?=h('Manage Invoices')?></h3>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['id' => 'invoice_form', 'type' => 'get'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding" style="width:50%;"><!-- start of pagination -->
                <?php echo $this->Element('pagination'); ?>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div" style="width:50%;"><!-- start of buttons -->
				<input type="hidden" id="download_invoice" name="download_invoice" value="0" />
				<?=$this->Form->button('Download Invoice', ['type' => 'button', 'id' => 'download_invoice_button', 'class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'InvoiceExport', '?' => $queryString], ['class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link('Reset Filter', ['controller' => 'Invoices'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
						<td>
							<?=$this->Form->checkbox('select_all', ['hiddenField' => false, 'value' => 0, 'id' => 'select_all']);?>
						</td>
                        <th><?=$this->Paginator->sort('id', 'Invoice Id')?></th>
                        <th><?=$this->Paginator->sort('order_id', 'Order No')?></th>
                        <th><?=$this->Paginator->sort('email', 'Email')?></th>
                        <th><?=$this->Paginator->sort('mobile', 'Mobile')?></th>
                        <th><?=$this->Paginator->sort('shipping_firstname', 'Ship to Name')?></th>
                        <th>Amount</th>
                        <th>Courier Name</th>
                        <th>Country</th>
                        <th><?=$this->Paginator->sort('created', 'Created')?></th>
                        <th><?=$this->Paginator->sort('payment_mode', 'Mode')?></th>
                        <th><?=$this->Paginator->sort('status', 'Status')?></th>
                        <th colspan="2"><?=__('Actions')?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
						<td data-title="Select">
							&nbsp;
						</td>
                        <td data-title="Id">
							<div class="input-group date">
								<div class="input-group-addon">PC</div>
								<?=$this->Form->text('id', ['value' => $id, 'class' => 'form-control', 'placeholder' => 'Invoice No']);?>
							</div>
						</td>
                        <td data-title="Order No">0
							<div class="input-group date">
								<div class="input-group-addon">PC</div>
								<?=$this->Form->text('order_id', ['value' => $orderId, 'class' => 'form-control', 'placeholder' => 'Order No']);?>
							</div>
                        </td>
                        <td data-title="Email">
                        	<?=$this->Form->text('email', ['value' => $email, 'class' => 'form-control', 'placeholder' => 'Email id']);?>
                        </td>
                        <td data-title="Mobile">
                        	<?=$this->Form->text('mobile', ['value' => $mobile, 'class' => 'form-control', 'placeholder' => 'Mobile']);?>
                        </td>
                        <td data-title="Ship to Name">
                        	<?=$this->Form->text('shipping_firstname', ['value' => $shippingFirstname, 'class' => 'form-control', 'placeholder' => 'First name']);?>
                        </td>
                        <td data-title="Amount">
							<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
								<div class="input-group-addon"></div>
								<?=$this->Form->text('payment_amount', ['value' => $paymentAmount, 'class' => 'form-control', 'placeholder' => 'Amount']);?>
							</div>
                        </td>
                        <td data-title="Courier Name">
                            <?=$this->Form->select('courierId', $couriers, ['empty' => true, 'value' => $courierId, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Country">
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

                        <td data-title="Action" conspan="2">&nbsp;</td>
                    </tr><!-- end of row_1 -->
                    <?php
foreach ($invoices as $value): ?>
						<tr>
							<td data-title="ID"><?=$this->Form->checkbox('select[]', ['hiddenField' => false, 'value' => $value->id, 'class' => 'select_checkbox', 'id' => 'select_' . $value->id]);?></td>
							<td data-title="Invoice No">PC<?=$value->id?></td>
							<td data-title="Order Number">PC<?=h($value->order_id)?></td>
							<td data-title="Email"><?=h($this->SubscriptionManager->checkValue($value->customer->email))?></td>
							<td data-title="Mobile"><?=h($this->SubscriptionManager->checkValue($value->customer->mobile))?></td>
							<td data-title="Ship to Name"><?=h($this->SubscriptionManager->checkValue($value->shipping_firstname));?></td>
							<td data-title="Amount" class="text-right">
                                <?php
echo $value->location->currency_logo . number_format($value->payment_amount, 2);
?>
                            </td>
                            <td data-title="Courier Name"><?php echo $value->courier->title ?? 'N/A'; ?> </td>
                            <td data-title="Country"><?php echo $value->location->title ?? 'N/A'; ?> </td>
							<td data-title="Created"><?=h($this->SubscriptionManager->emptyDate($value->created));?></td>
							<td data-title="Mode"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->payment_mode)))?></td>
							<td data-title="Status"><?php echo $this->SubscriptionManager->orderStatus[$value->status] ?? 'N/A'; ?></td>
							<td data-title="Action" class="text-center">
								<?=$this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view', $value->id, 'key', md5($value->id)], ['title' => 'View Invoice', 'escape' => false, 'target' => '_blank'])?>
							</td>
							<td class="text-center">
								<?=$this->Html->link(__('<i class="fa fa-download"></i>'), ['action' => 'download', $value->id, 'pdf', md5($value->id)], ['title' => 'Download Invoice', 'escape' => false])?>
							</td>
						</tr>
                    <?php endforeach;
if (!empty($invoices)):
?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center"><strong>Sorry, no record found!</strong></td>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>

<script type="text/javascript">
	$(document).ready(function(){
		if($('#select_all').length > 0)
		{
			$('#select_all').change(function()
			{
				if($(this).is(':checked'))
				{
					$('.select_checkbox').prop('checked', 'checked');
				}
				else
				{
					$('.select_checkbox').prop('checked', false);
				}
			});

			$('.select_checkbox').change(function()
			{
				$('#select_all').prop('checked', false);
			});

			$('#download_invoice_button').click(function()
			{
				if($('.select_checkbox').filter(':checked').length > 0)
				{
					$('#download_invoice').val(1);
					$('#invoice_form').submit();
				}
				else
				{
					alert('Please select atleast 1 invoice to download.');
				}
			});
		}
    });
</script>