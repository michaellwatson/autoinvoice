<?php
class Notificationmodel extends CI_Model {

    function __construct()
    {
        parent::__construct();
		$this->load->library('session');

		$this->data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		//print_r($this->data);
    }

    public function addNotification($notification_text, $contact_id, $notification_type, $related_id){
    	$insert['no_notification_text'] = $notification_text;
		$insert['no_contact_id'] = $contact_id;
		$insert['no_notification_type'] = $notification_type;
		$insert['no_related_id'] = $related_id;
		$insert['no_user_id'] = $related_id;
		$insert['no_time_stamp'] = strtotime('now');
		$insert['is_read'] = 0;
		$this->db->insert('notifications',$insert);
    }


    public function allNotifications($limit = NULL, $offset = NULL, $searchArray = array()){
		$this->db->select('*');

		$allStuff = false;
		$permissions = explode(',',$this->data['us_permissions']);
		if(!in_array('My_Stuff', $permissions)){
			$allStuff = true;
        }

		$this->db->join('pt_prospect', 'no_contact_id = pr_id');
		$this->db->join('pt_user', 'no_user_id = us_id');

		if($allStuff==true){
			$this->db->where('no_user_id',$this->data['us_id']);
		}
		$this->db->order_by('no_time_stamp', 'DESC');
		if(($limit==NULL)&&($offset==NULL)){
			$data = $this->db->get('pt_notifications')->result_array();
			return $data;
		}else{
			$data = $this->db->get('notifications', $limit, $offset)->result_array();	
			return $data;
		}
	}

	public function countNotifications($searchArray = array()) {

		$allStuff = false;
		$permissions = explode(',',$this->data['us_permissions']);
		if(!in_array('My_Stuff', $permissions)){
			$allStuff = true;
        }

        $this->db->select('no_id ,COUNT(*) as c');

        if($allStuff==true){
			$this->db->where('no_user_id',$this->data['us_id']);
		}

		$query = $this->db->get('notifications');
		$row = $query->row_array();
        return $row['c'];
	}

	// Retrieve unread notifications for a specific user
    public function get_unread_notifications($to_id) {
        $this->db->where('no_related_id', $to_id);
        $this->db->where('is_read', 0);
        $this->db->order_by('no_time_stamp', 'DESC');
        $this->db->join('user', 'user.us_id = notifications.no_user_id');
        $query = $this->db->get('notifications');
        $data = $query->result();
        //echo $this->db->last_query();
        return $data;
    }

	public function count_unread_notifications($to_id) {
        $this->db->where('no_related_id', $to_id);
        $this->db->where('is_read', 0);
        $data = $this->db->count_all_results('notifications');
        //echo $this->db->last_query();
        return $data;
    }
    
    public function get_notifications($user_id, $start, $length) {
        $this->db->where('no_related_id', $user_id);
        $this->db->limit($length, $start);
        $this->db->order_by('no_time_stamp', 'DESC');
        $query = $this->db->get('pt_notifications');
        return $query->result_array();
    }

    public function get_notifications_count($user_id) {
        $this->db->where('no_related_id', $user_id);
        return $this->db->count_all_results('pt_notifications');
    }

    public function get_notification_by_id($notification_id, $user_id) {
        $this->db->where('no_related_id', $user_id);
        $query = $this->db->get_where('pt_notifications', array('no_id' => $notification_id));
        return $query->row_array();
    }

    public function mark_as_read($notification_id, $user_id) {

    	if($notification_id == 'ALL'){
    		
    		$this->db->set('is_read', 1);
        	$this->db->where('no_related_id', $user_id); // Ensuring users can only mark their own notifications
        	return $this->db->update('pt_notifications');

    	}else{

    		$this->db->set('is_read', 1);
        	$this->db->where('no_id', $notification_id);
        	$this->db->where('no_related_id', $user_id); // Ensuring users can only mark their own notifications
        	return $this->db->update('pt_notifications');

    	}

    }
}