<?php
class Statisticsmodel extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Advertcategorymodel');
    }

    function report_percent_complete($default_table, $id){

		$this->default_table = $default_table;
		$listing = $this->Advertcategorymodel->getAdvert($id, $default_table);
		//print_r($listing);
		$default_category_table = $default_table.'_categories';
		$total_cats = 0;			

		$show_roles_menu = 1;

		//category is normally 1
		$category = 1;
		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$this->db->order_by('ad_order','ASC');
		$this->db->order_by('ad_id','ASC');
		$result = $this->db->get($default_category_table)->result_array();
		//print_r($result);

		if(is_array($result)){
			$total_cats = sizeof($result);
		}
			

        $this->db->select('ad_role,name');
		$this->db->from($default_category_table);
		$this->db->join('roles', 'id=ad_role');
		$this->db->group_by('ad_role');
		$query = $this->db->get();
		$data['roles_list'] = $query->result();
		$roles_list = $data['roles_list'];

		$cat_array = [];
		$role_array = [];
		$parent_roles = $this->db->from($default_category_table)->where('ad_parent', 0)->get()->result();

		foreach($parent_roles as $p){

			$child_roles = $this->db->from($default_category_table)->where('ad_parent', $p->ad_id)->get()->result();

			foreach($child_roles as $c){
				$cat_array[$p->ad_name][] = array($c->ad_id, $c->ad_name, $c->ad_role);
			}
			
		}

		$cat = [];
		$fields_list = [];
		$cat_role = [];
		foreach($cat_array as $k => $v){
			foreach($v as $p){
				//echo $p[0].' '.$default_table;
				$cat[$p[0]] = '<b>'.$k.'</b> -> '.$p[1].' ('.$p[0].')';

				//$fields = $this->db->get_where('advert_fields', ['adv_category' => $p[0], 'adv_table'=> $this->default_table])->result();
				$fields = Advertfieldsmodel::where('adv_category', '=',  $p[0])->where('adv_table', '=', 'pt_'.$default_table)->where('adv_ignore_in_sections_report', '=', 0)->get();
				$fields_list[$p[0]] = $fields;
				$cat_role[$p[0]] = $p[2];

			}
		}

		//print_r($fields_list);
		$data['cats'] = $cat;
		$data['cat_role'] = $cat_role;
		$data['fields_list'] = $fields_list;
		$cats = $data['cats'];

		$overall_answers = 0;
		$overall_questions = 0;
		//print_r($listing);
		if(is_array($listing)){

			if($show_roles_menu > 0){

				foreach($roles_list as $r){


			    	$total_questions = [];
			    	$total_filled = [];

			    	
			    	foreach($cats as $k => $c){

						$filled = 0;

		    			if($cat_role[$k] == $r->ad_role){

							foreach($fields_list[$k] as $f){
								//print_r($f);
								if($f->fieldType->fi_type == 'image'){
									//SELECT * FROM `pt_images` WHERE up_field_id=91;
									//echo $f->adv_id.'#';
									$image = Imagemodel::where('up_field_id', '=', $f->adv_id)->where('up_entry_id', '=', $listing['ad_id'])->where('up_table', '=', $this->default_table)->get();
									//print_r($image);
									if(!empty((array)$image)){

										$filled++;
							 			$total_filled[$r->ad_role]++;
							 			
									}
								}else if($listing['ad_'.$f->adv_column] != ''){
							 		$filled++;
							 		$total_filled[$r->ad_role]++;
							 	}
							}
							//print_r($total_filled);

		    				$total_questions[$r->ad_role]+=count($fields_list[$k]);

							if(count($fields_list[$k]) > 0){
			
								$per = ($filled/count($fields_list[$k]))*100;

		    				}
						}
		    		}
		    		
		    		//if(!empty($total_filled)){ 
										
						//$overall_answers = 0;
						//$overall_questions = 0;
						//echo '##';

						$overall_answers += $total_filled[$r->ad_role];
						$overall_questions += $total_questions[$r->ad_role];
										
		    		//} 

				    if($total_questions[$r->ad_role] > 0){
						$per = ($total_filled[$r->ad_role]/$total_questions[$r->ad_role])*100;
					}

			    }//foreach roles end 

			    if($overall_answers > 0){
					$per = ($overall_answers/$overall_questions)*100;
				}
				//echo $overall_answers.'/'.$overall_questions.'<br>';
				return round($per);
			}
		} 
	}
}