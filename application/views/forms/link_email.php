
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Link Message - <i><?php echo $doc_name;?></i></legend>

        <form class="form-horizontal" role="form" id="add_link_form">

          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Message</label>

            </div>
            <div class="form-group col-md-4">
              
              <select class="form-control" id="message" name="message">
              <option value="">Please Select</option>
              <?php foreach($messages as $m){?>
                <option value="<?php echo $m['ad_id'];?>" <?php if(isset($table_id)){ if($table_id==$m['me_forms_tables_id']){ echo 'selected="selected"'; } } ?>><?php echo $m['ad_Subject'];?></option>
              <?php } ?>
              </select>

            </div>

          </div>


          <div class="form-row">

            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_email_link">
                <span class="ladda-label">Save</span>
              </button> 
            </div>

          </div>

        </div>
        <input type="hidden" value="<?php echo $table_id;?>" name="table_id">
      </form>
    </fieldset>
  </div>
</div>


          