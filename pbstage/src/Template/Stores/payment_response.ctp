<center>
<table width="50%">
    <tr>
		<td align="center" valign="middle"><div style="position:absolute; top:5%; transform:translateY(-50%); left:50%; transform:translateX(-50%); z-index:999; font-weight:bold;"><?=$waitMessage?></div></td>
	</tr>
<tr>
	<td align="center" valign="middle">
		<form action="<?=$redirectUrl;?>" method="post" name="pbResponseForm"></form>
	</td>
</tr>
<tr>
	<td align="center" valign="middle">
		<div style="position:absolute; top:15%; transform:translateY(-50%); left:50%; transform:translateX(-50%); z-index:999;">
<?php if ($pgId > 0) {
    echo $this->Html->image('gif-main2.gif', ['alt' => 'Loading...']);
    ?>
			<script type="text/javascript">document.pbResponseForm.submit(); </script>
<?php } else {?>
			<button onClick="document.pbResponseForm.submit();" class="btn btn-div-buy btn-1b">Click to Home</button>
<?php }?>
		</div>
	</td>
</tr>
</table>
</center>

