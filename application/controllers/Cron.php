<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Illuminate\Database\Capsule\Manager
use Illuminate\Database\Capsule\Manager as Capsule;

class Cron extends CI_Controller {
        
    public function __construct()
    {
        // ini_set('display_errors', '1');
        // ini_set('display_startup_errors', '1');
        // error_reporting(E_ALL);
        
        parent::__construct();
        $this->load->model('Usermodel');
        $this->load->model('Entriesmodel');
        $this->load->model('Quotesmodel');
        $this->load->model('Notificationmodel');
        $this->load->model('Mailmodel');
    }

    public function deleteExpiredLocks() {

        ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
        // Calculate the timestamp for 30 minutes ago
        $expirationTime = strtotime('-30 minutes');

        // Convert the timestamp to MySQL datetime format
        $expirationDateTime = date('Y-m-d H:i:s', $expirationTime);

        // Delete expired locks from the database
        $this->db->where('locked_at <=', $expirationDateTime);
        $this->db->delete('record_locks');
        //echo $this->db->last_query();

        // Log the number of deleted locks
        $numDeletedLocks = $this->db->affected_rows();
        //log_message('info', 'Deleted '.$numDeletedLocks.' expired locks');

        $this->db->where('unlocked_at !=', NULL);
        $this->db->delete('record_locks');
        //echo $this->db->last_query();
    }

    function update_blocks_completion_percentage(){

        //ini_set('display_errors', 1); 
        //ini_set('display_startup_errors', 1); 
        //error_reporting(E_ALL);
        $this->load->model('Statisticsmodel');

        $blocks = $this->db->get('blocks')->result_array();

        foreach($blocks as $b){

            echo $b['ad_id'].'#';
            $percent = $this->Statisticsmodel->report_percent_complete('blocks', $b['ad_id']);


            $update['ad_percentcomplete'] = $percent;
            $this->db->where('ad_id', $b['ad_id']);
            $this->db->update('blocks', $update);

        }

    }

    function sendInvoices(){

        $data['send_cron']  = $this->db->where('name', 'send')->get('cron_status')->row();
        
        if($data['send_cron']->status == 1){

            $invoices = $this->db->where('ad_sent', NULL)->or_where('ad_sent', '0')->limit(50)->get('invoices')->result();

            if(empty($invoices)){

                $update['finished_at'] = date('Y-m-d H:i:s');
                $update['status'] = 0;
                $this->db->where('id', 1);
                $this->db->update('cron_status', $update);

            }else{

                foreach($invoices as $i){

                    $returnArray = $this->Quotesmodel->generate_quote($category, $i->ad_id, NULL, 0, true, false, 'pdf', NULL, 'invoices', 'downloadPdfBeforeMerge', false);

                    $custom['firstname'] = $i->ad_firstname;
                    $custom['id'] = $i->ad_id;
                    $attachments = [];
                    $attachments[] =  $returnArray['data']['path'];
                    $this->Mailmodel->sendEmail('newinvoice', null, $i->ad_emailaddress, $custom , $attachments, 'email');

                    $update['ad_sent'] = 1;
                    $update['ad_flag'] = 4;
                    $update['ad_dateemailsent'] = time();
                    $this->db->where('ad_id', $i->ad_id);
                    $this->db->update('invoices', $update);

                }
            }
        }
    }

    /**
     * Cronjob Time: 0/1 * * * *
     * Cronjob Time: At every minute from 0 through 59
     * Live Url: https://cladding2.rowe.ai/cron/generateNewReportPdfWithAttachment
     * Local Url: http://localhost/cladding-working-CLD-1610.1314/cron/generateNewReportPdfWithAttachment
     */
    public function generateNewReportPdfWithAttachment(){
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $status = false; $message = ''; $responseData = array();

        $whereArray = array('status' => 'Pending', 'category' => 32, 'template' => 'SBA');
        $ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getRecordByStatus($whereArray);
        if($ptQuoteReportDownloadsData){
            // _pre($ptQuoteReportDownloadsData);

            $id = $ptQuoteReportDownloadsData->id;
            $userId = $ptQuoteReportDownloadsData->user_id;
            $category = $ptQuoteReportDownloadsData->category;
            $adId = $ptQuoteReportDownloadsData->ad_id;
            $format = $ptQuoteReportDownloadsData->format;
            $template = $ptQuoteReportDownloadsData->template;
            $status = $ptQuoteReportDownloadsData->status;
            $formID = 13;

            $userData = $this->Usermodel->getInfoById($userId);

            $where = array('id' => $id);
            $crud = array('status' => 'In_Progress', 'updated_at' => get_current_date_time());
            $this->PtQuoteReportDownloadsModel->updateData($crud, $where);
            
            $returnFrom = 'getResponsePdfAfterMerge';
            $returnArray = $this->Quotesmodel->generate_quote($category, $adId, NULL, 0, true, false, $format, NULL, $template, $returnFrom);

            if($returnArray['status']){
                $status = true;

                $fileName = $returnArray['data']['fileName'];

                $randomString = $returnArray['data']['random_string'];

                $mainPdf = $returnArray['data']['main_pdf']; // URL

                $mainPdfPath = $returnArray['data']['main_pdf_path']; // PATH

                $responseData = $returnArray['data'];

                $where = array('id' => $id);
                $crud = array('random_string' => $randomString, 'filename' => $fileName, 'status' => 'Completed', 'updated_at' => get_current_date_time());
                $this->PtQuoteReportDownloadsModel->updateData($crud, $where);


                $notificationData = array(
                    'notificationText' => "Your ".$template." report pdf generated successfully",
                    'contactId' => 0,
                    'notificationType' => "report",
                    'relatedId' => $userId,
                );
                $this->_sendCompletedReportNotification($notificationData);


                $downloadReportsTabUrl = base_url('Post/downloadReport/'.$adId.'?formID='.$formID);
                $subjectEmail = $template." report pdf generated successfully";
                $messageEmail = $subjectEmail;
                $messageEmail .= "<br /><a href='".$downloadReportsTabUrl."' target='_blank'>Click here</a> to download pdf";

                $emailData = array(
                    'toEmail' => $userData->us_email,
                    'subjectEmail' => $subjectEmail,
                    'pathAttachment' => $mainPdfPath,
                    'messageEmail' => $messageEmail,
                );
                $this->_sendCompletedReportEmail($emailData);
            }
            else{
                $message = 'Failed to generate pdf';
            }

            $cronjobData = array(
                'status' => $status,
                'message' => $message,
                'data' => $responseData,
            );
            $cronjobDataArray = json_encode($cronjobData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $where = array('id' => $id);
            $crud = array('cronjob_data' => $cronjobDataArray, 'updated_at' => get_current_date_time());
            $this->PtQuoteReportDownloadsModel->updateData($crud, $where);
        }
        else{
            $message = "No records available at this time";
        }

        $resultData = array(
            'status' => $status, 'message' => $message, 'data' => $responseData,
        );
        _pre($resultData);
    }

    /**
     * Cronjob Time: 0/1 * * * *
     * Cronjob Time: At every minute from 0 through 59
     * Live Url: https://cladding2.rowe.ai/cron/generateFraewReportPdfWithAttachment
     * Local Url: http://localhost/cladding-working-main-branch/cron/generateFraewReportPdfWithAttachment
     */
    public function generateFraewReportPdfWithAttachment(){
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $status = false; $message = ''; $responseData = array();

        $whereArray = array('status' => 'Pending', 'category' => 56, 'template' => 'FRAEW');
        $ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getRecordByStatus($whereArray);
        if($ptQuoteReportDownloadsData){
            // _pre($ptQuoteReportDownloadsData);

            $id = $ptQuoteReportDownloadsData->id;
            $userId = $ptQuoteReportDownloadsData->user_id;
            $category = $ptQuoteReportDownloadsData->category;
            $adId = $ptQuoteReportDownloadsData->ad_id;
            $format = $ptQuoteReportDownloadsData->format;
            $template = $ptQuoteReportDownloadsData->template;
            $status = $ptQuoteReportDownloadsData->status;
            $formID = 13;

            $userData = $this->Usermodel->getInfoById($userId);

            $where = array('id' => $id);
            $crud = array('status' => 'In_Progress', 'updated_at' => get_current_date_time());
            $this->PtQuoteReportDownloadsModel->updateData($crud, $where);
            
            $returnFrom = 'getResponsePdfAfterMerge';
            $returnArray = $this->Quotesmodel->generate_quote($category, $adId, NULL, 0, true, false, $format, NULL, $template, $returnFrom);

            if($returnArray['status']){
                $status = true;

                $fileName = $returnArray['data']['fileName'];

                $randomString = $returnArray['data']['random_string'];

                $mainPdf = $returnArray['data']['main_pdf']; // URL

                $mainPdfPath = $returnArray['data']['main_pdf_path']; // PATH

                $responseData = $returnArray['data'];

                $where = array('id' => $id);
                $crud = array('random_string' => $randomString, 'filename' => $fileName, 'status' => 'Completed', 'updated_at' => get_current_date_time());
                $this->PtQuoteReportDownloadsModel->updateData($crud, $where);


                $notificationData = array(
                    'notificationText' => "Your ".$template." report pdf generated successfully",
                    'contactId' => 0,
                    'notificationType' => "report",
                    'relatedId' => $userId,
                );
                $this->_sendCompletedReportNotification($notificationData);


                $downloadReportsTabUrl = base_url('Post/downloadReport/'.$adId.'?formID='.$formID);
                $subjectEmail = $template." report pdf generated successfully";
                $messageEmail = $subjectEmail;
                $messageEmail .= "<br /><a href='".$downloadReportsTabUrl."' target='_blank'>Click here</a> to download pdf";

                $emailData = array(
                    'toEmail' => $userData->us_email,
                    'subjectEmail' => $subjectEmail,
                    'pathAttachment' => $mainPdfPath,
                    'messageEmail' => $messageEmail,
                );
                $this->_sendCompletedReportEmail($emailData);
            }
            else{
                $message = 'Failed to generate pdf';
            }

            $cronjobData = array(
                'status' => $status,
                'message' => $message,
                'data' => $responseData,
            );
            $cronjobDataArray = json_encode($cronjobData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $where = array('id' => $id);
            $crud = array('cronjob_data' => $cronjobDataArray, 'updated_at' => get_current_date_time());
            $this->PtQuoteReportDownloadsModel->updateData($crud, $where);
        }
        else{
            $message = "No records available at this time";
        }

        $resultData = array(
            'status' => $status, 'message' => $message, 'data' => $responseData,
        );
        _pre($resultData);
    }
    private function _sendCompletedReportNotification($notificationData){
        $notificationText = $notificationData['notificationText'];
        $contactId = $notificationData['contactId'];
        $notificationType = $notificationData['notificationType'];
        $relatedId = $notificationData['relatedId'];

        $this->Notificationmodel->addNotification($notificationText, $contactId, $notificationType, $relatedId);
    }
    private function _sendCompletedReportEmail($emailData){
        $toEmail = $emailData['toEmail'];
        $subjectEmail = $emailData['subjectEmail'];
        $pathAttachment = $emailData['pathAttachment'];
        $messageEmail = $emailData['messageEmail'];

        $fromEmail = $this->config->item('email');
        $fromName = $this->config->item('admin');
        
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

        $this->email->to($toEmail);
        $this->email->from($fromEmail, $fromName);

        $this->email->subject($subjectEmail);

        $this->email->message($messageEmail);    

        // $this->email->attach($pathAttachment);

        $this->email->send();
    }
    public function testEmail(){
        $this->load->library('email');

        $fromEmail = $this->config->item('email');
        $fromName = $this->config->item('admin');
        $toEmail = "developercoffeeshop@gmail.com";

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp-pulse.com';
        $config['smtp_user'] = 'Michael@platformjam.com';
        $config['smtp_pass'] = 'KEmFt87oiP';
        $config['smtp_port'] = 2525;

        $this->email->initialize($config);

        $this->email->from($fromEmail, $fromName);
        $this->email->to($toEmail);
        $this->email->subject('Email Subject');
        $this->email->message('Email Message');

        $a = $this->email->send();
        var_dump($a);
        exit;
    }

    public function downloadReportPdf($fileName, $randomString=''){
        if(!empty($fileName)){ 
            if(empty($randomString)){
                $filePath = FCPATH.'assets/quotes/'.$fileName;
            }
            else{
                $filePath = FCPATH."output/".$randomString."/".$fileName;
            }
            if(!empty($fileName) && file_exists($filePath)){ 
                // Define headers 
                header("Cache-Control: public"); 
                header("Content-Description: File Transfer"); 
                header("Content-Disposition: attachment; filename=$fileName"); 
                header("Content-Type: application/zip"); 
                header("Content-Transfer-Encoding: binary"); 
                 
                // Read the file 
                readfile($filePath); 
                exit; 
            }
            else{
                echo 'The file does not exist.';
            } 
        }
        else{
            echo 'The file does not exist.';
        }
        exit;
    }
}