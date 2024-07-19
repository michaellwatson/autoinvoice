<tr class="row">
	<td width="33%" class="dashed"><?php echo $item;?></td>
	<td width="33%" class="dashed"></td>
	<td class="dashed right">
		£<?php echo number_format($price,2);?>
		<?php if($is_vat!=1){?>
			<small>ex vat</small>
		<?php } ?>
	</td>
</tr>
