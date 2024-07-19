<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
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

		$this->data = $this->Usermodel->selectUser($this->session->userdata("us_id"));

		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			redirect(base_url('dashboard')); 
		}
	}


	function confirm(){
		$data['message'] = $this->lang->line('account_created');
		$data['prediction_footer'] = $this->lang->line('verify_account_footer');
		$data['login_background'] = $this->Quotesmodel->getSetting('login_background');
		$data['logo'] = $this->Quotesmodel->getSetting('logo');
		//echo $data['message'];
		$html = '';
		$html.=$this->load->view('common/header',$data, true);
		$html.=$this->load->view('forms/thanks',$data, true);
		$html.=$this->load->view('common/footer',$data, true);
		echo $html;
		exit();
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */