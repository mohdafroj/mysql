<?php echo $this->Element('Products/top_menu'); ?>
			<?=$this->Form->create(null, ['type' => 'get', 'class' => 'form-horizontal', 'novalidate' => true]);?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<?php echo $this->Element('Products/sub_menu'); ?>
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
						<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
							<?php echo $this->Element('pagination'); ?>
						</div><!-- end of pagination -->

						<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
							<?=$this->Html->link('Reset Filter', ['controller' => 'Products', 'action' => 'product-reviews', $id, 'key', md5($id)], ['class' => 'btn btn-div-cart btn-1e']);?>
							<?=$this->Form->button('Search', ['type' => 'submit', 'class' => 'btn btn-div-buy btn-1b']);?>
						</div><!-- end of buttons -->
					</div><!-- end of pagination or buttons -->

					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr><th>S No</th><th>Email</th><th>Country</th><th>Title</th><th>Description</th><th>Rating</th><th>Created At</th><th>Status</th><th>Action</th></tr>
							</thead>
							<tbody>
								<tr><!-- start of row_1 -->
									<td data-title="S No"></td>
									<td data-title="Email">
										<?=$this->Form->text('email', ['value' => $email, 'class' => 'form-control', 'placeholder' => 'Enter customer email']);?>
									</td>
									<td data-title="Country">

									</td>

									<td data-title="Title">
										<?=$this->Form->text('title', ['value' => $title, 'class' => 'form-control', 'placeholder' => 'Enter title']);?>
									</td>

									<td data-title="Description">
										<?=$this->Form->text('description', ['value' => $description, 'class' => 'form-control', 'placeholder' => 'Enter description']);?>
									</td>

									<td data-title="Rating">
										<?=$this->Form->text('rating', ['value' => $rating, 'class' => 'form-control', 'placeholder' => 'Enter rating']);?>
									</td>
									<td data-title="Created At">
										<div class="input-group date">
											<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
											<?=$this->Form->text('created', ['value' => $created, 'id' => 'datepicker1', 'class' => 'form-control']);?>
										</div>
									</td>
									<td data-title="Status">
										<?=$this->Form->select('is_active', $this->Admin->reviewStatus, ['value' => $isActive, 'default' => '', 'empty' => true, 'style' => 'width:100%;', 'class' => 'form-control'])?>
									</td>
									<td data-title="Action"></td>
								</tr>
                    <?php $i = 1;
foreach ($reviews as $value): ?>
								<tr>
									<td data-title="S No"><?php echo $i++; ?></td>
									<td data-title="Email"><?=h($this->Admin->checkValue($value->customer->email))?></td>
									<td data-title="Country"><?=h($this->Admin->checkValue($value->location->title))?></td>
									<td data-title="Title"><?=h($this->Admin->checkValue($value->title))?></td>
									<td data-title="Description"><?=h($this->Admin->checkValue($value->description))?></td>
									<td data-title="Rating"><?=h($this->Admin->checkValue($value->rating))?></td>
									<td data-title="Created At"><?=h($this->Admin->emptyDate($value->created));?></td>
									<td data-title="Status"><?=h($this->Admin->checkValue(ucfirst($value->is_active)))?></td>
									<td data-title="Action" class="text-center">
										<?=$this->Html->link(__('Edit'), ['controller' => 'Reviews', 'action' => 'edit', $value->id, 'key', md5($value->id)])?>
									</td>
								</tr>
					<?php endforeach;?>
							</tbody>
						</table>
					</div><!-- end of table -->
				</section>
                </div><!-- end of right_part -->
            </div><!-- end of profile -->

        </div><!-- end of right_part -->

    </div><!-- end of tab -->
</section>
            <?=$this->Form->end();?>
