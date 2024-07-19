<?php
$can_edit = true;
if(isset($job)){
  if(!$edit_jobs){
    $can_edit = false;
  }
}else{
  if(!$add_jobs){
    $can_edit = false;
  }
}
?>
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Create A Job</legend>
        
        <form class="form-horizontal" role="form" id="add_job_form">
          <?php 
          if(isset($job)){
            ?>
            <input type="hidden" id="id" name="id" value="<?php echo $job->id;?>">

            <?php
             if($approve_jobs){
            ?>
            <div class="form-row">
              <div class="form-group col-md-2">

                <label class=" control-label">Approved</label>

              </div>
              <div class="form-group col-md-4">

                  <select class="form-control" id="approved" name="approved">
                    <option>Please Select</option>

                    <option value="0" <?php if(isset($job->approved)){ if($job->approved==0){ echo 'selected="selected"'; } }?>>Not Approved</option>
                    <option value="1" <?php if(isset($job->approved)){ if($job->approved==1){ echo 'selected="selected"'; } }?>>Approved</option>

                  </select>
              
              </div>
            </div>
          <?php
            }else{
            ?>
            <div class="form-row">
              <div class="form-group col-md-2">

                <label class=" control-label">Approved</label>

              </div>
              <div class="form-group col-md-4">
                  <b><?php if(isset($job->approved)){ if($job->approved==1){ ?>Approved<?php }else{ ?>Not Approved<?php } } ?></b>
              </div>
            </div>
            <?php
            }
          }
          ?>



          <div class="form-row">
            <div class="form-group col-md-2">

              <label class=" control-label">Taken By</label>

            </div>
            <div class="form-group col-md-4">
                <?php if($can_edit){?>
                <select class="form-control" id="taken_by" name="taken_by">
                  <option>Please Select</option>
                  <?php foreach($users as $u){?>
                  <option value="<?php echo $u['us_id'];?>" <?php if(isset($job->taken_by)){ if($job->taken_by==$u['us_id']){ echo 'selected="selected"'; } }?>><?php echo $u['us_firstName'];?> <?php echo $u['us_surname'];?></option>
                  <?php } ?>
                </select>
                <?php }else{ 
                    $user = UserEl::find($job->taken_by);
                    echo '<b>'.$user['us_firstName'].' '.$user['us_surname'].'</b>';
                 } ?>
            </div>
            <div class="form-group col-md-2">

              <label class="control-label">Date of Instruction</label>

            </div>
            <div class="col-md-4">
              <?php if($can_edit){?>
                <input type="text" class="form-control" id="date_of_instruction" name="date_of_instruction" placeholder="Date of Instruction" value="<?php if(isset($job->date_of_instruction)){ echo date('d/m/Y', strtotime($job->date_of_instruction)); }?>" autcomplete="false">
              <?php }else{ 
                    echo '<b>'.date('d/m/Y', strtotime($job->date_of_instruction)).'</b>';
                    } ?>
            </div>

            
          </div>
          <div class="form-row">
          
            <div class="form-group col-md-2">
            
              <label class="control-label">Method of Instruction</label>

            </div>
            <div class="form-group col-md-4">
                
                <?php if($can_edit){?>
                <select class="form-control" id="method_of_instruction" name="method_of_instruction">
                  <option>Please Select</option>
                  <?php foreach($method as $m){?>
                  <option value="<?php echo $m->id;?>" <?php if(isset($job->method_of_instruction)){ if($job->method_of_instruction==$m->id){ echo 'selected="selected"'; } }?>><?php echo $m->name;?></option>
                  <?php } ?>
                </select>
                <?php 
                  }else{

                    $method = Methodmodel::find($job->method_of_instruction);
                    echo '<b>'.$method->name.'</b>';

                  } ?>

            </div>


              <div class="form-group col-md-2">
                <label class="control-label">Client</label>
              </div>
              <div class="form-group col-md-4">
                
                <?php if($can_edit){?>
                <select class="form-control" id="client" name="client">
                  <option>Please Select</option>
                  <?php foreach($clients as $c){?>
                  <option value="<?php echo $c['c_id'];?>" <?php if(isset($job->client)){ if($job->client==$c['c_id']){ echo 'selected="selected"'; } }?>><?php echo $c['clientName'];?> (<?php echo $c['companyName'];?>)</option>
                  <?php } ?>
                </select>
                <?php }else{ 
                        $client = $this->Clientmodel->getClientData($job->client);
                        echo '<b>'.$client['clientName'].'</b>';
                } ?>

              </div>
            </div>

          <div class="form-row">

                <div class="form-group col-md-2">
                  <label class="control-label">Personnel</label>
                </div>
                <div class="form-group col-md-4">
                  
                  <?php if($can_edit){ ?>
                  <select class="form-control" id="personnel" name="personnel">
                    <option>Please Select</option>
                    <?php foreach($personnel as $p){?>
                    <option value="<?php echo $p['id'];?>" <?php if(isset($job->personnel_id)){ if($job->personnel_id==$p['id']){ echo 'selected="selected"'; } }?>><?php echo $p['firstname'];?> <?php echo $p['lastname'];?></option>
                    <?php } ?>
                  </select>
                  <?php }else{ 
                          $personnel = Personnelmodel::find($job->personnel_id);
                          echo '<b>'.$personnel['firstname'].' '.$personnel['lastname'].'</b>';
                  } ?>
                </div>          

          </div>

          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Nature Of Instruction</label>

            </div>
            <div class="form-group col-md-10">

			<ul class="twocolumn">
			             
              <?php if($can_edit){?>

              <?php foreach($nature as $n){?>
              	<li> 
	              <div class="form-check form-check-inline">
	                <input class="form-check-input" type="checkbox" id="<?php echo $n['id'];?>" value="<?php echo $n['id'];?>" name="nature_of_instruction[]" <?php 
	                if(isset($job->nature_of_instruction)){ 
	                  if(in_array($n['id'], $job->nature_of_instruction)){ 
	                    echo 'checked="checked"'; 
	                  } 
	                } ?>>
	                <label class="form-check-label"><?php echo $n['name'];?></label>
	              </div>
              	</li>
              <?php } ?>

              <?php }else{ 
                      foreach($nature as $n){
                        echo '<li><b>'.$n['name'].'</b><br></li>';
                      }
                    } ?>
            
            </ul>

            </div>

          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
            
              <label class="control-label">Fee Amount</label>

            </div>

            <div class="col-md-4">
              <?php if($can_edit){?>
                <input type="text" class="form-control" id="fee_amount" name="fee_amount" placeholder="Fee Details" value="<?php if(isset($job->fee_amount)){ echo $job->fee_amount; }?>">
              <?php }else{ 
                echo '<b>'.$job->fee_amount.'</b>';
               } ?>
            </div>

            <div class="form-group col-md-2">
            
              <label class="control-label">Fee Notes</label>

            </div>

            <div class="col-md-4">
                <?php if($can_edit){?>
                <textarea class="form-control" id="fee_notes" name="fee_notes" rows="3"><?php if(isset($job->fee_notes)){ echo $job->fee_notes; }?></textarea>
                <?php }else{
                    echo '<b>'.$job->fee_notes.'</b>';
                } ?>
            </div>
          </div>


          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Choose Standard Items</label>

            </div>
            <div class="form-group col-md-10">
              <ul class="twocolumn">
              <?php if($can_edit){?>
              <?php foreach($items as $i){?>
              <li>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="<?php echo $i['id'];?>" value="<?php echo $i['id'];?>" name="standard_items[]" <?php 
                if(isset($job->standard_items)){ 
                  if(in_array($i['id'], $job->standard_items)){ 
                    echo 'checked="checked"'; 
                  } 
                } ?>>
                <label class="form-check-label"><?php echo $i['name'];?></label>
              </div>
              </li>
              <?php } ?>
              <?php 
                }else{ 
                  foreach($items as $i){
                    echo '<li><b>'.$i['name'].'</b><br></li>';
                  }
                } ?>
               </ul>
            </div>

          </div>
          <?php if($can_edit){?>
          <div class="form-row">

            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_job">
                <span class="ladda-label">Save</span>
              </button> 
            </div>

          </div>
          <?php } ?>

          <?php 
          if(isset($job)){
          ?>
          <!--timeline-->
          <div class="col-md-8">
                <!--timeline-->
                <div class="timeline-centered">
                <?php 
                foreach($job->events as $e){ ?>
                    <article class="timeline-entry">
                       <div class="timeline-entry-inner">
                          <time class="timeline-time" datetime="2014-01-10T03:45">
                          <span><?php echo date('M d, Y', strtotime($e['created_at']));?></span> <!--<span>x days ago</span>-->
                          </time>

                          <div class="timeline-icon <?php if($e->note=='Job Approved'){?>timeline_success<?php }else if(($e->note=='Job Un-approved')||($e->note=='Job Archived')){?>timeline_warning<?php }else{ ?>timeline_primary<?php } ?>">
                            <i class="entypo-feather"></i>
                          </div>
                          <div class="timeline-label">
                            <h2><?php echo date('H:i A', strtotime($e['created_at']));?></h2>
                            <p>
                                <?php echo $e->note;?>
                                <br>
                                <small><?php echo $e->user->us_firstName;?> <?php echo $e->user->us_surname;?></small>
                            </p>
                          </div>

                       </div>
                    </article>
                <?php } ?>
                </div>
            <!--timeline-->
            <?php } ?>

        </div>
      </form>
    </fieldset>
  </div>
</div>


          