<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Filemanager extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		session_start();
		$this->form_name = 'Filemanager';
		$this->field_name = 'filemanager';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->lang->load('msg');

		$this->user_role = $this->session->userdata("us_role");
		$this->user_id 	 = $this->session->userdata("us_id");

	}

	function get($data){
		return $this->$data;
	}

	function test(){
		echo $this->form_name.' Installed';
	}

	function list($data){

	}

	function field_id($c){
		return "{name:'".$c[0]."', type:'string'}";
	}

	function config($d){

	}

	function save_config(){

	}

	function search(){

	}
	
	function install(){
		//check file/db exists

		$form_name 	= $this->form_name;
		$table 		= $this->table;
		$categories = $this->categories;

		//create custom field types
		//do we need this?

		$insert = [];
		$insert['fi_type'] = strtolower(str_replace(' ', '', $form_name));
		$insert['fi_name'] = $form_name;
		$insert['fi_hasDatasource'] = 0;
		$this->db->insert('field_type', $insert);
		$field_id = $this->db->insert_id();

	}

	public function create_folder(){
		//print_r($_POST);
		$newFolderName = $this->input->post('newFolderName');
		$path = $this->input->post('path');
	   	// Validate input
	  	if (empty($newFolderName) || preg_match('/\.\.\//', $newFolderName)) {
	    	echo json_encode(array('status' => 0 , 'msg' => $this->lang->line('invalid_path')));
	    	return;
	  	}
	  
	  	if (empty($path) || preg_match('/\.\.\//', $path)) {
	    	echo json_encode(array('status' => 0, 'msg' => $this->lang->line('invalid_path')));
	    	return;
	  	}
	  
	  	// Restrict folder creation to current directory and its subdirectories
	  	$basePath = $_SESSION['path'];
	  
	  	if (strpos($path, $basePath) !== 0) {
	    	echo json_encode(array('status' => 0, 'msg' => $this->lang->line('invalid_path')));
	    	return;
	  	}
		
		$newFolderPath = $path . '/' . $newFolderName;
		  
		if (!file_exists($newFolderPath)) {

		    mkdir($newFolderPath, 0777, true);
		    echo json_encode(array('status' => 1, 'msg' => $this->lang->line('folder_created')));

		} else {

		    echo json_encode(array('status' => 0, 'msg' => $this->lang->line('invalid_path')));

		}
	}
	
	//if you're creating a new field type, handle its appearance with this function
	function field($data){

		$this->load->helper('files');
		//print_r($data);
		$path = $_SERVER['DOCUMENT_ROOT'].'/assets/uploads/files/';
		//print_r($data);
		if(isset($data['listing']['ad_id'])){
			//echo '##'.$path.$data['listing']['ad_id'];
			if(!is_dir($path.$data['listing']['ad_id'])){
				mkdir($path.$data['listing']['ad_id']);
			}
			$path = $path.$data['listing']['ad_id'].'/'; 
			$default_path = $path;
			$dir = '';

			if (isset($_GET['dir'])) {
				if($_GET['dir'] != '..'){
			    	$path .= $_GET['dir'] . '/';
			    	$dir .= $_GET['dir'] . '/';
				}else{
					$path = $default_path;
				}
			}

			$files = scandir($path); // read the files in the directory

			$_SESSION['path'] = $path;

			foreach ($files as $file) {
		        if ($file == '.') {
		            continue; // skip the current directory link
		        }
		        $filepath = $path . $file;
		        if (is_dir($filepath)) {
		        	//echo substr($path, -2).'##';
		        	if (substr($path, -2) === '//') {

					  $path = substr($path, 0, -1);

					}

		        	if(($file == '..') && ($path != $default_path)){
		        		
		        		// display a link to enter the directory
		        		
		        		$dir = rtrim($dir, '/\\');
		        		if($dir !== ''){
			        		$dirparts = explode('/', $dir);
			        		//print_r($dirparts);
			        		array_pop($dirparts);
			        		//array_pop($dirparts);
			        		$rev_dir = implode('/', $dirparts);
		        		}
		            	$directories[] = '<a href="?dir=' .$rev_dir. '&formID='.$_GET['formID'].'"><i class="fa fa-folder" aria-hidden="true"></i> ' . htmlspecialchars($file) . '</a><br>';
		        	}else{

		        		if($file !== '..'){

		        			//echo $dir;
		        			// display a link to enter the directory
		        			$directories[] = '<a href="?dir=' .$dir.'/'. urlencode($file) . '&formID='.$_GET['formID'].'"><i class="fa fa-folder" aria-hidden="true"></i> ' . htmlspecialchars($file) . '</a><br>';
		        		}
		            	
		        	}
		           
		        } else {
		        	if(@is_array(getimagesize('assets/uploads/files/'.$data['listing']['ad_id'].'/'.$dir.'/'.htmlspecialchars($file)))){
					    $image = true;
					} else {
					    $image = false;
					}
		            // display a link to download the file
		            if($image){
		            	$my_files[] = '<a href="' . base_url('assets/uploads/files/'.$data['listing']['ad_id'].'/'.$dir.'/'.htmlspecialchars($file)) . '" target="_blank" data-lightbox="files"><img src="'.base_url('assets/uploads/files/'.$data['listing']['ad_id'].'/'.$dir.'/'.htmlspecialchars($file)).'" style="width:50px;margin-right:15px;"><i class="fa '.get_file_icon($file).'" aria-hidden="true"></i> ' . htmlspecialchars($file) . '</a><br>';
		            }else{
		            	$my_files[] = '<a href="' . base_url('assets/uploads/files/'.$data['listing']['ad_id'].'/'.$dir.'/'.htmlspecialchars($file)) . '" target="_blank"><i class="fa '.get_file_icon($file).'" aria-hidden="true"></i> ' . htmlspecialchars($file) . '</a><br>';
		            }
		        }
			}
			?>
			<div class="container">


			    <div class="row">
			      
			        <label class="col-sm-2 col-md-2 control-label">
			            <?php echo $data['field']['adv_text'];?>
			        </label>
			        
			        <div class="col-md-10 col-sm-10">   
			        	
			        	<div class="alert alert-warning" role="alert">
				  			Filemanager is a new feature, please proceed with caution
						</div>

			        	Current Path: <?php echo $path;?>
			        	<br>
			        	<a class="btn btn-primary new-folder-button"><i class="fa fa-folder" aria-hidden="true"></i></a>

				        <table class="table table-striped" style="width:100%;">

							<?php
							foreach($directories as $directory){
								?>
							   <tr><td><?php echo $directory;?></td></tr>
								<?php
							}
							foreach($my_files as $file){
								?>
							   <tr><td><?php echo $file;?></td></tr>
							   <?php
							}
						?>
						</table>
			      	</div>
			    
			    </div>

			    <div class="dashed-grey"></div>

			    <div class="row">
			    	<label class="col-sm-2 col-md-2 control-label"></label>
			    	 <div class="col-md-10 col-sm-10"> 
						<?php 
						$this->load->view("fileuploader-document", $data);
						$this->load->view("scripts_fileuploader_filemanager", $data);
						?>
					</div>
			    </div>

			</div>


			<div class="modal" tabindex="-1" role="dialog" id="newfolder">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title">New Folder</h5>
			        <button type="button" class="close newfolder-close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <div class="container">
				        <div class="row">
				        	<label class="col-md-6 col-sm-6">
				        		Folder name
				        	</label>
				        	<div class="col-md-6 col-sm-6">
				        		<input type="text" name="newfolderinput" id="newfolderinput" class="form-control" value="" autocomplete="off">
				        	</div>
				        </div>
			    	</div>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-primary createfolder ladda-button" data-style="expand-right">Create</button>
			        <button type="button" class="btn btn-secondary newfolder-close" data-dismiss="modal">Close</button>
			      </div>
			    </div>
			  </div>
			</div>
			<script>
				
				$(document).ready(function() {
				    // This WILL work because we are listening on the 'document', 
				    // for a click on an element with an ID of #test-element
				    $(document).on("click",".new-folder-button", function() {
				    	$('#newfolder').show();
				    });

				    $(document).on("click",".newfolder-close", function() {
						$('#newfolder').hide();
				    });

				    $(document).on("click",".createfolder", function(e) {
						e.preventDefault();
  						
  						var l = Ladda.create(document.querySelector('.createfolder'));
						l.start();
						
						  var newFolderName = $('#newfolderinput').val();
						  var path = '<?php echo $_SESSION['path']; ?>';
						  
						  $.ajax({
						    url: '<?php echo base_url("filemanager/create_folder"); ?>',
						    type: 'POST',
						    dataType: "json",
						    data: {
						      newFolderName: newFolderName,
						      path: path
						    },
						    success: function(response) {
						      // handle success response
						      toastr.success(response.msg);
						      l.stop();
						      
						      setTimeout(function(){
						      	window.location.reload();
						      }, 1000);
						      
						      
						    },
						    error: function(xhr, status, error) {
						      // handle error response
						      toastr.error(response.msg);
						      l.stop();
						    }
						  });
				    });

				    
				    
				});

				
			</script>
			<?php

		}else{
			?>
			<div class="container">


			    <div class="row">
			      
			        <label class="col-sm-2 col-md-2 control-label">
			            <?php echo $data['field']['adv_text'];?>
			        </label>
			        
			        <div class="col-md-10 col-sm-10">  
						<div class="alert alert-info" role="alert">
							Please save the entry to access the File Manager
						</div>
					</div>
				</div>

			</div>
			<?php
		}
		
	}

	//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data){

	}

	function search_field($field){

	}

	
}