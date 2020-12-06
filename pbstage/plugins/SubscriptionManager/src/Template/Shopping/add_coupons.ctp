<?php echo $this->Element('Shopping/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
			<?=$this->Form->create(null, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
                                            <div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">Coupon Qty <span class="text-red">*</span> </div>
                                                    <?=$this->Form->text('couponQty', ['class' => 'form-control', 'placeholder' => 'Enter coupon code quantity!']);?>
                                                </div>
												<div class="text-red">
                                                    <?php echo isset($error['couponQty']['integer']) ? $error['couponQty']['integer'] : null; ?>
                                                    <?php echo isset($error['couponQty']['_empty']) ? $error['couponQty']['_empty'] : null; ?>
												</div>
                                            </div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">Code Length <span class="text-red">*</span></div>
                                                    <?=$this->Form->text('couponLength', ['class' => 'form-control', 'placeholder' => 'Enter coupon code length!']);?>
                                                </div>
												<div class="text-red">
                                                    <?php echo isset($error['couponLength']['integer']) ? $error['couponLength']['integer'] : null; ?>
                                                    <?php echo isset($error['couponLength']['_empty']) ? $error['couponLength']['_empty'] : null; ?>
												</div>
                                            </div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
                                            <div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">Code Prefix</div>
                                                    <?=$this->Form->text('codePrefix', ['class' => 'form-control', 'placeholder' => 'Enter prefix for coupon code!']);?>
                                                </div>
												<div class="text-red">
                                                    <?php echo isset($error['codePrefix']['length']) ? $error['codePrefix']['length'] : null; ?>
                                                    <?php echo isset($error['codePrefix']['charNum']) ? $error['codePrefix']['charNum'] : null; ?>
												</div>
                                            </div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">Code Suffix</div>
                                                    <?=$this->Form->text('codeSuffix', ['class' => 'form-control', 'placeholder' => 'Enter suffix for coupon code!']);?>
                                                </div>
												<div class="text-red">
                                                    <?php echo isset($error['codeSuffix']['length']) ? $error['codeSuffix']['length'] : null; ?>
                                                    <?php echo isset($error['codeSuffix']['charNum']) ? $error['codeSuffix']['charNum'] : null; ?>
												</div>
                                            </div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label"> </label>
										<div class="col-sm-9">
                                            <div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">New Code:</div>
                                                    <?=$this->Form->text('newCode', ['class' => 'form-control', 'placeholder' => 'Enter coupon code to generate!']);?>
                                                </div>
                                            </div>
											<div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
                                                <div class="input-group date">
                                        <?php if ($rule->status == 'inactive') {
    echo 'Sorry, Rule status are inactive!, so you can not add more coupons.';
} else {?>
                                                    <button type="submit" class="btn btn-div-buy btn-1b">Generate</button>
                                        <?php }?>
                                                </div>
                                            </div>
										</div>
									</div>
									<div class="form-group">
                                        <label class="col-sm-3 control-label"> </label>
                                        <label class="col-sm-9">
                                            Note:
                                            <ul>
                                            <li>"Code Prefix" and "Code Suffix" not included with "Code Length"!</li>
                                            <li>If you use "new code" box then other fields are not applicable! </li>
                                            </ul>
                                        </label>
									</div>
                                </div>
            <?=$this->Form->end();?>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            </div><!-- end of tab -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->

    <?=$this->Form->create(null, ['type' => 'get', 'class' => 'form-horizontal', 'novalidate' => true]);?>
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
                <?=$this->Html->link('Export To CSV', ['action' => 'exports', $rule->id, 'key', md5($rule->id), '_ext' => 'csv', $csvName, '?' => $queryString], ['class' => 'btn btn-div-buy btn-1b']);?>
                <?=$this->Html->link(__('Reset Filter'), ['controller' => 'Shopping', 'action' => 'addCoupons', $rule->id, 'key', md5($rule->id)], ['class' => 'btn btn-div-cart btn-1e'])?>
                <?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?=$this->Paginator->sort('id', 'Id')?></th>
                        <th><?=$this->Paginator->sort('coupon', 'Coupon Code')?></th>
                        <th><?=$this->Paginator->sort('used', 'Usages')?></th>
                        <th class="text-center">Search by Created Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="Coupon Code">
                        	<?=$this->Form->text('coupon', ['value' => $coupon, 'class' => 'form-control', 'placeholder' => 'Search by coupon code']);?>
                        </td>
                        <td data-title="Usages"></td>
                        <td data-title="Created Date">
                            <div class="col-sm-6 col-xs-12 no-padding-left xs-no-padding">
                                <div class="input-group date">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <?=$this->Form->text('createdFrom', ['value' => $createdFrom, 'id' => 'datepicker1', 'class' => 'form-control', 'placeholder' => 'Enter from date...']);?>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 no-padding-right xs-no-padding">
                                <div class="input-group date">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <?=$this->Form->text('createdTo', ['value' => $createdTo, 'id' => 'datepicker2', 'class' => 'form-control', 'placeholder' => 'Enter to date...']);?>
                                </div>
                            </div>
                        </td>
                        <td data-title="Status">
                            <?=$this->Form->select('status', $this->Admin->siteStatus, ['value' => $status, 'empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($coupons as $value): ?>
                    <tr>
                        <td data-title="Id"><?=$this->Number->format($value->id)?></td>
                        <td data-title="Coupon Code"><?=h($this->Admin->checkValue($value->coupon));?></td>
                        <td data-title="Usages"><?=h($value->used);?></td>
                        <td data-title="Created Date" class="text-center"><?=h($this->Admin->emptyDate($value->created));?></td>
                        <td data-title="Status" class="text-center"><?=h($this->Admin->checkValue(ucfirst($value->status)))?></td>
                        <td data-title="Action" class="text-center">
                            <?=$this->Html->link(__('<i class="fa fa-edit"></i>'), [], ['title' => 'Change Status', 'escape' => false, 'onclick' => 'return manageCoupon(' . $value->id . ', ' . "'put'" . ');'])?> |
                            <?=$this->Html->link(__('<i class="fa fa-trash"></i>'), [], ['title' => 'Delete this coupon', 'escape' => false, 'onclick' => 'return manageCoupon(' . $value->id . ', ' . "'delete'" . ');'])?>
                        </td>
                    </tr>
           <?php endforeach;?>
                </tbody>
            </table>
        </div><!-- end of table -->
    <?=$this->Form->end();?>
</section>
<script type="text/javascript">
function manageCoupon(id, reqType){
    var msg = '';
    if( reqType == 'put'){
        msg = 'Sure, you want to update status!';
    }else if( reqType == 'delete'){
        msg = 'Sure, you want to delete coupon code!';
    }
    if( confirm(msg) ){
        $.ajax({
            type: reqType,
            data: {"id":id},
            url: '<?php echo \Cake\Routing\Router::url(['action' => 'manageCoupons']); ?>',
            success: function(result){
                location.reload();
            }
        });
    }
    return false;
}
</script>