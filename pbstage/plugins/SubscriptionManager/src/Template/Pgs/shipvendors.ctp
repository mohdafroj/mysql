<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Shipping Vendors') ?></h3>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?= $this->Form->create(null, ['type'=>'post']) ?>        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th class="text-center" width="10%"><?= $this->Paginator->sort('id', 'S. No.') ?></th>
                        <th><?= $this->Paginator->sort('title', 'Title') ?></th>
                        <th class="text-center" width="10%"><?= $this->Paginator->sort('set_default', 'Set Default') ?></th>
                        <th class="text-center" width="15%"><?= $this->Paginator->sort('created', 'Created') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
           <?php foreach ($vendorList as $value):?>
                    <tr>
                        <td class="text-center" data-title="Id"><?= $value['id'] ?></td>
                        <td data-title="Title"><?= $value['title'] ?></td>
                        <td class="text-center" data-title="Default"><input type="radio" name="id" onchange="changeDefault();" value="<?= $value['id'] ?>" style="cursor:pointer;" <?php echo $value['set_default'] ? 'checked':''; ?> ></td>
                        <td class="text-center" data-title="Created"><?= $this->Admin->emptyDate($value['created']); ?></td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>

<script>
    function changeDefault(){
        var form = document.forms[0];
        form.submit();
    }
</script>

