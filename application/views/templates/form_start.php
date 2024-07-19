<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/waitme-gh-pages/waitMe.min.css'); ?>">
<script src="<?php echo base_url('assets/plugins/waitme-gh-pages/waitMe.min.js'); ?>"></script>

<!-- <link rel="stylesheet" type="text/css" href="<?php //echo base_url('assets/plugins/colorpick.js/src/colorPick.min.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php //echo base_url('assets/plugins/colorpick.js/src/colorPick.dark.theme.css'); ?>">
<script src="<?php //echo base_url('assets/plugins/colorpick.js/src/colorPick.min.js'); ?>"></script> -->

<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/spectrum-2.0.10/dist/spectrum.min.css'); ?>">
<script src="<?php echo base_url('assets/plugins/spectrum-2.0.10/dist/spectrum.min.js'); ?>"></script>

<script type="text/javascript">
var dynamicHideWaitmeLoaderFunctions = {};
function _generateRandomNumberJS(length) {
  let generator = "1209348756";
  let result = "";
  for (let i = 1; i <= length; i++) {
      result += generator.charAt(Math.floor(Math.random() * generator.length));
  }
  return result;
  /*var min = 1000000;
	var max = 7000000;
	return Math.floor(Math.random() * (+max + 1 - +min)) + +min;*/
}
function _getUniqueNumForColorPick( id ) {
  let idColorpickInput = id;
	let uniqueNumForColorPick;
	if(idColorpickInput){
		let lastIndex = idColorpickInput.lastIndexOf('_'); // Find the last index of '_'
		if(lastIndex == -1){
		}
		else{
			uniqueNumForColorPick = idColorpickInput.substring(lastIndex + 1); // Get the substring starting from the index after '_'
		}
	}
	return uniqueNumForColorPick;
}
</script>
<style type="text/css">
.hidden {
  display: none;
}

/* Custom CSS */
.table-striped-custom tbody tr:nth-of-type(odd) {
  background-color: #f9f9f9;
}

.table-striped-custom thead tr:nth-of-type(even) {
  background-color: #ebebeb;
}

.table-striped-custom thead tr:nth-of-type(odd) {
  background-color: #f5f5f5;
}

.table-striped-custom thead th {
  border-bottom: 2px solid #c5c5c5;
}

.table-striped-custom td {
  border-top: 1px solid #c5c5c5;
}

.table-striped-custom td:nth-child(odd) {
  background-color: #ebebeb;
}

.table-striped-custom td:nth-child(even) {
  background-color: #f9f9f9;
}

.table-striped-custom td:nth-child(odd):nth-last-child(2),
.table-striped-custom td:nth-child(even):last-child {
  background-color: #d6d6d6;
}

</style>
<div class="bs-example">
	
	<i class="fa fa-columns <?php echo isset($_SESSION["two-col"]) ? 'highlight' : ''; ?> column_toggle" aria-hidden="true" id="toggle-columns"></i>

	<?php
	if(isset($listing) && is_array($listing)){
		if($show_roles_menu > 0){
	?>
	<i class="fa fa-bars show_roles show-div-btn"></i>
	<?php 
		}
	} ?>


<?php //if(sizeof($tabs)>1){?>
<?php if(!isset($no_header)){ ?>
<nav class="nav nav-tabs">
  <?php foreach($tabs as $t){
  			if(is_array($listing)){
  	?>
  	<a href="<?php echo base_url('Post/create_form/'.$t->ad_id.'/'.$listing['ad_id'].'?formID='.$formID);?>" class="nav-item nav-link <?php if($t->ad_id == $category){?>active<?php } ?>"><?php echo $t->ad_name;?></a>
	<?php 
		}else{
	?>
		<a href="<?php echo base_url('Post/create_form/'.$t->ad_id.'/?formID='.$formID);?>" class="nav-item nav-link <?php if($t->ad_id == $category){?>active<?php } ?>"><?php echo $t->ad_name;?></a>
	<?php
		}
	} ?>
	<?php
	if(is_array($listing)){
	?>
  		<a href="<?php echo base_url('Post/history/'.$listing['ad_id'].'?formID='.$formID);?>" class="nav-item nav-link <?php if(isset($history)){?>active<?php } ?>">History</a>

  		<a href="<?php echo base_url('Post/downloadReport/'.$listing['ad_id'].'?formID='.$formID);?>" class="nav-item nav-link <?php if(isset($downloadReport)){?>active<?php } ?>">Download Reports</a>
  	<?php }else{ ?>
  		<a href="<?php echo base_url('Post/history/?formID='.$formID);?>" class="nav-item nav-link <?php if(isset($history)){?>active<?php } ?>">History</a>
  	<?php } ?>

</nav>
<?php //}
} 
?>

</div>

<script language="javascript">
$(document).ready(function() {
  // Attach click event to the icon
  $('#toggle-columns').click(function() {
    // Toggle the "highlight" class
    if (confirm("This will reload the page, make sure you've saved") == true) {
	    $(this).toggleClass('highlight');
	    
	    // Get the current value of the session variable
	    var isTwoCol = '<?php echo (int)$this->session->userdata("two-col") == 0 ? '1':'0'; ?>';
	    
	    // Toggle the value of the session variable and send it to the server
	    $.ajax({
	      type: 'POST',
	      url: url+'Post/toggle_two_col',
	      data: { two_col: isTwoCol },
	      success: function(data) {
	        console.log('Session variable updated successfully');
	        window.location.reload();
	      },
	      error: function() {
	        console.log('An error occurred while updating the session variable');
	      }
	    });
	}
  });


	$(document).ready(function() {
	  $(".show-div-btn").click(function() {
	  	
	    $("#hidden-div").slideToggle("slow");
	    $(this).toggleClass("fa-bars fa-times");

	  });
	});

});
</script>
<?php
$overall_answers = 0;
$overall_questions = 0;
if(is_array($listing)){
	if($show_roles_menu > 0){
?>
<div id="hidden-div" class="hidden">
 	<div class="card">
	  <div class="card-body">
	    
	    	<table class="table table-striped table-striped-custom">
	    		<tr>
	    			<td>Roles</td>
    				<?php foreach($cats as $c){?>
    					<td><span class="role_table_headers"><?php echo $c;?></span></td>
    				<?php } ?>
    				<td><span class="role_table_headers"><b>Overall Completion by role</b></span></td>
	    		</tr>
	    		<?php foreach($roles_list as $r){?>
	    			<tr>
	    				<td>

	    					<b><?php echo $r->name;?></b>
	    					<?php 
	    					$users_in_role = UserEl::where('us_role', '=', $r->ad_role)->get(); 
	    					$last_index = count($users_in_role) - 1;
	    					echo '<br>';
	    					foreach ($users_in_role as $index => $us) {
	    						echo $us->us_firstName.' '.$us->us_surname.' ';
	    						if ($index !== $last_index) {
							        echo ', ';
							    }
	    					}
	    					?>
	    				</td>

	    				<?php 

	    					
	    					$total_questions = [];
	    					$total_filled = [];
	    					foreach($cats as $k => $c){ ?>
    						<td class="text-center">
    							<?php

    							$filled = 0;
    							//print_r($r->ad_role);
    							if($cat_role[$k] == $r->ad_role){

	    							foreach($fields_list[$k] as $f){
	    								//print_r($f);
	    								if($f->fieldType->fi_type == 'image'){
	    									//SELECT * FROM `pt_images` WHERE up_field_id=91;
	    									//echo $f->adv_id.'#';
	    									$image = Imagemodel::where('up_field_id', '=', $f->adv_id)->where('up_entry_id', '=', $listing['ad_id'])->where('up_table', '=', $this->default_table)->get();
	    									//print_r($image);
	    									if(!empty((array)$image)){

	    										$filled++;
	    										if (!isset($total_filled[$r->ad_role])) {
	    											$total_filled[$r->ad_role] = 0;
	    										}
	    							 			$total_filled[$r->ad_role]++;
	    							 			
	    									}
	    								}else if($listing['ad_'.$f->adv_column] != ''){
	    							 		$filled++;
    							 			if (!isset($total_filled[$r->ad_role])) {
    											$total_filled[$r->ad_role] = 0;
    										}
	    							 		$total_filled[$r->ad_role]++;
	    							 	}
	    							}

    								echo $filled.'/'.count($fields_list[$k]);
    								if (!isset($total_questions[$r->ad_role])) {
    									$total_questions[$r->ad_role] = 0;
    								}
    								$total_questions[$r->ad_role]+=count($fields_list[$k]);

    								if(count($fields_list[$k]) > 0){
    								$per = ($filled/count($fields_list[$k]))*100;

    								$progress_class ='';
    								//echo (int)$per.'#';
	    							if ((int)$per <= 50 && (int)$per >= 0) {
	    								$progress_class = 'progress-bar-danger';
	    							}else if((int)$per >= 100){
										$progress_class = 'progress-bar-success';
	    							}
    								if((int)$per == 0){
    									$per = 2;
    								}
    								?>
									<div class="progress">
									  <div class="progress-bar <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $per;?>"
									  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $per;?>%">
									    <?php echo round($per);?>%
									  </div>
									</div>
    								<?php
    								}
    							}else{
    								echo '-';
    							}

    							?>
    						</td>
    					<?php 
    						}
    			
    					?>
    					<td>
    						<?php 
    						if(!empty($total_filled)){ 

								$overall_answers += (int)$total_filled[$r->ad_role];
								$overall_questions += (int)$total_questions[$r->ad_role];
    							?>
    							<b><?php echo $total_filled[$r->ad_role].'/'.$total_questions[$r->ad_role]; ?></b>
    						<?php }else{ 
    							$overall_answers += (int)$total_filled[$r->ad_role];
								$overall_questions += (int)$total_questions[$r->ad_role];
    							?>
    							<b><?php echo '0/'.$total_questions[$r->ad_role]; ?></b>
    						<?php } ?>

    						<?php 
    						    if($total_questions[$r->ad_role] > 0){
    								$per = ($total_filled[$r->ad_role]/$total_questions[$r->ad_role])*100;
    							}
    							$progress_class ='';
    							if($per < 50){
    								$progress_class = 'progress-bar-danger';
    							}else if($per >= 100){
									$progress_class = 'progress-bar-success';
    							}
    							if($per == 0){
    								$per = 1;
    							}
    						?>
							<div class="progress">
							  <div class="progress-bar <?php echo $progress_class;?>" role="progressbar" aria-valuenow="<?php echo $per;?>"
							  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $per;?>%">
							    <?php echo round($per);?>%
							  </div>
							</div>
    					</td>
	    			</tr>
	    		<?php } ?>
	    	</table>
	    	<br>
			<?php 
			    if($overall_answers > 0){
			    	echo $overall_answers.'/'.$overall_questions;
					$per = ($overall_answers/$overall_questions)*100;
				}

				$progress_class ='';
				if($per < 50){
					$progress_class = 'progress-bar-danger';
				}else if($per >= 100){
					$progress_class = 'progress-bar-success';
				}
			?>
			Overall, the report is <?php echo round($per);?>% complete<br>
			<div class="progress">
			  <div class="progress-bar <?php echo $progress_class;?>" role="progressbar" aria-valuenow="<?php echo $per;?>"
			  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $per;?>%">
			    <?php echo round($per);?>%
			  </div>
			</div>

	  </div>
	</div>
</div>
<?php 
	}
} ?>
<div class="modal fade" id="idModalLinkVersion">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Version Control</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-lg-12 clsFormVersionControl">
					</div>
					<div class="col-lg-12 clsListVersionControl">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="idModalBalconies">
	<div class="modal-dialog modal-lg" style="overflow-y: initial !important;">
		<div class="modal-content">
			<div class="modal-header clsRepeatModalHeader">
			</div>
			<div class="modal-body clsRepeatBalconies" style="height: 80vh; overflow-y: auto;">
			</div>
		</div>
	</div>
</div>
<form class="form-horizontal" role="form" method="post" id="form-add"  style="width: 98%;">
<input type="hidden" name="contactID" value="<?php if(isset($contactID)){ echo $contactID; }?>">
<script>
	var balconiesRepeatData = [];
	$(document).on('click', '.clsBtnBalconiesRepeatSubmit', function(e){
		var l = Ladda.create($('.clsBtnBalconiesRepeatSubmit').get(0));
		l.start();
		let data = new FormData( $('form[name="frmBalconiesRepeat"]').get(0) );
		let type = 'post';
		let url;
    // if($('#frmBalconiesRepeat input[name="pt_blocks_multiple_datas_id"]').length > 0){
    if($('input[name="pt_blocks_multiple_datas_id"]').length > 0){
			url = "<?php echo base_url('Post/updateBalconiesRepeat') . '/' ?>";
    }
    else{
			url = "<?php echo base_url('Post/saveBalconiesRepeat') . '/' ?>";
    }
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			contentType: false, cache: false, processData:false,
			success: function (response) {
				if(response.status){
					balconiesRepeatData.push(response.data);
					$('#idModalBalconies').modal('hide');
					saveAndNext();
				}
				else{
				}
				l.stop();
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
				l.stop();
			}
		});
	});
	$(document).on('click', '.clsDownloadReportPdf', function(e){
		let category = "<?php echo $category ?>";
		let adId = "<?php echo $ad_id ?>";
		let data = { category, adId };
		let type = 'post';
		let url = "<?php echo base_url('Post/generateReportPdf') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					window.open(response.data.downloadUrl, '_blank');
				}
				else{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	});
	
	$(document).on('click', '.clsLinkVersionControl', function(e){
		getVersionControls();
		getVersionControlDataForm();
		$('#idModalLinkVersion').modal('show');
	});

	function getVersionControls(){
		let category = "<?php echo $category ?>";
		let adId = "<?php echo $ad_id ?>";
		let data = { category, adId };
		let type = 'post';
		let url = "<?php echo base_url('Post/getVersionControls') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					$('.clsListVersionControl').html(response.data.html);
				}
				else{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	}

	$(document).on('click', '.clsSubmitVersionControl', function(e){
		let id = $(e.target).data("id");
		let category = "<?php echo $category ?>";
		let adId = "<?php echo $ad_id ?>";
		let data = new FormData( $('form[name="frmVersionControl"]').get(0) );
				data.append('category', category);
				data.append('adId', adId);
		if(id){
			data.append('id', id);
			updateVersionControl(data);
		}
		else{
			addVersionControl(data);
		}
	});

	function addVersionControl(data){
		let type = 'post';
		let url = "<?php echo base_url('Post/saveVersionControl') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			contentType: false, cache: false, processData:false,
			success: function (response) {
				if(response.status){
					toastr.success(response.message);
					resetFormVersionControl();
					getVersionControls();
					getVersionControlDataForm();
				}
				else{
					toastr.error(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	}

	function updateVersionControl(data){
		let type = 'post';
		let url = "<?php echo base_url('Post/updateVersionControl') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			contentType: false, cache: false, processData:false,
			success: function (response) {
				if(response.status){
					toastr.success(response.message);
					resetFormVersionControl();
					getVersionControls();
					getVersionControlDataForm();
				}
				else{
					toastr.error(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	}

	function resetFormVersionControl(){
		$('form[name="frmVersionControl"] input[name="txtVersion"]').val('');
		$('form[name="frmVersionControl"] input[name="txtImplementedBy"]').val('');
		$('form[name="frmVersionControl"] input[name="txtRevisionDate"]').val('');
		$('form[name="frmVersionControl"] input[name="txtApprovedBy"]').val('');
		$('form[name="frmVersionControl"] textarea[name="txtReason"]').val('');
	}

	$(document).on('click', '.clsEditVersionControl', function(e){
		let id = $(e.target).data("id");
		getVersionControlDataForm(id);
	});
	
	function getVersionControlDataForm(id=''){
		var data = {};
		if(id != ''){
			data = { id };
		}
		else{
			data = {};
		}
		let type = 'post';
		let url = "<?php echo base_url('Post/getVersionControlDataForm') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					$('.clsFormVersionControl').html(response.data.html);
				}
				else{
					toastr.error(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	}

	$(document).on('click', '.clsDeleteVersionControl', function(e){
		let id = $(e.target).data("id");
		let data = { id };
		let type = 'post';
		let url = "<?php echo base_url('Post/deleteVersionControl') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					toastr.success(response.message);
					getVersionControls();
				}
				else{
					toastr.error(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	});
	$(document).on('click', '.clsDownloadReportPdfWithAttachment', function(e){
		let category = "<?php echo $category ?>";
		let adId = "<?php echo $ad_id ?>";
		let data = { category, adId };
		let type = 'post';
		let url = "<?php echo base_url('Post/generateReportPdfWithAttachment') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					alert(response.message);
					
				}
				else{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	});

	$(document).on('click', '.clsDownloadFRAEWReportPdf', function(e){
		let category = "<?php echo $category ?>";
		let adId = "<?php echo $ad_id ?>";
		let data = { category, adId };
		let type = 'post';
		let url = "<?php echo base_url('Post/generateFRAEWReportPdf') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					window.open(response.data.downloadUrl, '_blank');
				}
				else{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	});
</script>