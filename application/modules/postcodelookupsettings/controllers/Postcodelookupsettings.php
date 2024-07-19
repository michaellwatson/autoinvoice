<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Postcodelookupsettings extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();

		$this->form_name = 'Postcode Lookup Settings';
		$this->table = str_replace(' ', '_', strtolower($this->form_name));
		$this->categories = $this->table.'_categories';
		$this->load->library('form_validation');


	}

	function test(){
		echo $this->form_name.' Installed';
	}
	
	function install(){
		//check file/db exists

		$form_name 	= $this->form_name;
		$table 		= $this->table;
		$categories = $this->categories;

		//create custom field types
		$insert = [];
		$insert['fi_type'] = strtolower(str_replace(' ', '', $form_name));
		$insert['fi_name'] = $form_name;
		$insert['fi_hasDatasource'] = 0;
		$this->db->insert('field_type', $insert);

		//create settings table
		$this->db->simple_query('CREATE TABLE '.$this->db->dbprefix.''.$table.' LIKE '.$this->db->dbprefix.'');
		$this->db->simple_query('CREATE TABLE '.$this->db->dbprefix.''.$categories.' LIKE '.$this->db->dbprefix.'c');

		//save the settings table as uneditable
		$insert = [];
		$insert['ft_name'] 				= $form_name;
		$insert['ft_database_table'] 	= $this->db->dbprefix.$table;
		$insert['ft_categories'] 		= $this->db->dbprefix.$categories;
		$insert['ft_hidden'] 			= 1;
		$insert['ft_settings'] 			= 1;
		$this->db->insert('pt_forms_tables', $insert);
		$form_tables_id = $this->db->insert_id();

		$permissions = [];
		//create default permissions
		$input = array(
			'name'=>'Create '.$this->input->post('form_name'),
			'permission'=>'create',
			'form_id'=>$form_tables_id,
		);
		$this->db->insert('permissions',$input);
		$permissions[] = $this->db->insert_id();

		$input = array(
			'name'=>'Read '.$this->input->post('form_name'),
			'permission'=>'read',
			'form_id'=>$form_tables_id,
		);
		$this->db->insert('permissions',$input);
		$permissions[] = $this->db->insert_id();

		$input = array(
			'name'=>'Update '.$this->input->post('form_name'),
			'permission'=>'update',
			'form_id'=>$form_tables_id,
		);
		$this->db->insert('permissions',$input);
		$permissions[] = $this->db->insert_id();

		$input = array(
			'name'=>'Delete '.$this->input->post('form_name'),
			'permission'=>'delete',
			'form_id'=>$form_tables_id,
		);
		$this->db->insert('permissions',$input);
		$permissions[] = $this->db->insert_id();

		//assign default permissions to admin
		$admin = $this->db->where('us_role', 1)->get('pt_user')->row();
		foreach($permissions as $k => $v){
			$UserPermissions = new UserPermissions();
			$UserPermissions->user_id = $admin->us_id;
			$UserPermissions->permission_id = $v;
			$UserPermissions->save();
		}
		//setup categories
		$this->db->simple_query("INSERT INTO `pt_postcode_lookup_settings_categories` (`ad_id`, `ad_name`, `ad_parent`, `ad_order`, `ad_hide`, `ad_role`) VALUES
		(1, 'Postcode Lookup Settings', 0, 0, 0, 0),
		(2, 'API Settings', 1, 0, 0, 0);");

		//setup fields
		$this->db->simple_query("
		INSERT INTO `pt_advert_fields` (
			`adv_field_type`, 
			`adv_text`, 
			`adv_post_text`, 
			`adv_info`, 
			`adv_category`, 
			`adv_order`, 
			`adv_required`, 
			`adv_datasourceId`, 
			`adv_column`, 
			`adv_associated_fieldId`, 
			`adv_link`, 
			`adv_table`, 
			`adv_search`, 
			`adv_hidden`, 
			`adv_show_field`, 
			`adv_linkedtableid`
		) VALUES
		(	5, 
			'API Key', 
			'', 
			'', 
			2, 
			0, 
			1, 
			0, 
			'APIKey', 
			0, 
			'', 
			'".$this->db->dbprefix.$table."', 
			0, 
			0, 
			0, 
			0);");
		

			$this->db->simple_query('ALTER TABLE '.$this->db->dbprefix.$table.' ADD ad_APIKey VARCHAR(255)');

			$this->db->simple_query("INSERT INTO `pt_postcode_lookup_settings` (`ad_id`, `ad_userid`, `ad_added`, `ad_last_update`, `ad_position`, `ad_contact`, `ad_rand`, `ad_vat`, `ad_quote_id`, `ad_flag`, `ad_APIKey`) VALUES (1, 2, '2020-02-05 21:23:58', '2020-02-05 21:23:58', 4, 0, '54A019', 0, NULL, 0, 'PT11-BY93-CX13-ZU44')");

	}

	function linked_fields(){

		$fields = $field = [];
		$t = time();
		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Line 1';
		$field['adv_field_column']	= 'line_1_'.time();
		$fields[] = $field;

		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Line 2';
		$field['adv_field_column']	= 'line_2_'.time();
		$fields[] = $field;

		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Line 3';
		$field['adv_field_column']	= 'line_3_'.time();
		$fields[] = $field;

		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Postcode';
		$field['adv_field_column']	= 'postcode_'.time();
		$fields[] = $field;

		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Lat';
		$field['adv_field_column']	= 'lat_'.time();
		$fields[] = $field;

		$field['adv_field_type'] 	= 5;
		$field['adv_text']			= 'Lng';
		$field['adv_field_column']	= 'lng_'.time();
		$fields[] = $field;

		return $fields;
	}

	function field($data){

		$linked_fields = json_decode($data['field']['adv_linked_fields']);
		foreach($linked_fields as $l){
			$field = $this->Advertcategorymodel->getField($l);
			$data['linked_fields'][] = $field;
		}
		if(isset($_SESSION['two-col']) && (int)$_SESSION['two-col']){
			$this->load->view('field_2col', $data);
		}else{
			$this->load->view('field', $data);
		}

	}
	
	//additional functions as needed

	//additional functions as needed
	public function lookupPostcode(){
		
		$api = $this->db->select('ad_APIKey')->where('ad_id', 1)->get('postcode_lookup_settings')->row();
		//echo $this->db->last_query();
		$this->api_key = $api->ad_APIKey;
		//echo $this->api_key.'#';

		$this->form_validation->set_rules('postcode', 'Postcode', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0, 'msg' => validation_errors()));
		}else{
			//Example usage
			//-------------
			$postcode = $this->input->post('postcode');

			$array = array();

		    $loqate = new \Baikho\Loqate\Loqate($this->api_key);
			$result = $loqate->address()->find($postcode);


			foreach($result->Items as $i){

				$result = (new \Baikho\Loqate\Address\Find($this->api_key))
					->setIsMiddleWare(TRUE)
					->setContainer($i->Id)
					->makeRequest();
				if(!empty($result->Items)){
					//echo '##';
					foreach($result->Items as $i){
						//print_r($i);
						$array[] = array('id' => $i->Id,'text' => $i->Text);

					}
				}
			}
		
			echo '<option value="">Please Select</oprion>';
		    foreach($array as $a){
				echo '<option value="'.$a['id'].'">'.$a["text"].'</oprion>';
			}
		}
	}
	

	public function lookupID(){
		
		$api = $this->db->select('ad_APIKey')->where('ad_id', 1)->get('postcode_lookup_settings')->row();
		$this->api_key = $api->ad_APIKey;

		$this->form_validation->set_rules('id', 'id', 'required');
		if ($this->form_validation->run() == FALSE){
			echo validation_errors();
		}else{

			$id =  $this->input->post('id');
			header('Content-Type: application/json');
			// Simple example.
			//echo $id;
			//$loqate = new \Baikho\Loqate\Loqate($this->api_key);
			//$result = $loqate->address()->retrieve($id);
			//print_r($result);
			$result = (new \Baikho\Loqate\Address\Retrieve($this->api_key))->setId($id)->makeRequest();
			//print_r($result);

			if(isset($result->Items)){
				foreach ($result->Items as $item)
			   {

			   		if (!empty((string)$item->BuildingNumber)) {
					  $address .= trim((string)$item->BuildingNumber) . ' ';
					}

					if (!empty((string)$item->Line2)) {
					  $address .= (string)$item->Line2 . ', ';
					}

					if (!empty((string)$item->City)) {
					  $address .= (string)$item->City . ', ';
					}

					if (!empty((string)$item->PostalCode)) {
					  $address .= (string)$item->PostalCode;
					}

					$loqate = new \Baikho\Loqate\Loqate($this->api_key);
					$geo = $loqate->geocoding()->ukGeocode($address);
					if(isset($geo->Items[0])){
						$lat = $geo->Items[0]->Latitude;
						$lng = $geo->Items[0]->Longitude;
					}
			   	 echo json_encode(array('line1' =>  (string)$item->BuildingNumber, 'line2' =>  (string)$item->Line2, 'line3' =>  (string)$item->City, 'postcode' => (string)$item->PostalCode, 'lat' => $lat, 'lng' => $lng));
			   }
			}
		}
	}

	function alter_table($table){

		/*
		echo $item["Id"] . " 1<br/>";
	    echo $item["DomesticId"] . " 2<br/>";
	    echo $item["Language"] . " 3<br/>";
	    echo $item["LanguageAlternatives"] . " 4<br/>";
      	echo $item["Department"] . " 5<br/>";
      	echo $item["Company"] . " 6<br/>";
     	echo $item["SubBuilding"] . " 7<br/>";
      	echo $item["BuildingNumber"] . " 8<br/>";
      	echo $item["BuildingName"] . " 9<br/>";
      	echo $item["SecondaryStreet"] . " 10<br/>";
      	echo $item["Street"] . " 11<br/>";
      	echo $item["Block"] . " 12<br/>";
      	echo $item["Neighbourhood"] . " 13<br/>";
      	echo $item["District"] . " 14<br/>";
      	echo $item["City"] . " 15<br/>";
      	echo $item["Line1"] . " 16<br/>";
      	echo $item["Line2"] . " 17<br/>";
      	echo $item["Line3"] . " 18<br/>";
      	echo $item["Line4"] . " 19<br/>";
      	echo $item["Line5"] . " 20<br/>";
      	echo $item["AdminAreaName"] . " 21<br/>";
      	echo $item["AdminAreaCode"] . " 22<br/>";
     	echo $item["Province"] . " 23<br/>";
      	echo $item["ProvinceName"] . " 24<br/>";
      	//echo $item["ProvinceCode"] . " 25<br/>";
      
      	//echo $item2["PostalCode"] . " 26<br/>";
      
      	echo $item["CountryName"] . " 27<br/>";
      	echo $item["CountryIso2"] . " 28<br/>";
      	echo $item["CountryIso3"] . " 29<br/>";
      	echo $item["CountryIsoNumber"] . " 30<br/>";
      	echo $item["SortingNumber1"] . " 31<br/>";
      	echo $item["SortingNumber2"] . " 32<br/>";
      	echo $item["Barcode"] . " 33<br/>";*/
      	//echo $item["POBoxNumber"] . " 34<br/>";
      
      	//echo $item2["Label"] . " 35<br/>";
      
      	//echo $item["Type"] . " 36<br/>";
      	//echo $item["DataLevel"] . " 37<br/>";

		//$columns = [ 'lookup_address_1', 'lookup_address_1', 'lookup_postcode']
		//$this->db->simple_query('ALTER TABLE '.$table.' ADD ad_'.$column.' LONGTEXT');
	}

}