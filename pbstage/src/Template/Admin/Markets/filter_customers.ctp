<?php echo $this->element('Admin/Markets/top_menu');
$i = 0;
switch ($bucketId) {
    case 1:
        $filters = [
            'all' => 'All Customers',
            'cart' => 'Abandoned Cart',
            'repeated' => 'Repeated Purchased',
            'perfume' => 'Perfume Buyers',
            'deo' => 'Deo Buyer',
            'bodymist' => 'Body Mist Buyer',
            'perfumeselfie' => 'Perfume Selfie Buyer',
            'scent_shot' => 'Scent Shot Buyers',
            'refill' => 'Refill Buyers',
            'member' => 'Prive Members',
            'never' => 'Not Purchased',
        ];
        break;
    case 2:$filters = ['cart' => 'Abandoned Cart'];
        break;
    case 3:$filters = [
            'delivered' => 'Purchased',
            'repeated' => 'Repeated Purchased',
        ];
        break;
    default:
        $filters = [
            'cart' => 'Abandoned Cart',
            'repeated' => 'Repeated Purchased',
            'perfume' => 'Perfume Buyers',
            'deo' => 'Deo Buyer',
            'scent_shot' => 'Scent Shot Buyers',
            'refill' => 'Refill Buyers',
            'member' => 'Prive Members',
            'never' => 'Not Purchased',
        ];
}

$keyExist = array_key_exists($selected, $filters);
$readOnly = ($keyExist && $schedule_type);
?>

<!-- Main content -->
<section class="content col-sm-12 col-xs-12">

    <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
    <?=$this->Form->create(null, ['type'=>'get', 'id'=>'filterCustomerId', 'class' => 'form-horizontal']);?>

            <div class="col-sm-12 col-xs-12 row-flex no-padding"><!-- start of middle_content -->
                <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding" style="margin:0% 20%;"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                    <?php
                        foreach ($filters as $key => $value) {?>
                                <div class="form-group <?php if($key == $selected){echo 'bg-success';}?>">
                                    <div class="col-sm-4 no-padding">
                                        <label class="control-label"><input type="radio" id="<?=$i?>" name="selected_key" value="<?=$key?>" onClick="keyChnage(this.value, <?=$i?>);" style="cursor:pointer;" /> <?=$value?>:</label>
                                    </div>
                                    <div class="col-sm-8 no-padding">
                                        <div class="col-sm-4">
                                            <input type="hidden" name="keyword[]" value="<?=$key?>" class="form-control" />
                                            <?=$this->Form->select('schedule_type[]', ['Select Type', 'Set Days', 'Set Hours'], ['value' => ($schedule_type[$key] ?? 0), 'onChange' => 'selectChangeType(' . $i . ', this.value)', 'style' => 'width:100%; cursor:pointer;', 'class' => 'form-control schedule_type',  'readonly'=>$readOnly])?>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group date">
                                                <div class="input-group-addon">From:</div>
                                                <input type="number" name="start[]" min="0" onChange="rangChange(<?php echo $i; ?>);" value="<?=($start[$key] ?? 0)?>" class="form-control" <?php if($readOnly){ echo 'readonly'; } ?> />
                                                <div class="input-group-addon">To:</div>
                                                <input type="number" name="end[]" min="0" onChange="rangChange(<?php echo $i++; ?>);" value="<?=($end[$key] ?? 0)?>" class="form-control"  <?php if($readOnly){ echo 'readonly'; } ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php }?>
                                <div class="form-group">
                                    <div class="col-sm-12 no-padding text-center">
                                        <label id="errorMessage" class="control-label text-danger"></label>
                                    </div>
                                    <div class="col-sm-12 no-padding text-center">
                                <?php if( $readOnly ){?>        
                                        <div class="col-sm-6 no-padding text-center">
                                            <?= $this->Html->link('Export To CSV', ['controller' => 'Markets', 'action' => 'exports', '_ext' => 'csv', 'customersList', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                                        </div>
                                        <div class="col-sm-6 no-padding text-center">
                                            <?= $this->Html->link('Reset', ['controller' => 'Markets', 'action' => 'filterCustomers', $bucketId, 'key', md5($bucketId)], ['class'=>'btn btn-div-buy btn-1b']);?>
                                        </div>
                                <?php }else{?>
                                        <a onclick="submitFilterForm()" class="btn btn-div-buy btn-1b">Submit</a>
                                <?php } ?>            
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
            </div><!-- end of middle_content -->
            <?=$this->Form->end();?>
<?=$this->Form->create(null, ['type'=>'get','id'=>'filterForm','class' => 'form-horizontal']);?>
<input type="hidden" name="selected" value="<?=$selected?>" />
<input type="hidden" name="schedule_type" value="<?=$schedule_type?>" />
<input type="hidden" name="start" value="<?=$start?>" />
<input type="hidden" name="end" value="<?=$end?>" />
<?=$this->Form->end();?>


    </div><!-- end of tab -->
</section>
    <!-- /.content -->

<script>
$(document).ready(function(){
    $('#filterCustomerId select').prop("disabled",true);
    $('#filterCustomerId input[type=number]').prop("disabled",true);

    var selected = '<?=$selected?>';
    var keyExist = '<?=$keyExist?>';
    if( keyExist ){
        $('#filterCustomerId input[type=radio][value='+selected+']').prop("checked","checked");
        var index = $('#filterCustomerId input[type=radio][value='+selected+']').prop("id");
        $("select[name='schedule_type[]']")[index].disabled = false;
        $("input[name='start[]']")[index].disabled = false;
        $("input[name='end[]']")[index].disabled = false;

        $("select[name='schedule_type[]']")[index].value = <?=$schedule_type?>;
        $("input[name='start[]']")[index].value = <?=$start?>;
        $("input[name='end[]']")[index].value = <?=$end?>;
    }
});
    function submitFilterForm(){
        $("#errorMessage").html("");
        var submitStatus = true;
        var selectedOption = 0;
        $.each($(".schedule_type"), function(){
            if(!$(this).prop("disabled")){
                selectedOption = $(this).val();
            }
        });
        if( selectedOption == 0 || selectedOption == undefined ){
            $("#errorMessage").html("Please select option from enabled dropdown!");
            submitStatus = false;
        }
        var selected = $('#filterCustomerId input[type=radio]:checked').val();
        if( selected == '' || selected == undefined ){
            $("#errorMessage").html("Please choose radio button!");
            submitStatus = false;
        }
        if(submitStatus){
            document.getElementById('filterForm').submit();
        }
        return false;
    }
    function keyChnage(key, index){
        $("input[name=selected]").val(key);
        $('#filterCustomerId select').prop("disabled",true);
        $('#filterCustomerId input[type=number]').prop("disabled",true);
        $('#filterCustomerId select').prop("value",0);
        $('#filterCustomerId input[type=number]').prop("value",0);

        $("select[name='schedule_type[]']")[index].disabled = false;
        $("input[name='start[]']")[index].disabled = false;
        $("input[name='end[]']")[index].disabled = false;
        return false;
    }

    function selectChangeType(k, value){
        var start = parseInt($("input[name='start[]']")[k].value);
        var end   = parseInt($("input[name='end[]']")[k].value);
        $("input[name=schedule_type]").val(value);
        $("input[name=start]").val(start);
        $("input[name=end]").val(end);
        if( value == 0 ){
            $("input[name='start[]']")[k].value = 0;
            $("input[name='end[]']")[k].value = 0;
        }else{
            //if( start >= end ){
                //$("input[name='end[]']")[k].value = start + 1;
            //}
            if( start >= 0 ){
                if( end < 1 ){
                    $("input[name='end[]']")[k].value = 1;
                }
            }
        }
        return false;
    }

    function rangChange(k){
        var schedule = parseInt($("select.schedule_type")[k].value);
        var start = parseInt($("input[name='start[]']")[k].value);
        var end   = parseInt($("input[name='end[]']")[k].value);
        $("input[name=start]").val(start);
        $("input[name=end]").val(end);
        if( schedule == 0){
            $("input[name='start[]']")[k].value = 0;
            $("input[name='end[]']")[k].value = 0;
        }else{
            //if( start >= end ){
                //$("input[name='end[]']")[k].value = start + 1;
            //}
            if( start >= 0 ){
                if( end < 1 ){
                    $("input[name='end[]']")[k].value = 1;
                }
            }
        }
        return false;
    }

</script>
