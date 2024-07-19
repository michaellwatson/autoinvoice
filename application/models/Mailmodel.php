<?php

use SendGrid\Mail\Mail;
use SendGrid\Mail\Attachment;

class Mailmodel extends CI_Model {

	private $apiUsername = '';
	private $apiPwd		 = '';

    function __construct()
    {
        parent::__construct();
		$this->load->library('email');
		$this->load->helper('url');
		$config['mailtype'] = 'html';
		//$config['protocol']		= 'sendmail';
		$this->email->initialize($config);
    }

    function getAllMessages(){
    	$this->db->select('*');
    	return $this->db->get('messages')->result_array();
    }

    function sendSMS($to, $message, $originator='Finance4') {
        $URL = 'http://api.textmarketer.co.uk/gateway/'."?username=".$this->apiUsername."&password=".$this->apiPwd."&option=xml";
        $URL .= "&to=$to&message=".urlencode($message).'&orig='.urlencode($originator);
        //$fp = fopen($URL, 'r');
        //return fread($fp, 1024);
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $URL); 
        curl_setopt($ch, CURLOPT_HEADER, TRUE); 
        curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $head = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch); 
        return true;
    }


	public function sendEmail($slug, $userid, $email = NULL, $custom = NULL, $attachments = NULL, $emailOrTxt = 'email', $from = NULL){
		//get user

		$this->db->select('us_firstName as firstname, us_surname as surname, us_company as company, us_email as email, us_code as code, us_password as password');
		$this->db->where('us_id',$userid);
		$user = $this->db->get('user')->row_array();
		//echo $this->db->last_query();
		$this->config->load('form_config');
		//print_r($user);
		//get message
		$this->db->select('*');
		$this->db->where('ad_slug',$slug);
		$msg = $this->db->get('messages');
		//echo $this->db->last_query();
		$msg = $msg->row_array();
		//print_r($msg);
		//replace the user details
		if(is_array($user)){
			foreach($user as $key=>$value){
				$msg['ad_message'] = str_replace('{'.$key.'}',$value,$msg['ad_message']);
				$msg['ad_subject'] = str_replace('{'.$key.'}',$value,$msg['ad_subject']);
			}
		}
		//var_dump($custom);
		if(is_array($custom)){
			foreach($custom as $key=>$value){
				$msg['ad_message'] = str_replace('{'.$key.'}',$value,$msg['ad_message']);
				$msg['ad_subject'] = str_replace('{'.$key.'}',$value,$msg['ad_subject']);
			}
		}
		$msg['ad_message'] = str_replace('{url}',base_url(),$msg['ad_message']);
		$msg['ad_subject'] = str_replace('{url}',base_url(),$msg['ad_subject']);

		$msg['ad_message'] = str_replace('{sitename}', $this->config->item('sitename'), $msg['ad_message']);
		$msg['ad_subject'] = str_replace('{sitename}', $this->config->item('sitename'), $msg['ad_subject']);
		
		//$email = '';
		if($email==NULL){
			$email = $user['email'];	
		}

		if($emailOrTxt=='email'){

			if(!is_null($userid)){
				$user = $this->db->get_where('pt_user', ['us_id' => $userid])->row();
			}
			$semail = new \SendGrid\Mail\Mail();
			
			$semail->setFrom($this->config->item('email'), $this->config->item('admin'));
			$semail->setSubject($msg['ad_subject']);
			if(!is_null($userid)){
				$semail->addTo($email, $user->us_firstName);
			}else{
				$semail->addTo($email);
			}
			$semail->addBcc('info@rowe.ai');
			$semail->addContent("text/html", $msg['ad_message']);

			foreach($attachments as $url){
				$filename = basename($url);
				$file_encoded = base64_encode(file_get_contents($url));
				$attachment = new SendGrid\Mail\Attachment();
				$attachment->setType("application/pdf");
				$attachment->setContent($file_encoded);
				$attachment->setDisposition("attachment");
				$attachment->setFilename($filename);
				$semail->addAttachment($attachment);
			}
			//print_r($semail);

			$sendgrid = new \SendGrid($this->config->item('sendgrid_key'));
			try {
			    $response = $sendgrid->send($semail);
			    //print $response->statusCode() . "\n";
			    //print_r($response->headers());
			    //print $response->body() . "\n";
			} catch (Exception $e) {
			    echo 'Caught exception: '. $e->getMessage() ."\n";
			}


			/*
			$from = $this->config->item('email');
		    $to = $email;
		    //echo $to;
		    $subject = $msg['me_subject'];
		    $message = $msg['me_msg'];
		    $headers = "From:" . $this->config->item('admin');
		    mail($to,$subject,$message, $headers);
		    */
		    //echo "Test email sent";
			
		}else{

			$this->sendSMS($email, $msg['ad_message']);

		}
	}
}