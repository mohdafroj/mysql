<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Orders') ?> <?php //pr($this->Paginator->params())?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('Push to Vendors'), ['controller'=>'Orders', 'action' => 'pushVendors'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            <li><?= $this->Html->link(__('Sync to Shiprocket'), ['controller'=>'Orders', 'action' => 'shiprocket', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            <li><?= $this->Html->link(__('New Order'), ['controller'=>'Orders', 'action' => 'add'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<?= $this->Form->create(null, ['type'=>'get']) ?>
		<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-6 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->Element('Admin/pagination');?>
            </div><!-- end of pagination -->
            
            <div class="col-md-6 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <span id="downloadLabel" style="float:left;"></span>
                <?php if( $labelPdf > 0 ){ echo '<a href="#" id="getDownloadLabel" class="btn btn-div-buy btn-1b">Get Labels</a>'; } ?>
                <?= $this->Form->select('zoneId', $zones, ['empty'=>'Select Zone','value'=>$zoneId, 'style'=>'width:auto;','class'=>'btn btn-1e'])?>
                <?= $this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'orders', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                <?= $this->Html->link('Reset Filter', ['controller' => 'Orders'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><a href="#">S No</a></th>
                        <th><?= $this->Paginator->sort('id', 'Order No') ?></th>
                        <th><?= $this->Paginator->sort('tracking_code', 'Tracking Code') ?></th>
                        <th><?= $this->Paginator->sort('email', 'Email') ?></th>
                        <th><?= $this->Paginator->sort('mobile', 'Mobile') ?></th>
                        <th width="10%">Cost Price</th>
                        <th width="10%">Tax</th>
                        <th><?= $this->Paginator->sort('payment_amount', 'Amount') ?></th>
                        <th width="10%">Courier Name</th>
                        <th><?= $this->Paginator->sort('created', 'Created') ?></th>
                        <th><?= $this->Paginator->sort('payment_mode', 'Mode') ?></th>
                        <th><?= $this->Paginator->sort('status', 'Status') ?></th>
                        <th><a href="#">Actions</a></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="S No"></td>
                        <td data-title="Order No">
                        	<?= $this->Form->text('id', ['value'=>$orderNumber, 'class'=>'form-control', 'placeholder'=>'Order number']); ?>
                        </td>
                        <td data-title="Tracking Code">
                        	<?= $this->Form->text('tracking_code', ['value'=>$trackingCode, 'class'=>'form-control', 'placeholder'=>'Tracking Code']); ?>
                        </td>
                        <td data-title="Email">
                        	<?= $this->Form->text('email', ['value'=>$email, 'class'=>'form-control', 'placeholder'=>'Email id']); ?>
                        </td>
                        <td data-title="Mobile">
                        	<?= $this->Form->text('mobile', ['value'=>$mobile, 'class'=>'form-control', 'placeholder'=>'Mobile']); ?>
                        </td>
                        <td data-title="Cost Price"></td>
                        <td data-title="Tax"></td>
                        <td data-title="Amount">
							<div class="input-group date">
								<div class="input-group-addon">From: <?php echo $this->Admin->priceLogo; ?></div>
								<?= $this->Form->text('fromAmount', ['value'=>$fromAmount, 'class'=>'form-control']); ?>
							</div>
                        	<div class="input-group date">
                        		<div class="input-group-addon">To: <?php echo $this->Admin->priceLogo; ?></div>
                        		<?= $this->Form->text('toAmount', ['value'=>$toAmount, 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Courier Name">
                            <?= $this->Form->select('courierId', $couriers, ['empty'=>'Select Courier','value'=>$courierId, 'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon">From: <i class="fa fa-calendar"></i></div>
                        		<?= $this->Form->text('created_from', ['value'=>$createdFrom, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        	<div class="input-group date">
                        		<div class="input-group-addon">To: <i class="fa fa-calendar"></i></div>
                        		<?= $this->Form->text('created_to', ['value'=>$createdTo, 'id'=>'datepicker2', 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Mode">
                            <?= $this->Form->select('payment_mode', ['prepaid'=>'Prepaid','postpaid'=>'Postpaid'], ['value'=>$mode, 'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('status', $this->Admin->orderStatus, ['value'=>$status, 'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->

           <?php
                $totalAmount = 0;
                $page = $this->Paginator->param('page');
                $perPage = $this->Paginator->param('perPage');
                $i = ($page == 1) ? 1 : (($page - 1) * $perPage) + 1;
                $totalCostPrice = 0;
                foreach ($orders as $value):
                    $costPrice = 0;
                    foreach($value->order_details as $v){
                        $costPrice += $v->qty * $v->product->cost_price;
                    }
                    $totalCostPrice += $costPrice;
                ?>
                    <tr>
                        <td data-title="s No"><?= $i++ ?></td>
                        <td data-title="Order No"><?= h($value->id) ?></td>
                        <td data-title="Tracking Code">
                            <?php 
                            $totalAmount	+= $value->payment_amount;
							if( !empty($value->tracking_code) ){
                                if( $value->delhivery_pickup_id == 3 ){
                                    echo '<a href="https://www.delhivery.com/track/package/'.$value->tracking_code.'" target="_blank">'.$value->tracking_code.'</a>';
                                }else{
                                    echo '<a href="https://shiprocket.co/tracking/'.$value->tracking_code.'" target="_blank">'.$value->tracking_code.'</a>';
                                }
							}else{
                                if( ($value->status != 'pending') && ($value->status != 'paymentfail') ){
                                    echo $this->Html->link(__('Create'), ['action' => 'generate', $value->id, 'key', md5($value->id)], ['title'=>'Click to create AWB number']);
                                }else{
                                    echo 'N/A';
                                }
                            }
							?>
						</td>
                        <td data-title="Email"><?= h($this->Admin->checkValue($value->email)) ?></td>
                        <td data-title="Mobile"><?= h($this->Admin->checkValue($value->mobile))?></td>
                        <td data-title="Cost Price"><?php echo $this->Admin->priceLogo.number_format($costPrice, 2); ?></td>
                        <td data-title="Tax"><?php echo $this->Admin->priceLogo.number_format(($value->payment_amount - (($value->payment_amount * 100)/118)), 2); ?></td>
                        <td data-title="Amount"><?php echo $this->Admin->priceLogo; ?> <?= number_format($value->payment_amount, 2) ?></td>
                        <td data-title="Courier Name"><?php echo $couriers[$value->delhivery_pickup_id] ?? 'N/A'; ?></td>
                        <td data-title="Created"><?= h($this->Admin->emptyDate($value->created)); ?></td>
                        <td data-title="Mode"><?= h($this->Admin->checkValue(ucfirst($value->payment_mode))) ?></td>
                        <td data-title="Status"><?php echo isset($this->Admin->orderStatus[$value->status]) ? $this->Admin->orderStatus[$value->status] : $value->status;?></td>                        
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link(__('<i class="fa fa-eye"></i>'), ['action' => 'view', $value->id, 'key', md5($value->id)],['title'=>'View Order','escape'=>false]) ?> | 
                            <?= $this->Html->link(__('<i class="fa fa-eyedropper"></i>'), ['action' => 'add', $value->id, 'key', md5($value->id)], ['title'=>'Re-Order','escape'=>false]) ?>
							<?php
							if($value->status == 'accepted' || $value->status == 'proccessing')
							{
								?> | 
								<?= $this->Html->link(__('<i class="fa fa-close"></i>'), ['action' => 'cancel', $value->id, 'key', md5($value->id)], ['title'=>'Cancel Order','escape'=>false,'onclick' => "return confirm('Are you sure that you want to cancel this order?')"]) ?>
								<?php
							}
							?>
                        </td>
                    </tr>
            <?php endforeach;
                if( $totalAmount > 0 ):                
            ?>    
                    <tr>
                        <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                        <td><?php echo $this->Admin->priceLogo.number_format($totalCostPrice, 2); ?></td>
                        <td><?php echo $this->Admin->priceLogo.number_format(($totalAmount - (($totalAmount * 100)/118)), 2); ?></td>
                        <td><?php echo $this->Admin->priceLogo.number_format($totalAmount, 2); ?></td>
                        <td colspan="5"></td>
                    </tr>
            <?php else:?>        
                    <tr>
                        <td colspan="12" class="text-center"><strong>Sorry, no record found!</strong></td>
                    </tr>
            <?php endif; ?>        
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>

<script>

$(document).ready(function() {
    $("#getDownloadLabel").click(function(){
        $("#downloadLabel").html('Wait...');
        var csrfToken = <?= json_encode($this->request->getParam('_csrfToken')) ?>;
        var shipmentIds = <?php echo json_encode($shipmentIds); ?>;
        $.ajax({
            url: "<?php echo $this->Url->build(['action'=>'getLabels']) ?>",
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken
            },
            data:{"shipmentIds":shipmentIds},
            success: function( res )
            {	
                $("#downloadLabel").html(res);
                return false;
            }
        });
        return false;
    });    
});

</script>