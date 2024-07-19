<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Personnel extends My_Controller {

	public function __construct(){

		parent::__construct();

	}

	function dropdown($client_id = NULL){

		if(!is_null($client_id)){
			$personnel = Personnelmodel::select('id', 'firstname', 'lastname', 'phone',  'telephoneNumber', 'personnel.email as email', 'companyName')->where('client_id', $client_id)->join('client', 'client_id', '=', 'client.c_id')->get();
			?>
			<option value="">Please Select</option>
			<?php
			foreach($personnel as $p){
				?>
				<option value="<?php echo $p->id;?>"><?php echo $p->firstname.' '.$p->lastname;?></option>
				<?php
			}
		}
		
	}
	function results($client_id = NULL){
		$limit = $this->input->post('limit');
		$start = $this->input->post('start');

		if(is_null($limit)){
			$limit = 10;
		}
		if(is_null($start)){
			$start = 0;
		}
		if(!is_null($client_id)){
			$personnel = Personnelmodel::select('id', 'firstname', 'lastname', 'phone',  'telephoneNumber', 'personnel.email as email', 'companyName')->where('client_id', $client_id)->join('client', 'client_id', '=', 'client.c_id')->skip($start)->take($limit)->get();
		}else{
			$personnel = Personnelmodel::select('id', 'firstname', 'lastname', 'phone',  'telephoneNumber', 'personnel.email as email', 'companyName')->join('client', 'client_id', '=', 'client.c_id')->skip($start)->take($limit)->get();
		}
		//echo $this->db->last_query();
		echo json_encode(
			array(
			'store'=>$personnel,
			'totalProperty'=>Personnelmodel::where('client_id', $client_id)->count()
			)
		);	
	}

	function show($client_id = NULL)
	{
		if(!is_null($client_id)){
			$this->data['client_id'] = $client_id;
		}
		$this->load->view('dashboard/header', $this->data);
		$this->load->view('lists/personnel', $this->data);
		$this->load->view('dashboard/footer');
	} 

	function add($id = NULL){

		$data = $this->data;
		$data['user'] = $this->data['user'];
		$data['users'] = $this->Usermodel->get();
		$data['clients'] = $this->Clientmodel->get();


		if(!is_null($id)){
			$data['personnel'] = Personnelmodel::find($id);
		}
		//print_r($data['users']);
		$this->load->view('dashboard/header', $data);
		$this->load->view('forms/personnel', $data);
		$this->load->view('dashboard/footer');
		
	}

	function save(){
		//print_r($_POST);
		
		$Personnel = new Personnelmodel();

		$firstname				= $this->input->post('firstname');
		$lastname			 	= $this->input->post('lastname');
		$email 					= $this->input->post('email');
		$phone 					= $this->input->post('phone');
		$client_id 				= $this->input->post('client_id');

		if($this->data['approve_jobs']){
			$approved 			= $this->input->post('approved');
		}
		//https://laracasts.com/discuss/channels/laravel/inserting-if-record-not-exist-updating-if-exist?page=1
		$id = '';
		if($this->input->post('id')!==''){
			$id = $this->input->post('id');
		}
		if (Personnelmodel::where('id', '=', $id)->exists()) {
			$Personnel = Personnelmodel::find($id);
		}

		$Personnel->firstname 				= $firstname;
		$Personnel->lastname			 	= $lastname;
		$Personnel->email 					= $email;
		$Personnel->phone 					= $phone;
		$Personnel->client_id			 	= $client_id;
		$Personnel->save();

		echo json_encode(array('status'=>1, 'msg'=>$this->lang->line('saved_successfully')));
	}

	function delete()
	{
		$this->form_validation->set_rules('id', 'Personnel ID', 'required|numeric');
		$id = $this->input->post('id');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
		}else{
			$job = Jobmodel::where('personnel_id', '=', $id)->exists();

			if(!$job){
				$Personnelmodel = Personnelmodel::find($id);
				$Personnelmodel->delete();
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('client_deleted')));
			}else{
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('client_cant_be_deleted')));
			}
		}
	}
}