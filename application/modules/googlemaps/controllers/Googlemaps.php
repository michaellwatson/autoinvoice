<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Googlemaps extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();

		//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
		session_start();
		$this->form_name = 'Googlemaps';
		$this->field_name = 'googlemaps';
		$this->table = $this->field_name;
		$this->categories = $this->field_name.'_categories';
		//uncomment this when installing
		//$this->api_key = 'AIzaSyBJf7BXwqMNcLkp6_OoZtgs_VA3b_aaRMg';

		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->lang->load('msg');

		$this->user_role = $this->session->userdata("us_role");
		$this->user_id 	 = $this->session->userdata("us_id");

		//comment this out when installing
		$api = $this->db->select('ad_APIKey')->where('ad_id', 1)->get($this->table)->row();
		$this->api_key = $api->ad_APIKey;
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
		//return var_dump($d);
		$linked_table = FormsTablesmodel::find($d['adv_linkedtableid']);
		
		$linked_fields = Advertfieldsmodel::where('adv_table', $linked_table->ft_database_table)->get();
		//print_r($linkedfields);
		$data['d'] = $d;
		$data['linked_table'] 	= $linked_table;
		$data['linked_fields'] 	= $linked_fields;
		$this->load->view('config', $data);
	}

	function save_config(){
		//todo add validation
		$field_id 					= $this->input->post('field');
		$Advertfield 				= Advertfieldsmodel::find($field_id);
		$config 					= $this->input->post('config');
		$adv_config 				= (array)json_decode($Advertfield->adv_config);
		$adv_config[$config] 		= $this->input->post('data');
		$Advertfield->adv_config 	= json_encode($adv_config);
		$Advertfield->update();
		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
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

		//echo 'CREATE TABLE '.$this->db->dbprefix.''.$table.' LIKE '.$this->db->dbprefix.'';
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

		$this->db->simple_query("INSERT INTO `".$this->db->dbprefix.$categories."` (`ad_id`, `ad_name`, `ad_parent`, `ad_order`, `ad_hide`, `ad_role`) VALUES
		(1, 'Google Map Settings', 0, 0, 0, 0),
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

			$this->db->simple_query("INSERT INTO `".$this->db->dbprefix.$table."` (`ad_id`, `ad_userid`, `ad_added`, `ad_last_update`, `ad_position`, `ad_contact`, `ad_rand`, `ad_vat`, `ad_quote_id`, `ad_flag`, `ad_APIKey`) VALUES (1, 2, '".date('Y-m-d H:i:s', strtotime("now"))."', '".date('Y-m-d H:i:s', strtotime("now"))."', 4, 0, '54A019', 0, NULL, 0, `".$this->api_key."`)");

	}
	
	//if you're creating a new field type, handle its appearance with this function
	function field($data){

		$data['api_key'] = $this->api_key;

		if(isset($data['listing']['ad_id'])){
			
			$data['markers'] = $this->db->get_where('propertyview_latlng_list',  [
				'entry_id' => $data['listing']['ad_id'],
				'table_id' => $_SESSION['formID'],
			])->result();

			//echo $this->db->last_query();

		}

		$data['fields'] = $this->db->where('adv_table', $_SESSION['ft_database_table'])->group_start()->where('adv_field_type', 8)->or_where('adv_field_type', 11)->group_end()->get('pt_advert_fields')->result();

		if((int)$_SESSION['two-col']){
			$this->load->view('field_2col', $data);
		}else{
			$this->load->view('field', $data);
		}
		
	}

	//if you're creating a new field type, handle its appearance with this function
	function field_pdf($data){

	}

	function search_field($field){

	}

	function saveGoogleImage(){

		$entry_id = $this->input->post('entry_id');
		if(!is_numeric($entry_id)){
			echo json_encode(array('status'=> 0, 'msg' => 'Invalid entry'));
			exit();
		}
		//echo $this->input->post('url2');
		$url = str_replace('%20','',$this->input->post('url2'));
		if (strpos($url, 'maps.googleapis.com') == FALSE){
			echo json_encode(array('status'=> 0, 'msg' => 'Invalid URL'));
			return;
		}
		//echo $url;
		$url = str_replace('amp;','',$url);
		$url = str_replace(' ','',$url);
		$url = trim(preg_replace('/\r\n|\r|\n/', ' ', $url));
		$url = htmlspecialchars_decode($url);	
		$url = urldecode($url);
		$url = str_replace('amp;','',$url);
		//$urlThumb = str_replace('400x400', '150x100', $url);
		$name = $entry_id."-".rand().".png";
		
		if($this->input->post('useField') == 'true'){

			//filetype check
			$insert['up_filename'] 	= $name;
			$insert['up_entry_id'] 	= $entry_id;
			$insert['up_field_id'] 	= $this->input->post('saveToField');
			$insert['up_date']		= date('Y-m-d H:i:s', strtotime('now'));
			$insert['up_userid'] 	= $this->session->userdata("us_id");
			$insert['up_table']		= $_SESSION['ft_database_table'];
			$this->db->insert('images', $insert);

			$path = "assets/uploaded_images/".$name;

			$this->grab_image($url, $path);

			echo json_encode(array('status'=> 1, 'msg' => "File saved to Field"));

		}else{
			
			$path = $_SERVER['DOCUMENT_ROOT'].'/assets/uploads/files/';

			if(!is_dir($path.$entry_id)){
				mkdir($path.$entry_id);
			}
			$path = $path.$entry_id;
			if(!is_dir($path.'/Googlemaps')){
				mkdir($path.'/Googlemaps');
			}
			$path = $path.'/Googlemaps';
			$this->grab_image($url, $path.'/'.$name);

			echo json_encode(array('status'=> 1, 'msg' => "File saved to Filemanager"));

		}
	}

	public function grab_image($url,$saveto){
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		if(file_exists($saveto)){
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
	}

	public function saveLatLng(){

		$lat = $this->input->post('lat'); // retrieve the latitude value from the AJAX request
	  	$lng = $this->input->post('lng'); // retrieve the longitude value from the AJAX request
	  	$entry_id = $this->input->post('entry_id');

	  	$this->load->library('form_validation');

		// set validation rules for the latitude and longitude values
		$this->form_validation->set_rules('lat', 'Latitude', 'required|numeric');
		$this->form_validation->set_rules('lng', 'Longitude', 'required|numeric');
		$this->form_validation->set_rules('entry_id', 'Entry ID', 'required|numeric');

	  	// run validation on the latitude and longitude values
	  	if ($this->form_validation->run() == FALSE) {

	  		echo json_encode(array('status'=> 0, 'msg'=> $this->lang->line('validation_failed')));
	  		exit();
	  	}

	  	// insert the latitude and longitude values into the database table
	  	$this->db->insert('propertyview_latlng_list', array(
	    	'lat' 		=> $lat,
	    	'lng' 		=> $lng,
	    	'entry_id' 	=> $entry_id,
	    	'table_id'	=> $_SESSION['formID']
	  	));
	  	//echo $this->db->last_query();
	  	$marker_id = $this->db->insert_id();

	  	echo json_encode(array('status'=> 1, 'msg'=> $this->lang->line('saved_successfully'), 'marker_id' => $marker_id));
	  	exit();

	}

	public function lockMap(){

		$entry_id 		= $this->input->post('entry_id');
		$map_heading	= $this->input->post('map_heading');
		$map_pitch		= $this->input->post('map_pitch');
		$map_zoom		= $this->input->post('map_zoom');
		$marker_id		= $this->input->post('marker_id');

	  	$this->load->library('form_validation');

		// set validation rules for the latitude and longitude values
		$this->form_validation->set_rules('map_heading', 	'map_heading', 	'required|numeric');
		$this->form_validation->set_rules('map_pitch', 		'map_pitch', 	'required|numeric');
		$this->form_validation->set_rules('map_zoom', 		'map_zoom', 	'required|numeric');
		$this->form_validation->set_rules('entry_id', 		'Entry ID', 	'required|numeric');
		$this->form_validation->set_rules('marker_id', 		'Marker ID', 	'required|numeric');

	  	// run validation on the latitude and longitude values
	  	if ($this->form_validation->run() == FALSE) {

	  		echo json_encode(array('status'=> 0, 'msg'=> $this->lang->line('validation_failed')));
	  		exit();
	  	}

	  	$this->db->where('id', $marker_id);
	  	// insert the latitude and longitude values into the database table
	  	$this->db->update('propertyview_latlng_list', array(
	    	'map_heading' 		=> $map_heading,
	    	'map_pitch' 		=> $map_pitch,
	    	'map_zoom' 			=> $map_zoom,
	    	'locked'			=> 1
	  	));

	  	echo json_encode(array('status'=> 1, 'msg'=> $this->lang->line('updated_successfully')));
	  	exit();

	}

	public function getAdditonalMapInfo(){
		$marker_id		= $this->input->post('marker_id');
		$this->load->library('form_validation');

		// set validation rules for the latitude and longitude values
		$this->form_validation->set_rules('marker_id', 	'marker_id', 	'required|numeric');
		$result = $this->db->where('id', $marker_id)->get('propertyview_latlng_list')->row();

		echo json_encode(array('status'=> 1, 'msg'=> $result));
	  	exit();

	}

	
	public function deleteLatLng(){

	  	$sid = $this->input->post('sid');
	  	$entry_id = $this->input->post('entry_id');

	  	$this->load->library('form_validation');

		// set validation rules for the latitude and longitude values
		$this->form_validation->set_rules('entry_id', 'Entry ID', 'required|numeric');

	  	// run validation on the latitude and longitude values
	  	if ($this->form_validation->run() == FALSE) {

	  		echo json_encode(array('status'=> 0, 'msg'=> $this->lang->line('validation_failed')));
	  		exit();
	  	}

	  	// insert the latitude and longitude values into the database table
	  	$this->db->where('id', $sid);
	  	$this->db->where('entry_id', $entry_id);
	  	$this->db->delete('propertyview_latlng_list');

	  	//echo $this->db->last_query();

	  	echo json_encode(array('status'=> 1, 'msg'=> $this->lang->line('entry_deleted')));
	  	exit();
	}
}