<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post extends My_Controller {
	public function __construct()
    {
    	// ini_set('display_errors', '1');
		// ini_set('display_startup_errors', '1');
		// error_reporting(E_ALL);

     	parent::__construct();

     	$this->load->model('PtFieldTypeModel');
     	$this->load->model('Advertfieldsmodel');
     	$this->load->model('Advertcategorymodel');
     	$this->currentDatetime = get_current_date_time();

	}
	
	public function toggle_two_col() {
	    // Get the value of the "two_col" parameter
	    $two_col = (int)$this->input->post('two_col');
	    
	    // Update the session variable
	    $_SESSION['two-col'] = $two_col;

	    // Send a response back to the client
	    echo 'OK';
	}


	function update_image_display_config(){

		//todo add validation
		$field_id 					= $this->input->post('field');
		$entry_id 					= $this->input->post('entry_id');
		$Advertfield 				= Advertfieldsmodel::find($field_id);

		$layout_id 			= $this->input->post('layout_id');

		$Imagedisplayoptionsmodel = Imagedisplayoptionsmodel::where(['field_id' => $field_id, 'entry_id' => $entry_id])->first();

		if(!empty($Imagedisplayoptionsmodel)){

			if($this->input->post('layout_id')){
				if($layout_id!==''){
					$Imagedisplayoptionsmodel->layout_id = $layout_id;
				}
			}
			$Imagedisplayoptionsmodel->update();

		}else{

			$Imagedisplayoptionsmodel = new Imagedisplayoptionsmodel();
			$Imagedisplayoptionsmodel->field_id			= $field_id;
			$Imagedisplayoptionsmodel->entry_id			= $entry_id; 
			if($this->input->post('layout_id')){
				if($layout_id!==''){
					$Imagedisplayoptionsmodel->layout_id	= $layout_id;
				}
			}
			$Imagedisplayoptionsmodel->save();
		}

		echo json_encode(array('status' => 1, 'msg'=> $this->lang->line('updated_successfully')));
	}
	
	public function success($advertId)
	{
		if($advertId!=''){
			$data['advertId'] = $advertId;
		}
		//$this->load->view('welcome_message');
		$data['duration'] = $this->config->item('days_term');
		$this->load->view('common/header',$data);
		$this->load->view('advert/advert_posted',$data);
		$this->load->view('common/footer',$data);
	}

	public function test(){
		$api = $this->db->select('ad_APIKey')->where('ad_id', 1)->get('postcode_lookup_settings')->row();
		//echo $this->db->last_query();
		$this->api_key = $api->ad_APIKey;

		$loqate = new \Baikho\Loqate\Loqate($this->api_key);
		/*
		$result = $loqate->address()->find('M3 1NJ');
		print_r($result);
		//$result = $loqate->address()->retrieve('PC||TA-M31NJ');
		foreach($result->Items as $i){
			print_r($i);
			$result = (new \Baikho\Loqate\Address\Find($this->api_key))
				->setIsMiddleWare(TRUE)
				->setContainer($i->Id)
				->makeRequest();
			print_r($result);
		}
		
		$result = $loqate->address()->retrieve('GB|RM|A|51299798');
		print_r($result);
		*/
		

		$result = $loqate->geocoding()->ukGeocode('20 Tarif Street, Manchester, M1 2FJ');
		print_r($result);
	}

	private function _getResponseRepeatsectionSave($category, $adId){
		$returnArray = array();
		if($this->session->has_userdata('repeatsectionSave')){ // true
			$redirectUrl = base_url('Post/create_form/'.$category.'/'.$adId.'?formID='.$_SESSION['formID']);
			
			$returnArray['repeatsectionSave'] = true;
			$returnArray['repeatsectionSaveData'] = array(
				'redirectUrl' => $redirectUrl
			);

			$this->session->unset_userdata('repeatsectionSave'); // Remove
		}
		return $returnArray;
	}
	
	public function update_post($category){
		// _pre($_POST);
		//print_r($_POST);
		$position = 0;
		if($this->uri->segment(4)){

			$data['ad_id'] = $this->uri->segment(4);

			$this->db->select('ad_position, ad_contact');
			$this->db->where('ad_id',$data['ad_id']);
			$positionData = $this->db->get($this->default_table)->row_array();
			//print_r($positionData);
			//echo $this->db->last_query();
			$position = $positionData['ad_position'];
			$data['ad_contact'] = $positionData['ad_contact'];
			//echo '##'.$data['ad_contact'].'##';
		}

		$order = 0;
		if($this->input->post('order')){
			if($this->input->post('order')==1){
				$order = 1;
			}
		}

		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$result = $this->db->get($this->default_category_table)->result_array();
		$input = array();
		$tableData = array();
		$required = false;
		if($position<($category)){
			$input['ad_position']=$category;
		}

		$path = dirname(dirname(__FILE__));
		$dirs = scandir($path.'/modules/');
		$modules = array_diff($dirs, array('.', '..'));

		//print_r($modules);
		/*
		foreach($modules as $d){

			if(is_dir($path.'/modules/'.$d)){
				//method exists would be more elegant
				//try{
					$form_name = Modules::run(strtolower($d).'/'.ucfirst($d).'/get', 'form_name');
					echo $form_name.'#';
					//if('pt_'.$form_name == $result){
						$result = Modules::run(strtolower($d).'/'.ucfirst($d).'/update');
					//}
				//}catch(Exception $e){

				//}
			}
		}
		*/

		foreach($result as $r){
			$fields = $this->Advertcategorymodel->getFields($r['ad_id'],true, NULL, true);	

			foreach($fields as $f){
				//check required
				if($f['adv_required']==1){
					//echo $f['adv_column'];
					$required = true;
					$this->form_validation->set_rules($f['adv_column'], $f['adv_text'], 'required');

				}
				if($this->input->post($f['adv_column'])!=''){
					if($f['adv_column']=='Description'){
					
						$input['ad_'.$f['adv_column']] = $clean_html; 
					
					}else if($f['fi_type']=='signature'){
						
						$canvases[$f['adv_column']] = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post($f['adv_column'])));
					
					}else{

						$field_result = false;
						if(in_array(strtolower($f['fi_type']), $modules)){
							try{
								$field_result = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/update', $f, $data['ad_id']);
								//echo $field_result;
							} catch (Exception $e) {

							}
						}

						if(!$field_result){

							$post = $this->input->post($f['adv_column']);
							if(is_array($post)){
								$val = '';
								//print_r($post);
								foreach($post as $p){
									$val.=$p.',';
								}
								$input['ad_'.$f['adv_column']] = $val; 	
							}else{

								if($f['adv_field_type']==9){
									//we in blighty
									$input['ad_'.$f['adv_column']] = strtotime(str_replace('/', '-', $this->input->post($f['adv_column'])));
								}else{
									if( $this->input->post( $f['adv_column']."_trs" ) ){
										$tableData[] = $this->_getTableTrsData( "ad_".$f['adv_column']."_trs", $this->input->post( $f['adv_column']."_trs" ) );
									}
									$input['ad_'.$f['adv_column']] = $this->input->post($f['adv_column']);
								} 	
							}

						}

					}
				}
				if(in_array(strtolower($f['fi_type']), $modules)){
					Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/update', $f, $data['ad_id']);
				}

				/*
				if($f['fi_type']=='checkboxes'){
					$checks = '';
					if($this->input->post($f['adv_column'])){
						//echo $f['adv_column'];
						//print_r($this->input->post($f['adv_column']));
						foreach($this->input->post($f['adv_column']) as $check) {
							$checks.=$check.',';
						}
					}
					//echo $checks.'#';
					$input['ad_'.$f['adv_column']] = $checks; 
				}else{
					$input['ad_'.$f['adv_column']] = $this->input->post($f['adv_column']); 
				}
				*/
			}
		}

		if(isset($canvases)){
			foreach($canvases as $key => $value){
				//print_r($data);
				if(file_exists('assets/uploads/signature/'.$data['ad_id'].'/'.$key.'.png')){
					echo 'assets/uploads/signature/'.$data['ad_id'].'/'.$key.'.png';
					unlink('assets/uploads/signature/'.$data['ad_id'].'/'.$key.'.png');
					file_put_contents('assets/uploads/signature/'.$data['ad_id'].'/'.$key.'.png', $value);
					$input['ad_'.$key] = 'assets/uploads/signature/'.$data['ad_id'].'/'.$key.'.png';
				}
			}
		}

		if($required == true){

			//echo 'hit';
			if ($this->form_validation->run() == FALSE){
					//var_dump(validation_errors());
					echo json_encode(array('status'=>0,'msg'=>validation_errors()));
					exit();
			}else{	


				//$this->db->where('ad_userid',$this->session->userdata("us_id"));
				
				/*
				$role = $this->session->userdata('us_role');

				switch($role){
					case 1:
						$input['ad_role1'] = 1;
					break;
					case 2:
						$input['ad_role2'] = 1;
					break;
					case 3:
						$input['ad_role3'] = 1;
					break;
				}
				*/
				
				//print_r($input);
				//exit();

				$this->db->where('ad_id',$data['ad_id']);
				$this->db->update($this->default_table,$input);

				if(!empty($tableData)){
					$crudPtBlocksTableDatas = array(
						'table_data' => json_encode($tableData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
						'updated_at' => $this->currentDatetime,
					);
					$wherePtBlocksTableDatas = array(
						'block_id' => $data['ad_id'],
					);
					$this->PtBlocksTableDatasModel->updateData($crudPtBlocksTableDatas, $wherePtBlocksTableDatas);
				}

				//$quote_id = $this->Quotesmodel->generate_quote($category, $data['ad_id'], $data['ad_contact'], $order);
				if(isset($quote_id)){
					$input['ad_quote_id'] = $quote_id;
					$this->db->where('ad_id',$data['ad_id']);
					$this->db->update($this->default_table,$input);
					//echo $this->db->last_query();
				}

				$Eventsmodel 			= new Eventsmodel();
				$Eventsmodel->note 		= $this->default_table_name.' Updated';
				$Eventsmodel->table_id 	= $this->default_table;
				$Eventsmodel->user_id 	= $this->userid;
				$Eventsmodel->entry_id 	= $data['ad_id'];
				$Eventsmodel->save();

				if(isset($quote_id)){
					if($order==1){
						$this->Notificationmodel->addNotification('Order Created', $data['ad_contact'], 'order', $quote_id);
					}else{
						$this->Notificationmodel->addNotification('Quote Edited', $data['ad_contact'], 'quote', $quote_id);
					}
				}

				$responseData = array(
					'status'=>1,
					'msg'=>$this->lang->line('saved_successfully'),
					'ad_id'=>$data['ad_id']
				);

				$responseRepeatsectionSave = $this->_getResponseRepeatsectionSave($category, $data['ad_id']);
				if($responseRepeatsectionSave){
					$responseData['repeatsectionSave'] = $responseRepeatsectionSave['repeatsectionSave'];
					$responseData['repeatsectionSaveData'] = $responseRepeatsectionSave['repeatsectionSaveData'];
				}

				echo json_encode($responseData);
			}	
		}else{


			//$quote_id = $this->Quotesmodel->generate_quote($category, $data['ad_id'], $data['ad_contact'], $order);
			if(isset($quote_id)){
				$input['ad_quote_id'] = $quote_id;
			}

				/*
				$role = $this->session->userdata('us_role');
				switch($role){
					case 1:
						$input['ad_role1'] = 1;
					break;
					case 2:
						$input['ad_role2'] = 1;
					break;
					case 3:
						$input['ad_role3'] = 1;
					break;
				}
				*/

				//print_r($input);
				//exit();
				/*
				if((int)$this->data['user']['us_role']!==1){
					$this->db->where('ad_userid',$this->session->userdata("us_id"));
				}
				*/
				//$this->db->where('ad_userid',$this->session->userdata("us_id"));
				$this->db->where('ad_id',$data['ad_id']);
				$this->db->update($this->default_table, $input);
				//echo $this->db->last_query();

				if(!empty($tableData)){
					$crudPtBlocksTableDatas = array(
						'table_data' => json_encode($tableData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
						'updated_at' => $this->currentDatetime,
					);
					$wherePtBlocksTableDatas = array(
						'block_id' => $data['ad_id'],
					);
					$this->PtBlocksTableDatasModel->updateData($crudPtBlocksTableDatas, $wherePtBlocksTableDatas);
				}


			$Eventsmodel 			= new Eventsmodel();
			$Eventsmodel->note 		= $this->default_table_name.' Updated';
			$Eventsmodel->table_id 	= $this->default_table;
			$Eventsmodel->user_id 	= $this->userid;
			$Eventsmodel->entry_id 	= $data['ad_id'];
			$Eventsmodel->save();

			if(isset($quote_id)){
				if($order==1){
					$this->Notificationmodel->addNotification('Order Created', $data['ad_contact'], 'quote', $quote_id);
				}else{
					$this->Notificationmodel->addNotification('Quote Edited', $data['ad_contact'], 'quote', $quote_id);
				}
			}
			
			$responseData = array(
				'status'=>1,
				'msg'=>$this->lang->line('saved_successfully'),
				'ad_id'=>$data['ad_id']
			);

			$responseRepeatsectionSave = $this->_getResponseRepeatsectionSave($category, $data['ad_id']);
			if($responseRepeatsectionSave){
				$responseData['repeatsectionSave'] = $responseRepeatsectionSave['repeatsectionSave'];
				$responseData['repeatsectionSaveData'] = $responseRepeatsectionSave['repeatsectionSaveData'];
			}

			echo json_encode($responseData);
		}
		exit();
	}

	private function _getTableTrsData($arrayKey, $data){
		$dataArray = json_decode($data, true);
		// _pre($dataArray);

		$colorsArray = array();

		if($dataArray){
			foreach ($dataArray as $key => $arr) {
				if(is_array($arr)){
					$lastProperty = end(array_keys($arr));
    				$colorsArray[] = $arr[$lastProperty];
    			}
			}
		}

		$returnArray = array(
			$arrayKey => array(
				'data' => $dataArray,
				'colorsArray' => $colorsArray,
			)
		);

		return $returnArray;
	}
	
	public function save_post($category){
		// _pre($_POST);
		/*$adBalconiesFraew = array(
			array(
				"1", "11"
			),
			array(
				"2", "22"
			),
			array(
				"3", "33"
			),
			array(
				"4", "44"
			)
		);

		$ptImage = array(
			"up_filename" => null,
			"up_entry_id" => null,
			"up_field_id" => null,
			"up_date" => null,
			"up_userid" => null,
			"up_order" => null,
			"up_table" => null,
			"up_caption" => null,
		);
		$ptImages[] = $ptImage;

		$ptImage = array(
			"up_filename" => null,
			"up_entry_id" => null,
			"up_field_id" => null,
			"up_date" => null,
			"up_userid" => null,
			"up_order" => null,
			"up_table" => null,
			"up_caption" => null,
		);
		$ptImages[] = $ptImage;

		$array = array(
			'ad_numberofbalconies_fraew' => 5,
			'ad_balconies_fraew' => $adBalconiesFraew,
			'pt_images' => $ptImages
		);

		$arrayJson = json_encode($array, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		_pre($arrayJson);*/

		// _pre($_POST);
		/*$hdnBalconiesRepeat = $this->input->post('hdnBalconiesRepeat');
		$hdnBalconiesRepeatArray = json_decode($hdnBalconiesRepeat, true);
		_pre($hdnBalconiesRepeatArray);*/
		/*Array
		(
		    [0] => Array
		        (
		            [93] => Array
		                (
		                    [ad_numberofbalconies_fraew] => b
		                )
		        )
		    [1] => Array
		        (
		            [93] => Array
		                (
		                    [ad_numberofbalconies_fraew] => c
		                )
		        )
		    [2] => Array
		        (
		            [93] => Array
		                (
		                    [ad_numberofbalconies_fraew] => d
		                )
		        )
		)*/

		//echo getcwd() . "\n";
		//html cleaner doesn't work because of server side stuff
		/*require_once 'application/third_party/html_tidy/HTMLCleaner.php';
		
		$this->Options = array(
                        ‘RemoveStyles’    => true,  //removes style definitions like style and class
                        ‘IsWord’                => true,        //Microsoft Word flag - specific operations may occur
                        ‘UseTidy’              => true,       //uses the tidy engine also to cleanup the source (reccomended)
                        ‘CleaningMethod’        => array(TAG_WHITELIST,ATTRIB_BLACKLIST),       //cleaning methods
                        ‘OutputXHTML’      => true,   //converts to XHTML by using TIDY.
                        ‘FillEmptyTableCells’ => true,  //fills empty cells with non-breaking spaces
                        ‘DropEmptyParas’        => true,        //drops empty paragraphs
                        ‘Optimize’                  =>true,            //Optimize code - merge tags
                        ‘Compress’                  => false);    //trims all spaces (line breaks, tabs) between tags and between words.

		// Specify TIDY configuration
		$this->TidyConfig = array(
		       ‘indent’         => true, //a bit slow
		       ‘output-xhtml’   => true, //Outputs the data in XHTML format
		           ‘word-2000′    => false, //Removes all proprietary data when an MS Word document has been saved as HTML
		           //’clean’        => true, //too slowr4gt
		           ‘drop-proprietary-attributes’ =>true, //Removes all attributes that are not part of a web standard
		           ‘hide-comments’ => true, //Strips all comments
		           ‘preserve-entities’ => true, // preserve the well-formed entitites as found in the input
		           ‘quote-ampersand’ => true,//output unadorned & characters as &.
		           ‘wrap’           => 200); //Sets the number of characters allowed before a line is soft-wrapped
		
		//echo $this->input->post('Description');
		$cleaner=new HTMLCleaner();
		
		$cleaner->Options['UseTidy']=true;
		$cleaner->Options['IsWord']=true;
		$cleaner->TidyConfig['word-2000']=true;
		$cleaner->Options['Optimize']=true;
		$cleaner->html = $this->input->post('Description');
		$cleanHTML=$cleaner->cleanUp();
		//use html purifier to get rid of js bad code etc
		*/
		//only if there is a description
		if($this->input->post('Description')!=''){
			require_once 'application/third_party/library/HTMLPurifier.auto.php';
		    $config = HTMLPurifier_Config::createDefault();
	        //$config->set('Core', 'Encoding', 'ISO-8859-1');
	        //$config->set('HTML', 'Doctype', 'XHTML 1.0 Transitional');
	        $config->set('HTML', 'TidyLevel', 'heavy');
	        $config->set('Core','AcceptFullDocuments',true);
	        $config->set('HTML', 'Allowed', 'a[href|title],em,p,blockquote,img');
	               
	        $purifier = new HTMLPurifier($config);
		    $clean_html = $purifier->purify($this->input->post('Description'));
	    }
	    //echo '#'.$clean_html;
	    

		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$result = $this->db->get($this->default_category_table)->result_array();

		//echo $this->db->last_query();
		$input = array();
		$tableData = array();
		$canvases = array();
		$ptBlockStaffsCrud = array();

		foreach($result as $r){
			$fields = $this->Advertcategorymodel->getFields($r['ad_id'],true,NULL,true);	

			foreach($fields as $f){
				//check required
				if($f['adv_required']==1){
					//echo '##';
					$this->form_validation->set_rules($f['adv_column'], $f['adv_text'], 'required');

				}
				if($f['adv_column']=='Description'){
					$input['ad_'.$f['adv_column']] = $clean_html; 
				}else{

					if($f['fi_type']=='signature'){
						
						$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->input->post($f['adv_column'])));
						$canvases[$f['adv_column']] = $data;
					}else{

						$postArray1 = $this->input->post($f['adv_id']."_".$f['adv_column']);
						if(is_array($postArray1)){
							foreach ($postArray1 as $key => $valueStaffId) {
								$tempCrud = array(
									'ad_id' => null,
									'ad_blockid' => null,
									'ad_staffid' => $valueStaffId,
									'created_at' => date('Y-m-d H:i:s'),
								);
								$ptBlockStaffsCrud[] = $tempCrud;
							}
						}

						$post = $this->input->post($f['adv_column']);
						if(is_array($post)){
							$val = '';
							//print_r($post);
							foreach($post as $p){
								$val.=$p.',';
							}
							$input['ad_'.$f['adv_column']] = $val; 	
						}else{
							
							if($f['adv_field_type']==9){
								//we in blighty
								if($this->input->post($f['adv_column'])!==''){
									$input['ad_'.$f['adv_column']] = strtotime(str_replace('/', '-', $this->input->post($f['adv_column'])));
								}
							}else{
								if( $this->input->post( $f['adv_column']."_trs" ) ){

									$tableData[] = $this->_getTableTrsData( "ad_".$f['adv_column']."_trs", $this->input->post( $f['adv_column']."_trs" ) );


									// $tableData[] = array(
									// 	$f['adv_column']."_trs" => 
									// );
								}

								$input['ad_'.$f['adv_column']] = $this->input->post($f['adv_column']);
							} 
						}	
					}			
				}
			}
		}
		
		$input['ad_userid']=$this->session->userdata("us_id");
		$input['ad_added']=date('Y-m-d H:i:s',strtotime('now'));
		$input['ad_last_update']=date('Y-m-d H:i:s',strtotime('now'));

		$input['ad_contact'] = $this->input->post('contactID');

		$input['ad_rand'] = strtoupper(md5(uniqid(rand(), true)));

		$input['ad_position'] = 1;

		if ($this->form_validation->run() == FALSE){
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
				exit();
		}else{	
			// _pre($tableData, 0);
			// _pre($input);
			$this->db->insert($this->default_table, $input);

			$ad_id = $this->db->insert_id();
			// _pre($ad_id);

			if(!empty($tableData)){
				$crudPtBlocksTableDatas = array(
					'block_id' => $ad_id,
					'table_data' => json_encode($tableData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
					'created_at' => $this->currentDatetime,
				);
				$this->PtBlocksTableDatasModel->insertData($crudPtBlocksTableDatas);
			}

			if(!empty($ptBlockStaffsCrud)){
				// _pre($ptBlockStaffsCrud, 0);
				foreach ($ptBlockStaffsCrud as $key => $value) {
					$ptBlockStaffsCrud[$key]['ad_id'] = $ad_id;
					$ptBlockStaffsCrud[$key]['ad_blockid'] = $ad_id;
				}
				// _pre($ptBlockStaffsCrud);
				$this->PtBlockStaffsModel->insertBatch($ptBlockStaffsCrud);
			}

			$path = dirname(dirname(__FILE__));
			$dirs = scandir($path.'/modules/');
			$modules = array_diff($dirs, array('.', '..'));
			//get any module stuff


			$Alertsmodel = new Alertsmodel();
			$alerts = $Alertsmodel::where('table_id', $_SESSION['formID'])->get();
			
			$Pendingcomsmodel = new Pendingcomsmodel();
			$Advertfieldsmodel = new Advertfieldsmodel();

			foreach($alerts as $a){

				//get the date field
				$Pendingcomsmodel = new Pendingcomsmodel();
				$Advertfieldsmodel = new Advertfieldsmodel();
				$date = $Advertfieldsmodel::find($a->date_field_id);

				$Pendingcomsmodel->entry_id 	= $ad_id;
				$Pendingcomsmodel->table_id 	= $_SESSION['formID'];
				$Pendingcomsmodel->message_id 	= $a->message_id;
				$Pendingcomsmodel->send_at 		= $input['ad_'.$date['adv_column']]+($a->delay_increment*$a->delay);
				$Pendingcomsmodel->date_field_id= $a->date_field_id;
				$Pendingcomsmodel->send_to_id	= $a->email_field_id;
				$Pendingcomsmodel->status 		= 0;
				$Pendingcomsmodel->save();

			}

			$Eventsmodel 			= new Eventsmodel();
			$Eventsmodel->note 		= $this->default_table_name.' Created';
			$Eventsmodel->table_id 	= $this->default_table;
			$Eventsmodel->user_id 	= $this->userid;
			$Eventsmodel->entry_id 	= $ad_id;	
			$Eventsmodel->save();

			//$quote_id = $this->Quotesmodel->generate_quote($category, $ad_id, $this->input->post('contactID'));
			//$this->Notificationmodel->addNotification('Quote Created', $this->input->post('contactID'), 'quote', $quote_id);
			$path = 'assets/uploads/signature/'.$ad_id;
			if(!file_exists($path)){
				mkdir($path);
			}
			foreach($canvases as $key => $value){
				file_put_contents('assets/uploads/signature/'.$ad_id.'/'.$key.'.png', $value);
				$update['ad_'.$key] = 'assets/uploads/signature/'.$ad_id.'/'.$key.'.png';
			}
			if(isset($update)){
				$this->db->where('ad_id', $ad_id);
				$this->db->update($this->default_table, $update);
			}

			$update2['up_entry_id'] = $ad_id;
			$this->db->where('up_entry_id', 0);
			$this->db->where('up_userid', $this->userid);
			$this->db->update('pt_documents', $update2);

			$update2['up_entry_id'] = $ad_id;
			$this->db->where('up_entry_id', 0);
			$this->db->where('up_userid', $this->userid);
			$this->db->update('pt_images', $update2);

			$update3['pt_block_id'] = $ad_id;
			$this->db->where('pt_block_id', 0);
			$this->db->where('userid', $this->userid);
			$this->db->update('pt_blocks_multiple_datas', $update3);

			//echo $this->db->last_query();



			$path = dirname(dirname(__FILE__));
			$dirs = scandir($path.'/modules/');
			$modules = array_diff($dirs, array('.', '..'));

			foreach($fields as $f){
				if(in_array(strtolower($f['fi_type']), $modules)){
	
					//$form_name = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/get', 'form_name');
					//echo $form_name;
					$result = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/create', $f, $ad_id);
					//echo $result;
							
				}
			}

			$responseData = array(
				'status'=>1,
				'msg'=>$this->lang->line('saved_successfully'),
				'ad_id'=>$ad_id
			);

			$responseRepeatsectionSave = $this->_getResponseRepeatsectionSave($category, $ad_id);
			if($responseRepeatsectionSave){
				$responseData['repeatsectionSave'] = $responseRepeatsectionSave['repeatsectionSave'];
				$responseData['repeatsectionSaveData'] = $responseRepeatsectionSave['repeatsectionSaveData'];
			}
			echo json_encode($responseData);
		}	
	}

	public function gen_pdf($category, $ad_id, $format, $batch = NULL){

		if($this->input->get('template')){
			$template = $this->input->get('template');
			$template = str_replace('../', '', $template);
			//echo $template.'##';
		}else{
			$template = NULL;
		}
		//cats probably always going to be 1
		//public function generate_quote($category, $ad_id, $contact_id, $order=0, $stream = false, $attachState = false, $format = 'pdf'){
		if($batch!=NULL){
			
			if($format=='doc'){
				//echo 'hit';
				//exit();
				$this->Quotesmodel->generate_quote($category, $ad_id, NULL, 0, true, false, $format, $batch, $template);
			}else{
				echo 'Batch only word with DOC currently';
				exit();
			}
		}else{
			$this->Quotesmodel->generate_quote($category, $ad_id, NULL, 0, true, false, $format, NULL, $template);	
		}
	}

	public function download_pdf($category, $ad_id){
		//cats probably always going to be 1
		$this->Quotesmodel->generate_quote($category, $ad_id, NULL, 0, true, true);
	}

	public function create_form($category){
		// ini_set('display_errors', '1');
		// ini_set('display_startup_errors', '1');
		// error_reporting(E_ALL);
		//
		$this->load->helper('links');

		$data = [];
		$permission = $this->Usermodel->has_permission('create', $this->input->get('formID'));
		$permissions['create'] = $permission;
		
		if($this->uri->segment(4)){
			$data['listing'] = $this->Advertcategorymodel->getAdvert($this->uri->segment(4));
		}
		
		$permission = $this->Usermodel->has_permission('update', $this->input->get('formID'));
		$permissions['update'] = $permission;
	

		$read = $this->Usermodel->has_permission('read', $this->input->get('formID'));
		$permissions['read'] = $read;
		$data = $this->data;

		if($this->input->get('no_header')){
			$data['no_header'] = true;
		}

		$data['category'] = $category;

		$permissions['role'] = $this->data['user']['us_role'];
		if(($permissions['create']||$permissions['update'])||($read)){
			
			$data['user'] = $this->data['user'];
			$this->load->helper('cookie');

			$type = $this->input->get('type');
			$data['type'] = $type; 

			$data['order'] = '';
			if($this->input->get('order')){
				$data['order'] = $this->input->get('order');
			}
			//echo $date['order'];
			//containerEnd view has most of the JS in it
			
			$data['width'] = 6;
			$data['ad_id'] = 0;
			$data['listing'] = '';

			$data['preview'] = true;

			if($this->input->get('contactID')!=''){
				$data['contactID'] = $this->input->get('contactID');
			}
			$idx = NULL;
			if(($this->uri->segment(4))||($this->input->get('template'))){
				//$idx = $this->uri->segment(4);
				if($this->uri->segment(4)){
					$idx = $this->uri->segment(4);
				}
				if($this->input->get('template')){
					$idx = $this->input->get('template');
				}

				$count = 0;
				if(isset($_SESSION['searches'])){
					foreach ($_SESSION['searches'] as $s) {
					    if ($s['ad_id'] == $idx) {
					        break;
					    }
					    $count++;
					}
					$data['before'] = $_SESSION['searches'][$count - 1];
					//$current = $all[$idx];
					$data['after'] = $_SESSION['searches'][$count + 1];
				}

				if($this->uri->segment(4)){
					$data['listing'] = $this->Advertcategorymodel->getAdvert($this->uri->segment(4));	
				}
				if($this->input->get('template')){
					$data['listing'] = $this->Advertcategorymodel->getAdvert($this->input->get('template'));	
				}
				//}
				$data['ad_id'] = $this->uri->segment(4);

				$this->db->where('ad_id', $data['ad_id']);
				$arry = array('ad_position'=>4);
				$this->db->update($this->default_table, $arry);
			}
			//var_dump($data['listing']);
			//$category = 1;

			$data['tabs'] = $this->db->where('ad_parent', 0)->get($this->default_category_table)->result();
			//echo $this->db->last_query();
			//print_r($data['tabs']);

			$this->db->select('*');
			$this->db->where('ad_parent',$category);
			$this->db->order_by('ad_order','ASC');
			$this->db->order_by('ad_id','ASC');
			$result = $this->db->get($this->default_category_table)->result_array();
			//echo $this->db->last_query();
			//var_dump($result);
			$data['sellerDetails'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
			$data['category'] = $category;

			$data['image'] = base_url()."/assets/images/communications.png";
			$data['section'] = 'Eco Funding Induction Pack &nbsp;&nbsp;';

			$data['button'] = '';
			$data['button'] ='<button type="submit" class="btn btn-primary ladda-button submitButton" data-style="expand-right" id="submitButtonHeader" onclick="saveAndNext();">Save</button>';
			
			$data['subtitle'] = '';
			$data['formID'] = $this->formID;

			
			$this->load->view('dashboard/header', $data);

			if (substr($this->default_table, 0, strlen($this->db->dbprefix)) == $this->db->dbprefix) {
			    $folder = substr($this->default_table, strlen($this->db->dbprefix));
			} 

			$data['show_button'] = true;
			
			$total_cats = 0;			
			$can_fill_cats = 0;
			$show_roles_menu = 0;
			if(is_array($result)){
				$total_cats = sizeof($result);
			}

			foreach($result as $r){
				if( $data['user']['us_role'] == 1){
					$can_fill_cats++;
					$show_roles_menu++;
				}else{
					//role has been set 
					if($r['ad_role']!=0){
						$show_roles_menu++;
						if($data['user']['us_role'] == $r['ad_role']){
							$can_fill_cats++;
						}
					}else{
						$can_fill_cats++;
					}
				}
			}
			
			$data['show_roles_menu'] = $show_roles_menu;
			//echo $total_cats.'#';

			if($can_fill_cats == 0){
				$data['show_button'] = false;
			}

			//echo $folder.'#';
			//load in custom view parts, eg sticky menu
			$myview = 'forms/'.$folder.'/sticky';
	        if (is_file(APPPATH.'views/' . $myview . EXT))
	        {

	            $this->load->view($myview, $data);

	        } 

	        $this->db->select('ad_role,name');
			$this->db->from($this->default_category_table);
			$this->db->join('roles', 'id=ad_role');
			$this->db->group_by('ad_role');
			$query = $this->db->get();
			$data['roles_list'] = $query->result();

			$cat_array = [];
			$role_array = [];
			$parent_roles = $this->db->from($this->default_category_table)->where('ad_parent', 0)->get()->result();

			foreach($parent_roles as $p){

				$child_roles = $this->db->from($this->default_category_table)->where('ad_parent', $p->ad_id)->get()->result();

				foreach($child_roles as $c){
					$cat_array[$p->ad_name][] = array($c->ad_id, $c->ad_name, $c->ad_role);
				}
				
			}

			$cat = [];
			$fields_list = [];
			$cat_role = [];
			foreach($cat_array as $k => $v){
				foreach($v as $p){
					$cat[$p[0]] = '<b>'.$k.'</b> -> '.$p[1].' ('.$p[0].')';

					//$fields = $this->db->get_where('advert_fields', ['adv_category' => $p[0], 'adv_table'=> $this->default_table])->result();
					//echo $p[0].' '.$this->default_table;
					$fields = Advertfieldsmodel::where('adv_category', '=',  $p[0])->where('adv_table', '=', $this->default_table)->where('adv_ignore_in_sections_report', '=', 0)->get();
					$fields_list[$p[0]] = $fields;
					$cat_role[$p[0]] = $p[2];

				}
			}
			//print_r($fields_list);
			
			$data['missing_links'] = [];
			foreach($fields as $f){
				if(($f->adv_field_type == 5)||($f->adv_field_type == 6)){
					//echo $f->adv_column.'##';
					if(isset($data['listing']['ad_'.$f->adv_column])){
						$res = find_and_check_links($data['listing']['ad_'.$f->adv_column]);
						if($res == true){
							//echo 'missing link:'.$result;
							$data['missing_links'][] = $f->adv_column;
							$data['display'] = 'block';
							$data['alert'] = 'warning';

						}
					}
				}
			}
			if(!empty($data['missing_links'])){
				$data['msg'] = 'Some of the Links are invalid in the following fields:';
				foreach($data['missing_links'] as $m){
					$data['msg'].= ' <b>'.$m.'</b><br>';
				}
			}

			//print_r($data['missing_links']);

			$data['cats'] = $cat;
			$data['cat_role'] = $cat_role;
			$data['fields_list'] = $fields_list;
			//print_r($data['fields_list']);


			$this->load->view('templates/form_start', $data);

			$data['alert'] = 'success';


		
			$data['alert'] = 'danger';
			$data['alertid'] = 'warningMessage';
			$this->load->view('common/alert', $data);

			$data['default_table'] = $this->default_table;
			$data['formID'] = $this->formID;
			//print_r($data);
			
			switch($category){
				default:
					//print_r($result);
					foreach($result as $keyR => $r){
						//print_r($r);
						$data['moduleCategory'] = $r;
						
						$r['listing'] = $data['listing'];
						$r['default_table'] = $data['default_table'];
						$this->load->view('common/wrapper',$r);
						
						/*
						if(isset($this->data['us_role'])){
							$r['role'] = $this->data['us_role'];
						}else{
							$this->data['us_role'] = 0;
						}
						if(isset($this->data['roles'])){
							$r['roles'] = $this->data['roles'];
						}
						*/
						
						$r['tKey'] = $keyR;
						$r['tCategory'] = $category;
						$this->load->view('common/title_switch',$r);

						//$whereArray = array("pt_blocks_category_id" => $r['ad_id']);
						//$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
						// echo $this->db->last_query();exit;
						//$data['ptBlocksMultipleDatas'] = $ptBlocksMultipleDatas;
						// _pre($ptBlocksMultipleDatas);
						// _pre($data);
						
						$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);

						$randomNumberFileMerger = _generateRandomNumber(7);
						$data['randomNumberFileMerger'] = $randomNumberFileMerger;

						//if($this->data['us_role']!=0){
						//	if($this->data['us_role']==$r['ad_role']){
						if(($read)&&(($permissions['create']||$permissions['update']))){

							//admins
							//echo $data['user']['us_role'].'#';
							if( $data['user']['us_role'] == 1){

								if(isset($_SESSION['two-col']) && (int)$_SESSION['two-col']){
									$this->load->view('templates/advert_2col',$data);
								}else{
									//print_r($data);
									$this->load->view('templates/advert',$data);
									// _pre($data);
									if(!empty($data['ptBlocksMultipleDatas'])){
										$this->_loadViewForMultipleData($data);
									}
								}
								$can_fill_cats++;

							}else{

								//role has been set 
								if($r['ad_role']!=0){

									if($data['user']['us_role'] == $r['ad_role']){

										if((int)$_SESSION['two-col']){
											$this->load->view('templates/advert_2col',$data);
										}else{
											$this->load->view('templates/advert',$data);
										}
										$can_fill_cats++;

									}else{
										//$this->load->view('templates/advert_text',$data);
										$this->load->view('templates/advert',$data);
									}

								}else{

									if((int)$_SESSION['two-col']){
										$this->load->view('templates/advert_2col',$data);
									}else{
										$this->load->view('templates/advert',$data);
									}
									$can_fill_cats++;
								}
							}


						}else{
							
							//$this->load->view('templates/advert_text',$data);
							$this->load->view('templates/advert',$data);
						
						}
								

						//	}else{
						//	}
						//}else{
						//	$this->load->view('templates/advert',$data);
						//}
						$this->load->view('common/wrapperEnd',$r);
					}
				break;
			}
			$data['user'] = $this->data;
			$data['permissions'] = $permissions;
			$form = FormsTablesmodel::findorFail($data['formID']);
			$table = $form->ft_database_table;

			if($this->uri->segment(4)){
				$data['id']		 = $this->uri->segment(4);
			}else{
				$data['id'] 	= NULL;
			}


			$data['scripts_containerEnd'] = true;

			$data['events'] = Eventsmodel::where('entry_id', $idx)->where('table_id', $table)->orderBy('created_at', 'DESC')->get();  
			
			$this->load->view('templates/form_end', $data);
			$this->load->view('templates/scripts_containerEnd', $data);
			$this->load->view('modals/edit_datasource', $data);
			$this->load->view('dashboard/footer', $data);
		}else{
			$this->load->view('dashboard/header', $data);
			$this->load->view('templates/no_permission', $data);
			$this->load->view('dashboard/footer', $data);
		}
	}

	private function _loadViewForMultipleData($data){
		// editRepeatsection
		// _pre($data);
		if($data['ptBlocksMultipleDatas']){
			foreach ($data['ptBlocksMultipleDatas'] as $key => $value) {
				// $this->load->view('templates/_dynamic_form_edit',$data);
				
				// $result = Modules::run('repeatsection/Repeatsection/editRepeatsection', $data, $value);
				// if(!is_null($result)){
				// 	// echo $result;
				// }
			}
		}
	}

	public function getUploadedPicture($id){
		$this->load->view('forms/uploaded_image',$r);
	}
	
	public function hide_category(){
		$this->form_validation->set_rules('id', 'ID', 'required|numeric');
		$this->form_validation->set_rules('val', 'Value', 'required|numeric');
	
		if ($this->form_validation->run() != FALSE){
			$input['ad_hide'] = $this->input->post('val');
			
			$this->db->where('ad_id',$this->input->post("id"));
			$this->db->update($this->default_category_table, $input);
			//echo $this->db->last_query();
			//external_wall_insulation_cat
		}
	}

	public function change_role(){
		$this->form_validation->set_rules('category_id', 'Category ID', 'required|numeric');
		$this->form_validation->set_rules('role_id', 'Role ID', 'required|numeric');
	
		if ($this->form_validation->run() != FALSE){
			$input['ad_role'] = $this->input->post('role_id');
			
			$this->db->where('ad_id',$this->input->post("category_id"));
			$this->db->update($this->default_category_table, $input);
			//echo $this->db->last_query();
			//external_wall_insulation_cat
		}
	}

	public function crop(){
	ini_set("memory_limit","10000M");
	$this->form_validation->set_rules('w', 'width', 'required');
	$this->form_validation->set_rules('h', 'height', 'required');
	
	$this->form_validation->set_rules('x', 'xpos', 'required');
	$this->form_validation->set_rules('y', 'ypos', 'required');

	$this->form_validation->set_rules('img', 'Image', 'required');
	$this->form_validation->set_rules('imgId', 'ImageId', 'required');
	
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			//var_dump($_GET);	
			$userImage = $this->Advertcategorymodel->getImage($this->input->post('imgId'), $this->session->userdata("us_id"));
			if($userImage!=false){
				$targ_w = round($this->input->post('w'),2);
				$targ_h = round($this->input->post('h'),2);
	
				$x = round($this->input->post('x'),2);
				$y = round($this->input->post('y'),2);
				$jpeg_quality = 90;
				
				$src = "assets/uploaded_images/".$userImage[0]['up_filename'];
				$img_r = imagecreatefromjpeg($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	
				$output_filename = "assets/uploaded_images/".$userImage[0]['up_filename'];
			
				imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$targ_w,$targ_h);    
				header('Content-type: image/jpeg');
				//imagejpeg($dst_r, null, $jpeg_quality);
				imagejpeg($dst_r, $output_filename, $jpeg_quality);
				
				echo json_encode(array('filename'=>$output_filename));
			}else{
				echo json_encode(array('status'=>0));
			}
			
		}
	}
	public function cropAccount(){

	$this->form_validation->set_rules('w', 'width', 'required');
	$this->form_validation->set_rules('h', 'height', 'required');
	
	$this->form_validation->set_rules('x', 'xpos', 'required');
	$this->form_validation->set_rules('y', 'ypos', 'required');
	
	$this->form_validation->set_rules('img', 'Image', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			//var_dump($_GET);	
			
			$userImage = $this->data['us_image'];
			if($userImage!=false){
				$targ_w = $this->input->post('w');
				$targ_h = $this->input->post('h');
	
				$x = $this->input->post('x');
				$y = $this->input->post('y');
				$jpeg_quality = 90;
				
				$src = "assets/uploads/".$userImage;
				$img_r = imagecreatefromjpeg($src);
				$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
				
				$rand = rand();
				$output_filename = "assets/uploads/".$rand.$userImage;
			
				imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$targ_w,$targ_h);    
				header('Content-type: image/jpeg');
				//imagejpeg($dst_r, null, $jpeg_quality);
				imagejpeg($dst_r, $output_filename, $jpeg_quality);
				
				$this->makeThumbs($rand.$userImage);
				$input = array(
					'us_image'=>$rand.$userImage,
				);
				$this->db->where('us_id',$this->session->userdata("us_id"));
				$this->db->update('pt_user',$input);

				echo json_encode(array('filename'=>$output_filename));
			}else{
				echo json_encode(array('status'=>0));
			}
			
		}
	}
	//I put this in its own function
	//as it gets called each user action
	//couldn't think if a more reliable way to do it
	//with out a save button or some thing
	//its important this function remains private
	private function makeThumbs($name){
		//create a thumb on each crop
				$config['image_library'] = 'gd2';
				$config['library_path'] = 'assets/uploads/';
				$config['source_image'] = 'assets/uploads/'.$name;
				$config['create_thumb'] = TRUE;
				$config['thumb_marker'] = false;
				$config['new_image'] = 'assets/uploads/thumbs/180/'.$name;
				$config['width'] = '180';
				$config['height'] = '180';
				$config['maintain_ratio'] = true;
				$config['master_dim'] = 'width';
				$this->image_lib->initialize($config); 
				
				if ( ! $this->image_lib->resize())
				{
					echo $this->image_lib->display_errors();
				}
				
				//create a thumb on each crop
				$config['image_library'] = 'gd2';
				$config['library_path'] = 'assets/uploads/';
				$config['source_image'] = 'assets/uploads/'.$name;
				$config['create_thumb'] = TRUE;
				$config['thumb_marker'] = false;
				$config['new_image'] = 'assets/uploads/thumbs/270/'.$name;
				$config['width'] = '270';
				$config['height'] = '270';
				$config['maintain_ratio'] = true;
				$config['master_dim'] = 'width';
				$this->image_lib->initialize($config); 
				
				if ( ! $this->image_lib->resize())
				{
					echo $this->image_lib->display_errors();
				}
				//
				$config['image_library'] = 'gd2';
				$config['library_path'] = 'assets/uploads/';
				$config['source_image'] = 'assets/uploads/'.$name;
				$config['create_thumb'] = TRUE;
				$config['thumb_marker'] = false;
				$config['new_image'] = 'assets/uploads/thumbs/760/'.$name;
				$config['width'] = '760';
				$config['height'] = '570';
				$config['maintain_ratio'] = true;
				$config['master_dim'] = 'width';
				$this->image_lib->initialize($config); 
				
				if ( ! $this->image_lib->resize())
				{
					echo $this->image_lib->display_errors();
				}
	}
	
	public function getDocuments($entryId, $field_id){
		if(is_numeric($entryId)){
			$images = $this->Advertcategorymodel->getDocuments($entryId, $field_id);
			$r['documents'] = $images;
			$this->load->view('forms/uploaded_document',$r);
		}
	}

	// http://localhost/cladding-working/Post/getImages/308/206
	public function getImages($entry_id, $field_id, $ptBlocksMultipleDatasId=''){
		// echo $entry_id;exit;
		//echo $field_id;
		// _pre($_POST);
		
		$additionalFormType = '';
		if(isset($_POST['additionalFormType'])){
			$additionalFormType = $_POST['additionalFormType'];
		}

		$imageRepeaterAdvIdsArr = array();
		$categoryId = 56;
		$advertFieldsFraewRepeater = $this->PtBlocksCategoryModel->getAdvertFieldsFraewRepeater($categoryId);
		foreach ($advertFieldsFraewRepeater as $key => $value) {
			$advertFieldsByImageFieldType = $this->PtBlocksCategoryModel->getAdvertFieldsByImageFieldType($value->adv_category);
			foreach ($advertFieldsByImageFieldType as $key => $value) {
				$imageRepeaterAdvIdsArr[] = $value->adv_id;
			}
		}
		// _pre($imageRepeaterAdvIdsArr);
		if( in_array($field_id, $imageRepeaterAdvIdsArr) || !empty($additionalFormType) ){
			// echo "if";exit;
			if($ptBlocksMultipleDatasId == ''){// in repeater section for page
				$ptImagesIdsArray = getBalconyAdditionalPtImageIds($entry_id);
				$images = $this->Advertcategorymodel->getImages1($entry_id, $field_id, $ptImagesIdsArray);
				// _pre($images);
			}
			else{// in repeater section for modal
				$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);
				$single_row_data = $ptBlocksMultipleDatas->single_row_data;
				$single_row_data_array = json_decode($single_row_data, true);
				// _pre($single_row_data_array);
				if(!empty($single_row_data_array['pt_images_ids'])){
					foreach ($single_row_data_array['pt_images_ids'] as $key1 => $value1) {
						$ptImagesIdsArray[] = $value1;
					}
				}
				if($ptImagesIdsArray){
					$images = $this->Advertcategorymodel->getImages2($ptImagesIdsArray);
				}
				else{
					$images = array();
				}
			}
		}
		else{
			// echo "else";exit;
			$images = $this->Advertcategorymodel->getImages($entry_id, $field_id);
		}

		$r['images'] = $images;
		$r['field_id'] = $field_id;

		$r['field'] = $this->Advertcategorymodel->getField($field_id);

		$r['additionalFormType'] = $additionalFormType;
		$r['ptBlocksMultipleDatasId'] = $ptBlocksMultipleDatasId;

		// _pre($r);

		$this->load->view('forms/uploaded_image',$r);
	}

	public function getImagesRepeaterAdd($entry_id, $field_id, $ptBlocksMultipleDatasId=''){
		$status = false; $message = ''; $resultData = array();
		// echo $entry_id;exit;
		//echo $field_id;
		// _pre($_POST);

		/*
		http://localhost/cladding-working/post/getImagesRepeaterAdd/0/206
		entry_id = 0
		field_id = 206
		Array
		(
		    [hdnTempAddImage] => [164]
		    [additionalFormType] => add
		)
		*/

		$hdnTempAddImage = $_POST['hdnTempAddImage'];
		$hdnTempAddImageArray = json_decode($hdnTempAddImage, true);
		$ptImagesIdsArray = $hdnTempAddImageArray;

		$additionalFormType = '';
		if(isset($_POST['additionalFormType'])){
			$additionalFormType = $_POST['additionalFormType'];
		}

		$imageRepeaterAdvIdsArr = array();
		$categoryId = 56;
		$advertFieldsFraewRepeater = $this->PtBlocksCategoryModel->getAdvertFieldsFraewRepeater($categoryId);
		foreach ($advertFieldsFraewRepeater as $key => $value) {
			$advertFieldsByImageFieldType = $this->PtBlocksCategoryModel->getAdvertFieldsByImageFieldType($value->adv_category);
			foreach ($advertFieldsByImageFieldType as $key => $value) {
				$imageRepeaterAdvIdsArr[] = $value->adv_id;
			}
		}
		// _pre($imageRepeaterAdvIdsArr);
		$images = array();
		if( in_array($field_id, $imageRepeaterAdvIdsArr) || !empty($additionalFormType) ){
			if($ptImagesIdsArray){
				$images = $this->Advertcategorymodel->getImages2($ptImagesIdsArray);
			}
		}

		if($images){
			$status = true;
		}

		$r['images'] = $images;
		$r['field_id'] = $field_id;

		$r['field'] = $this->Advertcategorymodel->getField($field_id);

		$r['additionalFormType'] = $additionalFormType;
		$r['ptBlocksMultipleDatasId'] = $ptBlocksMultipleDatasId;

		// _pre($r);

		$html = $this->load->view('forms/uploaded_image', $r, true);

		$resultData = array(
			'html' => $html
		);

		$responseData = array('status' => $status, 'message' => $message, 'data' => $resultData);

		// return $responseData;
		echo json_encode($responseData);
        exit;
	}

	public function getImagesArrow($entry_id, $field_id){
		$status = false; $message = ''; $resultData = array();

		$postData = $this->input->post();

		// $imgId = $this->input->post('imgId');

		// _pre($postData);

		/*Array
		(
		    [imgId] => 183
		)*/

		//echo $entry_id;
		//echo $field_id;
		// $images = $this->Advertcategorymodel->getImages($entry_id, $field_id);
		// _pre()
		// $r['images'] = $images;
		// $r['field_id'] = $field_id;

		// $r['field'] = $this->Advertcategorymodel->getField($field_id);
		// _pre($r);

		// $imageInfo = $this->Advertcategorymodel->getInfoById($imgId);

		$data = array(
			// 'images' => $images,
			// 'imageInfo' => $imageInfo
			'entry_id' => $entry_id,
			'field_id' => $field_id
		);

		$html = $this->load->view('admin/modal_image_arrow', $data, true);

		$resultData = array(
			'html' => $html
		);

		$status = true;
		$responseData = array('status' => $status, 'message' => $message, 'data' => $resultData);

		// return $responseData;
		echo json_encode($responseData);
        exit;
	}

	//guess what this one does??
	public function rotate($direction){
		//need to add a check here 
		//to make sure users can only rotate their own image
		// File and rotation
		$this->form_validation->set_rules('img', 'Image', 'required');
		$this->form_validation->set_rules('imgId', 'ImageId', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//check its their image
			$userImage = $this->Advertcategorymodel->getImage($this->input->post('imgId'), $this->session->userdata("us_id"));
			if($userImage!=false){
				$filename = "assets/uploaded_images/".$userImage[0]['up_filename'];	
				switch($direction){
					case 'clockwise':
						$degrees = -90;
					break;
					case 'anticlockwise':
						$degrees = 90;
					break;	
				}
				
				// Content type
				//header('Content-type: image/jpeg');
				// Load
				$source = imagecreatefromjpeg($filename);
				// Rotate
				$rotate = imagerotate($source, $degrees, 0);
				// Output
				imagejpeg($rotate, $filename);
				
				imagedestroy($source);
				imagedestroy($rotate);
			}
		}	
	}

	
	public function rotateAccount($direction){
		//need to add a check here 
		//to make sure users can only rotate their own image
		// File and rotation
		$this->form_validation->set_rules('img', 'Image', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//check its their image
			$userImage = $this->data['us_image'];
			if($userImage!=false){
				$filename = "assets/uploads/".$userImage;	
				switch($direction){
					case 'clockwise':
						$degrees = -90;
					break;
					case 'anticlockwise':
						$degrees = 90;
					break;	
				}
				
				// Content type
				//header('Content-type: image/jpeg');
				// Load
				$source = imagecreatefromjpeg($filename);
				// Rotate
				$rotate = imagerotate($source, $degrees, 0);
				// Output
				imagejpeg($rotate, $filename);
				// Free the memory
				$this->makeThumbs($userImage);
				
				imagedestroy($source);
				imagedestroy($rotate);
			}
		}	
	}

	//saves user image caption
	public function saveCaption(){
		$this->form_validation->set_rules('imageId', 'Image Id', 'required');
		$this->form_validation->set_rules('caption', 'caption', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//check the user owns this image
			if($this->Advertcategorymodel->getImage($this->input->post('imageId'), $this->session->userdata("us_id"))!=false){
				$update = array('im_caption'=>$this->input->post('caption'));
				$this->db->where('im_id',$this->input->post('imageId'));
				$this->db->update('pt_images',$update);
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('captionUpdated')));
			}
		}
	}
	
	//saves user image caption
	public function deleteImage(){
		$this->form_validation->set_rules('imageId', 'Image Id', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//check the user owns this image
			if($this->input->post('type')=='document'){
				if($this->Advertcategorymodel->getDocument($this->input->post('imageId'), $this->session->userdata("us_id"))!=false){
					$this->db->select('*');
					$this->db->where('do_id',$this->input->post('imageId'));
					$file = $this->db->get('pt_documents')->row_array();
					$this->db->delete('pt_documents', array('do_id' => $this->input->post('imageId'))); 
					unlink('assets/documents/'.$file['do_name']);
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('imageDeleted')));
				}
			}else if(strtolower($this->input->post('type'))=='plan'){
				if($this->Advertcategorymodel->getFloorPlan($this->input->post('imageId'), $this->session->userdata("us_id"))!=false){
					$this->db->select('*');
					$this->db->where('fl_id',$this->input->post('imageId'));
					$file = $this->db->get('pt_floorplan')->row_array();
					$this->db->delete('pt_floorplan', array('fl_id' => $this->input->post('imageId'))); 
					unlink('assets/plans/'.$file['fl_name']);
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('imageDeleted')));
				}
			}else{
				if($this->Advertcategorymodel->getImage($this->input->post('imageId'), $this->session->userdata("us_id"))!=false){
					$this->db->delete('pt_images', array('im_id' => $this->input->post('imageId'))); 
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('imageDeleted')));
				}
			}
		}
	}

	public function checkCode(){
		$this->form_validation->set_rules('promoId', 'Promo ID', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			//ok we need to 
			//check the code exists
			//check if it is private or trade
			//if private
			//has it been used
			//single use, log user who used it
			//credit balance
			//if trade
			//check if has advert open?
			//no more credits than has adverts needing posted
			//max 3
			//credit balance
			$this->db->select('*');
			$this->db->where('pr_name',$this->input->post('promoId'));
			$promoData = $this->db->get('pt_promo_codes')->row_array();
			//var_dump($promoData);
			if(sizeof($promoData)>0){
				if($promoData['pr_type']==0){
					//its a private code
					if($this->data['us_usertype']==0){
						//check if this user has used this code
						$this->db->select('*');
						$this->db->where('pro_promoId',$promoData['pr_id']);
						$this->db->where('pro_userid',$this->userid);
						$hasUsed = $this->db->get('pt_promocode_use_log')->row_array();
						if(sizeof($hasUsed)>0){
							echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codeUsed')));
							exit();	
						}else{
							//its fine to use	
							//get credit amount
							//get first credit amount
							$credits = $this->Paymentmodel->getCredits(1);
							$this->db->query('UPDATE pt_user SET us_credits=us_credits+'.$credits['cr_credits'].' WHERE us_id="'.$this->userid.'"');
							//its replaced with this method, allowing us to 'expire' credits
							$this->Paymentmodel->creditExpire($this->userid, $credits['cr_credits']);
							$this->Paymentmodel->logTransaction($credits['cr_credits'].' Credits added to your account', $this->userid,1);
							//log use of the code
							$insert = array('pro_promoId'=>$promoData['pr_id'],'pro_userid'=>$this->userid,'pro_date'=>date('Y-m-d H:i:s',strtotime('now')));
							$this->db->insert('pt_promocode_use_log',$insert);
							echo json_encode(array('status'=>1,'msg'=>$this->lang->line('creditsRedeemed')));
							exit();	
						}
					}else{
						echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codeTradeNotPrivate')));
						exit();			
					}				
				}else if($promoData['pr_type']==1){
					//echo '##';
					if($this->data['us_usertype']==1){
						if($promoData['pr_userid']==$this->userid){
							$this->db->select('*');
							$this->db->where('ad_hasPayed',0);
							$this->db->where('ad_userid',$this->userid);
							$pendingAds = $this->db->get($this->default_table)->result_array();
							$pendingAdsNumber = sizeof($pendingAds);
							//echo $pendingAdsNumber;
							$this->db->select('*');
							$this->db->where('pro_promoId', $promoData['pr_id']);
							$this->db->where('pro_userid',$this->userid);
							$usedCodes = $this->db->get('pt_promocode_use_log')->result_array();
							$usedCodesNumber = sizeof($usedCodes);
							//echo '#'.$usedCodesNumber;
							//if(($pendingAdsNumber-$usedCodesNumber)>1){
							//they wanted this security switched off
							/*
								if($pendingAdsNumber>3){
									echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codeNotUsed')));
									exit();	
								}else{
							*/
									$credits = $this->Paymentmodel->getCredits(1);
									$this->db->query('UPDATE pt_user SET us_credits=us_credits+'.$credits['cr_credits'].' WHERE us_id="'.$this->userid.'"');
									//its replaced with this method, allowing us to 'expire' credits
									$this->Paymentmodel->creditExpire($this->userid, $credits['cr_credits']);
									$this->Paymentmodel->logTransaction($credits['cr_credits'].' Credits added to your account', $this->userid,1);
									//log use of the code
									$insert = array('pro_promoId'=>$promoData['pr_id'],'pro_userid'=>$this->userid,'pro_date'=>date('Y-m-d H:i:s',strtotime('now')));
									$this->db->insert('pt_promocode_use_log',$insert);
									
									echo json_encode(array('status'=>1,'msg'=>$this->lang->line('creditsRedeemed')));
									exit();	
								//}
							//}else{ 
								//Not sure that this error is required, removed it and see what happens
								/*
								echo json_encode(array('status'=>0,'msg'=>($pendingAdsNumber-$usedCodesNumber).'Unpublished Adverts:'.$pendingAdsNumber.'<br>Number times code used:'.$usedCodesNumber.'<br>'.$this->lang->line('codeInvalid')));
								exit();
								*/		
							//}
						}else{
							echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codeNotUserId')));
							exit();		
						}
					}else{
						echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codePrivateNotTrade')));
						exit();		
					}
				}
			}else{
				//the promo code doesn't exist
				echo json_encode(array('status'=>0,'msg'=>$this->lang->line('codeNotExist')));
				exit();				
			}
			
		}
	}
	
	
	public function view_quote($category){
		//containerEnd view has most of the JS in it
		$data = '';
		$data['width'] = 6;
		$data['ad_id'] = 0;
		$data['listing'] = '';

		$data['preview'] = true;

		if($this->input->get('contactID')!=''){
			$data['contactID'] = $this->input->get('contactID');
		}

		if($this->uri->segment(4)){
			//echo $this->uri->segment(3);	
			if(!$this->Advertcategorymodel->checkAdvertOwner($this->uri->segment(4), $this->session->userdata("us_id"))){
				redirect(base_url('myaccount'));
			}else{
				$data['listing'] = $this->Advertcategorymodel->getAdvert($this->uri->segment(4));	
			}
			$data['ad_id'] = $this->uri->segment(4);

			$this->db->where('ad_id', $entryId);
			$arry = array('ad_position'=>4);
			$this->db->update($this->default_table, $arry);
		}
		//var_dump($data['listing']);
		//$category = 1;
		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$this->db->order_by('ad_order','ASC');
		$result = $this->db->get($this->default_category_table)->result_array();
		//var_dump($result);
		$data['sellerDetails'] = $this->Usermodel->selectUser($this->session->userdata("us_id"));
		$data['category'] = $category;

		$data['image'] = base_url()."/assets/images/contacts.png";
		$data['section'] = 'View Quote';
		$data['subtitle'] = '';
		
		$this->load->view('admin/leftpanel');
		$this->load->view('admin/header', $data);
		$this->load->view('forms/form_start', $data);
		$data['alert'] = 'success';

		$data['alert'] = 'danger';
		$data['alertid'] = 'warningMessage';
		$this->load->view('common/alert', $data);
		//print_r($result);


		$datasourceColumns = $this->Entriesmodel->getDatasourceColumns();
		$cost = 0;
		$retail = 0;
		$vat = 0;
		//var_dump($datasourceColumns);
		foreach($datasourceColumns as $col){
			//echo $d['ad_'.$col['adv_column']];
			$price = $this->Advertcategorymodel->getDataValuePricing($data['listing']['ad_'.$col['adv_column']]);
			//print_r($price);
			$cost+=$price['ds_cost_price'];
			$retail+=$price['ds_retail_price'];
			$vat+=$price['ds_vat_price'];
		}
		
		$data['cost']=$cost;
		$data['retail']=$retail;
		$data['vat']=$vat;	


		switch($category){
			default:
				foreach($result as $r){
					$r['listing'] = $data['listing'];
					$this->load->view('common/wrapper',$r);
					
					$this->load->view('common/title',$r);
					
					$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);	
					//echo $r['ad_id'].'##';

					$this->load->view('forms/advert_text',$data);
					$this->load->view('common/wrapperEnd',$r);
				}
			break;
		}

		$r['ad_name'] = 'Pricing';
		$this->load->view('common/title',$r);

		$data['text'] = 'Cost price';
		$data['value'] = '&pound;'.number_format($cost);
		$this->load->view('forms/advert_text_single',$data);

		$data['text'] = 'Retail Ex Vat';
		$data['value'] = '&pound;'.number_format($retail);
		$this->load->view('forms/advert_text_single',$data);

		$data['text'] = 'Margin Ex Vat';
		$data['value'] = '&pound;'.number_format(($retail-$cost));
		$this->load->view('forms/advert_text_single',$data);
		//print_r($r);
		
		if($r['listing']['ad_vat']==1){
			//echo '##'.$r['ad_vat'].'##';
			$data['text'] = 'Vat Price';
			$data['value'] = '&pound;'.number_format(($data['vat']));
			$this->load->view('forms/advert_text_single',$data);
		}


		$data['user'] = $this->data;

		$this->load->view('admin/right');
		//$data['scripts_containerEnd'] = true;
		$this->load->view('admin/footer', $data);
		//$this->load->view('forms/form_end');
	}

	function additional_field(){

		$this->form_validation->set_rules('name', 'Name', 'required');
		if ($this->form_validation->run() == FALSE){
			exit();
		}else{
			$r['name'] = $this->input->post('name');
			$r['id'] = substr(md5(uniqid(mt_rand(), true)), 0, 5);
			$this->load->view('forms/additional_fields_fields',$r);
		}
	}

	function remove_field(){

		$this->form_validation->set_rules('deleteID', 'Delete ID', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			exit();
		}else{
			$this->db->where('adi_id',$this->input->post('deleteID'));
			$this->db->delete('pt_additional_items');
		}
	}

	public function orderImages(){

		$sectionids = $this->input->post('order');
		$order = explode(',',$sectionids);
        $count = 1;

        if (is_array($order)) {
            foreach ($order as $sectionid) {
               
               	$update = array('up_order'=>$count);
                // your DB query here
                $this->db->where('up_id', $sectionid);
                $this->db->update('pt_images', $update);
               
               	echo $this->db->last_query();

                $count++;
            }
           
            echo '{"status": 1}';
        } else {
            echo '{"status": 0}';
        }
	}

	public function orderDocuments(){

		$sectionids = $this->input->post('order');
		$order = explode(',',$sectionids);
        $count = 1;

        if (is_array($order)) {
            foreach ($order as $sectionid) {
               
               	$update = array('up_order'=>$count);
                // your DB query here
                $this->db->where('up_id', $sectionid);
                $this->db->update('pt_documents', $update);
               
               	//echo $this->db->last_query();

                $count++;
            }
           
            echo '{"status": 1}';
        } else {
            echo '{"status": 0}';
        }
	}


	public function deleteImages(){

		$this->form_validation->set_rules('id', 'id', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			$this->db->where('up_id', $this->input->post('id'));
			$data = $this->db->get('pt_images')->row_array();
			unlink('assets/uploaded_images/'.$data['up_filename']);

			$this->db->where('up_id',$this->input->post('id'));
			$this->db->delete('pt_images');

			$ptBlocksMultipleDatasId = $this->input->post('ptBlocksMultipleDatasId');
			$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);
			$single_row_data = $ptBlocksMultipleDatas->single_row_data;

			$single_row_data_array = json_decode($single_row_data, true);
			// _pre($single_row_data_array);
			$single_row_data_array_temp = $single_row_data_array;
			if(!empty(@$single_row_data_array_temp['pt_images_ids'])){
				$key = array_search($this->input->post('id'), @$single_row_data_array_temp['pt_images_ids']);
				if ($key !== false) {
					unset($single_row_data_array_temp['pt_images_ids'][$key]);
					$single_row_data_array_temp['pt_images_ids'] = array_values($single_row_data_array_temp['pt_images_ids']);
					
					$whereArray = array('id' => $ptBlocksMultipleDatasId);
					$crudArray = array('single_row_data' => json_encode($single_row_data_array_temp, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
					$this->PtBlocksMultipleDatas->updateData($crudArray, $whereArray);
				}
			}
			echo '{"status": 1}';
		}
	}

	public function deleteDocuments(){

		$this->form_validation->set_rules('id', 'id', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			$this->db->where('up_id', $this->input->post('id'));
			$data = $this->db->get('pt_documents')->row_array();
			if(isset($data['up_filename'])){
				unlink('assets/uploads/'.$data['up_filename']);
				$this->db->where('up_id',$this->input->post('id'));
				$this->db->delete('documents');


				$Eventsmodel 			= new Eventsmodel();
				$Eventsmodel->note 		= $this->default_table_name.' Document Deleted '.$data['up_filename'];
				$Eventsmodel->table_id 	= $this->default_table;
				$Eventsmodel->user_id 	= $this->userid;
				$Eventsmodel->entry_id 	= $data['up_entry_id'];		
				$Eventsmodel->status 	= 2;
				$Eventsmodel->save();

				$ptBlocksMultipleDatasId = $this->input->post('ptBlocksMultipleDatasId');
				$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getInfoById($ptBlocksMultipleDatasId);
				$single_row_data = $ptBlocksMultipleDatas->single_row_data;

				$single_row_data_array = json_decode($single_row_data, true);
				// _pre($single_row_data_array);
				$single_row_data_array_temp = $single_row_data_array;
				if(!empty(@$single_row_data_array_temp['pt_documents_ids'])){
					$key = array_search($this->input->post('id'), @$single_row_data_array_temp['pt_documents_ids']);
					if ($key !== false) {
						unset($single_row_data_array_temp['pt_documents_ids'][$key]);
						$single_row_data_array_temp['pt_documents_ids'] = array_values($single_row_data_array_temp['pt_documents_ids']);
						
						$whereArray = array('id' => $ptBlocksMultipleDatasId);
						$crudArray = array('single_row_data' => json_encode($single_row_data_array_temp, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
						$this->PtBlocksMultipleDatas->updateData($crudArray, $whereArray);
					}
				}

				echo json_encode(array('status'=>1));
			}else{
				echo json_encode(array('status'=>0));
			}
		}
	}

	public function caption(){
		
		$this->form_validation->set_rules('image_id', 'Image ID', 'required|numeric');
		$this->form_validation->set_rules('caption', 'Caption', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{	
			$update['up_caption'] = $this->input->post('caption');

			$this->db->where('up_id', $this->input->post('image_id'));
			$this->db->update('pt_images', $update);
			echo '{"status": 1}';
		}
	}


	public function history($entry_id){

		$data = [];
		$permission = $this->Usermodel->has_permission('create', $this->input->get('formID'));
		$permissions['create'] = $permission;
		
		if($this->uri->segment(4)){
			$data['listing'] = $this->Advertcategorymodel->getAdvert($this->uri->segment(4));
		}
		
		$permission = $this->Usermodel->has_permission('update', $this->input->get('formID'));
		$permissions['update'] = $permission;
	

		$read = $this->Usermodel->has_permission('read', $this->input->get('formID'));
		$permissions['read'] = $read;
		$data = $this->data;

		$data['category'] = $category;

		$permissions['role'] = $this->data['user']['us_role'];
		$data['permissions'] = $permissions;
		
		$data['history'] = true;

		$data['listing'] = $this->Advertcategorymodel->getAdvert($entry_id);
		$data['tabs'] = $this->db->where('ad_parent', 0)->get($this->default_category_table)->result();
		$data['formID'] = $this->formID;

		$data['events'] = Eventsmodel::where('entry_id', $entry_id)->where('table_id', $this->default_table)->orderBy('created_at', 'DESC')->get();  
		
		$data['default_table'] = $this->default_table;

		$this->load->view('dashboard/header', $data);
		$this->load->view('templates/form_start', $data);
		$this->load->view('common/wrapper',$data);
		
		$this->load->view('forms/history',$data);

		$this->load->view('common/wrapperEnd',$data);
		$this->load->view('dashboard/footer', $data);
	}

	public function downloadReport($entry_id){
		// _pre($entry_id);

		$data = [];
		$permission = $this->Usermodel->has_permission('create', $this->input->get('formID'));
		$permissions['create'] = $permission;
		
		$segmentsArray = $this->uri->segment_array();
		// _pre($segmentsArray);
		/*Array
		(
		    [1] => Post
		    [2] => downloadReport
		    [3] => 25
		)*/

		if($this->uri->segment(4)){
			$data['listing'] = $this->Advertcategorymodel->getAdvert($this->uri->segment(4));
		}
		
		$permission = $this->Usermodel->has_permission('update', $this->input->get('formID'));
		$permissions['update'] = $permission;
	

		$read = $this->Usermodel->has_permission('read', $this->input->get('formID'));
		$permissions['read'] = $read;
		$data = $this->data;

		$data['category'] = $category;

		$permissions['role'] = $this->data['user']['us_role'];
		$data['permissions'] = $permissions;
		
		$data['downloadReport'] = true;

		$data['listing'] = $this->Advertcategorymodel->getAdvert($entry_id);
		$data['tabs'] = $this->db->where('ad_parent', 0)->get($this->default_category_table)->result();
		$data['formID'] = $this->formID;

		$data['events'] = Eventsmodel::where('entry_id', $entry_id)->where('table_id', $this->default_table)->orderBy('created_at', 'DESC')->get();  

		$userId = $this->userid;
		$data['entry_id'] = $entry_id;
		
		/*$whereArray = array('user_id' => $userId, 'ad_id' => $entry_id);
		$ptQuoteReportDownloadsData = $this->PtQuoteReportDownloadsModel->getData($whereArray);*/
		// _pre($ptQuoteReportDownloadsData);

		// $data['ptQuoteReportDownloadsData'] = $ptQuoteReportDownloadsData;

		$this->load->view('dashboard/header', $data);
		$this->load->view('templates/form_start', $data);
		$this->load->view('common/wrapper',$data);
		
		$this->load->view('forms/download_report_tab',$data);

		$this->load->view('common/wrapperEnd',$data);
		$this->load->view('dashboard/footer', $data);
	}
	public function getQuoteReportDownloadsDataAjax(){
        $userId = $this->userid;

        $category = $this->input->post('category');
        $entryId = $this->input->post('entryId');
        
        $searchArray = array(
            'category' => $category,
            'userId' => $userId,
            'entryId' => $entryId,
        );
        $list = $this->PtQuoteReportDownloadsModel->get_list($searchArray);
        // echo $this->db->last_query();exit;
        // _pre($list);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $list_key => $list_value) {
            $no++;
            $p_key = $list_value->id;
            $row = array();
            $row[] = $no;
            $row[] = date('d/m/Y', strtotime($list_value->created_at));
            $row[] = $list_value->template;
            $row[] = _set_dash($list_value->filename);
            $row[] = $list_value->status;

            $actionHtml = $downloadButton = '';
			if($list_value->status == "Completed"){
				$downloadButton = "<a href='".base_url('Post/downloadReportPdf/'.$list_value->filename.'/'.$list_value->random_string)."' target='_blank'>Download</a>";
			}
			else{
				$downloadButton = "-";
			}
			$actionHtml .= $downloadButton;
            
            $row[] = $actionHtml;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->PtQuoteReportDownloadsModel->count_all($searchArray),
            "recordsFiltered" => $this->PtQuoteReportDownloadsModel->count_filtered($searchArray),
            "data" => $data,
        );
        //output to json format
        echo json_encode($output);
    }
	public function generateReportPdf(){
		$status = false; $message = ''; $responseData = array();

		$category = $this->input->post('category');
		$adId = $this->input->post('adId');
		$format = 'pdf';
		$template = 'SBA';

		$returnFrom = 'downloadPdfBeforeMerge';

		$returnArray = $this->Quotesmodel->generate_quote($category, $adId, NULL, 0, true, false, $format, NULL, $template, $returnFrom);

		if($returnArray['status']){
			$fileName = $returnArray['data']['fileName'];
			$status = true;
			$responseData = array(
				'downloadUrl' => base_url('Post/downloadReportPdf/'.$fileName)
			);
		}
		else{
			$message = 'Failed to generate pdf';
		}

		$resultData = array(
			'status' => $status,
			'message' => $message,
			'data' => $responseData,
		);
		echo json_encode($resultData);
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

	public function generateReportPdfWithAttachment(){
		$status = false; $message = ''; $responseData = array();

		$category = $this->input->post('category');
		$adId = $this->input->post('adId');
		$format = 'pdf';
		
		if($category == 32){
			$template = 'SBA';
		}
		else if($category == 56){
			$template = 'FRAEW';
		}

		$userId = $this->userid;

		$crud = array(
			'user_id' => $userId,

			'category' => $category,
			'ad_id' => $adId,

			'format' => $format,
			'template' => $template,

			'status' => 'Pending',

			'created_at' => $this->currentDatetime,
		);

		$insertedId = $this->PtQuoteReportDownloadsModel->insertData($crud);
		if($insertedId){
			$status = true;
			$message = 'Generating report, please check back shortly';
		}
		else{
			$message = 'Failed to generate pdf';
		}

		$resultData = array(
			'status' => $status, 'message' => $message, 'data' => $responseData,
		);
		echo json_encode($resultData);
        exit;
	}
	public function generateFRAEWReportPdf(){
		$status = false; $message = ''; $responseData = array();

		$category = $this->input->post('category');
		$adId = $this->input->post('adId');
		$format = 'pdf';
		$template = 'FRAEW';

		$returnFrom = 'downloadPdfBeforeMerge';

		$userId = $this->userid;

		$returnArray = $this->Quotesmodel->generate_quote($category, $adId, NULL, 0, true, false, $format, NULL, $template, $returnFrom);

		if($returnArray['status']){
			$status = true;
			$fileName = $returnArray['data']['fileName'];

			$responseData = array(
				'downloadUrl' => base_url('Post/downloadReportPdf/'.$fileName)
			);

			$crud = array(
				'user_id' => $userId,

				'category' => $category,
				'ad_id' => $adId,

				'format' => $format,
				'template' => $template,

				'filename' => $fileName,

				'status' => 'Completed',

				'created_at' => $this->currentDatetime,
			);

			$insertedId = $this->PtQuoteReportDownloadsModel->insertData($crud);
			if($insertedId){
			}
			else{
			}
		}
		else{
			$message = 'Failed to generate pdf';
		}

		$resultData = array( 'status' => $status, 'message' => $message, 'data' => $responseData );
		echo json_encode($resultData);
        exit;
	}

	private function _getArrayForTable1($postData, $advColumn){
		$output = array();
	    $flag = 0;
	    $tempArray = array();
	    foreach ($postData as $key => $value) {
	    	if($key == $advColumn."_row_0_column0"){
	    		$tempArray[] = $value[0];
	    		$flag = 1;
	    	}
	    	else if($key == $advColumn."_row_0_column1"){
	    		$tempArray[] = $value[0];
	    		$flag = 1;
	    	}
	    }
    	if($flag){
    		$output[] = $tempArray;
    	}
	    return $output;
	}
	private function _getArrayForTable2($postData){
		$output = array();
		for($i=2; ; $i++) {
			$str1 = 'row_'.$i.'_column2';
			$str2 = 'row_'.$i.'_column3';
			if( isset($postData[$str1]) && isset($postData[$str2]) ){
				$tempArray = array( $postData[$str1][0], $postData[$str2][0] );
				$output[] = $tempArray;
			}
			else{
				break;
			}
		}
	    return $output;
	}

	
	private function _getArr1($postData, $advColumn){
		$arr1 = array();
		foreach ($postData as $key => $value) {
			if(strpos($key, $advColumn.'_row_') === 0){
				$arr1[] = $key;
			}
		}
		return $arr1;
	}
	private function _getArr2($postData){
		$arr2 = array();
		foreach ($postData as $key => $value) {
			if(strpos($key, 'row_') === 0){
				$arr2[] = $key;
			}
		}
		return $arr2;
	}
	private function _getArrayForTable1Update($postData, $arr1, $advColumn){
		$output = array();
		for($i=0; ; $i++) {
			$str1 = $advColumn.'_row_'.$i.'_column0';
			$str2 = $advColumn.'_row_'.$i.'_column1';
			if( isset($postData[$str1]) && isset($postData[$str2]) ){
				$tempArray = array( $postData[$str1][0], $postData[$str2][0] );
				$output[] = $tempArray;

				$key = array_search($str1, $arr1);
				if ($key !== false) {
					unset($arr1[$key]);
				}
				$key = array_search($str2, $arr1);
				if ($key !== false) {
					unset($arr1[$key]);
				}
			}
			if(empty($arr1)){
				break;
			}
		}
	    return $output;
	}
	private function _getArrayForTable2Update($postData, $arr2){
		$output = array();
		for($i=0; ; $i++) {
			$str1 = 'row_'.$i.'_column2';
			$str2 = 'row_'.$i.'_column3';
			if( isset($postData[$str1]) && isset($postData[$str2]) ){
				$tempArray = array( $postData[$str1][0], $postData[$str2][0] );
				$output[] = $tempArray;

				$key = array_search($str1, $arr2);
				if ($key !== false) {
					unset($arr2[$key]);
				}
				$key = array_search($str2, $arr2);
				if ($key !== false) {
					unset($arr2[$key]);
				}
			}
			if(empty($arr2)){
				break;
			}
		}
	    return $output;
	}
}