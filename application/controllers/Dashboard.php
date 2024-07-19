<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Illuminate\Database\Capsule\Manager
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

class Dashboard extends My_Controller {
	
	public $menu = '';
		
	public function __construct()
    {
     	parent::__construct();
	}

	function index(){
		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);

		//$start = strtotime('yesterday midnight');
		//$end = strtotime('today midnight');
		$days = 7;
		$start = date('Y-m-d H:i:s', strtotime('today midnight -'.$days.' days'));
		$end = date('Y-m-d H:i:s', time());

		$data['start'] =  date('m/d/Y', strtotime($start)); // Used in daterangepicker
		$data['end'] = date('m/d/Y', strtotime($end)); // Used in daterangepicker

		$data['startYmd'] =  date('Y-m-d', strtotime($start)); // Used in default first ajax
		$data['endYmd'] = date('Y-m-d', strtotime($end)); // Used in default first ajax

		$data['send_cron'] = $cron = $this->db->where('name', 'send')->get('cron_status')->row();

		$this->load->view('dashboard/header', $data);
		$this->load->view('dashboard/index', $data);
		$this->load->view('dashboard/footer');
	}

	public function getWidgetDataAjax(){
		$status = false; $message = ''; $responseData = array();

		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');

		$start = $startDate;
		$end = $endDate;

		$html = '';

		$widgets = Widgetsmodel::all()->sortBy("widget_order");
		//::orderBy('name')->get();
		foreach($widgets as $w){

			if($w->widget_type=='count_entries'){
				$FormsTablesmodel = FormsTablesmodel::findorFail($w->widget_form_id);
				//print_r($FormsTablesmodel);
				//$this->db->get($FormsTablesmodel->ft_database_table)->num_row();
				//print_r($w);
				//print_r($w->conditions);

				foreach($w->conditions as $c){
					
					$field = Advertfieldsmodel::findorFail($c->field_id);
					$this->db->where('ad_'.$field->adv_column.' '.$c->operator, $c->value);

				}
				if(!is_null($w->widget_date_field)){

					if(!empty($start) && !empty($end)){
		                // $this->db->where("$w->widget_date_field BETWEEN '$start' AND '$end'");

		                $this->db->where($w->widget_date_field.' >=', $start);
						$this->db->where($w->widget_date_field.' <=', $end);
		            }
		            else if(!empty($start) && empty($end)){
		                $this->db->where("$w->widget_date_field >= '$start'");
		            }
		            else if(empty($start) && !empty($end)){
		                $this->db->where("$w->widget_date_field <= '$end'");
		            }
					/*$this->db->where($w->widget_date_field.' >=', $start);
					$this->db->where($w->widget_date_field.' <=', $end);*/
					
				}
				//$this->db->get($FormsTablesmodel->ft_database_table)->num_rows();
				$card['result'] = $this->db->get($FormsTablesmodel->ft_database_table)->num_rows();

				// echo $this->db->last_query();exit;

				$card['field'] = $w->widget_name;
				$card['color'] = $w->widget_color;
				//echo $this->db->last_query();

				$html .= $this->load->view('dashboard/partials/card', $card, true);
			}
			
		}

		$responseData = array(
			'html' => $html
		);

		$status = true;

		$resultData = array( 'status' => $status, 'message' => $message, 'data' => $responseData );
		echo json_encode($resultData);
        exit;
	}

	function choose_widget(){
		$this->data['tablesList'] = $this->Advertcategorymodel->formsTables();

		$this->load->view('dashboard/header', $data);
		$this->load->view('dashboard/choose_widget_type', $this->data);
		$this->load->view('dashboard/footer');
	}

	function add_widget(){
		$this->data['tablesList'] = $this->Advertcategorymodel->formsTables();

		$this->load->view('dashboard/header', $data);
		$this->load->view('dashboard/choose_form', $this->data);
		$this->load->view('dashboard/footer');
	}

	function create_widget($formID){
		$data = $this->data;
		$data['user'] = $this->data['user'];

		$form = FormsTablesmodel::findorFail($formID);
		$data['form_id'] = $form->ft_id;

		//get any hidden date fields on the table not listed but are date fields
		$db_fields = $this->db->list_fields($form->ft_database_table);

		$fields = Advertfieldsmodel::where('adv_table', $form->ft_database_table)->join('field_type', 'adv_field_type', '=', 'fi_id')->orderBy('adv_order', 'asc')->get(); 
		$data['fields'] = $fields;

		$table_fields = [];
		foreach($data['fields'] as $f){
			$table_fields[] = 'ad_'.$f->adv_column;
		}

		$not_listed = array_diff($db_fields, $table_fields);

		$fields_meta = $this->db->field_data($form->ft_database_table);
		$fields_meta_sorted = [];
		foreach ($fields_meta as $field)
		{

			$fields_meta_sorted[$field->name] = $field;

		}

		$data['spare_date_fields'] = [];
		foreach($not_listed as $n){
			if($fields_meta_sorted[$n]->type == 'datetime'){
				$data['spare_date_fields'][] = $n;
			}
		}
		//end date fields

		$form = FormsTablesmodel::findorFail($formID);
		
		$this->load->view('dashboard/header', $data);
		//$this->load->view('forms/user', $data);
		$this->load->view('dashboard/widget_info', $data);
		$this->load->view('dashboard/footer');	
	}

	function save_widget(){

		//ini_set('display_errors', 1);
		//ini_set('display_startup_errors', 1);
		//error_reporting(E_ALL);
		Capsule::enableQueryLog();
	

		$Widgetsmodel 						= new Widgetsmodel();
		$Widgetsmodel->widget_type 			= $this->input->post('widget_type');
		$Widgetsmodel->widget_form_id 		= $this->input->post('form_id');
		$Widgetsmodel->widget_name 			= $this->input->post('widget_name');
		$Widgetsmodel->widget_color 		= $this->input->post('widget_color');
		foreach($this->input->post('date') as $d){
			$Widgetsmodel->widget_date_field 	= $d;
		}
		$Widgetsmodel->save();

		foreach($this->input->post('where') as $w){
			$Widgetconditionsmodel = new Widgetsconditionsmodel();
			$Widgetconditionsmodel->widget_id = $Widgetsmodel->id;
			$Widgetconditionsmodel->field_id = $w;
			$Widgetconditionsmodel->operator = $this->input->post('operator_'.$w);
			$Widgetconditionsmodel->value = $this->input->post('value_'.$w);
			$Widgetconditionsmodel->save();
		}
		//dd(Capsule::getQueryLog());
		//success message
	}

	public function send(){
		// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
		$cron = $this->db->where('name', 'send')->get('cron_status')->row();

		if($cron->status == 0){

			$update['status'] = 1;
			$update['started_at'] = date('Y-m-d H:i:s');
			$this->db->where('id', 1);
			$this->db->update('cron_status', $update);

			$resultData = array( 'status' => 1, 'msg' => 'Cron Request Started');
			echo json_encode($resultData);
		}else{
			//cron is already running
			$resultData = array( 'status' => 0, 'msg' => 'Cron already running');
			echo json_encode($resultData);
		}
		
	}

	function old(){
		$data = $this->data;

		$this->load->helper('url');
		redirect(base_url('entries/show/1?formID=13'));
		exit();

		$start = strtotime('this week');
		$finish = time();

		$ra_week = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $start)->where('ad_DatePackapprovedbyFreeholder', '<=',  $finish)->get();
		$install_week = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $start)->where('ad_InstalledDate', '<=',  $finish)->get();
		
		$data['ra_week_total'] 		= 0;
		$data['install_week_total'] = 0;
		
		foreach($ra_week as $r){
			$data['ra_week_total']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_week as $r){
			$data['install_week_total']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['this_week'] = $data['ra_week_total']+$data['install_week_total'];



		$previous_week = strtotime("last week monday");
		$start_week = strtotime("last week sunday");

		$ra_previous_week = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $previous_week)->where('ad_DatePackapprovedbyFreeholder', '<=',  $start_week)->get();
		$install_previous_week = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $previous_week)->where('ad_InstalledDate', '<=',  $start_week)->get();
		
		$data['ra_previous_week'] 		= 0;
		$data['install_previous_week']  = 0;

		foreach($ra_previous_week as $r){
			$data['ra_previous_week']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_previous_week as $r){
			$data['install_previous_week']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['last_week'] = $data['ra_previous_week']+$data['install_previous_week'];



		$previous_week2 = strtotime("2 weeks ago monday");
		$start_week2 = strtotime("2 weeks ago sunday");
		
		$ra_previous_week2 = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $previous_week2)->where('ad_DatePackapprovedbyFreeholder', '<=',  $start_week2)->get();
		$install_previous_week2 = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $previous_week2)->where('ad_InstalledDate', '<=',  $start_week2)->get();
		
		$data['ra_previous_week2'] 		= 0;
		$data['install_previous_week2'] = 0;

		foreach($ra_previous_week as $r){
			$data['ra_previous_week2']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_previous_week as $r){
			$data['install_previous_week2']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['last_week2'] = $data['ra_previous_week2']+$data['install_previous_week2'];




		$month_start = strtotime(date('Y-m-01',strtotime('now')));
		$month_finish = strtotime(date('Y-m-t',strtotime('now')));
		
		$ra_month = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $month_start)->where('ad_DatePackapprovedbyFreeholder', '<=',  $month_finish)->get();
		$install_month = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $month_start)->where('ad_InstalledDate', '<=',  $month_finish)->get();
		
		$data['ra_month'] 		= 0;
		$data['install_month']  = 0;

		foreach($ra_month as $r){
			$data['ra_month']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_month as $r){
			$data['install_month']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['this_month'] = $data['ra_month']+$data['install_month'];




		$last_month_start = strtotime(date('Y-m-01',strtotime('last month')));
		$last_month_finish = strtotime(date('Y-m-t',strtotime('last month')));

		$ra_last_month = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $last_month_start)->where('ad_DatePackapprovedbyFreeholder', '<=',  $last_month_finish)->get();
		$install_last_month = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $last_month_start)->where('ad_InstalledDate', '<=',  $last_month_finish)->get();
		
		$data['ra_last_month'] 		= 0;
		$data['install_last_month'] 	= 0;

		foreach($ra_month as $r){
			$data['ra_last_month']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_month as $r){
			$data['install_last_month']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['last_month'] = $data['ra_last_month']+$data['install_last_month'];




		$month2_start = strtotime(date('Y-m-01',strtotime('2 months ago')));
		$month2_finish = strtotime(date('Y-m-t',strtotime('2 months ago')));

		$ra_month2 = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $month2_start)->where('ad_DatePackapprovedbyFreeholder', '<=',  $month2_finish)->get();
		$install_month2 = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $month2_start)->where('ad_InstalledDate', '<=',  $month2_finish)->get();
		$data['ra_month2'] 		= 0;
		$data['install_month2'] = 0;
		foreach($ra_month2 as $r){
			$data['ra_month2']+= count($r->project->schemes) * $r->fibreclient->ad_RouteApproval;
		}
		foreach($install_month2 as $r){
			$data['install_month2']+= count($r->project->schemes) * $r->fibreclient->ad_Install;
		}
		$data['month2_ago'] = $data['ra_month2']+$data['install_month2'];


		/*
		Decrease = Original Number - New Number
		*/
		$d = $data['last_week']-$data['this_week'];

		/*
		Then: divide the decrease by the original number and multiply the answer by 100.
		% Decrease = Decrease ÷ Original Number × 100
		*/
		if($data['last_week']==0){
			$data['pd'] = 'INF';
		}else{
			$data['pd'] = ($d/$data['last_week'])*100;
		}

		/*
		Decrease = Original Number - New Number
		*/
		$d = $data['last_week2']-$data['last_week'];
		/*
		Then: divide the decrease by the original number and multiply the answer by 100.
		% Decrease = Decrease ÷ Original Number × 100
		*/
		if($data['last_week2']==0){
			$data['pd2'] = 'INF';
		}else{
			$data['pd2'] = ($d/$data['last_week2'])*100;
			//echo $data['pd2'];
		}

		/*
		Decrease = Original Number - New Number
		*/
		$d = $data['last_month']-$data['this_month'];
		/*
		Then: divide the decrease by the original number and multiply the answer by 100.
		% Decrease = Decrease ÷ Original Number × 100
		*/
		if($data['last_month']==0){
			$data['pm'] = 'INF';
		}else{
			$data['pm'] = ($d/$data['last_month'])*100;
		}
		/*
		Decrease = Original Number - New Number
		*/
		$d = $data['month2_ago']-$data['last_month'];
		/*
		Then: divide the decrease by the original number and multiply the answer by 100.
		% Decrease = Decrease ÷ Original Number × 100
		*/
		if($data['month2_ago']==0){
			$data['pm2'] = 'INF';
		}else{
			$data['pm2'] = ($d/$data['month2_ago'])*100;
		}
		/*
		If your answer is a negative number then this is a percentage increase.
		Read more at: https://www.skillsyouneed.com/num/percent-change.html
		*/
		$days_30 = strtotime('-30 days');


		$data['ra_30'] = Surveyprocessmodel::where('ad_DatePackapprovedbyFreeholder', '>=',  $days_30)->get();
		$data['install_30'] = Surveyprocessmodel::where('ad_InstalledDate', '>=',  $days_30)->get();

		$ra_30 = [];
		$ra_30_2 = [];

		$install_30 = [];
		$install_30_2 = [];

		foreach($data['ra_30'] as $v){
			$houses = count($r->project->schemes);
			$ra_30[date('Y-m-d', $v->ad_DatePackapprovedbyFreeholder)] = ($houses * $v->fibreclient->ad_RouteApproval);
		}

		foreach($data['install_30'] as $v){
			$houses = count($r->project->schemes);
			$install_30[date('Y-m-d', $v->ad_InstalledDate)] = ($houses * $v->fibreclient->ad_Install);
		}

		
		$x = 30;

		while(($x*-1) <= 0) {
			$date = date('Y-m-d',strtotime('-'.$x.' days'));

			if(!array_key_exists($date, $ra_30)){
		    	$ra_30_2[strtotime($date)] = 0;
			}else{
				$ra_30_2[strtotime($date)] = $ra_30[$date];
			}

			if(!array_key_exists($date, $install_30)){
		    	$install_30_2[strtotime($date)] = 0;
			}else{
				$install_30_2[strtotime($date)] = $install_30[$date];
			}

		    $x--;
		} 

		$data['ra_30'] 			= $ra_30_2;
		$data['install_30'] 	= $install_30_2;

		$this->load->view('dashboard/header', $data);
		$this->load->view('dashboard/index', $data);
		$this->load->view('dashboard/footer');

	}
}