
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Link Document - <i><?php echo $doc_name;?></i></legend>

        <form class="form-horizontal" role="form" id="add_link_form">

          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Nature Of Instruction</label>

            </div>
            <div class="form-group col-md-4">
              
              <select class="form-control" id="nature_of_instruction" name="nature_of_instruction">
              <option value="">Please Select</option>
              <?php foreach($nature as $n){?>
                <option value="<?php echo $n['id'];?>" <?php if(isset($table_id)){ if($table_id==$n['forms_tables_id']){ echo 'selected="selected"'; } } ?>><?php echo $n['name'];?></option>
              <?php } ?>
              </select>

            </div>

          </div>

          <div class="form-row">
            <div class="form-group col-md-12 ">
              <div class="strike">
                  <span>OR</span>
              </div>
            </div>
          </div>

          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Choose Standard Items</label>

            </div>
            <div class="form-group col-md-4">

              <select class="form-control" id="standard_items" name="standard_items">
              <option value="">Please Select</option>
              <?php foreach($items as $i){?>
                <option value="<?php echo $i['id'];?>" <?php if(isset($table_id)){ if($table_id==$i['forms_tables_id']){ echo 'selected="selected"'; } } ?>><?php echo $i['name'];?></option>
              <?php } ?>
              </select>


            </div>

          </div>

          <div class="form-row">

            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_link">
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


          