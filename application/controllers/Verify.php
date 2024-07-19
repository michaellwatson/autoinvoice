<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verify extends CI_Controller {
	public function __construct()
    {
     	parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->database();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('msg', 'english');
		//$this->load->model('Datamodel');
		$this->userid = 1;
		$this->load->model('Usermodel');
		$this->load->model('Quotesmodel');
		$this->data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			//nothing to do user is authenticated
			//i guess they shouldn't be here
			$this->userid = $this->session->userdata("us_id");
			redirect(base_url('dashboard')); 
		}

	}
	public function reset($code){
		
		$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
		$data['logo'] = $this->Quotesmodel->getSetting('logo');

		if($code!=''){
			$this->db->select('*');
			$this->db->where('us_forgot_key',$code);
			$userData = $this->db->get('user')->row_array();
			$data['code'] = $code;
			if(sizeof($userData)>0){
				$this->load->view('common/header',$data);
				$this->load->view('forms/reset_password',$data);
				$this->load->view('common/footer',$data);
			}else{
				$this->load->view('common/header',$data);
				$this->load->view('forms/reset_invalid_code',$data);
				$this->load->view('common/footer',$data);
			}
		}else{
			redirect(base_url('login'));
		}
	}
	public function resetPassword(){
		$this->form_validation->set_rules('passwordAdd', 'Password', 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('passwordConfirm', 'Password Confirm', 'required');
		$this->form_validation->set_rules('passwordHint', 'Password Hint', 'required');
		$this->form_validation->set_rules('code', 'code', 'required');

		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			if($this->input->post('passwordConfirm')!=$this->input->post('passwordAdd')){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('passNotMatch')));
				exit();
			}
			if (strpos($this->input->post('passwordHint'),$this->input->post('passwordAdd')) !== false){
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('passHint')));
				exit();
			}
			$pass = hash_hmac('sha1',$this->input->post('passwordAdd'), $this->config->item('encryption_key'));
			$update = array(
								'us_password'=>$pass,
								'us_passwordHint'=>$this->input->post('passwordHint'),
								'us_forgot_key'=>''
							);
			$this->db->where('us_forgot_key',$this->input->post('code'));				
			$this->db->update('user', $update);
			echo json_encode(array('status'=>1,'msg'=>$this->lang->line('passwordReset')));
			exit();
		}
	}
	public function confirm(){
		$data = array();
		$code = $this->uri->segment(3);
		
		$data['message'] = '';
		$data['showHeader'] = false;
		$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
		$data['logo'] = $this->Quotesmodel->getSetting('logo');

		//echo $code;
		if($code!=''){
			$this->db->select('*');
			$this->db->where('us_code',$code);
			$result = $this->db->get('user')->result_array();
			//var_dump($result);
			if(sizeof($result)>0){
				$update = array('us_verified'=>1);
				$this->db->where('us_code',$code);
				$this->db->update('user',$update);
				$data['success'] = 1;
				$data['msg'] = $this->lang->line('verificationCodeAccepted');
			}else{
				$data['success'] = 0;
				$data['msg'] = $this->lang->line('verificationCodeInvalid');
			}
		}
		$this->load->view('common/header',$data);
		$this->load->view('forms/login',$data);
		$this->load->view('common/footer',$data);
	}
}