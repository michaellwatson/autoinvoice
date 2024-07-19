<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Valums extends My_Controller {

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
	}
	


	function upload_images(){

		$this->load->library('qquploadedfilexhr');
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array("jpeg", "jpg", "png", "csv");
		// max file size in bytes
		$sizeLimit = 12 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload('assets/uploaded_images/');	
		
		$_SESSION['item'] = $result['filename'];

		$insert['up_filename'] 	= $result['filename']; 
		$insert['up_field_id'] 	= $this->input->get('field_id'); 
		$insert['up_entry_id'] 	= $this->input->get('entry_id'); 
		$insert['up_date']		= date('Y-m-d H:i:s', strtotime('now'));
		$insert['up_userid']	= $this->userid;
		$insert['up_table']		= $this->default_table;
		$this->db->insert('pt_images', $insert);
		$imgid = $this->db->insert_id();
		$result['imgid'] = $imgid;

		$doc_id = $this->db->insert_id();

		$Eventsmodel 			= new Eventsmodel();
		$Eventsmodel->note 		= $this->default_table_name.' Image Added '.$result['filename'];
		$Eventsmodel->table_id 	= $this->default_table;
		$Eventsmodel->user_id 	= $this->userid;
		$Eventsmodel->entry_id 	= $insert['up_entry_id'];
		$Eventsmodel->status 	= 1;		
		$Eventsmodel->save();
		
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
		exit();
	}

	function upload_documents(){
		
		$this->load->library('qquploadedfilexhr');
		$allowedExtensions = array('csv', 'pdf', 'jpeg', 'jpg', 'png', 'doc', 'docx');
		// max file size in bytes
		$sizeLimit = 12 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload('assets/uploads/');	
		
		$_SESSION['item'] = $result['filename'];

		$insert['up_filename'] 	= $result['filename']; 
		$insert['up_field_id'] 	= $this->input->get('field_id'); 
		$insert['up_entry_id'] 	= $this->input->get('entry_id'); 
		$insert['up_date']		= date('Y-m-d H:i:s', strtotime('now'));
		$insert['up_userid']	= $this->userid;
		$insert['up_table']		= $this->default_table;
		$this->db->insert('pt_documents', $insert);

		$doc_id = $this->db->insert_id();

		$Eventsmodel 			= new Eventsmodel();
		$Eventsmodel->note 		= $this->default_table_name.' Document Added '.$result['filename'];
		$Eventsmodel->table_id 	= $this->default_table;
		$Eventsmodel->user_id 	= $this->userid;
		$Eventsmodel->entry_id 	= $insert['up_entry_id'];
		$Eventsmodel->status 	= 1;		
		$Eventsmodel->save();

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
		exit();
		
	}

	function upload_filemanager(){
		
		$this->load->library('qquploadedfilexhr');
		$allowedExtensions = array('csv', 'pdf', 'jpeg', 'jpg', 'png', 'doc', 'docx');
		// max file size in bytes
		$sizeLimit = 12 * 1024 * 1024;
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($_SESSION['path']);	
		
		$_SESSION['item'] = $result['filename'];

		/*
		$insert['up_filename'] 	= $result['filename']; 
		$insert['up_field_id'] 	= $this->input->get('field_id'); 
		$insert['up_entry_id'] 	= $this->input->get('entry_id'); 
		$insert['up_date']		= date('Y-m-d H:i:s', strtotime('now'));
		$insert['up_userid']	= $this->userid;
		$insert['up_table']		= $this->default_table;
		$this->db->insert('pt_documents', $insert);
		*/

		$doc_id = $this->db->insert_id();

		$Eventsmodel 			= new Eventsmodel();
		$Eventsmodel->note 		= $this->default_table_name.' Filemanager File Added '.$result['filename'];
		$Eventsmodel->table_id 	= $this->default_table;
		$Eventsmodel->user_id 	= $this->userid;
		$Eventsmodel->entry_id 	= $this->input->get('entry_id'); 
		$Eventsmodel->status 	= 1;		
		$Eventsmodel->save();

		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
		exit();
		
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */