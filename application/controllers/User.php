<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends My_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Mailmodel','',TRUE);
		$this->load->helper('string');
	}

	function sidebar_toggle(){
		$min = $this->input->post('min');
		$update['sidebar_minimized'] = $this->input->post("min");
		$this->db->where('us_id', $this->userid);
		$this->db->update('user', $update);
		echo json_encode(array('status' => 1));
	}

	function results(){
		$limit = $this->input->post('limit');
		$start = $this->input->post('start');

		if(is_null($limit)){
			$limit = 10;
		}
		if(is_null($start)){
			$start = 0;
		}
		//$users = $this->Usermodel->get();
		$this->db->select('us_id, us_firstName, us_surname, us_company, us_joined, us_lastlogin, us_verified, us_blocked, roles.name as role_name');
    	//$this->db->order_by('us_surname', 'ASC');
    	$this->db->join('roles', 'user.us_role=roles.id');
    	
    	//echo $this->input->get('sort');
		if($this->input->get('sort')){
			
			$sortDate = json_decode($this->input->get('sort'));
			$this->db->order_by($sortDate[0]->property, $sortDate[0]->direction);

		}else{
			$this->db->order_by('us_surname', 'ASC');
		}
		
		$users = $this->db->get('user')->result_array();
		//echo $this->db->last_query();
		
		echo json_encode(
			array(
				'store'=>$users,
				'totalProperty'=>count($users)
			)
		);
	}

	function add($id = NULL){
		$data = $this->data;
		if(!is_null($id)){
			$data['user'] = (object)$this->Usermodel->selectUser($id);
			//print_r($data['user']);
			//echo '###';
		}
		
		$data['tables']	= FormsTablesmodel::where('ft_hidden', '!=', 1)->get();
		foreach($data['tables'] as $t){
			$t['permissions'] = Permissionmodel::where('form_id', '=', $t->ft_id)->get();
		}

		$data['permissions'] = Permissionmodel::where('form_id', '=', 0)->get();
		$data['roles']	=	Rolemodel::all();
		$u = UserEl::find($id);
		$data['user_permissions'] = [];
		foreach($u->userpermissions as $u){

			$data['user_permissions'][] = $u->{'permission_id'};
		}
		//print_r($data['user_permissions']);
		/*
		foreach($u->userpermissions as $p){
			echo $p->permission_id;
		}
		*/
		$permission = $this->Usermodel->has_permission('edit_users');

		$this->load->view('dashboard/header', $data);
		if($permission){
			$this->load->view('forms/user', $data);
		}else{
			$this->load->view('templates/no_permission', $data);
		}
		$this->load->view('dashboard/footer');
	}

	function show(){
		$permission = $this->Usermodel->has_permission('view_users');
		
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$this->load->view('dashboard/header', $data);
		if($permission){
			$this->load->view('lists/user', $data);
		}else{
			$this->load->view('templates/no_permission', $data);
		}
		$this->load->view('dashboard/footer');
	}

	function save(){
		//print_r($_POST);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$UserEl = new UserEl();
		//https://laracasts.com/discuss/channels/laravel/inserting-if-record-not-exist-updating-if-exist?page=1
		$id = '';
		if($this->input->post('id')!==''){
			$id = $this->input->post('id');
		}
		if (UserEl::where('us_id', '=', $id)->exists()) {
			$UserEl = UserEl::find($id);
		}

		//we're not in admin
		//if(!$this->input->post('role')){
			$pass = random_string('alnum', 8);	
			$password = hash_hmac('sha1',$pass, $this->config->item('encryption_key'));
		//}
		
		

		$UserEl->us_firstName = $this->input->post('firstname');
		$UserEl->us_surname = $this->input->post('surname');
		$UserEl->us_email = $this->input->post('email');
		$UserEl->us_blocked = $this->input->post('blocked');
		$UserEl->us_role = $this->input->post('role');
		$UserEl->us_code = random_string('alnum', 16);
		
		//if(!$this->input->post('role')){
			$UserEl->us_password = $password;
		//}
		$UserEl->save();

		UserPermissions::where('user_id', $UserEl->us_id)->delete();
		foreach($this->input->post('permissions[]') as $p){
			$up = new UserPermissions();
			$up->user_id = $UserEl->us_id; 
			$up->permission_id = $p;
			$up->save(); 
		}

		//if(!$this->input->post('role')){
		//	echo '##';
			$this->Mailmodel->sendEmail('signupwithpass', $UserEl->us_id, NULL, array('password_uncrypt' => $pass));
		//}
		
		echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
	}

	function delete($id = NULL){
		$data = $this->data;

		$this->load->library('form_validation');
		$permission = $this->Usermodel->has_permission('delete_users');

		$id = $this->input->post('id');
		//print_r($_POST);

		//$this->form_validation->set_rules('id', 'ID', 'required');

        //if ($this->form_validation->run() == FALSE){
        	if($permission){

	        	$data['user'] = (object)$this->Usermodel->selectUser($id);

	        	$this->db->where('ad_userid', $id);
	        	$this->db->delete('vehicles');
	        	//echo $this->db->last_query();

	        	$this->db->where('us_id', $id);
	        	$this->db->delete('user');
	        	//echo $this->db->last_query();

	        	echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('user_deleted')));

        	}else{

        		echo json_encode(array('status' => 0, 'msg'=>$this->lang->line('no_permission')));

        	}

        //}else{

        	//echo json_encode(array('status' => 0, 'msg'=>validation_errors()));

        //}
	}

	function stay_on_page_preferences(){

		// Load the form validation library
	    $this->load->library('form_validation');

	    // Set validation rules
	    $this->form_validation->set_rules('formID', 'Form ID', 'required|integer');
	    $this->form_validation->set_rules('checked', 'Checked', 'required');

	    if ($this->form_validation->run() == false) {
	      	// Form validation failed

	      	// You can retrieve the validation errors using validation_errors() function
	      	$errors = validation_errors();
	      	echo json_encode(array('status'=>0, 'msg'=> $errors));

	      	// Handle the validation errors (e.g., show an error message or redirect back to the form page)
	      	// ...
	    } else {
	      	// Form validation passed

	      	// Retrieve the form data
	      	$formID = $this->input->post('formID');
	      	$checked = $this->input->post('checked');

	      	// Use the Eloquent model to create or delete the entry based on 'checked' value
	      	if ($checked == 'true') {
	        	// Create a new entry
	        	$stayOnPageModel = new Stayonpagemodel();
	        	$stayOnPageModel->form_id = $formID;
	       		$stayOnPageModel->user_id = $this->userid;
	        	$stayOnPageModel->save();
	        	echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('preferences_updated')));
	      	} else {
	        	// Delete the existing entry
	        	$stayOnPageModel = Stayonpagemodel::where('user_id', $this->userid)->where('form_id', $formID)->first();
	        	if ($stayOnPageModel) {
	          		$stayOnPageModel->delete();
	        	}
	        	echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('preferences_updated')));
	      	}
	    }
	}
}