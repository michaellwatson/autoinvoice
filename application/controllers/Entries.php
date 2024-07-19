<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries extends My_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('msg', 'english');
		//$this->load->model('User');
		$this->load->model('Usermodel');
		$this->load->model('Mailmodel');
		$this->load->model('Advertcategorymodel');
		
		$this->userid = 1;
		
		$email = $this->input->cookie('email', false);
		$password = $this->input->cookie('password', false);

		$this->data['user'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		//print_r($_SESSION);
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			//nothing to do user is authenticated
			//var_dump($this->data);
			$this->userid = $this->session->userdata("us_id");
			$this->username = $this->data['user']['us_username'];
		}else{
			//$this->load->view('login',$data);
			redirect(base_url('login')); 
		}

		$this->default_table 			= $this->config->item('default_table');
		$this->default_category_table 	= $this->config->item('default_category_table');
		$this->default_table_name 		= $this->config->item('default_table_name');
		$this->default_category_depth = '';

		$formID = isset($_GET['formID']) ? $_GET['formID'] : '';

		if($formID!=''){
			if(is_numeric($_GET['formID'])){
				$tableData = $this->Advertcategorymodel->formsTables($_GET['formID']);

				$_SESSION['ft_database_table'] = $tableData['ft_database_table'];
				$_SESSION['ft_categories'] = $tableData['ft_categories'];
				$_SESSION['ft_name'] = $tableData['ft_name'];

				$this->default_table = $tableData['ft_database_table'];
				$this->default_category_table = $tableData['ft_categories'];
				$this->default_table_name = $tableData['ft_name'];
				$this->import = $tableData['ft_import'];

				$this->pdf = $tableData['ft_pdf'];
				$this->word = $tableData['ft_word'];
			}
		}
		//echo $this->default_table.'#';
		//echo $this->default_category_table.'#';
		//echo $this->default_table_name.'#';
		$this->data['formID'] = $formID;

		if(isset($_SESSION['ft_database_table'])){
			if($_SESSION['ft_database_table']!=''){
				$this->default_table = $_SESSION['ft_database_table'];
			}
		}
		if(isset($_SESSION['ft_categories'])){
			if($_SESSION['ft_categories']!=''){
				$this->default_category_table = $_SESSION['ft_categories'];
			}
		}
		if(isset($_SESSION['ft_name'])){
			if($_SESSION['ft_name']!=''){
				$this->default_table_name = $_SESSION['ft_name'];
			}
		}
		//print_r($tableData);
		if(isset($tableData)){
			$this->data['name'] = $tableData['ft_name'];
		}
		
		$this->data['view_users'] = $this->Usermodel->has_permission('view_users');
		$this->data['edit_client'] 	= $this->Usermodel->has_permission('edit_jobs');

		$this->data['approve_jobs'] = $this->Usermodel->has_permission('approve_jobs');
		$this->data['edit_jobs'] 	= $this->Usermodel->has_permission('edit_jobs');
		$this->data['add_jobs'] 	= $this->Usermodel->has_permission('add_jobs');
		$this->data['edit_personnel'] = $this->Usermodel->has_permission('edit_personnel');

		//get fields
		if(isset($tableData)){
			$this->db->where('adv_table', $tableData['ft_name']);
			$this->db->where('fi_type !=', 'text');
			$this->db->where('fi_type !=', 'textarea');
			$this->db->join('field_type','fi_id=adv_field_type');
			$this->db->order_by('adv_order','ASC');
			$this->data['fields'] = $this->db->get('advert_fields')->result_array();
		}
	}


	function results(){
		
		$equals = [];
		//ini_set('display_errors', '1');
		//ini_set('display_startup_errors', '1');
		//error_reporting(E_ALL);
		$_SESSION[$this->default_table]['search_url'] = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$search_fields = Advertfieldsmodel::where('adv_search', '=', 1)->where('adv_table', '=', $this->default_table)->get();
		//print_r($search_fields);

		$limit = $this->input->get('limit');
		$start = $this->input->get('start');
		//echo $start.'#'.$limit;
		if(is_null($limit)){
			$limit = 25;
		}
		if(is_null($start)){
			$start = 0;
		}
		//echo $start.'#'.$limit;
		$this->db->select('*');
		//echo $this->data['user']['us_role'];
		if((int)$this->data['user']['us_role']!==1){
			if($this->db->field_exists('ad_User', $this->default_table) || $this->db->field_exists('ad_user', $this->default_table)){
				//echo '##';
				//$this->db->where('ad_User', $this->data['user']['us_id']);	
				$role = Rolemodel::find((int)$this->data['user']['us_role']);
				if(!$role->role_can_see_all_content){
					$equals[] = array($this->default_table.'.ad_user', $this->data['user']['us_id']);
				}
			}
		}
		$entries_count = $this->db->get($this->default_table)->num_rows();
		//get all fields
		$fields_array = [];
		$fields_array[] = $this->default_table.'.ad_id';
		$fields_array[] = $this->default_table.'.ad_flag';
		
		$this->db->select('adv_column');
		$this->db->where('adv_table', $this->default_table);
		$fields = $this->db->get('advert_fields')->result_array();
		//have to create a simple array from the database result for the select
		foreach($fields as $f){
			$fields_array[] = $this->default_table.'.ad_'.$f['adv_column'];
		}

		//linked table, get any linked table fields
		$this->db->select('adv_column, adv_linkedtableid');
		$this->db->where('adv_table', $this->default_table);
		$this->db->where('adv_linkedtableid !=', 0);
		$linkedfields = $this->db->get('advert_fields')->result_array();
		$first_fields = [];

		$j = 0;
		//foreach linked fields
		$joins = [];
		$alpha = range('a', 'z');
		$table_keys = [];

		foreach($linkedfields as $l){
			//get the linked table name
			$linked_table = $this->db->where('ft_id', $l['adv_linkedtableid'])->get('forms_tables')->row_array();

			//for now the default name field is the first one
			//so get the first field of the linked table 
			$this->db->select('adv_column');
			$this->db->where('adv_table', $linked_table['ft_database_table']);
			$first_fields[] = $this->db->get('advert_fields')->row_array()['adv_column'];
			//save the joins
			if($linked_table['ft_is_users']==1){
				$joins[] = array(strtolower($linked_table['ft_database_table']), strtolower($this->default_table.'.ad_'.$l['adv_column'].' = '.$linked_table['ft_database_table'].'.us_id'));
			}else{
				//no need to join if its linked to itself
				if(strtolower($this->default_table) != strtolower($linked_table['ft_database_table'])){
					//$joins[] = array(strtolower($linked_table['ft_database_table']), strtolower($this->default_table.'.ad_'.$l['adv_column'].' = '.$linked_table['ft_database_table'].'.ad_id'));
					$joins[] = array(strtolower($linked_table['ft_database_table']).' as '.$alpha[$j], strtolower($this->default_table.'.ad_'.$l['adv_column'].' = '.$alpha[$j].'.ad_id'));
					$table_keys[$linked_table['ft_database_table']] = $alpha[$j];
				}
			}
			//print_r($joins);
			//get the key from the select of the original field name and replace it with the linked one

			$key = array_search($this->default_table.'.ad_'.$l['adv_column'], $fields_array);
			if($key!==false){
				if($linked_table['ft_is_users']==1){
					$parts = explode('.', $fields_array[$key]);
					$fields_array[$key] = ' CONCAT(us_firstName, " ", us_surname) as '. $parts[1];
				}else{
					$field_parts = explode('.', $fields_array[$key]);
					//$fields_array[$key] = $linked_table['ft_database_table'].'.ad_'.$first_fields[$j].' as '.$field_parts[1];
					if(strtolower($this->default_table) == strtolower($linked_table['ft_database_table'])){

						$fields_array[$key] = $this->default_table.'.'.$field_parts[1];
					
					}else{
						
						if (strpos($first_fields[$j], 'ad_') !== 0) {
					        // If not, add 'ad_' prefix
					        $field_temp = 'ad_' . $first_fields[$j];
					    }else{
					    	$field_temp = $first_fields[$j];
					    }
						$fields_array[$key] = $alpha[$j].'.'.$field_temp.' as '.$field_parts[1];
					
					}
					
				}
			}	
			$j++;
		}


		$likes = [];

		foreach($search_fields as $s){
			if($this->input->get($s->adv_column)!=''){
				//echo $s->adv_linkedtableid;
				if($s->adv_linkedtableid!=0){
					$linked_table = $this->db->where('ft_id', $s->adv_linkedtableid)->get('forms_tables')->row_array();
					if($linked_table['ft_is_users']==1){
						$likes[] = array($linked_table['ft_database_table'].'.us_firstName', $this->input->get($s->adv_column));
						$likes[] = array($linked_table['ft_database_table'].'.us_surname', $this->input->get($s->adv_column));
					}else{
						//echo $this->db->last_query();
						//print_r($linked_table);
						$adv_config 				= (array)json_decode($s->adv_config);

						$display_field		= $adv_config['display'];
						$search_field		= $adv_config['search'];

						$Searchfield 				= Advertfieldsmodel::findorFail($search_field);
						$Displayfield 				= Advertfieldsmodel::findorFail($display_field);

						$field = $this->db->where('adv_table', $linked_table['ft_database_table'])->order_by('adv_order', 'ASC')->get('advert_fields')->row_array();

						
						//linked to an external table
						if (isset($table_keys[$linked_table['ft_database_table']]) && $table_keys[$linked_table['ft_database_table']] != '') {
							$likes[] = array($table_keys[$linked_table['ft_database_table']].'.ad_'.$Searchfield->adv_column, $this->input->get($s->adv_column));
						}else{
							//linked to itself
							$likes[] = array($this->default_table.'.ad_'.$Searchfield->adv_column, $this->input->get($s->adv_column));
						}
					}
						
				}else if($s->adv_datasourceId!=0){
		
					$values = $this->db->where('ds_ds_id', $s->adv_datasourceId)->like('ds_value', $this->input->get($s->adv_column), 'both',false)->get('ds_values')->row_array();
					if (isset($values) && is_array($values) && isset($values['ds_id'])) {
						$likes[] = array($this->default_table.'.ad_'.$s->adv_column, $values['ds_id']);
					}
					
				}else{
					$val = $this->input->get($s->adv_column);
					$boolArray = array('true', 'false');

					if(in_array($val, $boolArray)){
						//echo '##';
						//echo $val;
						if((string)$val=='true'){
							$val = 1;
							//echo $val;
						}else{
							$val = 0;
						}
						if($val){
							$equals[] = array($this->default_table.'.ad_'.$s->adv_column, $val);
						}
					}else{
						$likes[] = array($this->default_table.'.ad_'.$s->adv_column, $val);
					}
				}
			}
		}

		//print_r($equals);
		$this->db->select(implode (", ", $fields_array));
		foreach($joins as $j){
			$this->db->join($j[0], $j[1], 'left');
		}
		foreach($equals as $e){
			$this->db->where($e[0], $e[1]);
		}
		foreach($likes as $l){
			if($l[1]!==''){
				if($l[1]!=='null'){
					$this->db->like($l[0], $l[1], 'both', false);
				}
			}
		}


		//any custom query definitions
		$Queryconditionsmodel = new Queryconditionsmodel();
		$query_conditions = $Queryconditionsmodel->where('form_id', '=', $this->formID)->get();	
		foreach($query_conditions as $q){
			$field = Advertfieldsmodel::findOrFail($q->field_id);
			if($q->operator == 'IS'){
				$this->db->where('ad_'.$field->adv_column.' IS '.$q->value, NULL, false);
			}else{
				$this->db->where('ad_'.$field->adv_column.' '.$q->operator, $q->value);
			}	
		}

		//print_r($_SESSION);
		if(($this->input->get('sort_order') != 'null') && ($this->input->get('sort_order') != '')){
			//echo $this->input->get('sort_order');
			//echo '##';
			$sort_order = $this->input->get('sort_order');

			if($sort_order!=='0'){
				switch($sort_order){
					case 'ASC':
						$sort_order = 'ASC';
					break;
					case 'DESC':
						$sort_order = 'DESC';
					break;
					default:
						$sort_order = 'ASC';
					break;
				}
				$_SESSION[$this->default_table]['sort_order'] = $sort_order;
				$this->db->order_by($this->default_table.'.ad_added', $sort_order);
			}

		}else if($this->input->get('sort')){
			//echo '#';
			//if($_SESSION[$this->default_table]['sort_order'] == '0'){
				
				
				$sortDate = json_decode($this->input->get('sort'));
				$this->db->order_by($sortDate[0]->property, $sortDate[0]->direction);

			//}
			
		}else if(isset($_SESSION[$this->default_table]['sort_order'])){
				//echo '@@';
				$sort_order = $_SESSION[$this->default_table]['sort_order'];
				switch($sort_order){
					case 'ASC':
						$sort_order = 'ASC';
					break;
					case 'DESC':
						$sort_order = 'DESC';
					break;
					default:
						$sort_order = 'ASC';
					break;
				}
				$this->db->order_by($this->default_table.'.ad_added', $sort_order);
			//}
		}
		
		$countQuery = clone $this->db;
		$entries = $this->db->get($this->default_table, $limit, $start)->result_array();
		//echo $this->db->last_query();
		
		$entries_count = $countQuery->get($this->default_table)->num_rows();
		//echo $countQuery->last_query();

		$entries_ids = [];

		foreach($entries as $e){
			$entries_ids[] = ['ad_id' => $e['ad_id']];
		}
		$_SESSION[$this->default_table]['entries'] = $entries_ids;
		
		//echo $this->db->last_query();
		//datasource fields
		$this->db->select('adv_column');
		$this->db->where('adv_table', $this->default_table);
		$this->db->where('adv_datasourceId !=', 0);
		$dsfields = $this->db->get('advert_fields')->result_array();

		//date fields
		$this->db->select('adv_column');
		$this->db->where('adv_table', $this->default_table);
		$this->db->where('adv_field_type', 9);
		$datefields = $this->db->get('advert_fields')->result_array();

		//yes no fields
		$this->db->select('adv_column');
		$this->db->where('adv_table', $this->default_table);
		$this->db->where('adv_field_type', 1);
		$yesnofields = $this->db->get('advert_fields')->result_array();

		//textarea fields
		$this->db->select('adv_column');
		$this->db->where('adv_table', $this->default_table);
		$this->db->where('adv_field_type', 6);
		$textareas = $this->db->get('advert_fields')->result_array();

		//print_r($yesnofields);
		//print_r($dsfields);
		$dsValues = array();
		if(!empty($dsfields)){
			foreach($dsfields as $d){
				$dsValues[] = $d['adv_column'];
			}
		}
		$dateValues = array();
		if(!empty($datefields)){
			foreach($datefields as $d){
				$dateValues[] = $d['adv_column'];
			}
		}
		$i = 0;

		//need to filter this by datasource too
		//because tables may share field names
		foreach($entries as $e){

			foreach($dsValues as $d){
				$ds_value = $this->Advertcategorymodel->getDataValue($e['ad_'.$d]);
				if(!empty($ds_value)){
					$entries[$i]['ad_'.$d] = $ds_value['ds_value'];
				}
				
			}
			foreach($dateValues as $d){
				if($e['ad_'.$d]!=0){
					$entries[$i]['ad_'.$d] = date('d/m/Y', (int)$e['ad_'.$d]);
				}else{
					$entries[$i]['ad_'.$d] = '-';
				}
			}
			foreach($yesnofields as $d){

				switch($e['ad_'.$d['adv_column']]){
					case '0':
						$entries[$i]['ad_'.$d['adv_column']] = 'No';
					break;
					case '1':
						$entries[$i]['ad_'.$d['adv_column']] = 'Yes';
					break;
				}
			}

			foreach($textareas as $d){

				$v = strip_tags($e['ad_'.$d['adv_column']]);
				$v = trim(preg_replace('/\s\s+/', ' ', $v));

				if (strlen($e['ad_'.$d['adv_column']]) >= 20) {
				    $entries[$i]['ad_'.$d['adv_column']] = substr($v, 0, 10). " ... ";
				} else {

					if ((
						is_string($entries[$i]['ad_'.$d['adv_column']])
					) && is_string($e['ad_'.$d['adv_column']])) {
						$entries[$i]['ad_'.$d['adv_column']] = strip_tags($e['ad_'.$d['adv_column']]);
					}
				}
			}

			$i++;
		}
        /*                  
                if($f['fi_type']=='radioBool'){
                    switch($listing['ad_'.$f['adv_column']]){
                    	case 0:
                        	$data_text = 'No';
                     	break;
                      	case 1: 
                        	$data_text = 'Yes';
                      	break;
                    }
                }
		*/

		echo json_encode(
			array(
				'store'			=>$entries,
				'totalProperty' => $entries_count,
				'sort'			=> isset($_SESSION[$this->default_table]['sort_order']) ? $_SESSION[$this->default_table]['sort_order'] : ''
			)
		);	
	
	}


	function show(){
		//search fields
		$search_fields = Advertfieldsmodel::where('adv_search', '=', 1)->where('adv_table', '=', $this->default_table)->join('field_type', 'fi_id', '=', 'adv_field_type')->get();
		$fields = Advertfieldsmodel::where('adv_table', '=', $this->default_table)->where('adv_show_field', '=', 1)->join('field_type', 'fi_id', '=', 'adv_field_type')->orderBy('adv_order', 'ASC')->get();
		$data = $this->data;
		$data['user'] 			= $this->data['user'];
		$data['search_fields']	= $search_fields;
		$data['fields']			= $fields;
		if(isset($this->import)){
			$data['import']			= $this->import;
		}
		if(isset($this->pdf)){
			$data['pdf']			= $this->pdf;
		}
		if(isset($this->word)){
			$data['word']			= $this->word;
		}

		$permission = $this->Usermodel->has_permission('read', $this->input->get('formID'));

		$data['create'] = $this->Usermodel->has_permission('create', $this->input->get('formID'));

		//$data['fields'] = $this->db->field_data($this->default_table);
		//print_r($data['fields']);
		$data['columns'] = array();
		$data['columns'][] = array('ad_id', 'system');
		$data['columns'][] = array('ad_flag', 'system');
		//$i = 5;
		$c = 0;

		$data['table_name'] = str_replace(' ', '_', $this->default_table_name);
		$data['default_table_name'] = $this->db->dbprefix.strtolower($this->default_table_name);

		foreach($data['fields'] as $f){
			//if($c<=$i){
				$data['columns'][] = array('ad_'.$f['adv_column'], $f['fi_type']);
				$c++;
			//}
		}


		$strcolumns = '';
		$count = 0;
		//print_r($data['columns']);
		foreach($data['columns'] as $c){

			$path = dirname(dirname(__FILE__)).'/';

			$module_loaded = false;
			//echo $path.'modules/'.$f['fi_type'];
			if(is_dir($path.'modules/'.$c[1])){
				//method exists would be more elegant
				try{
					$field_name = Modules::run(strtolower($c[1]).'/'.ucfirst($c[1]).'/get', 'field_name');
					if($field_name == $c[1]){

						$strcolumns.= Modules::run(strtolower($c[1]).'/'.ucfirst($c[1]).'/field_id', $c);

						$module_loaded = true;
					}
				}catch(Exception $e){
					$module_loaded = false;
				}
			}

			if($module_loaded === false){
				$strcolumns.= "'".$c[0]."'";
			}

		  if($count<(sizeof($data['columns'])-1)){
		    $strcolumns.= ',';
		  }
		  $count++;

		}
		$data['strcolumns'] = $strcolumns;

		if(isset($_SESSION['page_'.$this->formID])){
			$data['currentPage'] = $_SESSION['page_'.$this->formID];
		}else{
			$data['currentPage'] = '1';
		}


		$this->load->view('dashboard/header', $data);
		if($permission){
			$this->load->view('lists/entries', $data);
		}else{
			$this->load->view('templates/no_permission', $data);
		}
		$this->load->view('dashboard/footer');
	}

	function query($column){
		$results = array();
		$query = $this->input->get('query');

		$_SESSION['search_fields'][$this->formID][$column] = $query;
		//check we're allowed this field
		$fields = Advertfieldsmodel::where('adv_column', '=', $column)->where('adv_table', '=', $this->default_table)->first();

		if((int)$this->data['user']['us_role']!==1){
			$this->db->where('ad_User', $this->data['user']['us_id']);	
		}


		if($fields){
			if($fields->adv_field_type==3){
				if($fields->adv_linkedtableid!==0){
					//linked table

					$source_table = $this->db->where('ft_id', $fields->adv_linkedtableid)->get('forms_tables')->row_array();
					if($source_table['ft_is_users']==1){

						$values = $this->db->or_like('us_firstName', $query)->or_like('us_surname', $query)->get($source_table['ft_database_table'])->result_array();
						//echo $this->db->last_query();
						//print_r($results);
						foreach($values as $v){
							$result = array();
							$result['text'] = $v['us_firstName'].' '.$v['us_surname'];
							$results[] = $result;
						}
						echo json_encode($results);
					
					}else{
						//print_r($source_table);
						//like field
						$field = $this->db->where('adv_table', $source_table['ft_database_table'])->order_by('adv_order', 'ASC')->get('advert_fields')->row_array();
						//print_r($field['adv_column']);
						$values = $this->db->like('ad_'.$field['adv_column'], $query)->get($source_table['ft_database_table'])->result_array();
						//echo $this->db->last_query();
						//print_r($results);
						foreach($values as $v){
							$result = array();
							$result['text'] = $v['ad_'.$field['adv_column']];
							$results[] = $result;
						}
						echo json_encode($results);
					}

				}else if($fields->adv_datasourceId!==0){
					//linked datasource
					$values = Datasourcevaluesmodel::where('ds_ds_id', $fields->adv_datasourceId)->where('ds_value', 'LIKE', '%'.$query.'%')->get();
					foreach($values as $v){
						$result = array();
						$result['text'] = $v->ds_value;
						$results[] = $result;
					}
					echo json_encode($results);
				}

			}else if($fields->adv_field_type==5){
					$values = $this->db->select('ad_'.$fields->adv_column)->like('ad_'.$fields->adv_column, $query)->group_by('ad_'.$fields->adv_column)->get($this->default_table)->result_array();
					//print_r($results);
					foreach($values as $v){
						$result = array();
						$result['text'] = $v['ad_'.$fields->adv_column];
						$results[] = $result;
					}
					echo json_encode($results);
			}
		}
	}


	public function delete(){

		$permission = $this->Usermodel->has_permission('delete', $this->input->get('formID'));
		if($permission){

			$this->form_validation->set_rules('id', 'ID', 'required|numeric');
			if ($this->form_validation->run() == FALSE){
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			}else{
				$id = $this->input->post('id');

				$Eventsmodel = new Eventsmodel();
				$Eventsmodel->where('entry_id', $this->input->post('id'))->delete();
					
				$this->db->where('ad_id', $id)->delete($this->default_table); 

				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('job_deleted')));
			}
			
		}else{

			echo json_encode(array('status'=>0,'msg'=>$this->lang->line('no_permission')));
		
		}
		
	}

	public function saveCurrentPage(){
		$this->form_validation->set_rules('currentPage', 'Current Page', 'required|numeric');
		$this->form_validation->set_rules('formID', 'Form ID', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
		}else{

			$url = $_SESSION[$this->default_table]['search_url'];

			// Parse the existing query string
			$queryString = parse_url($url, PHP_URL_QUERY);
			parse_str($queryString, $queryParams);

			$start = $this->input->post('from');
			$limit = (int)$this->input->post('to') - (int)$this->input->post('from');

			// Update the query parameters
			$queryParams = array_merge($queryParams, [
			    'start' => $start,
			    'limit' => $limit,
			    'page'  => $this->input->post('currentPage')
			]);

			// Build the new query string
			$newQueryString = http_build_query($queryParams);

			// Update the URL with the new query string
			$newUrl = preg_replace('/\?.*/', '', $url) . '?' . $newQueryString;

			// Output the updated URL
			// update the url to the correct page
			$_SESSION[$this->default_table]['search_url'] = $newUrl;

			//$this->session->set_userdata('home_currentPage', $this->input->post('currentPage'));
			$array = array('page_'.$this->input->post('formID')=>$this->input->post('currentPage'));
			$this->session->set_userdata($array);
			//var_dump($this->session->all_userdata());
			$_SESSION['page_'.$this->input->post('formID')] = $this->input->post('currentPage');
			echo json_encode(array('status'=>1,'msg'=>$this->session->userdata('home_currentPage')));
		}
	}


	function update_flag(){
		$this->form_validation->set_rules('id', 'ID', 'required|numeric');
		$this->form_validation->set_rules('val', 'val', 'required');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0, 'msg'=>validation_errors()));
		}else{
			$flag_val 	= $this->input->post('val');
			$id 		= $this->input->post('id');

			$update['ad_flag'] = $flag_val;
			$this->db->where('ad_id', $id)->update($this->default_table, $update);
			echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
		}
	}

	function update_dropdown(){

		$this->form_validation->set_rules('entry_id', 'Entry ID', 'required|numeric');
		$this->form_validation->set_rules('value', 'Value', 'required');
		$this->form_validation->set_rules('field', 'field', 'required');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0, 'msg'=>validation_errors()));
		}else{

			$entry_id 	= $this->input->post('entry_id');
			$field 		= $this->input->post('field');
			$value 		= $this->input->post('value');

			$table 		= $_SESSION['ft_database_table'];

			$field_exists = $this->db->get_where('advert_fields', ['adv_column' => str_replace('ad_', '', $field), 'adv_table' => $table])->row_array();
			if(!empty($field_exists)){

				$permission = $this->Usermodel->has_permission('update', $_SESSION['formID']);
				if($permission){
					$update[$field] = $value;
					$this->db->where('ad_id', $entry_id);
					$this->db->update($table, $update);
					//echo $this->db->last_query();
					echo json_encode(array('status'=>1, 'msg'=>'Updated Successfully'));
				}else{
					echo json_encode(array('status'=>0, 'msg'=>'Invalid Permission'));
				}
			}
		}
	}

}