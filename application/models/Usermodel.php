<?php
class Usermodel extends CI_Model {

    function __construct()
    {
        parent::__construct();
		$this->load->library('session');
		$this->user_id = $this->session->userdata("us_id");
    }

    public function has_permission($name, $formid = NULL){ 
    	//$user = new UserEl();
		//print_r($user->find($this->user_id)->userpermissions);
		$this->db->where('user_id', $this->user_id);
		$this->db->where('permission', $name);
		$this->db->join('permissions', 'permission_id=permissions.id');
		if(!is_null($formid)){
			$this->db->where('form_id', $formid);
		}
    	$permissions = $this->db->get('users_permissions')->result_array();
    	if(sizeof($permissions)>0){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function get(){
    	$this->db->select('us_id, us_firstName, us_surname, us_company, us_joined, us_lastlogin, us_verified, us_blocked, roles.name as role_name');
    	$this->db->order_by('us_surname', 'ASC');
    	$this->db->join('roles', 'user.us_role=roles.id');
    	return $this->db->get('user')->result_array();
    }
    /*
    public function has_permission($permission){
    	$data = $this->Usermodel->selectUser($this->user_id);

    	$permissions = explode(',',$data['us_permissions']);
		if(!in_array($permission, $permissions)){
			return false;
		}else{
			return true;
		}
    }
	*/

	public function login($username, $password, $type = NULL) {

			$hash_password = hash_hmac('sha1',$password, $this->config->item('encryption_key'));
			//echo $hash_password;
			if($type == NULL){
				$sql = "SELECT * FROM pt_user WHERE (us_email = ? or us_username = ? ) AND us_password = ? AND us_verified = '1' LIMIT 1"; 
				$query = $this->db->query($sql, array($username, $username, $hash_password));
			}else{
				$sql = "SELECT * FROM pt_user WHERE (us_email = ? or us_username = ? ) AND us_password = ? and us_usertype=? AND us_verified = '1' LIMIT 1"; 
				$query = $this->db->query($sql, array($username, $username, $hash_password, $type));
			}
			//echo $this->db->last_query();
			//$this->session->sess_destroy();
			foreach($query->result_array() as $row){
				//print_r($row);
				//exit();
				//Create a fresh, brand new session
				//$this->session->sess_create();
				$row['logged_in'] = true;
				$this->session->set_userdata($row);   

				//Set logged_in to true
				//$this->session->set_userdata(array('logged_in' => true)); 
				//update last login
				//echo $this->db->last_query();
				//exit(); 
				$ins = array('us_lastlogin'=>date("Y")."-".date("m")."-".date("d"),'us_useragent'=>$_SERVER['HTTP_USER_AGENT']);
				$this->db->update('user', $ins, array('us_id' => $row['us_id']));

				if($this->input->post('remember')=='1'){
					$cookie = array(
						'name'   => 'email',
						'value'  => $username,
						'expire' =>  5184000,
						'secure' => false
					);
					$this->input->set_cookie($cookie); 
	
					$cookie = array(
						'name'   => 'password',
						'value'  => $hash_password,
						'expire' =>  5184000,
						'secure' => false
					);
					$this->input->set_cookie($cookie); 		
				}
				
				return true;
				exit();
			}
			
			return false;
	}
	
	public function selectUser($id) {
        $sql = "SELECT * FROM pt_user WHERE us_id = ? LIMIT 1"; 
		$query = $this->db->query($sql, array($id));
		foreach($query->result_array() as $row){
			return $row;
        }
        return false;
	}
	
	public function checkUserExists($id){
		//echo $id;
		$this->db->select('*');
		$this->db->where('us_id', $id);
		$data = $this->db->get('user')->row_array();
		//echo $this->db->last_query();
		//var_dump($data);
		if(sizeof($data)>0){
			return $data;
		}else{
			return false;
		}
	}
	
	public function checkUserExistsEmail($email){
		//echo $id;
		$this->db->select('*');
		$this->db->where('us_email', $email);
		$data = $this->db->get('user')->row_array();
		//echo $this->db->last_query();
		//var_dump($data);
		if(is_array($data)){
			if(sizeof($data)>0){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function checkUserExistsUsername($email){
		//echo $id;
		$this->db->select('*');
		$this->db->where('us_username', $email);
		$data = $this->db->get('user')->row_array();
		//echo $this->db->last_query();
		//var_dump($data);
		if(is_array($data)){
			if(sizeof($data)>0){
				return $data;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public function allUsers($limit = NULL, $offset = NULL, $hasTFOP = NULL, $promoCode = NULL, $searchArray = array()){
		//var_dump($searchArray);
		$this->db->select('*');

		//print_r($searchArray);

		foreach($searchArray as $s){
			switch($s[0]){
				case 'userType':
					switch($s[1]){
						case 1:
							$this->db->where('us_usertype',0);
						break;
						case 2:
							$this->db->where('us_usertype',1);
						break;
					}
				break;
				case 'verifiedStatus':
					switch($s[1]){
						case 1:
							$this->db->where('us_verified',1);
						break;
						case 2:
							$this->db->where('us_verified',0);
						break;
					}
				break;
				case 'dateCreated':
					switch($s[1]){
						case 1:
							$this->db->order_by('us_joined','DESC');
						break;
						case 2:
							$this->db->order_by('us_joined','ASC');
						break;
					}
				break;
				case 'searchByName':
					$this->db->like('CONCAT_WS(" ", us_firstName, us_surname)',$s[1]);
				break;

			}
		}
		if(($limit==NULL)&&($offset==NULL)){
			$data = $this->db->get('user')->result_array();
			//echo '<!--';
			//echo $this->db->last_query();
			//echo '-->';
			return $data;
		}else{
			$data = $this->db->get('user', $limit, $offset)->result_array();	
			//echo '<!--';
			//echo $this->db->last_query();
			//echo '-->';
			//echo $this->db->last_query();
			return $data;
		}
	}
	public function countUsers($promoCode = NULL, $searchArray = array()) {
        //$sql = "SELECT COUNT(*) as c FROM f4_user WHERE us_usertype!=2"; 
        $this->db->select('COUNT(*) as c');
        $this->db->where('us_usertype !=',2);

		foreach($searchArray as $s){
			switch($s[0]){
				case 'userType':
					switch($s[1]){
						case 1:
							$this->db->where('us_usertype',0);
						break;
						case 2:
							$this->db->where('us_usertype',1);
						break;
					}
				break;
				case 'verifiedStatus':
					switch($s[1]){
						case 1:
							$this->db->where('us_verified',1);
						break;
						case 2:
							$this->db->where('us_verified',0);
						break;
					}
				break;
				case 'dateCreated':
					switch($s[1]){
						case 1:
							$this->db->order_by('us_joined','DESC');
						break;
						case 2:
							$this->db->order_by('us_joined','ASC');
						break;
					}
				break;
				case 'searchByName':
					$this->db->like('CONCAT_WS(" ", us_firstName, us_surname)',$s[1]);
				break;
				case 'searchByCompany':
					$this->db->like('us_company',$s[1]);
				break;
			}
		}

		$query = $this->db->get('user');
		//echo $this->db->last_query();
		$row = $query->row_array();
        return $row['c'];
	}


	public function setUpdateLoginEntry($uid, $session_id){
		$this->db->select('*');
		$this->db->where('li_session_id', $session_id);
		$data = $this->db->get('logged_in_log')->result_array();
		//echo $this->db->last_query();
		//var_dump($data);
		//check if the user exists
		if(sizeof($data)>0){
			/*
			$update = array('li_timestamp'=>time());
			$this->db->where('li_session_id',$session_id);
			$this->db->update('f4_logged_in_log', $update);
			*/
			$this->db->query("UPDATE IGNORE pt_logged_in_log SET li_timestamp='".time()."' WHERE li_session_id='".$session_id."'");
		}else{
			//$insert = array('li_uid'=>$uid,'li_timestamp'=>time(),'li_session_id'=>$session_id);
			//$this->db->insert('f4_logged_in_log', $insert);
			$this->db->query("INSERT IGNORE pt_logged_in_log (li_timestamp, li_session_id, li_uid)VALUES('".time()."','".$session_id."','".$uid."')");
		}
	}

	public function deleteLogs($session_id){
		//$this->db->where('li_session_id', $session_id);
		//$this->db->delete('logged_in_log'); 
		//echo $this->db->last_query();
	}

	public function deleteOldLogs(){
		//$this->db->where('li_timestamp <', strtotime("-30 minutes"));
		//$this->db->delete('logged_in_log'); 
		//echo $this->db->last_query();
	}
	public function getInfoById($id){
        $this->db->select('*');
        $this->db->from('pt_user');
        $this->db->where('us_id', $id);
        $response_data = $this->db->get()->row();
        return $response_data;
    }
}