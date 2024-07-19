<?php
setlocale(LC_ALL, 'en_US.UTF8');
class Advertcategorymodel extends CI_Model {
	//mailmodel get it?

    function __construct()
    {
        parent::__construct();

        $this->formID = 11;
        $this->default_table 			= $this->config->item('default_table');
		$this->default_category_table 	= $this->config->item('default_category_table');
		$this->default_table_name 		= $this->config->item('default_table_name');
		$this->default_category_depth = '';

		$formID = isset($_GET['formID']) ? $_GET['formID'] : '';
		if($formID!=''){
			if(is_numeric($_GET['formID'])){
				$tableData = $this->formsTables($_GET['formID']);

				$_SESSION['ft_database_table'] = $tableData['ft_database_table'];
				$_SESSION['ft_categories'] = $tableData['ft_categories'];
				$_SESSION['ft_name'] = $tableData['ft_name'];
				$_SESSION['formID'] = $_GET['formID'];

				$this->default_table = $tableData['ft_database_table'];
				$this->default_category_table = $tableData['ft_categories'];
				$this->default_table_name = $tableData['ft_name'];
			}
		}

		if(isset($_SESSION['formID'])){
			if($_SESSION['formID']!=''){
				$this->formID = $_SESSION['formID'];
			}
		}
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
    }
	
	public function toAscii($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return $clean;
	}

	public function getColumnById($fid){
		$this->db->select('adv_column');
		$this->db->where('adv_id',$fid);
		$data = $this->db->get('advert_fields')->row_array();	
		return $data['adv_column'];
	}

	public function getAdvertDetails($adId, $category){
		$fields = $this->getFields($category, false, NULL, true, true, false);
		$i = 0;
		$j = 0;
		$sql='';
		$sql2='';
		$sql.='SELECT ';
		//var_dump($fields);
		foreach($fields as $f){
			if($f['adv_datasourceId']!=0){
				$sql2.=' LEFT JOIN';
		  		$sql2.=' '.$this->db->dbprefix.'ds_values  AS p'.$i.' ';
			    $sql2.=' ON  p'.$i.'.ds_id = ad_'.$f['adv_column'].' ';
				$i++;
			}
		}
		foreach($fields as $f){
			if($f['adv_datasourceId']!=0){
				$sql.=' p'.$j.'.ds_value as '.$f['adv_column'].' ';
				$j++;
				if($j<$i){
					$sql.=',';
				}
			}
		}
		$sql.=' FROM '.$this->default_table.' '.$sql2;
		$sql.=' WHERE ad_id="'.$adId.'"';

		//echo '<br><br>';
		$data = $this->db->query($sql)->row_array();
		//echo '<!--';
		//echo $this->db->last_query();
		//echo '-->';
		//var_dump($data);
		return $data;
	}
	
	public function getDataValuePricing($dsId){
		if($dsId!=''){

			$this->db->select('ds_value, ds_cost_price, ds_retail_price, ds_vat_price');
			$this->db->where('ds_id',$dsId);
			$data = $this->db->get('ds_values')->row_array();
			//echo $this->db->last_query().'#';
			if(is_array($data)){
				if(sizeof($data)==0){
					return false;
				}else{
					return $data;
				}
			}
		}else{
			return false;
		}
	}

	public function getDataValue($dsId){
		$this->db->select('ds_value');
		$this->db->where('ds_id',$dsId);
		$data = $this->db->get('ds_values')->row_array();
		//echo $this->db->last_query();
		return $data;
	}
	
	public function getDataElement($dsId){
		$this->db->select('*');
		$this->db->where('da_id',$dsId);
		return $this->db->get('pt_datasources')->row_array();
	}

	public function getAdvert($adId, $table = NULL){
		$this->load->library('session');
		
		$CI =& get_instance();
		$CI->load->database();
		$CI->db->select('*');
		
		$CI->db->where('ad_id',$adId);
		//echo $table.'##@@';
		if($table==NULL){
			return $CI->db->get($this->default_table)->row_array();
		}else{

			$result = $CI->db->get($table)->row_array();
			//echo $this->db->last_query();
			return $result;
		}
	}

	public function checkAdvertOwner($adId, $ownerId){
		$this->db->select('*');
		$this->db->where('ad_id',$adId);
		$this->db->where('ad_userid',$ownerId);
		$listing = $this->db->get($this->default_table)->result_array();
		//echo $this->db->last_query();
		if(sizeof($listing)>0){
			//you own the listing
			return true;	
		}else{
			//stop tryng to edit other peoples listings, wanna be hacker pfft
			return false;	
		}
	}

	public function formsTables($ID = NULL){
		if($ID==NULL){
			$this->db->select('*');
			$this->db->where('ft_settings', 0);
			$formsTables = $this->db->get('forms_tables')->result_array();	
			//echo $this->db->last_query();
		}else{
			$this->db->select('*');
			$this->db->where('ft_id', $ID);
			$formsTables = $this->db->get('forms_tables')->row_array();	
		}
		return $formsTables;
	}

	public function getCategories($categoryId){
		//echo $this->default_category_table.'##';
		//exit();
		$this->db->select('*');
		$this->db->where('ad_parent',$categoryId);
		$this->db->order_by('ad_order','ASC');
		$categories = $this->db->get($this->default_category_table)->result_array();	
		//echo $this->db->last_query();
		return $categories;
	}

	public function getCategory($categoryId){
		$this->db->select('*');
		$this->db->where('ad_id',$categoryId);
		$category = $this->db->get($this->default_category_table)->row_array();	
		return $category;
	}

	public function getFieldTypes(){
		
		$this->db->select('*');	
		return $this->db->get('field_type')->result_array();
	}
	public function dataSourceExists($name){
		$this->db->select('*');	
		$this->db->where('da_name',$name);
		$data = $this->db->get('datasources')->result_array();
		if(sizeof($data)>0){
			return true;	
		}else{
			return false;
		}
	}
	public function dataSourceExistsId($id){
		$this->db->select('*');	
		$this->db->where('da_id',$id);
		$data = $this->db->get('datasources')->result_array();
		if(sizeof($data)>0){
			return true;	
		}else{
			return false;
		}
	}
	public function dsValueExistsInDs($value,$dsid){
		$this->db->select('*');	
		$this->db->where('ds_value',$value);
		$this->db->where('ds_ds_id',$dsid);
		$data = $this->db->get('ds_values')->result_array();
		if(sizeof($data)>0){
			return true;	
		}else{
			return false;
		}
	}
	public function breadcrumbBuilder($categoryId){
		if($categoryId>0){
			$this->db->select('*');
			$this->db->where('ad_id',$categoryId);
			$category = $this->db->get($this->default_category_table)->row_array();	
			$string = '';
			$string.='<li><a href="'.base_url('admin/advertAdmin/'.$category['ad_id']).'">'.$category['ad_name'].'</a></li>';
			echo $string;
			$this->breadcrumbBuilder($category['ad_parent']);
		}
	}


	public function getData($datasourceId, $column = NULL){

		//check if this is a numeric bas
		$this->db->select('*');
		$this->db->where('ds_ds_id',$datasourceId);
		$this->db->limit(1);
		$values = $this->db->get('ds_values')->row_array();
		//echo $this->db->last_query();
		//var_dump($values);
		//echo '<!--'.$values['ds_value'].'#-->';
		if (isset($values['ds_value'])) {
			$int = (int)preg_replace('/\D/', '', $values['ds_value']);
			
			if($int>0){
				//echo '<!--'.$int.'#-->';
				/*
				$this->db->select('*');
				$this->db->where('ds_ds_id',$datasourceId);
				$this->db->order_by(' CAST(ds_value AS int) ','DESC');
				$data = $this->db->get('ds_values')->result_array();
				*/
				//echo '<!--'.$column.'-->';
				$order = 'DESC';

				$query = "SELECT * FROM pt_ds_values WHERE ds_ds_id='$datasourceId' ORDER BY CAST(ds_value AS decimal) ".$order;
				$data = $this->db->query($query)->result_array();
			//	echo '<!--'.$this->db->last_query().'-->';
				return $data;
			}else{
				$this->db->select('*');
				$this->db->where('ds_ds_id',$datasourceId);
				$this->db->order_by('ds_value', 'ASC');
				return $this->db->get('ds_values')->result_array();
			}	
		}else{
			return [];
		}
	}
	public function getSecondDropDown($category){
		
		$this->db->select('*');
		$this->db->where('adv_field_type',3);
		$this->db->where('adv_category',$category);
		$result = $this->db->get('advert_fields')->result_array();		
		//echo $this->db->last_query();
		return $result;
	}
	public function getFieldHTML($f, $categoryId = NULL, $frontend = false, $listing = NULL, $customName = NULL, $customData=''){
			//var_dump($listing);
			$html = '';
			$html2 = '';
			switch($f['fi_type']){
				case 'signature':
					$html.='<div data-id="'.str_replace(' ', '_', $f['adv_column']).'" class="signature-pad">';
	    			$html.='<div class="signature-pad--body">';
	      			$html.='<canvas></canvas>';
	    			$html.='</div>';
	    			$html.='<div class="signature-pad--footer">';
	      			$html.='<div class="description">Sign above</div>';
					$html.='<div class="signature-pad--actions">';
	        		$html.='<div>';
	          		$html.='<button type="button" class="button clear" data-action="clear">Clear</button>';
	        		$html.='</div>';
	      			$html.='</div>';
	    			$html.='</div>';
					$html.='</div>';

					$base64 = '';
					//print_r($listing);
					$path = '' ;
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'], $listing)){
							$path = $listing['ad_'.$f['adv_column']];
						}
					}
				    if($path!=''){
				      $type = pathinfo($path, PATHINFO_EXTENSION);
				      $data = file_get_contents($path);
				      $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				      //$img = '<div class="holder '.$f['adv_column'].'_edit"><img src="'.$base64.'"><br><a href="javascript:show(\''.$f['adv_column'].'\');"/>Edit</a></div><br>';
				    }

					$html.= '<textarea name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" style="display:none;">'.$base64.'</textarea>';
				break;
				case 'radioBool':
					/*
					$html.='<div style="padding-top:5px;">';
					$html.='<input type="radio" name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" class="'.$f['adv_column'].'" value="1"';
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							if($listing['ad_'.$f['adv_column']]==1){
								$html.=' checked=\"checked\"';	
							}
						}
					}
					$html.='> Yes  <input type="radio" name="'.$f['adv_column'].'" value="0" class="'.$f['adv_column'].'"';
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							if($listing['ad_'.$f['adv_column']]==0){
								$html.=' checked=\"checked\"';
							}
						}
					}
					$html.='> No';
					$html.='</div>';
					*/
					$html.='<div class="inline"><div class="rdio rdio-success" style="float: left;padding-right: 15px;">';
					$html.='<input type="radio" name="'.$f['adv_column'].'" id="'.$f['adv_column'].'_1" class="'.$f['adv_column'].'" value="1"';
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							if($listing['ad_'.$f['adv_column']]==1){
								$html.=' checked="checked"';	
							}
						}
					}
					$html.='> <label for="'.$f['adv_column'].'_1">Yes</label></div><div class="rdio rdio-success" style="float: left;padding-right: 15px;"><input type="radio" name="'.$f['adv_column'].'" id="'.$f['adv_column'].'_0" value="0" class="'.$f['adv_column'].'"';
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							if($listing['ad_'.$f['adv_column']]==0){
								$html.=' checked="checked"';
							}
						}
					}
					$html.='> <label for="'.$f['adv_column'].'_0">No</label>';
					$html.='</div></div>';

				break;
				case 'radioValues':
					if($f['adv_datasourceId']!=0){
						$ds = $this->getData($f['adv_datasourceId']);
						$html.='<div>';
						foreach($ds as $d){
							$html.='<div class="rdio rdio-success" style="float: left;padding-right: 15px;"><input type="radio" name="'.$f['adv_column'].'" class="'.$f['adv_column'].'" id="'.$f['adv_column'].'_'.$d['ds_id'].'" value="'.$d['ds_id'].'"';
							if(is_array($listing)){
								if(array_key_exists('ad_'.$f['adv_column'],$listing)){
									if($listing['ad_'.$f['adv_column']]==$d['ds_id']){
										$html.=' checked=\"checked\"';
									}
								}
							}
							$html.='> <label for="'.$f['adv_column'].'_'.$d['ds_id'].'">'.$d['ds_value'].'</label>  </div>';
						}
						$html.='</div>';
					}
				break;
				case 'dropdown':
					if($f['adv_datasourceId']!=0){
						if($customName!=NULL){
							$html = '<select id="'.$customName.'" name="'.$customName.'" class="form-control">';
						}else{
							$html = '<select id="'.$f['adv_column'].'" name="'.$f['adv_column'].'" class="form-control">';
						}
						$html.='<option value="">Please Select</option>';

						$ds = $this->getData($f['adv_datasourceId'], $f['adv_column']);
						foreach($ds as $d){
							$html.='<option value="'.$d['ds_id'].'"';
							if(is_array($listing)){
								if(array_key_exists('ad_'.$f['adv_column'],$listing)){
									if($listing['ad_'.$f['adv_column']]==$d['ds_id']){
										$html.=' selected=\"selected\"';
									}
								}
							}
							$html.='>'.$d['ds_value'].'</option>';
						}
						$html.='</select>';
						
						if((int)$this->session->userdata("us_role")===1){
							$html.='<a href="javascript:editDS('.$f['adv_datasourceId'].', '.$f['adv_id'].');"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
						}
					}else if($f['adv_linkedtableid']!=0){

						if($customName!=NULL){
							$html = '<select id="'.$customName.'" name="'.$customName.'" class="form-control">';
						}else{
							$html = '<select id="'.$f['adv_column'].'" name="'.$f['adv_column'].'" class="form-control">';
						}
						$html.='<option value="">Please Select</option>';
						$table = FormsTablesmodel::findOrFail($f['adv_linkedtableid']);
						//get first column
						if($table->ft_is_users==1){

							//admin
							if((int)$this->session->userdata("us_role")==1){

								//$field = $this->db->where('adv_table', $table->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
								$ds = $this->db->get($table->ft_database_table)->result_array();

								foreach($ds as $d){
									$html.='<option value="'.$d['us_id'].'"';
									if(is_array($listing)){
										if(array_key_exists('ad_'.$f['adv_column'],$listing)){
											if($listing['ad_'.$f['adv_column']]==$d['us_id']){
												$html.=' selected=\"selected\"';
											}
										}
									}
									$html.='>('.$d['us_id'].') '.$d['us_firstName'].' '.$d['us_surname'].'</option>';
								}
								$html.='</select>';

							}else{
								//non admins can't pick this user field
								$html = '';

								if($customName!=NULL){
									$html = '<input type="hidden" id="'.$customName.'" name="'.$customName.'" ';
								}else{
									$html = '<input type="hidden" id="'.$f['adv_column'].'" name="'.$f['adv_column'].'" ';
								}
								
								if($listing['ad_'.$f['adv_column']]==NULL){
									$html.= ' value="'.$this->data['user']['us_id'].'">';
									$html.= '<label class="control-label">('.$this->data['user']['us_id'].') '.$this->data['user']['us_firstName'].' '.$this->data['user']['us_surname'].'</label>';
								}else{
									$entry_user = $this->Usermodel->selectUser($listing['ad_'.$f['adv_column']]);

									$html.= ' value="'.$listing['ad_'.$f['adv_column']].'">';
									$html = '<label class="control-label">('.$entry_user['us_id'].') '.$entry_user['us_firstName'].' '.$entry_user['us_surname'].'</label>';
								}
								
							}

						}else{

							$field = $this->db->where('adv_table', $table->ft_database_table)->limit(1)->order_by('adv_order', 'ASC')->get('pt_advert_fields')->row();
							$ds = $this->db->get($table->ft_database_table)->result_array();

							foreach($ds as $d){
								$html.='<option value="'.$d['ad_id'].'"';
								if(is_array($listing)){
									if(array_key_exists('ad_'.$f['adv_column'],$listing)){
										if($listing['ad_'.$f['adv_column']]==$d['ad_id']){
											$html.=' selected=\"selected\"';
										}
									}
								}
								$html.='>'.$d['ad_'.$field->adv_column].'</option>';
							}
							$html.='</select>';
						}
					}
				break;
				case '2dropdowns':
					if($f['adv_datasourceId']!=0){
						if($frontend==true){
							$html.='<div class="col-md-6 col-sm-6" style="padding-left:0px !important;">';
						}
						$html.='<select id="'.$f['adv_column'].'" name="'.$f['adv_column'].'" class="form-control">';
						$html.='<option value="">Please Select</option>';
						$ds = $this->getData($f['adv_datasourceId']);
						foreach($ds as $d){
							$html.='<option value="'.$d['ds_id'].'"';
							if(is_array($listing)){
								if(array_key_exists('ad_'.$f['adv_column'],$listing)){
									if($listing['ad_'.$f['adv_column']]==$d['ds_id']){
										$html.=' selected=\"selected\"';
									}
								}
							}
							$html.='>'.$d['ds_value'].'</option>';
						}
						$html.='</select>';
						if($frontend==true){
							$html.='</div>';
						}
					}
					if($f['adv_associated_fieldId']==0){ 
						if($frontend==true){
							$html.='<div class="col-md-6 col-sm-6" style="padding-left:0px !important;">';
						}
						$html.='<select class="form-control secondFields">';
						foreach($this->getSecondDropDown($categoryId) as $second){
							$html.='<option value="'.$f['adv_id'].':'.$second['adv_id'].'"';
							if(is_array($listing)){
								if(array_key_exists('ad_'.$f['adv_column'],$listing)){
									if($listing['ad_'.$f['adv_column']]==$d['ds_id']){
										$html.=' selected=\"selected\"';
									}
								}
							}
							$html.='>'.$second['adv_text'].'</option>';
						}
						$html.='</select>';
						if($frontend==true){
							$html.='</div>';
						}
					}else{
						$f2 = $this->db->select('*')->where('adv_id',$f['adv_associated_fieldId'])->join('field_type','fi_id=adv_field_type')->get('advert_fields')->row_array();
						//var_dump($f2);
						if($frontend==true){
							$html.='<div class="col-md-6 col-sm-6" style="padding-left:0px !important;padding-right:0px !important;">';
						}
						$html.=$this->getFieldHTML($f2,NULL,$frontend,$listing);
						if($frontend==true){
							$html.='</div>';
						}
					}
				break;
				case 'textbox':
					if(!empty($customData) && $customData['customName']!=NULL){
						$html='<input type="text" id="'.$customData['customName'].'" name="'.$customData['customName'].'" class="form-control" autocomplete="off"';
						if(is_array($listing)){
							if(array_key_exists('ad_'.$f['adv_column'],$listing)){
								$html.=' value="'.$listing['ad_'.$f['adv_column']].'"';
							}
						}
						$html.=' />';
					}
					else{
						$html='<input type="text" name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" class="form-control" autocomplete="off"';
						if(is_array($listing)){
							if(array_key_exists('ad_'.$f['adv_column'],$listing)){
								$html.=' value="'.$listing['ad_'.$f['adv_column']].'"';
							}
						}
						$html.=' />';
					}
				break;
				case 'date':
					$html='<input type="text" name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" class="form-control uk_datepicker" ';
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							if($listing['ad_'.$f['adv_column']]!=0){
								$html.=' value="'.date('d/m/Y H:i',$listing['ad_'.$f['adv_column']]).'"';
							}
						}
					}
					$html.=' />';
				break;
				case 'textarea':
					$maxLength = '400';
					//if($f['adv_column']=='Description'){
					//print_r($listing);
					
					

						$html.='<div>'.$html2.'<textarea name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" class="form-control ckeditor" maxlength="'.$maxLength.'" rows="10">';
					/*}else{
						$html='<textarea name="'.$f['adv_column'].'" id="'.$f['adv_column'].'" class="form-control" maxlength="'.$maxLength.' rows="10">';
					}*/
					if(is_array($listing)){
						if(array_key_exists('ad_'.$f['adv_column'],$listing)){
							$html.=$listing['ad_'.$f['adv_column']];
						}
					}
					$html.='</textarea></div>';
				break;
				case 'checkboxes':
					if($f['adv_datasourceId']!=0){
						$ds = $this->getData($f['adv_datasourceId']);
						$ids = array();
						if(is_array($listing)){
							if(array_key_exists('ad_'.$f['adv_column'],$listing)){
								$ids = explode(',',$listing['ad_'.$f['adv_column']]);
							}
						}
						//echo $listing['ad_'.$f['adv_column']];
						foreach($ds as $d){
							
							$html.='<input type="checkbox" name="'.$f['adv_column'].'[]" id="'.$f['adv_column'].'" value="'.$d['ds_id'].'"';
							if(in_array($d['ds_id'],$ids)){
								$html.="checked=\"checked\"";	
							}
							$html.='/> '.$d['ds_value'].'&nbsp<br>';
						}
					}
				break;
				case 'image':
					//print_r($f);
					if(isset($listing['ad_id'])){
						$f['ad_id'] = $listing['ad_id'];
					}else{
						$f['ad_id'] = 0;
					}

					if(!empty($customData) && $customData['customName']!=NULL){
						$f['listingAdId'] = @$customData['listingAdId'];
						if(!empty($f['listingAdId'])){
							$f['ad_id'] = @$customData['listingAdId'];
						}
					}

					if(isset($listing['ad_id'])){
						$f['selected_layout'] = Imagedisplayoptionsmodel::where(['field_id' => $f['adv_id'], 'entry_id' => $listing['ad_id']])->first();
					}else{
						$f['selected_layout'] = NULL;
					}

					if(!empty($customData) && $customData['customName']!=NULL){
						$f['customName'] = $customData['customName'];

						$f['additionalFormType'] = @$customData['additionalFormType'];
						$f['ptBlocksMultipleDatasId'] = @$customData['ptBlocksMultipleDatasId'];
					}

					$randomNumber = _generateRandomNumber(7);
					$f['randomNumber'] = $randomNumber;

					// if($f['adv_column'] == "photoupload_fraew"){
					// 	_pre($f);
					// }

					// _pre($f);
					
					//print_r($f['selected_layout']);
					$html = $this->load->view('forms/fileuploader-document', $f, true);
					$html.= $this->load->view('admin/scripts_fileuploader_images', $f, true);
					
				break;
				case 'image_arrow':
					//print_r($f);
					if(isset($listing['ad_id'])){
						$f['ad_id'] = $listing['ad_id'];
					}else{
						$f['ad_id'] = 0;
					}
					if(isset($listing['ad_id'])){
						$f['selected_layout'] = Imagedisplayoptionsmodel::where(['field_id' => $f['adv_id'], 'entry_id' => $listing['ad_id']])->first();
					}else{
						$f['selected_layout'] = NULL;
					}
					
					//print_r($f['selected_layout']);
					$html = $this->load->view('forms/fileuploader-document-image-arrow', $f, true);
					$html.= $this->load->view('admin/scripts_fileuploader_images_arrow', $f, true);
					
				break;
				case 'document':
					//print_r($f);

					if(isset($listing['ad_id'])){
						$f['ad_id'] = $listing['ad_id'];
					}else{
						$f['ad_id'] = 0;
					}

					$html = $this->load->view('forms/fileuploader-document', $f, true);
					$html.= $this->load->view('admin/scripts_fileuploader_documents', $f, true);
					
				break;
			}	
			return $html;
	}

	public function getFieldsData($advCategoryId, $frontEnd = false, $listing = NULL, $allFields = false, $noHTML = false, $dsOnly = false, $customData=array()){
		$this->db->select('*');
		if($advCategoryId!=NULL){
			$this->db->where('adv_category', $advCategoryId);
		}
		if($dsOnly==true){
			$this->db->where('adv_datasourceId !=',0);
		}
		if($allFields==false){
			$this->db->where('`adv_id` NOT IN (SELECT `adv_associated_fieldId` FROM pt_advert_fields)', NULL, FALSE);
		}
		if(isset($_SESSION['ft_database_table'])){
			if($_SESSION['ft_database_table']!=''){
				$this->default_table = $_SESSION['ft_database_table'];
				$this->db->where('adv_table',$this->default_table);
			}
		}
		$this->db->join('field_type','fi_id=adv_field_type');
		$this->db->order_by('adv_order','ASC');
		$fields = $this->db->get('advert_fields')->result_array();
		return $fields;
	}
	
	public function getFields($categoryId = NULL, $frontEnd = false, $listing = NULL, $allFields = false, $noHTML = false, $dsOnly = false, $customData=array()){
		$this->db->select('*');
		if($categoryId!=NULL){
			$this->db->where('adv_category',$categoryId);
		}
		//only pick main fields
		//$this->db->where('adv_associated_fieldId',0);
		//echo $dsOnly.'#';
		if($dsOnly==true){
			$this->db->where('adv_datasourceId !=',0);
		}
		if($allFields==false){
			$this->db->where('`adv_id` NOT IN (SELECT `adv_associated_fieldId` FROM pt_advert_fields)', NULL, FALSE);
		}
		if(isset($_SESSION['ft_database_table'])){
			if($_SESSION['ft_database_table']!=''){
				$this->default_table = $_SESSION['ft_database_table'];
				$this->db->where('adv_table',$this->default_table);
			}
		}
		//no children
		//$this->db->where('adv_parent_field_id', NULL);
		$this->db->join('field_type','fi_id=adv_field_type');
		$this->db->order_by('adv_order','ASC');
		$fields = $this->db->get('advert_fields')->result_array();
		//echo '<!--';
		//echo $this->db->last_query();
		//echo '-->';
		$i = 0;
		if($noHTML == false){
			foreach($fields as $f){
				$advCategory = $f['adv_category']; // numberofbalconies_fraew
				$advColumn = $f['adv_column']; // 93

				if(!empty($customData)){
					$customName = $advCategory."[ad_".$advColumn."]";

					$customData['customName'] = $customName;
					
					$fields[$i]['html'] = $this->getFieldHTML($f, $categoryId, $frontEnd, $listing, '', $customData);
				}
				else{
					$fields[$i]['html'] = $this->getFieldHTML($f, $categoryId, $frontEnd, $listing);
				}
				//print_r($fields[$i]['html']);
				$i++;
			}
		}
		return $fields;
	}
	public function getField($fieldId, $listing = NULL){
		$this->db->select('*');
		$this->db->where('adv_id',$fieldId);
		//only pick main fields
		//$this->db->where('adv_associated_fieldId',0);

		$this->db->join('field_type','fi_id=adv_field_type');
		$this->db->order_by('adv_order','DESC');
		$fields = $this->db->get('advert_fields')->result_array();
		//echo $this->db->last_query();
		$i = 0;
		foreach($fields as $f){
			$fields[$i]['html'] = $this->getFieldHTML($f, NULL, NULL, $listing);
			$i++;
		}
		return $fields;
	}

	public function countPages($field_id){
		$this->db->select_sum('up_number_of_pages');
		$this->db->where('up_field_id', $field_id);
		$query = $this->db->get('documents');
		//echo $this->db->last_query();
		// If you're expecting a single result, you can retrieve it like this:
		if ($query->num_rows() > 0) {
		    $result = $query->row();  // gets the first row from the result set
		    $num = $result->up_number_of_pages;
		    return $num;
		    exit();
		}
		return 0;
	}

	//prevent users hacking, submitting filenames they're not suposed to
	public function getImage($imgId, $userId){
		$this->db->select('*');
		$this->db->where('up_id',$imgId);
		$result = $this->db->get('images')->result_array();
		if(sizeof($result)>0){
			return $result;
		}else{
			return false;	
		}
	}


	public function getDocuments($entry_id, $field_id){
		$this->db->select('pt_documents.*, pt_advert_fields.adv_column');
		$this->db->from('pt_documents');
		$this->db->join('pt_advert_fields', 'pt_documents.up_field_id = pt_advert_fields.adv_id', 'left');
		$this->db->where('up_entry_id',$entry_id);
		$this->db->where('up_field_id',$field_id);
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query();
		return $result;
	}


	public function getImages($entry_id, $field_id){
		$this->db->select('*');
		$this->db->where('up_entry_id',$entry_id);
		$this->db->where('up_field_id',$field_id);
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get('images')->result_array();
		//echo $this->db->last_query();
		return $result;
	}

	public function getImages1($entry_id, $field_id, $up_id_array){
		$this->db->select('*');
		$this->db->where('up_entry_id',$entry_id);
		$this->db->where('up_field_id',$field_id);
		if($up_id_array){
			$this->db->where_not_in('up_id', $up_id_array);
		}
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get('images')->result_array();
		//echo $this->db->last_query();
		return $result;
	}

	public function getImages2($up_id_array){
		$this->db->select('*');
		$this->db->where_in('up_id', $up_id_array);
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get('images')->result_array();
		//echo $this->db->last_query();
		return $result;
	}

	public function getDocuments1($entry_id, $field_id, $up_id_array){
		$this->db->select('pt_documents.*, pt_advert_fields.adv_column');
		$this->db->from('pt_documents');
		$this->db->join('pt_advert_fields', 'pt_documents.up_field_id = pt_advert_fields.adv_id', 'left');
		$this->db->where('up_entry_id',$entry_id);
		$this->db->where('up_field_id',$field_id);
		if($up_id_array){
			$this->db->where_not_in('up_id', $up_id_array);
		}
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query();
		return $result;
	}

	public function getDocuments2($up_id_array){
		$this->db->select('pt_documents.*, pt_advert_fields.adv_column');
		$this->db->from('pt_documents');
		$this->db->join('pt_advert_fields', 'pt_documents.up_field_id = pt_advert_fields.adv_id', 'left');
		$this->db->where_in('up_id', $up_id_array);
		$this->db->order_by('up_order','ASC');
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get()->result_array();
		//echo $this->db->last_query();
		return $result;
	}

	public function getInfoById($upId){
		$this->db->select('*');
		$this->db->where('up_id', $upId);
		$this->db->where('up_table', $this->default_table);
		$result = $this->db->get('images')->row();
		//echo $this->db->last_query();
		return $result;
	}

	public function insertData($data = array()){
        $insert = $this->db->insert("pt_images", $data);
        if($insert){
            return $this->db->insert_id();
        }
        else{
            return false;
        }
    }

	public function updateData($data, $whereArray){
        if(!empty($data) && !empty($whereArray)){
            $update = $this->db->update("pt_images", $data, $whereArray);
            return $update?true:false;
        }
        else{
            return false;
        }
    }

	public function duplicateMySQLRecord($table, $primary_key_field, $primary_key_val) 
	{
	   /* generate the select query */
	   $this->db->where($primary_key_field, $primary_key_val); 
	   $query = $this->db->get($table);
	  
	    foreach ($query->result() as $row){   
	       foreach($row as $key=>$val){        
	          if($key != $primary_key_field){ 
	          /* $this->db->set can be used instead of passing a data array directly to the insert or update functions */
	          $this->db->set($key, $val);               
	          }//endif              
	       }//endforeach
	    }//endforeach

	    /* insert the new record into table*/
	    $this->db->insert($table); 
	    return $this->db->insert_id();
	}
}