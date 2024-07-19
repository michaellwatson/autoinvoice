<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends My_Controller {

	public function __construct(){
		parent::__construct();
	}

	function index($id = NULL){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$data['tables']	= FormsTablesmodel::where('ft_hidden', '!=', 1)->get();
		foreach($data['tables'] as $t){
			$t['permissions'] = Permissionmodel::where('form_id', '=', $t->ft_id)->get();
		}

		$data['permissions'] = Permissionmodel::where('form_id', '=', 0)->get();

		$data['role']	=	Rolemodel::where('id', '=', $id)->get();
		$data['id']		= $id;
		$data['role_permissions']	=	Rolepermissions::where('role_id', $id)->get()->toArray();
		//print_r($data['role_permissions']);

		$data['role_permissions_ids'] = [];
		foreach($data['role_permissions'] as $p){

			$data['role_permissions_ids'][] = $p['permission_id'];
		}

		$this->load->view('dashboard/header', $data);

		$this->load->view('forms/roles', $data);
		$this->load->view('dashboard/footer');	
	}


	function save(){

		//https://laracasts.com/discuss/channels/laravel/inserting-if-record-not-exist-updating-if-exist?page=1
		$id = '';
		if($this->input->post('id')!==''){
			$id = $this->input->post('id');
		}

		$role_can_see_all_content = 0;
		
		if($this->input->post('role_can_see_all_content')){
			$role_can_see_all_content = $this->input->post('role_can_see_all_content');
		}
		Rolemodel::where('id', $id)->update(['role_can_see_all_content' => $role_can_see_all_content]);

		Rolepermissions::where('role_id', $id)->delete();
		foreach($this->input->post('permissions[]') as $p){
			$up = new RolePermissions();
			$up->role_id = $id; 
			$up->permission_id = $p;
			$up->save(); 
		}
		
		echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
	}

	function permissions($role_id){
		$role_permissions	=	Rolepermissions::where('role_id', $role_id)->get();

		$role_permissions_ids = [];
		foreach($role_permissions as $p){

			$role_permissions_ids[] = $p->{'permission_id'};
		}

		echo json_encode(array('status'=>1, 'msg'=>$role_permissions_ids));
	}

}