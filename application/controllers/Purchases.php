<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases extends My_Controller {

	public function __construct(){
		parent::__construct();
	}

	function index($id){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$Freeholdermodel = new Freeholdermodel();
		try{

			$freeholder = $Freeholdermodel->findOrFail($id);
			$region = $this->Advertcategorymodel->getDataValue($freeholder->ad_Town);
			if(isset($region)){
				$freeholder['region'] = $region['ds_value'];
			}
			$data['freeholder'] = $freeholder;

			//$freeholder->ad_RouteApproval
			//$freeholder->ad_Install
			//$freeholder->ad_Signups
			
			$survey_processes_approved = $freeholder->survey_processes_approved;
			$data['survey_processes_approved'] = $survey_processes_approved;

			$survey_processes_installed = $freeholder->survey_processes_installed;
			$data['survey_processes_installed'] = $survey_processes_installed;

			$this->load->view('dashboard/header', $data);
			$this->load->view('tables/purchases', $data);
			//$this->load->view('templates/no_permission', $data);
			$this->load->view('dashboard/footer');

		}catch(ModelNotFoundException $e){

		}
	}
}