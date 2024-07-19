<?php
class Login extends CI_Controller {

	public function __construct()
    {
     	parent::__construct();
     	//exit();
		$this->load->database();
		$this->load->model('Usermodel','',TRUE);
		$this->load->model('Mailmodel','',TRUE);
		$this->load->model('Quotesmodel','',TRUE);
		$this->load->library('session');
		$this->load->helper('cookie');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('msg', 'english');
		$this->load->helper('string');
		$this->form_validation->set_error_delimiters('<div class="message error">', '</div>');

	}

	function logout(){
		//echo "##";
		$session_id = $this->session->userdata('session_id');
		$this->Usermodel->deleteLogs($session_id);
		$this->session->sess_destroy();
		$cookie = array(
    					'name'   => 'email',
    					'value'  => '',
    					'expire' =>  -86500,
    					'secure' => false
					);
					$this->input->set_cookie($cookie); 

					$cookie = array(
    					'name'   => 'password',
    					'value'  => '',
    					'expire' =>  -86500,
    					'secure' => false
					);
					$this->input->set_cookie($cookie); 	
					
		redirect(base_url('login'));
	}
	function resend()
	{
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url($this->config->item('default_page'))); 
		}
		//$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$data['message'] = '';
		$data['showHeader'] = false;
		$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
		$data['logo'] = $this->Quotesmodel->getSetting('logo');
		$data['login_background_credit'] = $this->Quotesmodel->getSetting('login_background_credit');
		
		$this->load->view('common/header',$data);
		$this->load->view('forms/resend',$data);
		$this->load->view('common/footer',$data);
	}
	
	function resendCheck(){
		
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url($this->config->item('default_page'))); 
		}
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$this->db->select('*');
			$this->db->where('us_email',$this->input->post('email'));
			$users = $this->db->get('user')->row_array();
			if(sizeof($users)>0){
				if($users['us_verified']==1){
					echo json_encode(array('status'=>0,'msg'=>$this->lang->line('alreadyVerified')));
					exit();
				}else{
					$this->load->helper('string');
					$key = random_string('alnum',20);
					//echo $key;
					$update = array('us_code'=>$key);
					$this->db->where('us_id',$users['us_id']);
					$this->db->update('user',$update);
					//sendEmail($slug,$userid, $email = NULL, $custom = NULL, $attachments = NULL, $emailOrTxt = 'email', $from = NULL)
					//$this->Mailmodel->sendEmail('signupemail',$uid, NULL, NULL, NULL, 'email', 'member.support@thesalespontoon.co.uk'); 
					$this->Mailmodel->sendEmail('signupemail',$uid); 
					//we don't want to auto login
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('resendSuccess')));
					exit();
				}
			}else{
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('emailAddressNotFound')));
				exit();
			}
		}
	}

	function forgot()
	{
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url($this->config->item('default_page'))); 
		}
		//$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		$data['message'] = '';
		$data['showHeader'] = false;
		$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
		$data['logo'] = $this->Quotesmodel->getSetting('logo');
		$data['login_background_credit'] = $this->Quotesmodel->getSetting('login_background_credit');

		$this->load->view('common/header',$data);
		$this->load->view('forms/forgot',$data);
		$this->load->view('common/footer',$data);
	}



	function forgotPasswordCheck(){
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url($this->config->item('default_page'))); 
		}
		$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$this->db->select('*');
			$this->db->where('us_email',$this->input->post('email'));
			$users = $this->db->get('user')->row_array();
			if(sizeof($users)>0){
				$this->load->helper('string');
				$key = random_string('alnum',20);
				//echo $key;
				$update = array('us_forgot_key'=>$key);
				$this->db->where('us_id',$users['us_id']);
				$this->db->update('user',$update);
				$custom = array('hint'=>$users['us_passwordHint'],'url'=>base_url(),'codex'=>$key);
				//echo $users['us_id'];
				$this->Mailmodel->sendEmail('resetPassword',$users['us_id'],NULL, $custom);
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('forgotPassword')));
				exit();
			}else{
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('emailAddressNotFound')));
				exit();
			}
		}
	}


	function index()
	{
		//$pass = hash_hmac('sha1','Testpass1', $this->config->item('encryption_key'));
		//echo $pass;

		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url($this->config->item('default_page'))); 
		}
		//$pass = hash_hmac('sha1','testpass', $this->config->item('encryption_key'));
		//echo $pass;
		$this->form_validation->set_rules('email', 'email', 'required');
		
		if($this->input->post('email')){
			$this->form_validation->set_rules('password', 'password', 'required|min_length[8]|max_length[21]');

			//check to see if cookies exist
			$email = $this->input->cookie('email', false);
			$password = $this->input->cookie('password', false);
			//$this->Usermodel->login($email,$password);

			if((!is_null($email))&&(!is_null($password))){
				if($this->Usermodel->login($email,$password)==true){
					$data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
					$this->session->set_userdata('us_email', $email);
					$this->session->set_userdata('us_pass', $password);
					
					$this->session->set_userdata('us_usertype', $data['us_usertype']);
					//redirect(base_url('index.php/home/manage'));	
					//exit();
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('login_success')));
					exit();
				}
			}
				
			if ($this->form_validation->run() == FALSE)
			{
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
				exit();
			}
			else
			{	
				$email = $this->input->post('email');
				//moved the hash
				$password = $this->input->post('password');
				
				if($this->Usermodel->login($email,$password)==true){
					$data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
					$this->session->set_userdata('session_guid', random_string('alnum', 16)); 
					
					//redirect(base_url('index.php/main/content'));
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('login_success'),'redirect'=>1));
					exit();
				}else{
					//$data['message'] = '<div class="message error">User details not found</div>';
					//$this->load->view('common/header',$data);
					//$this->load->view('common/login', $data);
					//$this->load->view('common/footer',$data);
					echo json_encode(array('status'=>0,'msg'=>$this->lang->line('login_failed')));
					exit();
				}
			}
		
		}else{
			$data['message'] = '';
			$data['showHeader'] = false;

			$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
			$data['logo'] = $this->Quotesmodel->getSetting('logo');
			$data['login_background_credit'] = $this->Quotesmodel->getSetting('login_background_credit');

			$this->load->view('common/header',$data);
			$this->load->view('forms/login',$data);
			$this->load->view('common/footer',$data);
		}
	}
}
?>