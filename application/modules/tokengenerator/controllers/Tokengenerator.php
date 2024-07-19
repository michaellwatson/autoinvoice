<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tokengenerator extends MX_Controller
{
	//private $CI;
	public $form_name;
	public $table;
	public $categories;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->form_name = 'TokenGenerator';
		$this->field_name = 'tokengenerator';
		$this->load->library('form_validation');
		$this->load->model('Advertcategorymodel');
		$this->load->model('Usermodel');

	}

	function test(){
		echo $this->form_name.' Installed';
	}

	function get($data){
		return $this->$data;
	}

	function list($data){
		$this->load->view('list', $data);
	}

	function field_id($c){
		return "{name:'".$c[0]."', type:'tokengenerator'}";
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

	//if you're creating a new field type, handle its appearance with this function
	function field($data){

		//print_r($data);
		if(isset($data['listing']) && isset($data['listing']['ad_id'])){
			$data['tokenRecord'] = Tokensmodel::where('client_id', $data['listing']['ad_id'])->where('expiry_date', '>', time())->first();
		}
		$this->load->view('field', $data);

	}

	public function generate_token()
    {
    	// Get the current timestamp
		$currentTimestamp = time();

		// Add 1 hour (3600 seconds) to the current timestamp
		$oneHourFromNowTimestamp = $currentTimestamp + (3600*25);

		// Format the new timestamp in MySQL-compatible format (Y-m-d H:i:s) expiry time
		$mysqlFormattedDateTime = date("Y-m-d H:i:s", $oneHourFromNowTimestamp);

        // Your token generation logic goes here
        $token = bin2hex(random_bytes(16)); // Generate a random 32-character token
        //echo $token; // You can do whatever you want with the generated token (e.g., store it in the database)
        $Tokensmodel = new Tokensmodel();

        $Tokensmodel->token			= $token;
        $Tokensmodel->client_id		= $this->input->post('entry_id');
        $Tokensmodel->user_id		= $this->session->userdata("us_id");
        $Tokensmodel->expiry_date 	= $mysqlFormattedDateTime;
        $Tokensmodel->save();

		$Eventsmodel 			= new Eventsmodel();
		$Eventsmodel->note 		= 'Token generated, expires '.$mysqlFormattedDateTime;
		$Eventsmodel->table_id 	= 'pt_clients';
		$Eventsmodel->user_id 	= $this->session->userdata("us_id");
		$Eventsmodel->entry_id 	= $this->input->post('entry_id');
		
		$Eventsmodel->save();
		echo json_encode(array('status' => 1, 'token' => $token));
    }
	
}