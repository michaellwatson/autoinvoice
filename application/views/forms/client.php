<?php $name =  $this->security->get_csrf_token_name(); ?>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Client Form <a href="<?php echo base_url('personnel/show/'.$formValues['c_id']);?>" class="btn btn-primary btn-sm">View Personnel</a></legend>
        
        <form class="form-horizontal" role="form" id="add_client_form">

          <div class="form-row">
            <div class="form-group col-sm-2">
              <label for="clientName" class="control-label">Client Name</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" value="<?php echo $formValues['clientName'];?>" id="clientName" name="clientName" placeholder="Client Name" required="required">
            </div>

            <div class="form-group col-sm-2">
              <label for="companyName" class="control-label">Company Name</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Company Name" value="<?php echo $formValues['companyName'];?>" required="required">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-sm-2">
              <label for="companyAddress" class="control-label">Company Address</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="companyAddress" name="companyAddress" placeholder="Company Address" value="<?php echo $formValues['companyAddress'];?>" required="required">
            </div>

            <div class="form-group col-sm-2">
              <label for="companyAddress2" class="control-label">Company Address 2</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="companyAddress2" name="companyAddress2" placeholder="Company Address 2" value="<?php echo $formValues['companyAddress2'];?>" required="required">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-sm-2">
              <label for="postcode" class="control-label">Postcode</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Postcode" value="<?php echo $formValues['postcode'];?>" required="required">
            </div>
          
            <div class="form-group col-sm-2">
              <label for="telephoneNumber" class="control-label">Telephone Number</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="telephoneNumber" name="telephoneNumber" placeholder="Telephone Number" value="<?php echo $formValues['telephoneNumber'];?>" required="required">
            </div>
          </div>
          
          <div class="form-row">
          
            <div class="form-group col-sm-2">
              <label for="email" class="control-label">Email</label>
            </div>
            <div class="form-group col-sm-4">
              <input type="text" class="form-control" id="email" value="<?php echo $formValues['email'];?>" name="email" placeholder="Email" required="required">
            </div>

            <div class="form-group col-sm-2">
              <label for="defaultEnergySupplier" class="control-label">Default Energy Supplier</label>
            </div>
            <div class="form-group col-sm-4">
              
              <select class="form-control" id="defaultEnergySupplier" name="defaultEnergySupplier">
              <option value="0">Not Set</option>
                <?php foreach($energySupplier as $es){?>
                  <option <?php if($es['es_id']==$formValues['defaultEnergySupplier']){echo 'selected';}?>  value="<?php echo $es['es_id']?>"><?php echo ucwords($es['es_name'])?></option>
                  <?php } ?>
              </select>
              
              
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group col-sm-4 offset-sm-2">

                <input type="hidden" id="csrf_token" name="<?php echo $name ?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                <input type="hidden" id="idOfClient" name="idOfClient" value="<?php echo $formValues['c_id'];?>"/>
                <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right" id="submitButton"><span class="ladda-label">Save</span></button> 
            
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
       

    