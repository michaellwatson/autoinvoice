<i class="fa fa-search au_search" aria-hidden="true" data-toggle="modal" data-target="#<?php echo $d['adv_column'];?>"></i> <i class="fa fa-television au_display" aria-hidden="true" data-toggle="modal" data-target="#<?php echo $d['adv_column'];?>"></i>


<div class="modal" tabindex="-1" role="dialog" aria-labelledby="<?php echo $d['adv_column'];?>" aria-hidden="true" id="<?php echo $d['adv_column'];?>"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title au_title">
                	Choose the field to search by in the Auto Complete
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">×</span>
		        </button> 
            </div> 
            <div class="modal-body">

            	<ul class="list-group">
				<?php 
				foreach($linked_fields as $f){	
				?>
				  <li class="list-group-item" style="color:#000;"> 

				  	<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" name="<?php echo $d['adv_column'];?>_field" id="<?php echo $d['adv_column'];?>_field" value="<?php echo $f['adv_id'];?>">
					  <label class="form-check-label" for="inlineRadio1"><?php echo $f['adv_text'];?> (<?php echo $f['adv_id'];?>)</label>
					</div>

			  	</li>
				<?php
				}
				?>
				</ul>


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
	let config = 'search';

	$(document).on('click', '.au_search', function(e){
		config = 'search';
		$('.au_title').html('Choose the field to search by in the Auto Complete');
	});

	$(document).on('click', '.au_display', function(e){
		config = 'display';
		$('.au_title').html('Choose the field to display in the Auto Complete');
	});


    $(document).on('click', '.<?php echo $d['adv_column'];?>_save', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.<?php echo $d['adv_column'];?>_save'));
        l.start();

        e.preventDefault();

		let val = $('input[name="<?php echo $d['adv_column'];?>_field"]:checked').val();
        $.ajax({
            type: "POST",
            url: base_url+'autocomplete/Autocomplete/save_config',
            data: {'config': config,'data': val, 'field': <?php echo $d['adv_id'];?>}, // serializes the form's elements.
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