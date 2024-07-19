<button class="btn btn-primary pull-right archive ladda-button" data-style="expand-right" data-id="<?php echo $job->id;?>"><?php if($job->archive==0){?>Archive<?php }else{ ?>Un-Archive<?php } ?></button>
<input type="hidden" id="archive" name="archive" value="<?php  if($job->archive==1){ echo '0'; }else{ echo '1'; } ?>">

<ul class="nav nav-pills" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="job-tab" data-toggle="tab" href="#job" role="tab" aria-controls="job" aria-selected="true">Job Details</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="docs-tab" data-toggle="tab" href="#docs" role="tab" aria-controls="docs" aria-selected="false">Documents</a>
  </li>
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane active" id="job" role="tabpanel" aria-labelledby="job-tab">
    <br>
    <?php $this->load->view('forms/job');?>
  </div>
  <div class="tab-pane" id="docs" role="tabpanel" aria-labelledby="docs-tab">
    
    <div class="container">
    <br>
    <div class="card">
    <div class="card-block">

    <div class="table-responsive">         
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nature of Instruction</th>
                <th></th>
              </tr>
            </thead>
            <tbody>   
            <?php 
            foreach($nature_s as $n){
              if(count((array)$n->documents)>0){
                ?>
                <tr>
                  <td>
                    <a href="<?php echo base_url('Post/create_form/1?formID='.$n->documents->ft_id.'&jobID='.$id);?>&type=nature"><?php echo $n['name']; ?></a>
                    <?php if($n['ad_is_read']==1){ ?><span class="label label-success">Success Label</span><?php } ?>
                  </td>
                 
                  <td class="table-action pull-right">

                    <a href="<?php echo base_url('Post/create_form/1?formID='.$n->documents->ft_id.'&jobID='.$id);?>&type=nature">
                      <i class="fa fa-pencil"></i>
                    </a>
                
                  </td>

                  <td class="table-action pull-right">

                    <a href="<?php echo base_url('Post/create_form/1?formID='.$n->documents->ft_id.'&jobID='.$id);?>&type=nature">
                      <i class="fa fa-envelope" aria-hidden="true"></i>
                    </a>
                
                  </td>
                </tr>
              <?php
                }else{
              ?>
                <tr>
                  <td colspan="3">
                    <?php echo $n['name']; ?>
                  </td>
                </tr>
              <?php
              }
            } 
            ?>
            </tbody>
          </table>
    </div>


    <div class="table-responsive">         
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Standard Items</th>
                <th></th>
              </tr>
            </thead>
            <tbody>   
            <?php 

            foreach($standard as $s){
              if(count((array)$s->documents)>0){

                $ci =& get_instance();
                $ci->db->where('jobs_id',$job->id);
                $ci->db->where('forms_tables_id',$s->documents->ft_id);
                $post_relation = $ci->db->get('standard_items_fill_relation')->row_array();

                $existing_post = '';

                if(isset($post_relation['post_id'])){
                  $existing_post = '/'.$post_relation['post_id'];
                }
                ?>
                <tr>
                  <td>
                    <a href="<?php echo base_url('Post/create_form/1'.$existing_post.'?formID='.$s->documents->ft_id.'&jobID='.$job->id);?>&type=standard"><?php echo $s['name']; ?></a>
                    <?php 
                          $ci =& get_instance();
                          $ci->db->where('ad_id',$post_relation['post_id']); 
                          $result = $ci->db->get($s->documents->ft_database_table)->row_array();
                    ?>
                    <?php if($result['ad_is_read']==1){ ?>
                      <span class="badge badge-success">Read</span>
                    <?php }else if($result['ad_is_read']==-1){ ?>
                      <span class="badge badge-primary">Issued</span>
                    <?php } ?>
                  </td>
                 
                  <td class="table-action pull-right">

                    <a href="<?php echo base_url('Post/create_form/1'.$existing_post.'?formID='.$s->documents->ft_id.'&jobID='.$job->id);?>&type=standard">
                    <i class="fa fa-pencil"></i>
                    </a>
                    <?php if($existing_post !== ''){?>

                    <a href="<?php echo base_url('Post/gen_pdf/1'.$existing_post.'/pdf?formID='.$s->documents->ft_id);?>">
                    <i class="fa fa-file-pdf-o"></i>
                    </a>

                    <a href="<?php echo base_url('Post/gen_pdf/1'.$existing_post.'/doc?formID='.$s->documents->ft_id);?>">
                    <i class="fa fa-file-word-o" aria-hidden="true"></i>
                    </a>

                    <a data-entryid="<?php echo preg_replace('~\D~', '', $existing_post);?>" data-tableid="<?php echo $s->documents->ft_id;?>" href="#" class="issue">
                      <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    </a>
                
                  </td>

                    <?php } ?>
                
                  </td>
                </tr>
              <?php
                }else{
              ?>
                <tr>
                  <td colspan="2">
                    <?php echo $s['name']; ?>
                  </td>
                </tr>
              <?php
              }
            } 
            ?>
            </tbody>
          </table>
    </div>
    </div>
    </div>


  </div>
</div>

<script language="javascript">
  $( document ).ready(function() {

    $('#email_list').tokenfield({
        autocomplete: {
          source: ['<?php echo $client['email']; ?>',<?php foreach ($personnel as $p) { echo '\''.$p->email.'\','; } ?>],
          delay: 100
        },
        showAutocompleteOnFocus: true
    })

  });
</script>

<div class="modal" tabindex="-1" role="dialog" id="issueModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Issue Document</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      
      <form id="email_send">

        <div class="form-row">
            <div class="form-group col-md-12">

                <label class="control-label">Email</label>

            </div>
            <div class="form-group col-md-12">

              <select class="form-control" id="pick_email">
                <option value="">Please Select</option>
                <option value="<?php echo $client['email']; ?>"><?php echo $client['email']; ?></option>
                <?php 
                  foreach ($personnel as $p) {
                ?>
                <option value="<?php echo $p->email; ?>"><?php echo $p->email; ?></option>
                <?php
                }
                ?>
              </select>
              <input type="text" class="form-control" id="email_list" name="email_list" value="" />
              
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">

                <label class=" control-label">Subject</label>

            </div>
            <div class="form-group col-md-12">

              <input type="text" name="iss_subject" id="iss_subject" value="" style="width:100%;" class="form-control">
              
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">

                <label class=" control-label">Message</label>

            </div>
            <div class="form-group col-md-12">

              <textarea class="form-control" id="iss_message" name="iss_message" rows="3"></textarea>
              
            </div>
        </div>

      <input type="hidden" name="iss_entryid" id="iss_entryid" value="" class="form-control">
      <input type="hidden" name="iss_tableid" id="iss_tableid" value="" class="form-control">
      <div class="modal-footer">
        
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary send_document ladda-button" data-style="expand-right">Send</button>
        
      </div>
    </div>
    </form>
  </div>
</div>