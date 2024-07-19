<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends My_Controller {

	public function __construct()
    {
     	parent::__construct();
	}

	public function get_by_document($id){
		$Messagemodel = Messagemodel::where('me_forms_tables_id', '=', $id)->first();
		echo json_encode(array('status'=>1, 'subject'=>$Messagemodel->ad_Subject, 'message'=>$Messagemodel->ad_Message));
	}

	public function index(){

		$data = $this->data;
		$data['user'] = $this->data['user'];

		if(!$this->data['messages']){
			show_404();
			exit();
		}
		$data['messages'] = $this->Mailmodel->getAllMessages();
		
		$this->load->view('dashboard/header',$data);
		$this->load->view('messages/messages',$data);
		$this->load->view('dashboard/footer',$data);
	}

	public function update_messages(){
		$this->form_validation->set_rules('subject', 'Subject', 'required');
		$this->form_validation->set_rules('message', 'Message', 'required');
		$this->form_validation->set_rules('id', 'ID', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			$update = array(
				'me_subject'=>$this->input->post('subject'),
				'me_msg'=>$this->input->post('message')
			);
			$this->db->where('me_id',$this->input->post('id'));
			$this->db->update('messages', $update);
			echo json_encode(array('status'=>1 ));
		}
	}

	public function send_message(){
		$this->form_validation->set_rules('iss_subject', 'Subject', 'required');
		$this->form_validation->set_rules('iss_message', 'Message', 'required');
		$this->form_validation->set_rules('iss_entryid', 'Entry ID', 'required');
		$this->form_validation->set_rules('iss_tableid', 'Table ID', 'required');
		
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	

			$FormsTablesmodel 	= FormsTablesmodel::find($this->input->post('iss_tableid'));

			$update['ad_is_read'] = -1;
			$this->db->where('ad_id', $this->input->post('iss_entryid'));
			$this->db->update($FormsTablesmodel->ft_database_table, $update);

			$data['listing'] 	= $this->Advertcategorymodel->getAdvert($this->input->post('iss_entryid'));
			$data['ref'] 		= date('dmy',strtotime($data['listing']['ad_added'])).'/'.$data['listing']['ad_rand'];
			$data['latest']	 	= $this->Quotesmodel->quote_latest($data['ref']);

			$path = 'assets/quotes/'.$data['latest']['quh_filename'].'.pdf';

			$this->load->library('email');
			//SMTP & mail configuration
			$config = array(
			    'protocol'  => 'smtp',
			    'smtp_host' => 'smtp-pulse.com',
			    'smtp_port' => 2525,
			    'smtp_user' => 'Michael@platformjam.com',
			    'smtp_pass' => 'KEmFt87oiP',
			    'mailtype'  => 'html',
			    'charset'   => 'utf-8'
			);
			$this->email->initialize($config);
			$this->email->set_mailtype("html");
			$this->email->set_newline("\r\n");

			$this->email->to($this->input->post('email_list'));
			$this->email->from($this->config->item('email'), $this->config->item('admin'));

			$this->email->subject($this->input->post('iss_subject'));

			$message = $this->input->post('iss_message');
			$message.='<br><img src="'.base_url('/engage/image?id='.$data['listing']['ad_id']).'&tableid='.$this->input->post('iss_tableid').'">';
			$this->email->message($message);	
			$this->email->attach($path);
			//Send email
			$this->email->send();

			$Eventsmodel 			= new Eventsmodel();
			$Eventsmodel->note 		= $FormsTablesmodel->ft_name.' sent to '.$this->input->post('email_list');
			$Eventsmodel->job_id 	= $data['listing']['ad_job_id'];
			$Eventsmodel->user_id 	= $this->userid;
			$Eventsmodel->save();

			echo json_encode(array('status'=>1 ));
		}
	}

}