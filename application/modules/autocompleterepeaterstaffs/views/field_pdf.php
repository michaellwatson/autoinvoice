<table>
    <?php foreach($items_rows as $i){ ?>
        <tr>
            <td>
                (<?php echo $i->ad_id;?>) <?php echo $i->$display_field_column;?>
            </td>
            <td>
                <?php echo $i->quantity;?>
            </td>
        </tr>
    <?php } ?>
</table>

