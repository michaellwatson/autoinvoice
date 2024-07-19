            <form method="post" class="dsValueForm">
            <tr>
                <td>
				<input type="hidden" name="id" id="id_<?php echo $ds_id?>" value="<?php echo $ds_id?>">
				<input type="text" name="value" id="value_<?php echo $ds_id?>" value="<?php echo $ds_value?>">
				</td>

                <td class="text-center"><button class="btn btn-success ladda-button saveValueButton" type="submit" data-id="<?php echo $ds_id?>" id="saveValueButton_<?php echo $ds_id?>" data-style="expand-right">Save</button></td>
            	<td class="text-center"><button class="btn btn-orange ladda-button deleteValueButton" type="submit" data-id="<?php echo $ds_id?>" id="deleteValueButton_<?php echo $ds_id?>" data-style="expand-right">Delete</button></td>
            </tr>
            </form>