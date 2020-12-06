<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="col-sm-12 col-xs-12 margin-md-top"><!-- start of error div -->
	<div class="alert alert-danger alert-dismissable">
    	<a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
    	<?= $message ?>
	</div>
</div>

