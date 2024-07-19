<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datasource extends My_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function refreshField($fieldId, $entry_id){

		$listing = $this->db->where('ad_id', $entry_id)->get($this->default_table)->row_array();
		$field = $this->Advertcategorymodel->getField($fieldId, $listing);
		echo json_encode(array('status' => 1, 'column' => $field[0]['adv_column'], 'msg' => $field[0]['html']));

	}

	public function listDatasources(){
		$this->db->select('*');
		$result = $this->db->get('datasources')->result_array();
		$html = '<h4>Custom</h4>';
		foreach($result as $r){
			$html.='<div id="datasource_'.$r['da_id'].'"><input type="radio" name="dsRadio" value="'.$r['da_id'].'" class="dsRadios" data-id="'.$r['da_name'].'">&nbsp;'.$r['da_name'].' <a href="javascript:editDS(\''.$r['da_id'].'\')">Edit</a> <i class="fa fa-times deleteDataSource" aria-hidden="true" data-id="'.$r['da_id'].'"></i><br></div>';
		}
		$html.= '<br><h4>Tables</h4>';
		$forms = FormsTablesmodel::all()->sortByDesc("ft_name");
		foreach($forms as $f){
			$html.='<div id="datasource_'.$f->ft_id.'"><input type="radio" name="dsTables" value="'.$f->ft_id.'" class="dsTables" data-id="'.$f->ft_name.'">&nbsp;'.$f->ft_name.'<br></div>';
		}
		echo $html;
	}
	public function updateSecondField(){
		$this->form_validation->set_rules('data', 'Data', 'required');
		if ($this->form_validation->run() == FALSE){
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
				exit();
		}else{	
			$d = explode(':',$this->input->post('data'));
			$update = array('adv_associated_fieldId'=>$d[1]);
			$this->db->where('adv_id',$d[0]);
			$this->db->update('advert_fields',$update);
			echo $this->db->last_query();
		}
	}
	public function saveDataSource(){
		$this->load->model('Advertcategorymodel');
		$this->form_validation->set_rules('dataSourceName', 'Data Source Name', 'required');
		if ($this->form_validation->run() == FALSE){
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
				exit();
		}else{	
				if($this->Advertcategorymodel->dataSourceExists($this->input->post('dataSourceName'))){
					echo json_encode(array('status'=>0,'msg'=>$this->lang->line('dataSourceNameDuplicate')));
					exit();
				}else{
					$data = array('da_name'=>$this->input->post('dataSourceName'));
					$this->db->insert('datasources',$data);
					echo json_encode(array('status'=>1,
											'id'=>$this->db->insert_id(),
											'name'=>$this->input->post('dataSourceName'),
											'msg'=>$this->lang->line('dataSourceNameAdded')
									));
					exit();		
				}
		}
	}
	
	public function saveDataSourceValues(){
		$this->load->model('Advertcategorymodel');
		$this->form_validation->set_rules('values', 'Data Source Values', 'required');
		$this->form_validation->set_rules('dsId', 'Data Source ID', 'required');
		if ($this->form_validation->run() == FALSE){
				echo json_encode(array('status'=>0,'msg'=>validation_errors()));
				exit();
		}else{	
				if(!$this->Advertcategorymodel->dataSourceExistsId($this->input->post('dsId'))){
					echo json_encode(array('status'=>0,'msg'=>$this->lang->line('dataSourceIDNotRecognized')));
					exit();
				}else{
					$valuesCSV = explode(',',$this->input->post('values'));
					foreach($valuesCSV as $v){
						$v = trim($v);
						//echo $v;
						if(preg_match("/[0-9]{1,3}-[0-9]{1,3}/", $v)){
							$num = explode('-',$v);
							while($num[0]<=$num[1]){
								if(!$this->Advertcategorymodel->dsValueExistsInDs($num[0], $this->input->post('dsId'))){
									$data = array('ds_value'=>$num[0],'ds_ds_id'=>$this->input->post('dsId'));
									//print_r($data);
									$this->db->insert('ds_values',$data);
									//echo $this->db->last_query();
								}
								$num[0]++;
							}
							echo json_encode(array('status'=>1));
							exit();	
						}

						if(!$this->Advertcategorymodel->dsValueExistsInDs($v, $this->input->post('dsId'))){
							$data = array('ds_value'=>$v,'ds_ds_id'=>$this->input->post('dsId'));
							//print_r($data);
							$this->db->insert('ds_values',$data);
							//echo $this->db->last_query();
						}
					}
					
					//we don't need any messages just shut the box
					echo json_encode(array('status'=>1));
					exit();		
				}
		}
	}

	public function getFieldsCategory($cat){
		$this->load->model('Advertcategorymodel');
		$this->data['fields'] = $this->Advertcategorymodel->getFields($cat);
		//var_dump($this->data['fields']);
		$this->load->view('admin/lists/ajax/fields', $this->data);
	}


	public function getField($id){
		$this->db->select('*');
		$this->db->join('field_type','fi_id=adv_field_type');
		$this->db->where('adv_id',$id);
		$row = $this->db->get('advert_fields')->row_array();
		if($row['adv_datasourceId']!=0){
			$this->db->select('*');
			$this->db->where('da_id',$row['adv_datasourceId']);
			$ds = $this->db->get('datasources')->row_array();
			$row['dsName'] = $ds['da_name']; 
			$row['dsId'] = $ds['da_id']; 
		}
		echo json_encode(array('status'=>1,'data'=>$row));	
	}

	public function getDatasourceValues($id){
		if($id!=''){
			if(is_numeric($id)){
				$this->db->select('*');
				$this->db->where('ds_ds_id',$id);
				$data = $this->db->get('ds_values')->result_array();	
				$this->load->view('templates/datasource/ds_values_table_open');
				foreach($data as $d){
					$this->load->view('templates/datasource/ds_values_edit', $d);
				}
				$this->load->view('templates/datasource/ds_values_add', $d);
				$this->load->view('templates/datasource/ds_values_table_close');
			}
		}
	}

	public function deleteDSValue(){
		$this->form_validation->set_rules('ds_ds_id', 'Datasource ID', 'required|numeric');
		$this->form_validation->set_rules('ds_id', 'Data ID', 'required|numeric');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//echo '#';
			$this->db->select('*');
			$this->db->where('adv_datasourceId',$this->input->post('ds_ds_id'));
			$fieldsUsingDatasource = $this->db->get('advert_fields')->result_array();
			//echo $this->db->last_query();
			if(sizeof($fieldsUsingDatasource)==0){
				//no fields are using this datasource
				//so delete as you like
				$this->db->where('ds_id', $this->input->post('ds_id'));
				$this->db->delete('ds_values'); 
				echo json_encode(array('status'=>1,'msg'=>$this->lang->line('dsItemDelete')));
				exit();
			}else{
				//exists in the adverts table somewhere
				//var_dump($fieldsUsingDatasource);
				$canDelete = true;
				foreach($fieldsUsingDatasource as $f){
					$this->db->select('*');
					$this->db->where('ad_'.$f['adv_column'],$this->input->post('ds_id'));
					$usingValue = $this->db->get(strtolower($f['adv_table']))->result_array();
					//the value is in use so can't be deleted
					if(sizeof($usingValue)>0){
						$canDelete = false;
						echo json_encode(array('status'=>0,'msg'=>$this->lang->line('dsItemNotDelete')));
						exit();
					}
				}
				if($canDelete==true){
					$this->db->where('ds_id', $this->input->post('ds_id'));
					$this->db->delete('ds_values'); 
					echo json_encode(array('status'=>1,'msg'=>$this->lang->line('dsItemDelete')));
					exit();
				}
			}
		}
	}

	public function editDSValue(){
		$this->form_validation->set_rules('id', 'ID', 'required|numeric');
		$this->form_validation->set_rules('value', 'Value', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$array = array('ds_value'=>$this->input->post('value'),'ds_cost_price'=>$this->input->post('cost_price'),'ds_retail_price'=>$this->input->post('retail_price'));

			if($this->input->post('manual')==1){
				$array['ds_vat_price'] = $this->input->post('vat_price');
				$array['ds_manual_added'] = 1;
			}else{
				//$array['ds_vat_price'] = '';
				$array['ds_manual_added'] = 0;

				$vatRate = $this->Quotesmodel->getSetting('vat');
				$vatRate = ($vatRate+100)/100;
				$array['ds_vat_price'] = ($this->input->post('retail_price')*$vatRate);
			}
			$this->db->where('ds_id',$this->input->post('id'));
			$this->db->update('ds_values', $array);
			//nothing to say really
			//echo $this->db->last_query();
			echo json_encode(array('status'=>1));
			exit();
		}
	}
	
	public function addDSValue(){
		$this->form_validation->set_rules('value', 'Value', 'required');
		$this->form_validation->set_rules('ds_id', 'DS Id', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			$array = array('ds_value'=>$this->input->post('value'),'ds_ds_id'=>$this->input->post('ds_id'));
			$this->db->insert('ds_values', $array);
			//echo $this->db->last_query();
			//nothing to say really
			echo json_encode(array('status'=>1));
			exit();
		}
	}
	

	public function delete_datasource(){
		$this->form_validation->set_rules('datasource_id', 'Datasource ID', 'required');
		if ($this->form_validation->run() == FALSE){
			echo json_encode(array('status'=>0,'msg'=>validation_errors()));
			exit();
		}else{
			//echo $this->input->post('field_id');
			$this->db->where('ds_ds_id',$this->input->post('datasource_id'));
			$this->db->delete('ds_values');

			$this->db->where('da_id',$this->input->post('datasource_id'));
			$this->db->delete('datasources');

			echo json_encode(array('status'=>1,'msg'=>validation_errors()));
			exit();
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */