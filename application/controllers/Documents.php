<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends My_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function templates(){

		$this->data['categoryName'] = '';
		$this->data['noNav'] = true;
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$this->data['tablesList'] = $this->Advertcategorymodel->formsTables();

		
		$this->load->view('dashboard/header', $this->data);
		$this->load->view('templates/advertTables', $this->data);
		$this->load->view('dashboard/footer');

	}

	public function update_role() {
	    // Validate form inputs
	    $this->form_validation->set_rules('val', 'Val', 'required|numeric');
	    $this->form_validation->set_rules('cat_id', 'Cat ID', 'required|numeric');

	    if ($this->form_validation->run() == FALSE) {
	    	echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
	    } else {
	    	$val = $this->input->post('val');
	    	$cat_id = $this->input->post('cat_id');

	        // Update pt_blocks_categories table
	       	$update['ad_role'] = $val;
	        $this->db->where('ad_id', $cat_id);
	        $this->db->update($this->default_category_table, $update);
	        //echo $this->db->last_query();

	        echo json_encode(array('status'=>0,'msg'=> $this->lang->line('updated_successfully')));
			exit();
	    }
	}

	public function check_module(){

		$field = explode(':',$this->input->post('fieldType'));
		$field_type = FieldTypemodel::findOrFail($field[0]);
		echo $field_type['fi_type'].'/'.$field_type['fi_type'].'/linked_fields';
		$linked_fields = Modules::run($field_type['fi_type'].'/'.$field_type['fi_type'].'/linked_fields');
		$map_array = [];
		$new_linkeds = [];
		foreach($linked_fields as $l){
			$maps = $this->db->where('adv_table', $this->default_table)->where('adv_field_type', $l['adv_field_type'])->get('advert_fields')->result_array();
			foreach($maps as $m){
				$map_array[]	= ['id'=>  $m['adv_id'], 'text' => $m['adv_text']];
			}
			$l['maps'] = $map_array;
			$new_linkeds[] = $l;
		}
		echo json_encode(array('status'=>1, 'linked_fields' => $new_linkeds));
	}

	public function saveTable(){

		$this->form_validation->set_rules('form_name', 'Name', 'required');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{

			//we want to add each field to the database
			//so we first need to check if that field is existing
			$table = $this->input->post('form_name');
			$table = preg_replace("/[^A-Za-z0-9]/", "", $table);
			$table = preg_replace('/\s+/', '', $table);
			$table = strtolower($table);

			$categories = $table.'_categories';
			
			if($table==$categories){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('category_and_table_same_name')));
				exit();
			}
			$this->db->select('*');
			$this->db->where('ft_database_table', $table);
			$a = $this->db->get('forms_tables')->result_array();
			if(sizeof($a)>0){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('table_exists')));
				exit();
			}

			$this->db->select('*');
			$this->db->where('ft_categories', $categories);
			$b = $this->db->get('forms_tables')->result_array();

			if(sizeof($b)>0){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('category_table_exists')));
				exit();
			}	

			$this->db->simple_query('CREATE TABLE '.$this->db->dbprefix.''.$table.' LIKE '.$this->db->dbprefix.'');
			//echo 'CREATE TABLE '.$this->db->dbprefix.''.$table.' LIKE '.$this->db->dbprefix.'';

			$this->db->simple_query('CREATE TABLE '.$this->db->dbprefix.''.$categories.' LIKE '.$this->db->dbprefix.'c');
			//echo 'CREATE TABLE '.$this->db->dbprefix.''.$categories.' LIKE '.$this->db->dbprefix.'c';

			$input = array(
				'ft_name'=>$this->input->post('form_name'),
				'ft_database_table'=>$this->db->dbprefix.$table,
				'ft_categories'=>$this->db->dbprefix.$categories,
			);
			$this->db->insert('forms_tables',$input);

			$form_tables_id = $this->db->insert_id();

			//create default permissions
			$input = array(
				'name'=>'Create '.$this->input->post('form_name'),
				'permission'=>'create',
				'form_id'=>$form_tables_id,
			);
			$this->db->insert('permissions',$input);

			$input = array(
				'name'=>'Read '.$this->input->post('form_name'),
				'permission'=>'read',
				'form_id'=>$form_tables_id,
			);
			$this->db->insert('permissions',$input);

			$input = array(
				'name'=>'Update '.$this->input->post('form_name'),
				'permission'=>'update',
				'form_id'=>$form_tables_id,
			);
			$this->db->insert('permissions',$input);

			$input = array(
				'name'=>'Delete '.$this->input->post('form_name'),
				'permission'=>'delete',
				'form_id'=>$form_tables_id,
			);
			$this->db->insert('permissions',$input);
			//end permission

			$insert['ad_name'] = $this->input->post('form_name');
			$insert['ad_parent'] = 0;
			$this->db->insert($categories, $insert);

			echo json_encode(array('status'=>1,'msg'=>$this->lang->line('fieldAdded')));
			exit();
		}
	}

	public function advertAdmin(){
		
		$this->data['formID'] = $this->formID;
		//echo $this->default_table;
		//exit();
		//$this->default_table = ''
		//I've added another bit where we check the table
		//don't include admin in roles
		$this->data['roles'] = Rolemodel::where('id', '!=', 1)->get();

		if($this->default_table==''){

			$this->data['categoryId'] = 0;
			//$this->data['fa'] = 'fa-list-alt';
			$this->data['image'] = base_url()."/assets/images/contacts.png";

			$this->load->view('dashboard/header', $this->data);

			$this->data['categoryName'] = 'Forms';
			
			$this->data['tablesList'] = $this->Advertcategorymodel->formsTables();

			$this->load->view('templates/advertTables', $this->data);
			//$recordid =  $this->session->flashdata('recordid');

			$this->load->view('dashboard/footer');

		}else{
			$this->load->model('Advertcategorymodel');
			$category = 0;
			if($this->uri->segment(3)!==NULL){
				$category = $this->uri->segment(3);
			}
			//if($category<$this->config->item('advert_admin_category_depth')){
				$this->data['categories'] = $this->Advertcategorymodel->getCategories($category);
			//}
			$this->data['fieldsList'] = $this->Advertcategorymodel->getFieldTypes();
			//var_dump($this->data['fieldsList']);
				
			$this->data['categoryName'] = 'Default';
			if($category != 0){
				$this->data['category'] = $this->Advertcategorymodel->getCategory($category);
				$this->data['categoryName'] = $this->data['category']['ad_name'];
			}
			$this->data['categoryId'] = $category;
			$this->data['fields'] = $this->Advertcategorymodel->getFields($category);

			$this->data['section'] = 'Form Categories';
			$this->data['subtitle'] = 'Create and manage the categories and fields in forms';
			$this->data['breadcrumbs'] = 'advertcategories';
			//at which point they stop being categories and become fields
			//need to change this with an "is category"
			//replace for some clever recursive stuff
			//var_dump($this->data['category']);
			if(isset($this->data['category']['ad_parent'])){
				if($this->data['category']['ad_parent']!=0){
					$this->data['depth'] = 1;
				}else{
					$this->data['depth'] = 0;
				}
			}else{
				$this->data['depth'] = 0;
			}
			//echo $this->data['depth'];
			//$this->data['depth'] = $this->config->item('advert_admin_category_depth')-1;
			//$this->data['fa'] = 'fa-list-alt';
			$this->data['image'] = base_url()."/assets/images/contacts.png";

			$this->data['default_table_name'] = $this->default_table_name;

			$this->load->view('dashboard/header', $this->data);
			$this->load->view('templates/advertCategories', $this->data);
			$this->load->view('dashboard/footer');
		}
	}

	public function saveCategory(){
		$this->form_validation->set_rules('categoryId', 'categoryId', 'required');
		$this->form_validation->set_rules('value', 'value', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$array = array('ad_name'=>$this->input->post('value'),'ad_parent'=>$this->input->post('categoryId'));
			$this->db->insert($this->default_category_table, $array);
			//echo $this->db->last_query();
			//nothing to say really
			echo json_encode(array('status'=>1));
			exit();
		}
	}

	public function updateOrder(){
		//echo '##';
		$sectionids = $this->input->post('order');
		$order = explode(',',$sectionids);
        $count = 1;
        //var_dump($order);
        if (is_array($order)) {
            foreach ($order as $sectionid) {

               	$update = array('adv_order'=>$count);
                // your DB query here
                $this->db->where('adv_id', $sectionid);
                $this->db->update('advert_fields', $update);
               
               	//echo $this->db->last_query();

                $count++;
            }
           
            echo '{"status": 1}';
        } else {
            echo '{"status": 0}';
        }
	}

	public function updateCatOrder(){
		//echo '##';
		$sectionids = $this->input->post('order');
		$order = explode(',',$sectionids);
        $count = 1;
        //var_dump($order);
        if (is_array($order)) {
            foreach ($order as $sectionid) {

               	$update = array('ad_order'=>$count);
                // your DB query here
                $this->db->where('ad_id', $sectionid);
                $this->db->update($this->default_category_table, $update);
               
               	//echo $this->db->last_query();

                $count++;
            }
           
            echo '{"status": 1}';
        } else {
            echo '{"status": 0}';
        }
	}

	public function delete_field(){
		$this->form_validation->set_rules('field_id', 'Field ID', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//echo $this->input->post('field_id');

			$this->db->where('adv_id',$this->input->post('field_id'));
			$field = $this->db->get('advert_fields')->row_array();

			$this->load->dbforge();
			$table = str_replace($this->db->dbprefix, '', $this->default_table);
			$this->dbforge->drop_column($table, 'ad_'.$field['adv_column']);

			$this->db->where('adv_id',$this->input->post('field_id'));
			$this->db->delete('advert_fields');
			echo json_encode(array('status'=>1,'msg'=>validation_errors()));
			exit();
		}
	}

	public function delete_datasource(){
		$this->form_validation->set_rules('datasource_id', 'Datasource ID', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//echo $this->input->post('field_id');
			$this->db->where('ds_ds_id',$this->input->post('datasource_id'));
			$this->db->delete('ds_values');

			$this->db->where('da_id',$this->input->post('datasource_id'));
			$this->db->delete('datasources');

			echo json_encode(array('status'=>1,'msg'=>validation_errors()));
			exit();
		}
	}

	public function delete_category(){
		
		$this->form_validation->set_rules('category_id', 'Category ID', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$this->db->select('*');
			$this->db->where('ad_parent',$this->input->post('category_id'));
			$categories = $this->db->get($this->default_category_table)->result_array();
			if(sizeof($categories)>0){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('delete_child_categories')));
				exit();
			}

			//get the fields
			$this->db->select('*');
			$this->db->where('adv_category',$this->input->post('category_id'));
			$fields = $this->db->get('advert_fields')->result_array();
			//echo $this->db->last_query();
			$this->load->dbforge();
			$table = str_replace($this->db->dbprefix, '', $this->default_table);
			foreach($fields as $f){
				$this->dbforge->drop_column($table, 'ad_'.$f['adv_column']);
				//echo $this->db->last_query();
			}

			$this->db->where('adv_category',$this->input->post('category_id'));
			$this->db->delete('advert_fields');

			$this->db->where('ad_id',$this->input->post('category_id'));
			$this->db->delete($this->default_category_table);
		}
	}

	public function saveField(){

		$linked_fields = [];
		/*
		adv_datasourceId = datasourceId
		adv_field_type = fieldType
		adv_text = text
		adv_post_text = post_text
		adv_info = info
		adv_category = category
		adv_required = required*/
		//exit();
		//if(!$this->input->post('editField')){
		$this->form_validation->set_rules('fieldType', 'Field Type', 'required');
		//}
		$this->form_validation->set_rules('text', 'Text', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			if(!$this->input->post('editField')){
			//we want to add each field to the database
			//so we first need to check if that field is existing
			$column = preg_replace("/[^A-Za-z0-9]/", "", $this->input->post('text'));
			$column = preg_replace('/\s+/', '', $column);
			$column = strtolower($column);
			$query = $this->db->query('SHOW COLUMNS FROM `'.$this->default_table.'` LIKE \'ad_'.$column.'\'');
			if(sizeof($query->result())>0){
				//the column name already exists
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('colExists')));
				exit();
			}

				$field = explode(':',$this->input->post('fieldType'));

				$linkedtableid = $this->input->post('datasourceTableId'); 
				$input = array(
					'adv_datasourceId'=>$this->input->post('datasourceId'),
					'adv_linkedtableid'=> $linkedtableid,
					'adv_field_type'=>$field[0],
					'adv_text'=>$this->input->post('text'),
					'adv_post_text'=>$this->input->post('post_text'),
					'adv_info'=>$this->input->post('info'),
					'adv_category'=>$this->input->post('category'),
					'adv_required'=>$this->input->post('required'),
					'adv_column'=>$column,
					'adv_link'=>$this->input->post('link'),
					'adv_table'=>$this->default_table
				);
				$this->db->insert('advert_fields',$input);	
				$parent_id = $this->db->insert_id();

				switch($field[1]){
				case 0:
				
					if(($field[0]==6) || ($field[0]==23)){
						$this->db->simple_query('ALTER TABLE '.$this->default_table.' ADD ad_'.$column.' LONGTEXT');
					}else{
						$this->db->simple_query('ALTER TABLE '.$this->default_table.' ADD ad_'.$column.' VARCHAR(255)');
					}
					
				break;
				case 1:
					//check its not a checkbox
					$this->db->select('*');
					$this->db->where('fi_id',$field[0]);
					$a = $this->db->get('field_type')->row_array();
					if($a['fi_type']!='checkboxes'){
						$this->db->simple_query('ALTER TABLE '.$this->default_table.' ADD ad_'.$column.' INT(11)');	
					}else{
						$this->db->simple_query('ALTER TABLE '.$this->default_table.' ADD ad_'.$column.' VARCHAR(255)');
					}
					
				break;	

				}


				//mapped fields
				foreach($this->input->post('maps[]') as $m){
					
					
					if (strpos($m, ':') !== false) {
		 			   //create new field
						$field_type = 
						$m_f = explode(':', $m);
						$mparts = explode('_', $m_f[1]);
						array_pop($mparts);
						//print_r($mparts);

						$field_type = $m_f[0];
						$field_name = ucwords(implode(' ', $mparts));

						//echo $field_type.'#'.$field_name;

						$map_input = array(
							'adv_field_type'		=>$field_type,
							'adv_text'				=>$field_name,
							'adv_column'			=>$m_f[1],
							'adv_table'				=>$this->default_table,
							'adv_parent_field_id'	=>$parent_id,
							'adv_category'=>$this->input->post('category'),
						);

						$this->db->insert('advert_fields',$map_input);	
						$linked_fields[] = $this->db->insert_id();
						$this->db->simple_query('ALTER TABLE '.$this->default_table.' ADD ad_'.$m_f[1].' VARCHAR(255)');

					}else{

						$map_input = array(
							'adv_parent_field_id'=>$parent_id,
						);
						$this->db->where('adv_id', $m);
						$this->db->update('advert_fields',$map_input);	
						$linked_fields[] =  $m;
					}
				}
				$update = [];
				$update['adv_linked_fields'] = json_encode($linked_fields);
				$this->db->where('adv_id', $parent_id);
				$this->db->update('advert_fields',$update);	
				//echo $this->db->last_query();
				//mapped end
				
			}else{
				
				$field = explode(':',$this->input->post('fieldType'));
				$input = array(
					'adv_datasourceId'=>$this->input->post('datasourceId'),
					'adv_linkedtableid'=>$this->input->post('datasourceTableId'),
					'adv_field_type'=>$field[0],
					'adv_text'=>$this->input->post('text'),
					'adv_post_text'=>$this->input->post('post_text'),
					'adv_info'=>$this->input->post('info'),
					'adv_required'=>$this->input->post('required'),
					'adv_link'=>$this->input->post('link')
				);
				$this->db->where('adv_id',$this->input->post('editField'));
				$this->db->update('advert_fields',$input);	
				//echo $this->db->last_query();
			}
			
			echo json_encode(array('status'=>1,'msg'=>$this->lang->line('fieldAdded')));
			exit();
		}
	}

	public function getFieldsCategory($cat){
		$this->load->model('Advertcategorymodel');
		$this->data['fields'] = $this->Advertcategorymodel->getFields($cat);
		//var_dump($this->data['fields']);
		$this->load->view('templates/lists/partials/fields', $this->data);
	}

	public function getField($id){
		$this->db->select('*');
		$this->db->join('field_type','fi_id=adv_field_type');
		$this->db->where('adv_id',$id);
		$row = $this->db->get('advert_fields')->row_array();
		if($row['adv_datasourceId']!=0){
			$this->db->select('*');
			$this->db->where('da_id',$row['adv_datasourceId']);
			$ds = $this->db->get('datasources')->row_array();
			$row['dsName'] = $ds['da_name']; 
			$row['dsId'] = $ds['da_id']; 
		}
		echo json_encode(array('status'=>1,'data'=>$row));	
	}

	function save_link(){
		//print_r($_POST);
		
		$nature_of_instruction 	= $this->input->post('nature_of_instruction');

		$standard_items 		= $this->input->post('standard_items');

		//clear any old associations
		if(StandardItemmodel::where('forms_tables_id', '=', $this->input->post('table_id'))->exists()){
				$StandardItemmodel	   				= StandardItemmodel::where('forms_tables_id', '=', $this->input->post('table_id'))->first();
				$StandardItemmodel->forms_tables_id = null;
				$StandardItemmodel->save();
		}
		//clear any old associations
		if(Naturemodel::where('forms_tables_id', '=', $this->input->post('table_id'))->exists()){
			$Naturemodel	   				= Naturemodel::where('forms_tables_id', '=', $this->input->post('table_id'))->first();	
			$Naturemodel->forms_tables_id 	= null;
			$Naturemodel->save();
		}

		if($standard_items!=''){

			$StandardItemmodel = StandardItemmodel::find($standard_items);
			$StandardItemmodel->forms_tables_id = $this->input->post('table_id');
			$StandardItemmodel->save();

		}else if($nature_of_instruction!=''){

			$Naturemodel	   = Naturemodel::find($nature_of_instruction);
			$Naturemodel->forms_tables_id = $this->input->post('table_id');
			$Naturemodel->save();

		}

		echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
	}


	public function link_email($id = NULL){

		$data = $this->data;
		$data['user'] = $this->data['user'];

		$tableData = $this->Advertcategorymodel->formsTables($id);
		
		$data['doc_name'] 	= $tableData['ft_name'];
		$data['id']			= $id;
		$data['table_id']	= $id;
		$data['messages']	= Messagemodel::all();

		//print_r($data['users']);
		$this->load->view('dashboard/header', $data);
		$this->load->view('forms/link_email', $data);
		$this->load->view('dashboard/footer');

	}

	function save_email_link(){
		//print_r($_POST);
		$message 			= $this->input->post('message');
		$Messagemodel	   	= Messagemodel::find($message);
		$Messagemodel->me_forms_tables_id = $this->input->post('table_id');
		$Messagemodel->save();

		echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
	}

	public function updateSearchField(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['adv_search']	= $include;
		$this->db->where('adv_id', $id);
		$this->db->update('advert_fields', $update);
		echo json_encode(array('status'=>1));
	}

	public function updateDisplayField(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['adv_show_field']	= $include;
		$this->db->where('adv_id', $id);
		$this->db->update('advert_fields', $update);
		echo json_encode(array('status'=>1));
	}

	public function updateImport(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['ft_import']	= $include;
		$this->db->where('ft_id', $id);
		$this->db->update('forms_tables', $update);
		echo json_encode(array('status'=>1));
	}

	public function updatePdf(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['ft_pdf']	= $include;
		$this->db->where('ft_id', $id);
		$this->db->update('forms_tables', $update);
		echo json_encode(array('status'=>1));
	}

	public function updateWord(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['ft_import']	= $include;
		$this->db->where('ft_id', $id);
		$this->db->update('forms_tables', $update);
		echo json_encode(array('status'=>1));
	}

	public function updateApi(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('include');
		$update['ft_api']	= $include;
		$this->db->where('ft_id', $id);
		$this->db->update('forms_tables', $update);
		//echo $this->db->last_query();
		echo json_encode(array('status'=>1));
	}

	public function updateApiField(){
		$id 					= $this->input->post('id');
		$include 				= $this->input->post('enabled');
		$update['adv_api']	= $include;
		$this->db->where('adv_id', $id);
		$this->db->update('advert_fields', $update);
		//echo $this->db->last_query();
		echo json_encode(array('status'=>1));
	}
}