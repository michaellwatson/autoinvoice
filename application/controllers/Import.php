<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Import extends My_Controller {

	public function __construct(){
		parent::__construct();
	}

	function upload($id = NULL){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$this->load->view('dashboard/header', $data);
		$this->load->view('forms/user', $data);
		$this->load->view('dashboard/footer');	
	}

	public function documents($formID){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$form = FormsTablesmodel::findorFail($formID);
		$data['form_id'] = $form->ft_id;
		$this->load->view('dashboard/header', $data);
		//$this->load->view('forms/user', $data);
		$this->load->view('forms/fileuploader-banner', $this->data);
		$this->load->view('dashboard/footer');	

	}

	public function mapping($formID){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$form = FormsTablesmodel::findorFail($formID);
		$data['form_id'] = $form->ft_id;

		$fields = Advertfieldsmodel::where('adv_table', $form->ft_database_table)->orderBy('adv_order', 'asc')->get();
		$data['fields'] = $fields;

		$form = FormsTablesmodel::findorFail($formID);
		$c = 0;
		if(isset($_SESSION['item'])){
			$first_line  = array();
			$handle = @fopen("assets/uploaded_images/".$_SESSION['item'], "r");
			if ($handle) {
			    while (($buffer = fgetcsv($handle, 4096, ",",'"')) !== false) {
			        $first_line[] = $buffer;
			        if($c>=2){
			        	break;
			    	}
			    }
			    if (!feof($handle)) {
			        //echo "Error: unexpected fgets() fail\n";
			    }
			    fclose($handle);
			}
			$c++;
		}
		/*
		$data['linked_fields'] = Advertfieldsmodel::where('adv_table', $form->ft_database_table)
		->where('adv_linkedtableid', '!=', 0)
		->join('field_type', 'fi_id', '=', 'adv_field_type')
		->orderBy('adv_order', 'asc')->get()->toArray();
		*/
		$data['first_line'] = $first_line[0];
		$data['second_line'] = $first_line[1];
		$this->load->view('dashboard/header', $data);
		//$this->load->view('forms/user', $data);
		$this->load->view('forms/mapping', $this->data);
		$this->load->view('dashboard/footer');	
	
	}

	public function save_mapping($formID){

		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$form = FormsTablesmodel::findorFail($formID);
		$table = $form->ft_database_table;

		$mapping = array();
		$datasources = array();
		$linked_tables = array();
		$value = array();
		$c = 0;

		foreach($_POST['maps'] as $m){
			if($m!==''){
				$field = Advertfieldsmodel::findorFail($m);
				$mapping[$c] = 'ad_'.$field['adv_column'];
				$datasources[$c] = $field['adv_datasourceId'];
				$linked_tables[$c] = $field['adv_linkedtableid'];
				$value[$c] = $_POST['input_'.$c];
			}else{
				$mapping[$c] = '';
				$datasources[$c] = 0;
				$linked_tables[$c] = 0;
			}
			$c++;
		}

		$csv = array();

		if(isset($_SESSION['item'])){
			
			$handle = @fopen("assets/uploaded_images/".$_SESSION['item'], "r");
			if ($handle) {
			    while (($buffer = fgetcsv($handle, 4096, ",",'"')) !== false) {
			        $csv[] = $buffer;
			    }
			    if (!feof($handle)) {
			        //echo "Error: unexpected fgets() fail\n";
			    }
			    fclose($handle);
			}
			$c++;
		}
		if($this->input->post('has_header')==1){
			array_shift($csv);
		}


		$fields = Advertfieldsmodel::where('adv_table', $form->ft_database_table)->orderBy('adv_order', 'asc')->get();	

		foreach($fields as $f){

			$input['ad_'.$f->adv_column] = $this->input->post($f->adv_column); 	

		}

		//print_r($input);
		$failed_rows = [];
		//print_r($csv);
		foreach($csv as $row){
			$insert = array();
			$time = time();
			$insert['ad_added'] = date('Y-m-d H:i:s', $time);
			//print_r($row);
			$c = 0;
			
			foreach($mapping as $m){
				if($m!==''){
					if($datasources[$c]!==0){
						//echo ucfirst(strtolower($row[$c])).'##';
						//echo $datasources[$c];
						//echo '#';
						$ds = Datasourcevaluesmodel::where('ds_ds_id', $datasources[$c])->where('ds_value', 'LIKE',  ucfirst(strtolower($row[$c])))->first();
						//print_r($ds);
						if(isset($ds['ds_id'])){
							$insert[$m] = $ds['ds_id'];
						}else{
							$insert[$m] = '';
						}
					}if($linked_tables[$c]!==0){

						$first_fields = [];
						//get the linked table name
						$linked_table = $this->db->where('ft_id', $linked_tables[$c])->get('forms_tables')->row_array();
						//for now the default name field is the first one
						//so get the first field of the linked table 
						

						if($linked_table['ft_database_table']!=='pt_user'){
							$this->db->select('adv_column');
							$this->db->where('adv_table', $linked_table['ft_database_table']);
							$first_fields[] = $this->db->order_by('adv_order','ASC')->get('advert_fields')->row_array()['adv_column'];	
							//get the key from the select of the original field name and replace it with the linked one
							$this->db->select('ad_id, ad_'.$first_fields[0]);
							$this->db->where($linked_table['ft_database_table'].'.ad_'.$first_fields[0], $row[$c]);
							$result = $this->db->get($linked_table['ft_database_table'])->row_array();
							//print_r($result);
							if(!empty($result)){
								$insert[$m] = $result['ad_id'];
							}else{
								$failed_rows[] = $row; 
							}
						}else{
							$this->db->flush_cache();
							$this->db->select('us_id, us_firstName');
							$this->db->where($linked_table['ft_database_table'].'.us_firstName', $row[$c]);
							$result = $this->db->get($linked_table['ft_database_table'])->row_array();
							//print_r($result);
							if(!empty($result)){
								$insert[$m] = $result['us_id'];
							}else{
								$failed_rows[] = $row; 
							}
						}

					}else{

						if(isset($value[$c])){
							if($value[$c]!==''){
								$insert[$m] = trim($value[$c]);
							}else{
								$insert[$m] = trim($row[$c]);
							}
						}else{
							$insert[$m] = trim($row[$c]);
						}
					}
				}
				$c++;
			}

			$invoice_number = $this->db->where('se_key','invoice_number_start')->get('settings')->row();

			$invoice_number_new = (int)$invoice_number->se_value;
			$invoice_number_new = $invoice_number_new+1;

			$insert['ad_invoicenumber'] = $invoice_number_new/100;

			$update_settings['se_value'] = $invoice_number_new;
			$this->db->where('se_key', 'invoice_number_start');
			$this->db->update('settings', $update_settings);
			//echo $this->db->last_query();
			//print_r($insert);
			$insert = array_merge($input, $insert);

			$this->db->insert($table, $insert);
			//echo $this->db->last_query();

			$id = $this->db->insert_id();

			$Eventsmodel 			= new Eventsmodel();
			$Eventsmodel->note 		= $form->ft_name.' Created via Import';
			$Eventsmodel->table_id 	= $table;
			$Eventsmodel->user_id 	= $this->userid;
			$Eventsmodel->entry_id 	= $id;
			$Eventsmodel->save();
		}
		echo json_encode(array('status'=>1));
	}
}