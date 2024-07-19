<?php 


foreach($allUsers as $users){?>			
            <tr>

                <td>
                    <a href="<?php echo base_url();?>assets/quotes/<?php echo $users['quh_filename'];?>.pdf" target="_blank">
                    <?php echo $users['quh_filename'];?>
                    </a>
                </td>
                <td>
                    <?php echo date('d/m/Y H:i:s',strtotime($users['quh_gen_date']));?>
                </td>
                <!--
                <td>&pound;<?php echo number_format($users['quh_cost_price'],2);?></td>
                <td>&pound;<?php echo number_format($users['quh_retail_price'],2);?></td>
                <td>&pound;<?php echo number_format($users['quh_retail_price']-$users['quh_cost_price'],2);?></td>

                <td>
                <?php //if($users['ad_vat']==1){?>
                &pound;<?php echo number_format($users['quh_vat_price'],2);?>
                <?php //}else{ ?>
                <?php //} ?>
                </td>
                -->
            
            </tr>
<?php } ?>