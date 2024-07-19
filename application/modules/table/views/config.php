<i class="fa fa-table" aria-hidden="true" data-toggle="modal" data-target="#<?php echo $d['adv_column'];?>"></i>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $d['adv_column'];?>" aria-hidden="true" id="<?php echo $d['adv_column'];?>"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title au_title">
                	How many Columns
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">×</span>
		        </button> 
            </div> 
            <div class="modal-body">

                <label for="numOfInputs">Number of Inputs:</label>
                <input type="number" class="form-control <?php echo $d['adv_column'];?>_text" id="<?php echo $d['adv_column'];?>_num_cols" min="1" max="10">

                <div id="<?php echo $d['adv_column'];?>_inputContainer">
                    <!-- Dynamic text input fields will be appended here -->
                </div>

            </div> 
            <div class="modal-footer">
            	
            	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            	<button type="button" class="btn btn-primary <?php echo $d['adv_column'];?>_save ladda-button" data-style="expand-right">Save</button>

            </div> 
        </div> 
    </div> 
</div>

<script>
$( document ).ready(function() {

    $(document).ready(function() {
      // Add event listener for input change
      $('#<?php echo $d['adv_column'];?>_num_cols').on('input', function() {
        var numOfInputs = parseInt($(this).val()); // Get the input value and parse it to an integer
        var $inputContainer = $('#<?php echo $d['adv_column'];?>_inputContainer'); // Get the input container element
        $inputContainer.empty(); // Empty the container before appending new input fields
        if (numOfInputs > 0) {
          // Set a maximum limit of 10
          if (numOfInputs > 10) {
            numOfInputs = 10;
            $(this).val(10); // Update the input value to 10
          }
          for (var i = 0; i < numOfInputs; i++) {
            // Append new text input fields with class "form-control"
            $inputContainer.append('<br><input type="text" class="form-control <?php echo $d['adv_column'];?>_field" placeholder="Column Header"><br>');
          }
        }
      });
    });

    $(document).on('click', '.<?php echo $d['adv_column'];?>_save', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.<?php echo $d['adv_column'];?>_save'));
        l.start();
        e.preventDefault();

		var values = []; // Array to store input field values
        $('.<?php echo $d['adv_column'];?>_field').each(function() {
          values.push($(this).val()); // Add input field values to the array
        });

        $.ajax({
            type: "POST",
            url: base_url+'table/Table/save_config',
            data: {columns: JSON.stringify(values), 'field': <?php echo $d['adv_id'];?>}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                l.stop();
                console.log(data);
                toastr.success(data.msg);
                $("#<?php echo $d['adv_column'];?>").modal('hide');
            } 
        });
    });
});
</script>