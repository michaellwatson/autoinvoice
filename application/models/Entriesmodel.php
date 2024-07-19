<?php
class Entriesmodel extends CI_Model {

    function __construct()
    {
        parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');

		$this->default_table 			= $this->config->item('default_table');
		$this->default_category_table 	= $this->config->item('default_category_table');
		$this->default_table_name 		= $this->config->item('default_table_name');
		
		$this->default_category_depth = '';

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
		$this->data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		$this->load->model('Quotesmodel');
    }

    public function getDatasourceColumns(){
    	$this->db->select('adv_column');
    	$this->db->where('adv_datasourceId !=','');
    	$this->db->where('adv_table',$this->default_table);
    	$datasourceColumns = $this->db->get('advert_fields')->result_array();
    	//echo $this->db->last_query();
    	//var_dump($datasourceColumns);
    	return $datasourceColumns;
    }
	public function allEntries($limit = NULL, $offset = NULL, $searchArray = array()){
		//get the vat price

		$allStuff = false;
		$permissions = explode(',',$this->data['us_permissions']);
		if(!in_array('My_Stuff', $permissions)){
			$allStuff = true;
        }

		$this->db->select('se_value');

		$this->db->where('se_key','vat');
		$vatRate = $this->db->get('pt_settings')->row_array();
		//echo $this->db->last_query();
		$vatRate = ($vatRate['se_value']+100)/100;

		//var_dump($searchArray);
		$datasourceColumns = $this->getDatasourceColumns();
		$this->db->reset_query();
		//print_r($datasourceColumns);
		$this->db->select('*');
		//$this->db->join('pt_prospect', 'ad_contact=pr_id', 'inner');
		
		$this->db->join('pt_quote_history', 'ad_quote_id = quh_id', 'left outer');
		/*
		$otherdb->where('sk_id',$data['listing']['ad_skeletons']);
		$skeletons = $otherdb->get('dm_skeletons')->row_array();
		$v = explode(',',$skeletons['sk_properties']);
		*/
		$this->db->join('ester.dm_skeletons', 'ad_skeletons=sk_id', 'INNER');
		$this->db->join('ester.dm_houses', 'sk_primary=id', 'INNER');
		$this->db->join('ester.dm_client', 'instructing_client=c_id', 'INNER');

		$role = $this->session->userdata('us_role');
		$company_id = $this->session->userdata('us_company_id');
		//0 is admins
		if($role!=0){
			//$this->db->join('pt_user', 'instructing_client=us_company_id', 'INNER');
			$this->db->where('instructing_client', $company_id);
		}
		
		$this->db->where('quh_order !=', 1);

		if($allStuff==true){
			//$this->db->where('quh_gen_by_id',$this->data['us_id']);
		}

		$this->db->order_by('ad_last_update','DESC');
		foreach($searchArray as $s){
			switch($s[0]){
				case 'contactID':
					$this->db->where('ad_contact',$s[1]);
				break;
				case 'address':
					$this->db->like('ad_SiteAddress',$s[1]);
				break;
				/*
				case 'quh_order':
					$this->db->where('quh_order',$s[1]);
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
				*/
			}
		}


		if(($limit==NULL)&&($offset==NULL)){
			//echo '##'.$this->default_table.'##';
			$data = $this->db->get($this->default_table)->result_array();
			//echo $this->db->last_query();
			$i = 0;
			foreach($data as $d){
				/*
				$cost = 0;
				$retail = 0;
				$vat = 0;
				foreach($datasourceColumns as $col){
					//echo $d['ad_'.$col['adv_column']];
					$price = $this->Advertcategorymodel->getDataValuePricing($d['ad_'.$col['adv_column']]);
					//print_r($price);
					$cost+=$price['ds_cost_price'];
					$retail+=$price['ds_retail_price'];
					$vat+=$price['ds_vat_price'];
				}

				$additionals = $this->Quotesmodel->get_Additional_Items($d['ad_id'], $this->default_table,1);
				$discounts = $this->Quotesmodel->get_Additional_Items($d['ad_id'], $this->default_table,0);

				$data[$i]['cost']=$cost;
				$data[$i]['retail']=$retail+$additionals-$discounts;


				$data[$i]['vat']=$vat+($additionals*$vatRate)-$discounts;
				//$data[$i]['vat']=($additionals*$vatRate);
				*/
				$order = $this->Ordersmodel->getQuoteInfo($d['ad_quote_id']);
				$data[$i]['cost'] = $order['quh_cost_price'];
				$data[$i]['retail'] = $order['quh_retail_price'];
				$data[$i]['vat'] = $order['quh_vat_price'];
				//print_r($order);
				$i++;

			}
			//var_dump($data);
			//echo $this->db->last_query();
			return $data;
		}else{
			$data = $this->db->get($this->default_table, $limit, $offset)->result_array();	
			//echo $this->db->last_query();
			return $data;
		}
	}

	public function countEntries($searchArray = array()) {
        //$sql = "SELECT COUNT(*) as c FROM pt_user WHERE us_usertype!=2"; 
        $this->db->select('ad_id ,COUNT(*) as c');

        
		foreach($searchArray as $s){
			switch($s[0]){
				case 'contactID':
					$this->db->where('ad_contact',$s[1]);
				break;
				case 'address':
					$this->db->like('ad_SiteAddress',$s[1]);
				break;
				/*
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
				break;*/
			}
		}

		$query = $this->db->get($this->default_table);
		//echo $this->db->last_query();
		$row = $query->row_array();
        return $row['c'];
	}
}
