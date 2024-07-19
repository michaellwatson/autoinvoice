<?php $name =  $this->security->get_csrf_token_name(); ?>
<div class="contentpanel">
      
  <div class="row"><!-- col-md-6 -->
        
    <div class="col-md-12">

<div class="alert alert-success" role="alert" style="display:none;" id="successMessage">
  This is a success alert—check it out!
</div>

<div class="alert alert-danger" role="alert" style="display:none;" id="errorMessage">
  This is a danger alert—check it out!
</div>


<div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th colspan="4">
                  <h3>CREATE/EDIT THE PREFILL TEXT</h3>
                </th>
              </tr>
            </thead>
            <tbody>
            <tr>
              <td>Name</td>
              <td>
                <input type="text" name="prefill_name" id="prefill_name" value="" class="form-control">
              </td>
            </tr>
            <?php
            if($ptAdvertFieldsRecord->adv_field_type == 23){
            ?>
              <tr>
                <td>Prefill Json</td>
                <td>
                  <textarea type="text" class="form-control" id="id_json_string" name="json_string" maxlength="4000"></textarea>
                  <input type="hidden" name="field_id" id="field_id" value="<?php echo $field_id;?>">
                  <input type="hidden" name="id" id="id" value="">
                </td>
              </tr>
            <?php
            }
            else{
            ?>
              <tr>
                <td>Prefill Text</td>
                <td>
                  <textarea type="text" class="form-control ckeditor" id="editor1" name="editor1" maxlength="4000"></textarea>
                  <input type="hidden" name="field_id" id="field_id" value="<?php echo $field_id;?>">
                  <input type="hidden" name="id" id="id" value="">
                </td>
              </tr>
            <?php
            }
            ?>
            <tr>
              <td colspan="2">
                <button class="btn btn-primary pull-right save_text">Save</button>
              </td>
            </tr>
            </tbody>
          </table>
</div>


<?php echo $prefills;?>
    
            

