<?php 
$CI =& get_instance();
$CI->load->model('Quotesmodel');


foreach($allUsers as $users){
    //$quoteID = substr($users['pr_first_name'], 0,1).substr($users['pr_surname'], 0,1).'_'.date('dmy',strtotime($users['ad_added'])).'_'.$users['ad_rand'];
    ?>			
            <tr>
              <td>
                <a href="<?php echo base_url();?>admin/view_contact/<?php echo $users['quh_prospect_id'];?>">
                <?php echo $users['pr_first_name'];?> <?php echo $users['pr_surname'];?>
                </a>
                <?php //print_r($users['order_info']);?>
              </td>
              <td>
                <a href="<?php echo base_url();?>assets/quotes/<?php echo $users['quh_filename'];?>.pdf">
                  <?php echo $users['quh_ref'];?>
                </a>
              </td>
              <td>
              <?php echo date('d/m/Y H:i:s', strtotime($users['quh_gen_date']));?>
              </td>
              <td>
                <?php echo '&pound;'.number_format($users['quh_margin_price'],2);?>
              </td>
              <td>
              <?php 
              if($users['quh_vat_price']==''){
                echo '&pound;'.number_format($users['quh_retail_price'],2);
              }else{ 
                echo '&pound;'.number_format($users['quh_vat_price'],2);
              } ?>
              </td>

              <td>
              <?php 
              if($users['quh_vat_price']==''){
                ?>
                Zero Rated
                <?php
              }else{ 
                ?>
                VAT Rated
                <?php
              } ?>
              </td>

              <td class="table-action">
              <!--
                  <a href="<?php echo base_url('pdfquote/history');?>/<?php echo $quoteID;?>">
                    <i class="fa fa-history"></i>
                  </a>
              -->
                  
                </td>

                <td>

                  <button class="btn btn-success ladda-button progressToOrder" type="submit" id="progressToOrder" data-style="expand-right" data-id="add" onclick="javascript:location.href='<?php echo base_url('orders');?>/<?php echo $users['quh_id'];?>'">View Order</button>

                </td>
              </tr>
<?php } ?>