<!--dark header start-->
<tr class="dark_header_small">
<td style="padding:12px;" colspan="3">
	Quote Summary
</td>
</tr>

<tr class="row">
	<td width="33%" class="dashed">Total Cost Of Boat</td>
	<td width="33%" class="dashed"></td>
	<td class="dashed right">
		<?php if($is_vat==1){?>
		£<?php echo number_format($vat,2);?>
		<?php }else{?>
		£<?php echo number_format($retail,2);?>
		<?php } ?>
		<?php if($is_vat!=1){?>
			<small>ex vat</small>
		<?php } ?>
	</td>
</tr>

<?php if($discount!=0){?>
<tr class="row">
	<td width="33%" class="dashed">Total Discount / Trade In</td>
	<td width="33%" class="dashed"></td>
	<td class="dashed right">
		£<?php echo number_format($discount,2);?>
	</td>
</tr>
<?php 
$total-=$discount;
} ?>
<!--
<tr class="alt_row">
	<td width="33%" class="dashed">Total Discount / Trade In</td>
	<td width="33%" class="dashed"></td>
	<td class="dashed right" >£58,523.00</td>
</tr>
-->
<tr class="dark_header_small">
<td style="padding:12px;" colspan="2">
	Total Contract Price
</td>
<td class="right">
	£<?php echo number_format($total,2);?>
	<?php if($is_vat!=1){?>
		<small>ex vat</small>
	<?php } ?>
</td>
</tr>
<!--dark header end-->
