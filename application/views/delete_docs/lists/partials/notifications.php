<?php 
$CI =& get_instance();
$CI->load->database();

foreach($allUsers as $users){
    $url = '';
    switch($users['no_notification_type']){
        case 'contact':
            $url = base_url().'admin/view_contact/'.$users['no_related_id'];
        break;
        case 'quote':
            $CI->db->where('quh_id',$users['no_related_id']);
            $row = $CI->db->get('pt_quote_history')->row_array();
            echo $this->db->last_query();
            //exit();
            
            $url = base_url().'assets/quotes/'.$row['quh_filename'].'.pdf';
        break;
        case 'order':
            $url = base_url().'orders/'.$users['no_related_id'];
        break;
    }
    ?>         
            <tr>
                <td><?php echo $users['no_notification_text']?></td>
                <td>
                <?php echo $users['pr_first_name']?> <?php echo $users['pr_surname']?>
                </td>
                <td>
                  <?php echo $users['us_firstName']?> <?php echo $users['us_surname']?>
                </td>
                <td>
                  <?php echo date('d/m/Y',$users['no_time_stamp']);?>
                </td>
                <td>
                    <a class="btn btn-success" id="View" data-id="view" href="<?php echo $url;?>" target="_blank">View <?php echo ucfirst($users['no_notification_type'])?></a>
                </td>
              </tr>
<?php } ?>