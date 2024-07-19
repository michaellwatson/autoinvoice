<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//class Client extends My_Controller {
class Client extends My_Controller {

	public function __construct(){
		parent::__construct();
	}
	/*
	public function companies(){
		//$update['scraped_contacts'] = 1;
		//$this->db->update('pt_companies_drill', $update);
		$i=0;
		$handle = fopen('public/scrape_output/10438969_recovery_center_scrape.csv', "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		if($i>0){
			$insert['firstname'] = $data[0];
			$insert['surname'] = $data[1];
			$insert['name'] = $data[2];
			$insert['link'] = $data[3];
			$insert['company_link'] = $data[4];
			$insert['company'] = $data[5];
			$insert['position'] = $data[6];
			$insert['website'] = 'https://svchc.org';
		   //$import="INSERT into companies(firstname,surname, name, link, company_link, company, position)values('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."','".$data[5]."','".$data[6]."')";
		   $this->db->insert('pt_companies_drill', $insert);
		}
		$i=1;
		}
	}
	*/
	public function test(){
		echo '<img src="'.base_url('assets/uploads/signature/phil_signature.png').'"><br>Philip S Diamond MRICS<br>
Chartered Building Surveyor<br>
Diamond & Company (Scotland) Ltd /<br>
Brooker Diamond Chartered Fire Engineering Ltd<br>';
	}
	public function results(){
		//echo $this->input->get('limit');
		//echo $this->input->get('start');
		if(($this->input->get('limit')!='')&&($this->input->get('start')!='')){
			//echo "hit";
			//echo $this->input->get('limit')."#";
			//echo $this->input->get('start')."#";
			echo json_encode(
			array(
			'store'=>$this->Clientmodel->clientList($this->input->get('limit'), $this->input->get('start')),
			'totalProperty'=>$this->Clientmodel->clientTotal()
			)
			);	
		}
	}
	public function show()
	{
		$this->load->view('dashboard/header', $this->data);
		$this->load->view('lists/client', $this->data);
		$this->load->view('dashboard/footer');
	} 
	
	public function delete()
	{
		$this->form_validation->set_rules('client_id', 'Client ID', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
		}else{
			if($this->Clientmodel->clientDeleteAble($this->input->post('client_id'))){
				$this->db->delete('client', array('c_id' => $this->input->post('client_id'))); 
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('personnel_deleted')));
			}else{
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('personnel_cant_be_deleted')));
			}
		}
	}
	
	public function add()
	{
		$data = $this->data;
		$data['formValues'] = array(
		'c_id' => NULL,
		'clientName'=> NULL,
		'companyName'=> NULL,
		'companyAddress'=> NULL,
		'companyAddress2'=> NULL,
		'postcode'=> NULL,
		'telephoneNumber'=> NULL,
		'email'=> NULL,
		'defaultEnergySupplier'=>NULL,
		'us_email'=>NULL,
		'us_password'=>NULL
		);
		if(is_numeric($this->uri->segment(3))){
			$dataToView = $this->Clientmodel->getClientData($this->uri->segment(3));
			$val = $this->Usermodel->checkUserExists($dataToView['added_by']);
			$result = array_merge($data['formValues'], $dataToView);
			$data['formValues'] =$result;
			$data['formValues']['us_email'] = $val['us_email'];
			$data['formValues']['us_password'] = $val['us_password'];
		}
		$data['propertyTypes'] = $this->Clientmodel->getPropertyTypes();
		$data['constructionTypes'] = $this->Clientmodel->getConstructionTypes();
		
		$data['energySupplier'] = $this->Clientmodel->energySupplier();
		

		$data['user'] = $this->data['user'];
		$this->load->view('dashboard/header', $data);
		$this->load->view('forms/client', $data);
		$this->load->view('dashboard/footer');
	}
	
	public function saveClientData(){
		//var_dump($_POST);	
		$this->form_validation->set_rules('clientName', 'Client Name', 'required');
		$this->form_validation->set_rules('companyName', 'Company Name', 'required');
		$this->form_validation->set_rules('companyAddress', 'Company Address', 'required');
		$this->form_validation->set_rules('companyAddress2', 'Company Address2', 'required');
		$this->form_validation->set_rules('postcode', 'Postcode', 'required');
		$this->form_validation->set_rules('telephoneNumber', 'Telephone Number', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
		}else{
			$date = date('Y-m-d H:i:s',strtotime('now'));
			//echo $date;
			//We Are Editing Here
			$userExists = false;
			if($this->input->post('idOfClient') !=''){
				//a user and a client are in different dbs
				//check username and password submitted
				if(($this->input->post('us_email'))&&($this->input->post('us_password'))){
				//echo $this->input->post('idOfClient');
				//echo '##';	
				//check if we need a user associated with this client
				$userInfo = $this->Clientmodel->getClientData($this->input->post('idOfClient')); 
				//var_dump($userInfo);
				}
				$input = array(
				'clientName'=>$this->input->post('clientName'),
				'companyName'=>$this->input->post('companyName'),
				'companyAddress'=>$this->input->post('companyAddress'),
				'companyAddress2'=>$this->input->post('companyAddress2'),
				'postcode'=>$this->input->post('postcode'),
				'telephoneNumber'=>$this->input->post('telephoneNumber'),
				'email'=>$this->input->post('email'),				
				'defaultEnergySupplier'=>$this->input->post('defaultEnergySupplier'),
				'last_edited'=>$date,
				'last_edited_by'=>$this->userid
				);

				$this->db->where('c_id', $this->input->post('idOfClient'));
				$this->db->update('client', $input); 
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('updated_successfully')));
				exit();
			}
			//Adding Below
			$input = array(
				'clientName'=>$this->input->post('clientName'),
				'companyName'=>$this->input->post('companyName'),
				'companyAddress'=>$this->input->post('companyAddress'),
				'companyAddress2'=>$this->input->post('companyAddress2'),
				'postcode'=>$this->input->post('postcode'),
				'telephoneNumber'=>$this->input->post('telephoneNumber'),
				'email'=>$this->input->post('email'),
				'defaultEnergySupplier'=>$this->input->post('defaultEnergySupplier'),
				'date_added'=>$date,
				'added_by'=>$this->userid,
				'last_edited'=>$date,
				'last_edited_by'=>$this->userid
				);
			$this->db->insert('client', $input); 
			$clientId = $this->db->insert_id();
			//save user record if supplied
			echo json_encode(array('status'=>1,'msg'=>$this->lang->line('added_successfully')));
		}
	}

	public function clientLike(){
		if($this->input->get('query')){
			$this->db->distinct();
			$this->db->select('companyName');
			$this->db->like('companyName', $this->input->get('query'));
			$this->db->limit(20);
			$query = $this->db->get('client')->result_array();
			//echo $this->db->last_query();
			$results = array();
			foreach($query as $q){
				$results[] = array('text'=>$q['companyName']);
			}
			echo json_encode($results); 
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */