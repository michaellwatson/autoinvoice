    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Personnel Form</legend>
        
        <form class="form-horizontal" role="form" id="add_personnel_form">

          <div class="form-row">

            <div class="form-group col-sm-2">
              <label for="defaultEnergySupplier" class="control-label">Company</label>
            </div>
            <div class="form-group col-sm-4">
              
              <select class="form-control" id="client_id" name="client_id">
              <option value="0">Not Set</option>
                <?php foreach($clients as $c){?>
                  <option <?php if(isset($personnel['client_id'])){ if($c['c_id']==$personnel['client_id']){ echo 'selected'; } }?>  value="<?php echo $c['c_id']?>"><?php echo $c['clientName'];?> (<?php echo $c['companyName'];?>)</option>
                  <?php } ?>
              </select>
              
              
            </div>
          </div>


          <div class="form-row">
            <div class="form-group col-sm-2">
              <label for="firstname" class="control-label">First name</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" value="<?php if(isset($personnel['firstname'])){ echo $personnel['firstname']; }?>" id="firstname" name="firstname" placeholder="First Name" required="required">
            </div>

            <div class="form-group col-sm-2">
              <label for="lastname" class="control-label">Last name</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" value="<?php if(isset($personnel['lastname'])){ echo $personnel['lastname']; } ?>" required="required">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-sm-2">
              <label for="email" class="control-label">Email</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php  if(isset($personnel['email'])){  echo $personnel['email']; }?>" required="required">
            </div>

            <div class="form-group col-sm-2">
              <label for="phone" class="control-label">Phone</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="phone" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?php if(isset($personnel['phone'])){ echo $personnel['phone']; } ?>" required="required">
            </div>
          </div>
          
          
          <div class="form-row">
            <div class="form-group col-sm-4 offset-sm-2">

                <input type="hidden" id="id" name="id" value="<?php if(isset($personnel['id'])){ echo $personnel['id']; } ?>"/>
                <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right" id="save_personnel"><span class="ladda-label">Save</span></button> 
            
            </div>
          </div>



        </form>
      </fieldset>
      </div>
    </div>
      
      
      <!-- Modal -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="alertModalLabel">Alert Message</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success" id="msgHereSuccess">...</div>
		<div class="alert alert-danger" id="msgHereError">...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
       

    