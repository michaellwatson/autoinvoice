<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Illuminate\Database\Capsule\Manager
use Illuminate\Database\Capsule\Manager as Capsule;
use chriskacerguis\RestServer\RestController;

class Api extends RestController {
		
	public function __construct()
    {
     	parent::__construct();
     	$this->load->helper('url');
     	$this->load->helper('string');
     	$this->data = [];
     	/*
     	$this->load->model('Usermodel');
     	$this->load->model('Quotesmodel');
     	if(get_current_user_id() != 0){

			$this->session->set_userdata(["us_id" =>  get_current_user_id()]);
		
		}
		$this->data['user'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		*/

	}

	public function upload_pdfs_post() {
		//echo '##';
		ini_set('memory_limit', -1); 
		error_reporting(E_ALL); 
		error_reporting(-1); 
		ini_set('error_reporting', E_ALL); 
		set_time_limit(30000);

		$this->load->model('PdfMergemodel');
		  
    	$random_string = random_string('alnum', 16);
    	
    	$output_path = FCPATH.'output/';
    	//var_dump($_POST);
    	//var_dump($_FILES);
		#echo json_encode(array('status' => 0, 'html' => "Data not available 0000",'post'=>$_FILES));exit;
	    // Check if the request contains any uploaded files
	    if (!empty($_FILES['main_pdf']['name']) || !empty($_FILES['additional_pdfs']['name'][0])) {

	        // Upload directory for main PDF and additional PDFs
	        $upload_path = FCPATH.'uploads/pdfs/'.$random_string.'/';
	        mkdir($upload_path);
	        $output_path.=$random_string.'/';

	        mkdir($output_path);
	        $config['upload_path'] = $upload_path;
	        $config['allowed_types'] = 'pdf';
	        $config['max_size'] = 10240000; // You can adjust this as needed (2MB in this example)

	        // Load the upload library and initialize with the config
	        $this->load->library('upload', $config);

	        // Upload the main PDF
	        if (!empty($_FILES['main_pdf']['name'])) {
	            if ($this->upload->do_upload('main_pdf')) {
	                $main_pdf_data = $this->upload->data();
	                $main_pdf_path = $upload_path . $main_pdf_data['file_name'];

	                $main_pdf_path_process = $upload_path . basename($main_pdf_data['file_name'], ".pdf").'_process.pdf';
	                // Process or store the main PDF file as needed
	            } else {
	                $error = $this->upload->display_errors();
	                // Handle the main PDF upload error
	            }
	        }
	        //echo $main_pdf_path.'##'.$main_pdf_path_process.'##';

	        exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \-dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$main_pdf_path_process." ".$main_pdf_path."");
	        //echo "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \-dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$main_pdf_path_process." ".$main_pdf_path."";
	        unlink($main_pdf_path);
	        $main_pdf_path = $main_pdf_path_process;

	        // Upload additional PDFs
	        $additional_pdf_paths = [];
	        //print_r($_FILES);
	      	//print_r($_FILES["additional_pdfs"]);
	        foreach ($_FILES['additional_pdfs']['name'] as $key => $filename) {
	            $_FILES['userfile']['name'] = $_FILES['additional_pdfs']['name'][$key];
	            $_FILES['userfile']['type'] = $_FILES['additional_pdfs']['type'][$key];
	            $_FILES['userfile']['tmp_name'] = $_FILES['additional_pdfs']['tmp_name'][$key];
	            $_FILES['userfile']['error'] = $_FILES['additional_pdfs']['error'][$key];
	            $_FILES['userfile']['size'] = $_FILES['additional_pdfs']['size'][$key];

	            if ($this->upload->do_upload('userfile')) {
	                $additional_pdf_data = $this->upload->data();

	                $processed_path = $upload_path . basename($additional_pdf_data['file_name'], ".pdf")."_processed.pdf";
	                $original_name = $upload_path.$additional_pdf_data['file_name'];

	                exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \-dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$processed_path." ".$upload_path . $additional_pdf_data['file_name']."");
	                //echo "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \-dNOPAUSE -dQUIET -dBATCH -sOutputFile=".$processed_path." ".$upload_path . $additional_pdf_data['file_name']."";

	                unlink($upload_path . $additional_pdf_data['file_name']);

	                rename($processed_path, $original_name);

	                $additional_pdf_paths[] = $original_name;
	                // Process or store the additional PDF file as needed
	            } else {
	                $error = $this->upload->display_errors();
	                // Handle the additional PDF upload error
	                print_r($error);
	            }
	        }
	        //exit();
			#echo json_encode(array('status' => 0, 'html' => $additional_pdf_paths,'post'=>$main_pdf_path));exit;
	        //print_r($main_pdf_path);
	        //print_r($additional_pdf_paths);
			//exit();
			$uploadFolderPath = FCPATH ;
			//echo "main pdf path:".$main_pdf_path;
			//echo "output path:".$output_path.basename($main_pdf_path);
			//echo "random_string:".$random_string;
			//exit();
	        $this->PdfMergemodel->processPDF2($main_pdf_path, $output_path.basename($main_pdf_path), 0, 0, $random_string);

	        $this->deleteDirectory('tmp/'.$random_string);
	        $this->deleteDirectory('uploads/pdfs/'.$random_string);
	        // echo json_encode(array('main_pdf' => base_url().$output_path.basename($main_pdf_path)));
	        echo json_encode(array(
	        	'random_string' => $random_string,
	        	'main_pdf' => base_url()."output/".$random_string."/".basename($main_pdf_path),
	        	'main_pdf_path' => FCPATH."output/".$random_string."/".basename($main_pdf_path),
	        ));
	        

	        // Now you can do further processing with the uploaded files and paths
	    } else {
	        // Handle the case where no files were uploaded
	        echo json_encode(array('nothing'));
	    }
	}

	function deleteDirectory($directory) {
	    if (!is_dir($directory)) {
	        //echo "Error: '$directory' is not a valid directory.";
	        return;
	    }

	    $files = glob($directory . '/*');
	    
	    foreach ($files as $file) {
	        if (is_file($file)) {
	            unlink($file); // Delete the file
	            //echo "Deleted file: $file <br>";
	        } elseif (is_dir($file)) {
	            deleteDirectory($file); // Recursively delete subdirectories
	        }
	    }

	    rmdir($directory); // Remove the directory itself
	    //echo "Deleted directory: $directory <br>";
	}

	function fetch_cladding_pdf_post(){

		//ini_set('display_errors', 1); 
        //ini_set('display_startup_errors', 1); 
        //error_reporting(E_ALL);
        $this->load->model('Usermodel');	
		$this->load->model('Quotesmodel');
		$this->load->model('Entriesmodel');
		$property_id = $this->input->post('property_id');
		$instructing_client = $this->input->post('instructing_client');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('property_id', 'Property ID', 'required|integer');
	   	$this->form_validation->set_rules('instructing_client', 'Instructing Client', 'required|integer');

	    if ($this->form_validation->run() == FALSE) {

	    	echo json_encode(array('status' => 0, 'msg' => validation_errors()));

		}else{

			$property_id = $this->input->post('property_id');
			$instructing_client = $this->input->post('instructing_client');

			$blocks = Blocksmodel::where('ad_instructingclient', $instructing_client)->where('ad_id', $property_id)->where('ad_percentcomplete', '100')->first();
			//print_r($_POST);

			if(!empty($blocks)){

				$Eventsmodel 			= new Eventsmodel();
				$Eventsmodel->note 		= 'Report viewed by client';
				$Eventsmodel->table_id 	= 'pt_blocks';
				$Eventsmodel->user_id 	= -1;
				$Eventsmodel->entry_id 	= $property_id;
				$Eventsmodel->status 	= 1;
				$Eventsmodel->save();
				$this->Quotesmodel->generate_quote(1, $property_id, NULL, 0, true, true, 'pdf');
				
			}else{

				echo json_encode(array('status' => 0, 'msg' => $this->lang->line("text_rest_unauthorized")));

			}
		}	
	}

	public function autocomplete_post(){

		//https://cladding2.rowe.ai/autocomplete/Autocomplete/search?field=9&linkedtable=13&type=search_field&_dc=1690539454711&query=t&page=1&start=0&limit=25&filter=%5B%7B%22property%22%3A%22text%22%7D%5D
		ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL);

        $this->load->library('form_validation');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('formId', 'Form ID', 'required|integer');
	    $this->form_validation->set_rules('field', 'Field', 'required|integer');
	   	$this->form_validation->set_rules('instructingclient', 'Instructing Client', 'required|integer');

	    if ($this->form_validation->run() == FALSE) {

	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));

	    } else {

	    	$formId = $this->input->post('formId');
	    	//echo $formId.'##';

		    $type = 'normal';

			$field_id 					= $this->input->post('field');
			$Advertfield 				= Advertfieldsmodel::find($field_id);
			//print_r($Advertfield);

			/*
			if($Advertfield->adv_config !== ''){

				$adv_config 				= (array)json_decode($Advertfield->adv_config);
				$search_field				= $adv_config['search'];
				$display_field				= $adv_config['display'];

				$Searchfield 				= Advertfieldsmodel::findorFail($search_field);
				$Displayfield 				= Advertfieldsmodel::findorFail($display_field);
			
			}	
			*/
			
			//print_r($Searchfield);
			$FormsTablesmodel = FormsTablesmodel::findOrFail($formId);
			//print_r($FormsTablesmodel);
			//echo $FormsTablesmodel->ft_database_table.'#';
			$field = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
			//echo $this->db->last_query();
			//print_r($field);
	 
			$linked_tables = $this->db->where('adv_table', $FormsTablesmodel->ft_database_table)->join('forms_tables', 'ft_id = adv_linkedtableid')->order_by('adv_order', 'ASC')->get('pt_advert_fields')->result();
			//echo $this->db->last_query();
			
			//check all the fields if they are linked to the user table
			$this->db->where('ad_instructingclient', $this->input->post('instructingclient'));
			
			
			//just start the query here
			//if(!isset($adv_config)){
				$ds = $this->db->like('ad_'.$Advertfield->adv_column, $this->input->post('query'))->get($FormsTablesmodel->ft_database_table)->result_array();
			/*}else{
				$ds = $this->db->like('ad_'.$Searchfield->adv_column, $this->input->post('query'))->get($FormsTablesmodel->ft_database_table)->result_array();
			}
			*/
			/*
				foreach $where
			*/
			//echo $this->db->last_query();
			//print_r($ds);
			$responses = [];
			$used_values = [];
			$html = '';

			foreach($ds as $d){
				
				//if(!isset($adv_config)){
					if (in_array($d['ad_'.$Advertfield->adv_column], $used_values)) {
				        continue; // skip if ad_id has already been used as a value
				    }
				/*}else{
					if (in_array($d['ad_'.$Displayfield->adv_column], $used_values)) {
				        continue; // skip if ad_id has already been used as a value
				    }
				}
				*/
				$response = array();
				$html.='<option value="'.$d['ad_id'].'"';
				if(isset($listing) && is_array($listing)){
					if(array_key_exists('ad_'.$f['adv_column'],$listing)){
						if($listing['ad_'.$f['adv_column']]==$d['ad_id']){
							$html.=' selected=\"selected\"';
						}
					}
				}

				//if(!isset($adv_config)){

					$response['value'] = $d['ad_'.$Advertfield->adv_column];
					$response['key'] = $d['ad_id']; 
					$used_values[] = $d['ad_'.$Advertfield->adv_column];
				
				/*}else{

					$response['value'] = $d['ad_'.$Displayfield->adv_column];
					$response['key'] = $d['ad_id']; 
					$used_values[] = $d['ad_'.$Displayfield->adv_column];

				}
				*/


				$responses[] = $response;
			}
			echo json_encode($responses);

	    }
	}

	public function entries_post(){
		//echo 'Hello world';
		//print_r($_POST);

        ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL);

        $this->load->library('form_validation');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('formid', 'Form ID', 'required|integer');
	    //$this->form_validation->set_rules('entryid', 'Entry ID', 'required|integer');

	    if ($this->form_validation->run() == FALSE) {

	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));

	    } else {

	        $form_id = $this->input->post('formid');
	        $recordExists = FormsTablesmodel::where('ft_id', $form_id)->where('ft_api', 1)->exists();
	        if($recordExists){

	        	//echo json_encode(array('status' => 1, 'msg' => 'table found'));
	        	$query_fields = $this->input->post('query_fields');
	        	$query_values = $this->input->post('query_values');

	        	if(sizeof($query_fields)!==sizeof($query_values)){

	        		echo json_encode(array('status' => 1, 'msg' => $this->lang->line('query_field_value_count_dont_match')));

	        	}else{
	        		$table = FormsTablesmodel::where('ft_id', $form_id)->first();
	        		if(!empty($table)){
	        			//($table->ft_database_table);

	        			$select_obj = [];
	        			$field_type = [];
	        			//select 
	        			$selects = Advertfieldsmodel::where('adv_table', $table->ft_database_table)->where('adv_api', 1)->orderBy('adv_order', 'asc')->get();

	        			foreach($selects as $s){
	        				$select_obj[] = 'ad_'.$s->adv_column;
	        				$field_type[] = $s->fieldType;
	        				$field_ids[] = $s->adv_id;
	        			}
	        			//print_r($field_ids);
	        			$field_types = [];
	        			//$field_ids = [];
	        			foreach($field_type as $t){
	        				$field_types[] = $t->fi_type;
	        				
	        			}

	        			$this->db->select(implode(', ', $select_obj).', ad_id');
	        			$i = 0;
	        			foreach($query_fields as $q){
	        				$qv = trim($query_values[$i]);
	        				if($qv!==''){
	        					$this->db->where($q, $qv);
	        				}
	        				$i++;
	        			}

	        			####
	        			//$this->db->where('ad_id', 109);
	        			####
	        			
	        			$entries = $this->db->get($table->ft_database_table)->result_array();
	        			//echo $this->db->last_query();


	        			$count = 0;
	        			$image_positions = [];
	        			foreach($field_types as $f){
	        				//print_r($f);
	        				if($f == 'image'){
	        					array_push($image_positions, $count);
	        				}
	        				$count++;
	        			}
	        			$pos1 = 0;
	        			//print_r($image_positions);
	        			foreach ($entries as $entry) {
	        				//print_r($entry);
	        				
	        				$pos = 0;
	        				foreach($entry as $k => $v){
	        					//print_r($k);
	        					if(in_array($pos, $image_positions)){
	        						//echo $field_ids[$pos];
	        						$images = $this->db->where('up_entry_id', $entry['ad_id'])->where('up_field_id', $field_ids[$pos])->get('images')->result();
	        						// Assuming $select_obj is an array, you can use isset() to ensure it exists before using []
	        						//echo $this->db->last_query();

						            if (isset($select_obj[$pos])) {
						                foreach ($images as $i) {
						                    $select_obj_key = $select_obj[$pos];
						                    (array)$entries[$pos1][$select_obj_key][] = $i->up_filename;
						                }
						            }
	        					}
	        					$pos++;
	        				}
	        				$pos1++;
						}
	        			echo json_encode(array('fields'=> $select_obj, 'field_types' => $field_types, 'field_ids' => $field_ids, 'entries'=>$entries));
	        			
	        		}
	        	}

	        }else{

	        	echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_access_to_api_function')));
	        
	        }
    	}
	}

	public function client_post(){
		//echo 'Hello world';
		//print_r($_POST);

        ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL);

        $this->load->library('form_validation');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('formid', 'Form ID', 'required|integer');


	    if ($this->form_validation->run() == FALSE) {

	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));

	    } else {

	        $form_id = $this->input->post('formid');
	        $recordExists = FormsTablesmodel::where('ft_id', $form_id)->where('ft_api', 1)->exists();
	        //print_r($recordExists);
	        if($recordExists){

	        	//echo json_encode(array('status' => 1, 'msg' => 'table found'));
	        	$query_fields = $this->input->post('query_fields');
	        	$query_values = $this->input->post('query_values');
	        	if(sizeof($query_fields)!==sizeof($query_values)){

	        		echo json_encode(array('status' => 1, 'msg' => $this->lang->line('query_field_value_count_dont_match')));

	        	}else{
	        		$table = FormsTablesmodel::where('ft_id', $form_id)->first();
	        		if(!empty($table)){
	        			//($table->ft_database_table);

	        			$select_obj = [];
	        			$field_type = [];
	        			//select 
	        			$selects = Advertfieldsmodel::where('adv_table', $table->ft_database_table)->where('adv_api', 1)->orderBy('adv_order', 'asc')->get();
	        			//print_r($selects);
	        			foreach($selects as $s){
	        				$select_obj[] = 'ad_'.$s->adv_column;
	        				$field_type[] = $s->fieldType;
	        				$field_ids[] = $s->adv_id;
	        			}
	        			//print_r($field_ids);
	        			$field_types = [];
	        			//$field_ids = [];
	        			foreach($field_type as $t){
	        				$field_types[] = $t->fi_type;
	        				
	        			}
	        			$this->db->select(implode(', ', $select_obj).', ad_id');
	        			$i = 0;
	        			foreach($query_fields as $q){
	        				$this->db->where($q, $query_values[$i]);
	        				$i++;
	        			}

	        			####
	        			//$this->db->where('ad_id', 109);
	        			####
	        			
	        			$entries = $this->db->get($table->ft_database_table)->result_array();


	        			$count = 0;
	        			$image_positions = [];
	        			foreach($field_types as $f){
	        				//print_r($f);
	        				if($f == 'image'){
	        					array_push($image_positions, $count);
	        				}
	        				$count++;
	        			}
	        			//print_r($image_positions);
	        			foreach ($entries as $entry) {
	        				//print_r($entry);
	        				$pos = 0;
	        				$pos1 = 0;
	        				foreach($entry as $k => $v){
	        					//print_r($k);
	        					if(in_array($pos, $image_positions)){
	        						//echo $field_ids[$pos];
	        						$images = $this->db->where('up_entry_id', $entry['ad_id'])->where('up_field_id', $field_ids[$pos])->get('images')->result();
	        						//save image into the entries
	        						foreach($images as $i){
	        							$entries[$pos1][$select_obj[$pos1]][] = $i->up_filename;
	        						}
	        						//print_r($images);
	        					}
	        					$pos++;
	        				}
	        				$pos1++;
						}
	        			echo json_encode(array('fields'=> $select_obj, 'field_types' => $field_types, 'field_ids' => $field_ids, 'entries'=>$entries));
	        			
	        		}
	        	}

	        }else{

	        	echo json_encode(array('status' => 0, 'msg' => $this->lang->line('no_access_to_api_function')));
	        
	        }
    	}
	}
	
	public function token_post(){
		//echo 'Hello world';
		//print_r($_POST);

        ini_set('display_errors', 1); 
        ini_set('display_startup_errors', 1); 
        error_reporting(E_ALL);

        $this->load->library('form_validation');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('token', 'Token', 'required');
	    //$this->form_validation->set_rules('entryid', 'Entry ID', 'required|integer');

	    if ($this->form_validation->run() == FALSE) {

	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));

	    } else {

	    	$token = $this->input->post('token');
	    	//Tokensmodel::where('token', $token)->where('expiry_date', '>', time())->toSql();
	    	$tokenRecord = $this->db->where('token', $token)->where('expiry_date >', time())->limit(1)->get('tokens')->row();
	    	//echo $this->db->last_query().'##';
            //print_r($tokenRecord);

	    	if ($tokenRecord) {

			    // Token found, and it is not expired
			    // get the email, we need that to
			    $client = $this->db->get_where('clients', array('ad_id' => $tokenRecord->client_id))->row();

			   	echo json_encode(array('status' => 1, 'instructing_client' => $tokenRecord->client_id, 'email' => $client->ad_emailaddress));

			} else {

			    // Token not found or expired
			     echo json_encode(array('status' => 0, 'msg' => $this->lang->line('token_invalid_or_expired') ));
			
			}

	    }
	}

	public function download_report_post(){
        // ini_set('display_errors', 1); 
        // ini_set('display_startup_errors', 1); 
        // error_reporting(E_ALL);
        $status = false; $message = ''; $responseData = array();

        $this->load->library('form_validation');

        $this->load->model('PtQuoteReportDownloadsModel');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('category', 'Category', 'required');
	    $this->form_validation->set_rules('adId', 'AdId', 'required');
	    $this->form_validation->set_rules('format', 'Format', 'required');
	    $this->form_validation->set_rules('template', 'Template', 'required');
	    // $this->form_validation->set_rules('returnFrom', 'ReturnFrom', 'required');

	    if($this->form_validation->run() == FALSE){
	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));
	        exit;
	    }
	    else{
	    	$category = $this->input->post('category');
	    	$adId = $this->input->post('adId');
	    	$format = $this->input->post('format');
	    	$template = $this->input->post('template');
	    	// $returnFrom = $this->input->post('returnFrom');

	    	// _pre($_POST);
	    	$whereArray = array(
	    		'category' => $category,
	    		'ad_id' => $adId,
	    		'format' => $format,
	    		'template' => $template,
	    		'status' => 'Completed',
	    	);
	    	$ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getLastRecord($whereArray);
	    	// _pre($ptQuoteReportDownloadsData);
	    	if($ptQuoteReportDownloadsData){
	    		$status = 1;
	    		$message = "";
	    		$downloadUrl = "";

	    		if($category == 32){ // Sba report
	    			$downloadUrl = base_url('Cron/downloadReportPdf/'.$ptQuoteReportDownloadsData->filename.'/'.$ptQuoteReportDownloadsData->random_string);
		    	}
		    	else if($category == 56){ // Fraew report
		    		$downloadUrl = base_url('Cron/downloadReportPdf/'.$ptQuoteReportDownloadsData->filename);
		    	}

	    		$responseData = array(
	    			'downloadUrl' => $downloadUrl
	    		);
	    	}
	    	else{
	    		$status = 0;
	    		$message = "Report not found";
	    	}
	    	$resultData = array(
				'status' => $status, 'msg' => $message, 'data' => $responseData,
			);
			echo json_encode($resultData);
	        exit;
	    }
	}
	
	public function check_report_available_post(){
        // ini_set('display_errors', 1); 
        // ini_set('display_startup_errors', 1); 
        // error_reporting(E_ALL);
        $status = false; $message = ''; $responseData = array();

        $this->load->library('form_validation');

        $this->load->model('PtQuoteReportDownloadsModel');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('category', 'Category', 'required');
	    $this->form_validation->set_rules('adId', 'AdId', 'required');
	    $this->form_validation->set_rules('format', 'Format', 'required');
	    // $this->form_validation->set_rules('template', 'Template', 'required');

	    if($this->form_validation->run() == FALSE){
	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));
	        exit;
	    }
	    else{
	    	$isSbaReportExist = false;
	    	$isFraewReportExist = false;

	    	$category = $this->input->post('category');
	    	$adId = $this->input->post('adId');
	    	$format = $this->input->post('format');
	    	// $template = $this->input->post('template');
	    	$template = "SBA";

	    	// _pre($_POST);
	    	$whereArray = array(
	    		'category' => $category,
	    		'ad_id' => $adId,
	    		'format' => $format,
	    		'template' => $template,
	    		'status' => 'Completed',
	    	);
	    	$ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getLastRecord($whereArray);
	    	if($ptQuoteReportDownloadsData){
	    		$isSbaReportExist = true;
	    	}
	    	else{
	    	}

	    	$template = "FRAEW";
	    	// _pre($_POST);
	    	$whereArray = array(
	    		'category' => $category,
	    		'ad_id' => $adId,
	    		'format' => $format,
	    		'template' => $template,
	    		'status' => 'Completed',
	    	);
	    	$ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getLastRecord($whereArray);
	    	//_pre($ptQuoteReportDownloadsData);
	    	if($ptQuoteReportDownloadsData){
	    		$isFraewReportExist = true;
	    	}
	    	else{
	    	}

	    	$responseData = array(
	    		'isSbaReportExist' => $isSbaReportExist, 'isFraewReportExist' => $isFraewReportExist,
	    	);

	    	$status = true;

	    	$resultData = array(
				'status' => $status, 'msg' => $message, 'data' => $ptQuoteReportDownloadsData,
			);
			echo json_encode($resultData);
	        exit;
	    }
	}

	public function mapdata_post(){
		//echo 'Hello world';
		//print_r($_POST);

        $this->load->library('form_validation');
    
	    // Set the validation rules
	    $this->form_validation->set_rules('property_id', 'Property ID', 'required');
	    $this->form_validation->set_rules('instructing_client', 'Instructing Client', 'required');
	    //$this->form_validation->set_rules('entryid', 'Entry ID', 'required|integer');

	    if ($this->form_validation->run() == FALSE) {

	        // Validation failed, display errors
	        echo json_encode(array('status' => 0, 'msg' => validation_errors()));

	    } else {

	    	$property_id 		= $this->input->post('property_id');
	    	$instructing_client = $this->input->post('instructing_client');

	    	$this->db->select('lat, lng, map_heading, map_pitch, map_zoom');
			$this->db->from('pt_propertyview_latlng_list');
			$this->db->join('pt_blocks', 'entry_id = ad_id');
			$this->db->where('entry_id', $property_id);
			$this->db->where('ad_instructingclient', $instructing_client);
			$this->db->where('map_heading IS NOT NULL');
			$this->db->where('map_pitch IS NOT NULL');
			$this->db->where('map_zoom IS NOT NULL');

			$query = $this->db->get();
			$result = $query->result();

			echo json_encode(array('status' => 1, 'map_data' => $result));

	    }

	}
	/*
	public function report_post($formID){

		$category = 1;
		$ad_id = $this->input->post('ad_id');
		$format = $this->input->post('format');	
		$data['formID'] = $formID;

		$this->db->select('*');
		$this->db->where('ft_id', $formID);
		$tableData = $this->db->get('forms_tables')->row_array();	
		//print_r($tableData);

		$_SESSION['ft_database_table'] 	= $tableData['ft_database_table'];
		$_SESSION['ft_categories'] 		= $tableData['ft_categories'];
		$_SESSION['ft_name'] 			= $tableData['ft_name'];
		$_SESSION['formID'] 			= $formID;

		$this->formID = $_SESSION['formID'];
		$this->default_table = $_SESSION['ft_database_table'];
		$this->default_category_table = $_SESSION['ft_categories'];
		$this->default_table_name = $_SESSION['ft_name'];

		$tableData['formID'] = $formID;

		$this->Quotesmodel->generate_quote($category, $ad_id, NULL, 0, true, false, $format, NULL, $tableData);

	}
	public function form_post($category, $formID, $listingId = null){


		//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
		$data['category'] = $category;
		$data['formID'] = $formID;

		$this->db->select('*');
		$this->db->where('ft_id', $formID);
		$tableData = $this->db->get('forms_tables')->row_array();	

		$_SESSION['ft_database_table'] 	= $tableData['ft_database_table'];
		$_SESSION['ft_categories'] 		= $tableData['ft_categories'];
		$_SESSION['ft_name'] 			= $tableData['ft_name'];
		$_SESSION['formID'] 			= $formID;

		$this->formID = $_SESSION['formID'];
		$this->default_table = $_SESSION['ft_database_table'];
		$this->default_category_table = $_SESSION['ft_categories'];
		$this->default_table_name = $_SESSION['ft_name'];

		$category_repeater = Categoryrepeater::where('form_id', $formID)->first();
		//print_r($category_repeater);
		if(!empty($category_repeater)){
			if($this->input->post('ad_id')){
				$repeater_field = Advertfieldsmodel::find($category_repeater->field_id);
				$repeat_times = $this->db->select('ad_'.$repeater_field->adv_column)->where(['ad_id' => $this->input->post('ad_id')])->get($this->default_table)->row();
				//print_r($repeat_times);
				//echo $this->db->last_query();
				if(!isset($_SESSION[$this->default_table]['REPEAT_TIMES'])){
					//minus 1 because the first time counts too
					$_SESSION[$this->default_table]['REPEAT_TIMES'] = $repeat_times->{'ad_'.$repeater_field->adv_column}-1;
					$_SESSION[$this->default_table]['PARENT_ENTRY'] = $this->input->post('ad_id');
					$_SESSION[$this->default_table]['REACHED_END'] = false;
				}
			}
		}

		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$this->db->order_by('ad_order','ASC');


				//echo $this->db->last_query();
		if($this->input->post('ad_id')){
			
			$ad_id = $this->input->post('ad_id');
			$data['ad_id'] = $ad_id;
		
		}


		if($this->input->post('order')){

			$order = $this->input->post('order');
			if(isset($category_repeater->cat_end)){
				//echo '###';
				//echo ($order).'@'.$category_repeater->cat_end;
				if(($order)>$category_repeater->cat_end){
					if((int)$_SESSION[$this->default_table]['REPEAT_TIMES'] > 0){
					//do the thing here if needed
					$order = $category_repeater->cat_start;
					//$this->db->where('ad_order', $next_cat);
					$_SESSION[$this->default_table]['REPEAT_TIMES'] = $_SESSION[$this->default_table]['REPEAT_TIMES']-1;
					//create a new entry here 
					
					$input['ad_userid']=$this->session->userdata("us_id");
					$input['ad_added']=date('Y-m-d H:i:s',strtotime('now'));
					$input['ad_last_update']=date('Y-m-d H:i:s',strtotime('now'));
					$input['ad_contact'] = $this->input->post('contactID');
					$input['ad_rand'] = strtoupper(md5(uniqid(rand(), true)));
					$input['ad_position'] = 1;
					$input['ad_linked_entries'] = $_SESSION[$this->default_table]['PARENT_ENTRY'];

					$this->db->insert($this->default_table, $input);
					$ad_id = $this->db->insert_id();
					//change the id to the newly created one
					$data['ad_id'] = $ad_id;

					$this->db->where('ad_order', $order);
				}else{
					//we reached the end, unset the session
					$data['ad_id'] = $_SESSION[$this->default_table]['PARENT_ENTRY'];
					$_SESSION[$this->default_table]['REACHED_END'] = true;
					$this->db->where('ad_order', $order);
				}

				}else{
					$this->db->where('ad_order',  $order);
				}
			}else{
				$this->db->where('ad_order', $order);
			}
		}
		//$this->db->order_by('ad_id','ASC');
		$this->db->limit(1);
		$result = $this->db->get($this->default_category_table)->result_array();

		if(empty($result)){
			//insert logic to take to purchasing page
			unset($_SESSION[$this->default_table]['REPEAT_TIMES']);
			
			if(isset($_SESSION[$this->default_table]['PARENT_ENTRY'])){

				$html = '<a class="btn btn-primary" href="'.base_url('Post/gen_pdf/1/'.$_SESSION[$this->default_table]['PARENT_ENTRY'].'/pdf').'">Click here to download PDF</a>';
				echo json_encode(array('status' => 1, 'html' => $this->html.$html));
				exit();

			}else{

				$html = '<a class="btn btn-primary" href="'.base_url('Post/gen_pdf/1/'.$ad_id.'/pdf').'">Click here to download PDF</a>';
				echo json_encode(array('status' => 1, 'html' => $this->html.$html));
				exit();

			}

		}

		$html = $this->html;
		ob_start();

		$this->load->model('Advertcategorymodel');
		$data = [];
		$this->formID = $formID;
	
		if(!is_null($ad_id)){
			$data['listing'] = $this->Advertcategorymodel->getAdvert($ad_id);
			//print_r($data['listing']);
		}
		//$data = $this->data;



		//$permissions['role'] = $this->data['user']['us_role'];
		//if(($permissions['create']||$permissions['update'])||($read)){
			
			$data['user'] = $this->data['user'];
			$this->load->helper('cookie');

			$type = $this->input->get('type');
			$data['type'] = $type; 

			$data['order'] = '';
			if($this->input->get('order')){
				$data['order'] = $this->input->get('order');
			}
			//echo $date['order'];
			//containerEnd view has most of the JS in it
			
			$data['width'] = 6;

			//$data['listing'] = '';

			$data['preview'] = true;


			//var_dump($data['listing']);
			//$category = 1;

			$data['tabs'] = $this->db->where('ad_parent', 0)->get($this->default_category_table)->result();
			//echo $this->db->last_query();
			//print_r($data['tabs']);

			//echo $this->db->last_query();
			//var_dump($result);
			$data['sellerDetails'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
			$data['category'] = $category;

			$data['image'] = base_url()."/assets/images/communications.png";
			$data['section'] = '';

			$data['button'] = '';
			$data['button'] ='<button type="submit" class="btn btn-primary ladda-button submitButton" data-style="expand-right" id="submitButtonHeader" onclick="saveAndNext();">Save</button>';
			
			$data['subtitle'] = '';
			$data['formID'] = $this->formID;

			
			//$this->load->view('dashboard/header', $data);

			$this->load->view('templates/form_start', $data);

			$data['alert'] = 'success';


		
			$data['alert'] = 'danger';
			$data['alertid'] = 'warningMessage';
			$this->load->view('common/alert', $data);

			switch($category){
				default:
					foreach($result as $r){
						//print_r($r);
						$r['listing'] = $data['listing'];
						$this->load->view('common/wrapper',$r);
						$this->load->view('common/title_switch',$r);
						
						$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);	

						//if($this->data['us_role']!=0){
						//	if($this->data['us_role']==$r['ad_role']){
						//	
						//if(($read)&&(($permissions['create']||$permissions['update']))){
							$this->load->view('templates/advert',$data);
						//}else{
						//	$this->load->view('templates/advert_text',$data);
						//}
								
						//	}else{
						//	}
						//}else{
						//	$this->load->view('templates/advert',$data);
						//}
						$this->load->view('common/wrapperEnd',$r);
					}
				break;
			}
			$permissions['create'] = true;
			$permissions['update'] = true;
			$data['user'] = $this->data;
			$data['permissions'] = $permissions;
			$form = FormsTablesmodel::findorFail($data['formID']);
			$table = $form->ft_database_table;

			$data['scripts_containerEnd'] = true;

			$data['next_cat'] = $result[0]['ad_order']+1;

			$data['events'] = Eventsmodel::where('entry_id', $idx)->where('table_id', $table)->orderBy('created_at', 'DESC')->get();  
			$this->load->view('templates/form_end', $data);
			//print_r($data);
			$data['data'] = $data;
			$this->load->view('templates/scripts_containerEnd', $data);
			$this->load->view('modals/edit_datasource', $data);
			$html.= ob_get_clean(); 

			//$html .= "<script src=\"" . base_url('public/js/app.me.js') . "\"></script>\n";

			//echo $html;
			echo json_encode(array('status' => 1, 'html' => $html.$_SESSION[$this->default_table]['REPEAT_TIMES']));
			//$this->load->view('dashboard/footer', $data);
	}
	*/
}