<?php 
$CI =& get_instance();
$CI->load->model('Quotesmodel');

$role = $CI->session->userdata('us_role');

foreach($allUsers as $users){
    $quoteID = substr($users['ad_FirstName'], 0,1).substr($users['ad_Surname'], 0,1).'_'.date('dmy',strtotime($users['ad_added'])).'_'.$users['ad_rand'];

    switch($formID){
                      case 11:
                        $category = 1;
                      break;
                      case 12:
                        $category = 35;
                      break;
    }
    ?>			
            <tr>
                <td>

                  <?php
                    if($formID==11){
                  ?>
                    <?php echo $users['ad_Salutation'];?> <?php echo $users['ad_FirstName'];?> <?php echo $users['ad_Surname'];?> 
                  <?php } ?> 
                  
                </td>
                <td>
                   <?php echo $users['ad_AddressLine1'];?>, <?php echo $users['ad_Town'];?>, <?php echo $users['ad_PostCode'];?> 
                </td>
                <td>
                  <?php echo $users['ad_MobileNumber'];?>
                </td>
                <td>
                <a href="<?php echo base_url();?>pdfquote/history/<?php echo $quoteID;?>">
                <?php echo date('dmy',strtotime($users['ad_added']));?>/<?php echo $users['ad_rand'];?>
                </a>
                </td>
                <td><?php echo date('d/m/Y',strtotime($users['ad_added']));?></td>
                <td><?php echo date('d/m/Y',strtotime($users['ad_last_update']));?></td>

                <td>
                  <select class="demo-htmlselect" id="flag_<?php echo $users['ad_id'];?>">
                      <option <?php if($users['ad_flag']==1){ echo 'selected="selected"'; }?> value="<?php echo $users['ad_id'];?>:1" data-imagesrc="<?php echo base_url('assets/admin/images/icons/flag_red.png');?>"></option>
                      <option <?php if($users['ad_flag']==2){ echo 'selected="selected"'; }?> value="<?php echo $users['ad_id'];?>:2" data-imagesrc="<?php echo base_url('assets/admin/images/icons/flag_orange.png');?>"></option>
                      <option <?php if($users['ad_flag']==3){ echo 'selected="selected"'; }?> value="<?php echo $users['ad_id'];?>:3" data-imagesrc="<?php echo base_url('assets/admin/images/icons/flag_green.png');?>"></option>
                  </select>
                </td>
                <td class="table-action">

                  <?php 
                 
                  $latest_quote = $CI->Quotesmodel->quote_latest($quoteID);

                  ?>
                  <?php  
                  if($latest_quote['quh_order']==0){?>
                  <a href="<?php echo base_url('Post/create_form');?>/<?php echo $category;?>/<?php echo $users['ad_id'];?>?contactID=<?php echo $users['ad_contact'];?>">
                    <i class="fa fa-pencil"></i>
                  </a>

                    <?php if($role==0){ ?>
                      <a href="javascript:if(confirm('Are you sure you want to delete?')){ deleteEntry('<?php echo $users['ad_id']?>','<?php echo str_replace('_','/',$quoteID);?>');}" class="delete-row">
                          <i class="fa fa-trash-o" id="suspend_<?php echo $users['ad_id']?>"></i>
                      </a>
                    <?php } ?>
                  
                  <?php } ?>


                  <a href="<?php echo base_url();?>Post/gen_pdf/<?php echo $category;?>/<?php echo $users['ad_id']?>/pdf" target="_blank" class="pdf_link">
                    <i class="fa fa-file-pdf-o"></i>
                  </a>

                  <!--
                  <a href="<?php echo base_url();?>Post/gen_pdf/<?php echo $category;?>/<?php echo $users['ad_id']?>/doc" target="_blank" data-id="<?php echo $users['ad_id']?>">
                    <i class="fa fa-file-word-o"></i>
                  </a>

                  <a href="<?php echo base_url('Post/create_form');?>/<?php echo $category;?>?contactID=<?php echo $users['ad_contact'];?>&template=<?php echo $users['ad_id']; ?>">
                    <i class="fa fa-clone"></i>
                  </a>
                  -->
                  <?php
                  /*
                    if($formID==11){
                  ?>
                  <a href="<?php echo base_url('Post/download_pdf');?>/<?php echo $category;?>/<?php echo $users['ad_id']?>">
                    <i class="fa fa-download" aria-hidden="true"></i>
                  </a>
                  <?php } */ ?>
                </td>

              </tr>
<?php } ?>