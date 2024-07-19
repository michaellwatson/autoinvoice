    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Create/Edit Staff</legend>
        
        <form class="form-horizontal" role="form" id="add_staff_form">
          <?php 
          if(isset($staff)){
            ?>
            <input type="hidden" id="ad_id" name="ad_id" value="<?php echo $staff->ad_id;?>">
            <?php
          }
          ?>
         <div class="form-row">
            <div class="form-group col-md-2">
              <label class="control-label">First Name</label>
            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="ad_firstname" name="ad_firstname" placeholder="First Name" value="<?php if(isset($staff->ad_firstname)){ echo $staff->ad_firstname; }?>" autcomplete="false">
            
            </div>
            <div class="form-group col-md-2">
              <label class="control-label">Last Name</label>
            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="ad_lastname" name="ad_lastname" placeholder="Last Name" value="<?php if(isset($staff->ad_lastname)){ echo $staff->ad_lastname; }?>" autcomplete="false">
            
            </div>
            
          </div>
          <div class="form-row">
          
            <div class="form-group col-md-2">
              <label class="control-label">Email</label>
            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php if(isset($staff->email)){ echo $staff->email; }?>" autcomplete="false">
            
            </div>
            <div class="form-group col-md-2">
              <label class="control-label">Description</label>
            </div>
            <div class="col-md-4">

                <textarea class="form-control" id="description" name="description" rows="5" cols="50"><?php if(isset($staff->description)){ echo $staff->description; }?></textarea>
            
            </div>
              
          
          </div>
          
          
          <div class="form-row">
            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_staff">
                <span class="ladda-label">Save</span>
              </button> 
            </div>
          </div>
        
        </div>
<script language="javascript">
jQuery(document).ready(function() { 
  
});
</script>