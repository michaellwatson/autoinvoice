<i class="fa fa-repeat repeat_cat" aria-hidden="true" data-toggle="modal" data-target="#<?php echo $d['adv_column'];?>"></i>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $d['adv_column'];?>" aria-hidden="true" id="<?php echo $d['adv_column'];?>"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
        	<form id="<?php echo $d['adv_column'];?>">
            <div class="modal-header"> 
                <h5 class="modal-title au_title">
                	Choose which categories will repeat X times based on this value
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">×</span>
		        </button> 
            </div> 
            <div class="modal-body">

            	<label>Start</label>
            	<select class="form-select form-control" name="start">
				  <option selected>Please Select</option>
				  <?php 
					foreach($categories as $c){	
					?>
					 <option  value="<?php echo $c->ad_id;?>"><?php echo $c->ad_name;?></option>
					<?php
					}
					?>
				</select>

				<br>
				<br>
				<label>End</label>
				<select class="form-select form-control" name="end">
				  <option selected>Please Select</option>
				  <?php 
					foreach($categories as $c){	
					?>
					 <option  value="<?php echo $c->ad_id;?>"><?php echo $c->ad_name;?></option>
					<?php
					}
					?>
				</select>

				<input type="hidden" name="field_id" value="<?php echo $d['adv_id'];?>">
            </div> 
            <div class="modal-footer">
            	
            	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            	<button type="submit" class="btn btn-primary <?php echo $d['adv_column'];?>_save ladda-button" data-style="expand-right">Save</button>

            </div> 
        	</form>
        </div> 
    </div> 
</div>


<script>
$( document ).ready(function() {


	$(document).on('click', '.repeat_cat', function(e){
        //alert('hit');
		$('#<?php echo $d['adv_column'];?>').modal('show');
	});

    $(document).on('submit', '#<?php echo $d['adv_column'];?>', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.<?php echo $d['adv_column'];?>_save'));
        l.start();

        e.preventDefault();

		let val = $('input[name="<?php echo $d['adv_column'];?>_field"]:checked').val();
        $.ajax({
            type: "POST",
            url: base_url+'repeatsection/repeatsection/save_config',
            data: $(this).serialize(), // serializes the form's elements.
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