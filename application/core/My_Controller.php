<?php

class MY_Controller extends CI_Controller {
    public function __construct() {
       parent::__construct();

       	$this->load->library('session');
		$this->load->database();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->lang->load('msg', 'english');
		//$this->load->model('Clientmodel');
		//$this->load->model('User');
		$this->load->model('Usermodel');
		$this->load->model('Mailmodel');

		$this->load->model('Advertcategorymodel');
		$this->load->model('Notificationmodel');
		$this->load->model('Entriesmodel');
		$this->load->model('Quotesmodel');

		$this->load->library('image_lib');
		
		$this->userid = 1;
		
		$email = $this->input->cookie('email', false);
		$password = $this->input->cookie('password', false);

		$this->data['user'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		
		$this->session->set_userdata('us_role', $this->data['user']['us_role']);
		
		if(($this->session->userdata("us_id")!="")&&($this->session->userdata("us_id")!="-1")){
			//nothing to do user is authenticated
			//var_dump($this->data);
			$this->userid = $this->session->userdata("us_id");
			$this->username = $this->data['user']['us_username'];
		}else{
			//$this->load->view('login',$data);
			redirect(base_url('login')); 
		}
		
		$this->data['messages'] = $this->Usermodel->has_permission('messages');
		$this->data['view_users'] = $this->Usermodel->has_permission('view_users');
		$this->data['edit_client'] 	= $this->Usermodel->has_permission('edit_jobs');
		$this->data['edit_users'] 	= $this->Usermodel->has_permission('edit_users');

		$this->data['approve_jobs'] = $this->Usermodel->has_permission('approve_jobs');
		$this->data['edit_jobs'] 	= $this->Usermodel->has_permission('edit_jobs');
		$this->data['add_jobs'] 	= $this->Usermodel->has_permission('add_jobs');
		$this->data['edit_personnel'] = $this->Usermodel->has_permission('edit_personnel');

		$this->data['fill_documents'] 	= $this->Usermodel->has_permission('fill_documents');
		$this->data['create_templates'] = $this->Usermodel->has_permission('create_templates');



		$this->default_table 			= $this->config->item('default_table');
		$this->default_category_table 	= $this->config->item('default_category_table');
		$this->default_table_name 		= $this->config->item('default_table_name');
		$this->default_category_depth = '';

		$formID = isset($_GET['formID']) ? $_GET['formID'] : '';
		if($formID!=''){
			if(is_numeric($_GET['formID'])){
				$tableData = $this->Advertcategorymodel->formsTables($_GET['formID']);

				$_SESSION['ft_database_table'] = $tableData['ft_database_table'];
				$_SESSION['ft_categories'] = $tableData['ft_categories'];
				$_SESSION['ft_name'] = $tableData['ft_name'];
				$_SESSION['formID'] = $_GET['formID'];

				$this->default_table = $tableData['ft_database_table'];
				$this->default_category_table = $tableData['ft_categories'];
				$this->default_table_name = $tableData['ft_name'];
			}
		}

		if(isset($_SESSION['formID'])){
			if($_SESSION['formID']!=''){
				$this->formID = $_SESSION['formID'];
			}
		}
		if(isset($_SESSION['ft_database_table'])){
			if($_SESSION['ft_database_table']!=''){
				$this->default_table = $_SESSION['ft_database_table'];
			}
		}
		if(isset($_SESSION['ft_categories'])){
			if($_SESSION['ft_categories']!=''){
				$this->default_category_table = $_SESSION['ft_categories'];
			}
		}
		if(isset($_SESSION['ft_name'])){
			if($_SESSION['ft_name']!=''){
				$this->default_table_name = $_SESSION['ft_name'];
			}
		}
    }
}