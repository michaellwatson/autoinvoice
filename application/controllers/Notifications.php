<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends My_Controller {

	public function __construct()
    {
     	parent::__construct();
     	$this->load->model('Notificationmodel');
    }
    public function view($id = NULL){

        if(!is_null($id)){
            $data['id'] = $id;
        }
        $this->load->view('dashboard/header',$data);
        $this->load->view('forms/notifications',$data);
        $this->load->view('dashboard/footer',$data);

    }

   	public function unread_count() {

        $user_id = $this->session->userdata("us_id"); // Assume user_id is stored in session data
        if(!$user_id) {
            // Handle unauthenticated state e.g., redirect, throw error, etc.
        }

        $count = $this->Notificationmodel->count_unread_notifications($user_id);
        
        // Example: Return as JSON response - useful for AJAX calls
        echo json_encode(['unread_count' => $count]);
    }

    public function unread_list() {
        $user_id = $this->session->userdata("us_id"); // Assuming user_id is stored in session data
        
        // Check for authenticated user
        if(!$user_id) {
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }

        // Fetching notifications and sending as JSON
        $notifications = $this->Notificationmodel->get_unread_notifications($user_id);
        if(empty($notifications)){
            
            $notifications[] = array('title' => 'No new notifications', 'body' => '', 'us_firstname' => '', 'us_surname' => '');
            echo json_encode(['notifications' => $notifications]);
        
        }else{
            // _pre($notifications);
            echo json_encode(['notifications' => $notifications]);
        }
        
    }

    public function mark_read() {
        $user_id = $this->session->userdata('us_id'); // Assuming user_id is stored in session data
        
        // Check for authenticated user
        if(!$user_id) {
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }
        
        $notification_id = $this->input->post('notification_id');

        // Validating notification_id
        if (!$notification_id) {
            echo json_encode(['error' => 'Invalid notification ID']);
            return;
        }

        // Updating the notification
        $success = $this->Notificationmodel->mark_as_read($notification_id, $user_id);

        // Responding to the AJAX request
        if($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to mark notification as read']);
        }
    }

    public function get_notifications_json() {
        $user_id = $this->session->userdata('us_id'); 
        
        if(!$user_id) {
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }

        $draw = intval($this->input->get("draw"));  // Draw counter. 
        $start = intval($this->input->get("start"));  // Starting point for data display.
        $length = intval($this->input->get("length"));  // Number of records to display.

        // Optionally: Implement search and order by functionalities using $this->input->get("search")
        // and $this->input->get("order"). 

        $notifications = $this->Notificationmodel->get_notifications($user_id, $start, $length);
        $total_notifications = $this->Notificationmodel->get_notifications_count($user_id);

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_notifications,
            "recordsFiltered" => $total_notifications,
            "data" => $notifications
        );

        echo json_encode($output);
    }

    public function view_notification($id){

        $user_id = $this->session->userdata('us_id'); // Assuming user_id is stored in session data
        
        // Check for authenticated user
        if(!$user_id) {
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }
        
        $notification = $this->Notificationmodel->get_notification_by_id($id, $user_id);
        $this->Notificationmodel->mark_as_read($id, $user_id);
        //print_r($notification);
        if(empty($notification)) {
            echo json_encode(['error' => 'Notification not found']);
        } else {
            echo json_encode(['success' => $notification]);
        }

    }
}