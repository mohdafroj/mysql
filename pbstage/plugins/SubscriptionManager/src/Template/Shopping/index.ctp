<?php
echo $this->Element('Shopping/top_menu');
?>
<section class="content col-sm-12 col-xs-12">
		<?=$this->Form->create(null, ['type' => 'get'])?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
            	<ul class="list-unstyled list-inline pagination_div">
                	<li>
                    	Page
                        <span class="span_1">
							<?=$this->Paginator->prev(__('Prev'))?>
                            <input type="text" class="form-control" value="<?=$this->Paginator->counter(['format' => __('{{page}}')])?>">
							<?=$this->Paginator->next(__('Next'))?>
                        </span>
                        of <?=$this->Paginator->counter(['format' => __('{{pages}}')])?> pages
                    </li>
                	<li>
                    	View
                        <span class="span_1 span_2">
                            <?=$this->Form->select('limit', $this->Admin->selectMenuOptions, ['value' => $this->Paginator->param('perPage'), 'default' => 50, 'empty' => false, 'onChange' => 'this.form.submit();', 'class' => 'form-control']);?>
                        </span>
                        per page
                    </li>
                	<li>Total <?=$this->Paginator->counter(['format' => __('{{count}}')])?> records found</li>
                </ul>
            </div><!-- end of pagination -->

            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?=$this->Html->link('Reset Filter', ['controller' => 'Shopping'], ['class' => 'btn btn-div-cart btn-1e']);?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->

        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('title', 'Rule Title')?></th>
                        <th><?=$this->Paginator->sort('coupon', 'Coupon Code')?></th>
                        <th><?= $this->Paginator->sort('discount_value', 'Discount Value') ?></th>
                        <th class="text-center" colspan="2">Search By Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="Rule Title">
                        	<?=$this->Form->text('title', ['value' => $title, 'class' => 'form-control', 'placeholder' => 'Enter title']);?>
                        </td>
                        <td data-title="Coupon Code">
                        	<?=$this->Form->text('coupon', ['value' => $coupon, 'class' => 'form-control', 'placeholder' => 'Enter coupon code']);?>
                        </td>
                        <td data-title="Discount">
                            <div class="input-group date">
                                <?= $this->Form->select('discount_type', $this->SubscriptionManager->discountType, ['style'=>'width:50%;','class'=>'form-control'])?>
                                <?= $this->Form->text('discount_value', ['value'=>$discountValue, 'class'=>'form-control', 'style'=>'width:50%;', 'placeholder'=>'Search by discount value']); ?>
                            </div>
                        </td>
                        <td data-title="From Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?=$this->Form->text('valid_from', ['value' => $validFrom, 'id' => 'datepicker1', 'class' => 'form-control', 'placeholder' => 'Enter from date']);?>
                        	</div>
                        </td>
                        <td data-title="To Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?=$this->Form->text('valid_to', ['value' => $validTo, 'id' => 'datepicker2', 'class' => 'form-control', 'placeholder' => 'Enter to date']);?>
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('status', $this->SubscriptionManager->siteStatus, ['value' => $status, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($cartRule as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$this->Number->format($value->id)?></td>
                        <td data-title="Rule Title"><?=$this->SubscriptionManager->checkValue($value->title)?></td>
                        <td data-title="Coupon Code">
                            <?php
if (isset($value->coupons[0]->coupon)) {
    echo $this->SubscriptionManager->checkValue($value->coupons[0]->coupon);
} else {
    if (isset($value->_matchingData['Coupons']->coupon)) {
        echo $this->SubscriptionManager->checkValue($value->_matchingData['Coupons']->coupon);
    } else {
        echo $this->SubscriptionManager->checkValue($value->coupon);
    }
}
?>
                        </td>
                        <td class="text-left" data-title="Discount">
                            <?php 
                                if($value->discount_type == 'rupees'){
                                    echo $this->SubscriptionManager->priceLogo.' '.$value->discount_value;
                                }else{
                                    echo $value->discount_value. ' %';
                                } 
                            ?>
                        </td>
                        <td class="text-center" data-title="From Date"><?=$this->SubscriptionManager->emptyDate($value->valid_from)?></td>
                        <td class="text-center" data-title="To Date"><?=$this->SubscriptionManager->emptyDate($value->valid_to)?></td>
                        <td class="text-center" data-title="Status"><?=h($this->SubscriptionManager->checkValue(ucfirst($value->status)))?></td>
                        <td class="text-center" data-title="Action">
                            <?=$this->Html->link(__('<i class="fa fa-pencil"></i>'), ['action' => 'editRule', $value->id, 'key', md5($value->id)], ['title' => 'Edit rule', 'escape' => false])?>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
        </div><!-- end of table -->
        <?=$this->Form->end()?>
</section>

