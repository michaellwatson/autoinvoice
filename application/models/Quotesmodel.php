<?php
//require_once './vendor/dompdf/dompdf/lib/html5lib/Parser.php';
//require_once './vendor/dompdf/dompdf/src/Autoloader.php';
//Dompdf\Autoloader::register();
use Dompdf\Dompdf;

class Quotesmodel extends CI_Model {

    function __construct()
    {
        parent::__construct();
		$this->load->library('session');

		$this->load->model('Notificationmodel');

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
		$this->load->model('Advertcategorymodel');
		$this->load->model('Usermodel');

		$this->data = $this->Usermodel->selectUser($this->session->userdata("us_id"));
    	$this->path = $_SERVER["DOCUMENT_ROOT"].'/ester_new/assets/quotes/';

    	$this->load->library('Fpdf/FpdfLib');
    }

	public function allEntries($limit = NULL, $offset = NULL, $searchArray = array()){

		$allStuff = false;
		$permissions = explode(',',$this->data['us_permissions']);
		if(!in_array('My_Stuff', $permissions)){
			$allStuff = true;
        }

		$this->db->select('*');

		foreach($searchArray as $s){
			switch($s[0]){
				case 'quh_ref':
					$this->db->where('quh_ref',$s[1]);
				break;
				case 'quh_prospect_id':
					$this->db->where('quh_prospect_id',$s[1]);
				break;
			}
		}

		if($allStuff==true){
			$this->db->where('quh_gen_by_id',$this->data['us_id']);
		}
		$this->db->order_by('quh_gen_date','DESC');

		if(($limit==NULL)&&($offset==NULL)){
			$data = $this->db->get('quote_history')->result_array();

			return $data;
		}else{
			$data = $this->db->get('quote_history', $limit, $offset)->result_array();	
			//echo $this->db->last_query();
			return $data;
		}
	}

	public function countEntries($searchArray = array()) {

		$allStuff = false;
		$permissions = explode(',',$this->data['us_permissions']);
		if(!in_array('My_Stuff', $permissions)){
			$allStuff = true;
        }
        //$sql = "SELECT COUNT(*) as c FROM pt_user WHERE us_usertype!=2"; 
        $this->db->select('COUNT(*) as c');

        
		foreach($searchArray as $s){
			switch($s[0]){
				case 'quh_ref':
					$this->db->where('quh_ref',$s[1]);
				break;
				case 'quh_prospect_id':
					$this->db->where('quh_prospect_id',$s[1]);
				break;
			}
		}

		if($allStuff==true){
			$this->db->where('quh_gen_by_id',$this->data['us_id']);
		}

		$query = $this->db->get('quote_history');
		//echo $this->db->last_query();
		$row = $query->row_array();
        return $row['c'];
	}

	private function _deleteTempPdfFiles($allDocuments){
		if(isset($allDocuments) && !empty($allDocuments)){
			foreach ($allDocuments as $allDocumentsKey => $allDocumentsValue) {
				if($allDocumentsKey > 0){ // $allDocumentsKey = 0 is the main pdf file
					@unlink($allDocumentsValue['destinationFilepath']);
				}
			}
		}
	}
	private function _setPageNumbersInPdfs($data){
		$status = false; $message = ''; $responseData = array();

		$ad_id = $data['ad_id'];
		$mainPdf = $data['mainPdf'];
		$mainPdfPath = $data['mainPdfPath'];

		$tempData = array('upFilepath' => $mainPdfPath);
		$pdfData = $this->fpdflib->getPdfData($tempData);
		if($pdfData['status']){
			$mainPdfPagesCount = $pdfData['data']['pagesCount'];
		}
		else{
			$mainPdfPagesCount = 0;
		}

		// ---------------------------------------------
		$allDocuments = array();
		$tempArray = array(
			'up_filename' => $mainPdf,
			'filePath' => $mainPdfPath,
			'up_number_of_pages' => $mainPdfPagesCount,
			'destinationFilename' => $mainPdf,
			'destinationFilepath' => $mainPdfPath,
		);
		$allDocuments[] = $tempArray;

		$Advertfieldsmodel = new Advertfieldsmodel();
		$merge_fields = $Advertfieldsmodel->where('adv_field_type', 28)->where('adv_table', $this->default_table)->get();
		// _pre($merge_fields->toArray(), 0);
		foreach($merge_fields as $m){
			$documents = $this->Advertcategorymodel->getDocuments($ad_id, $m->adv_id);
			// _pre($documents, 0);
			if($documents){
				foreach ($documents as $key => $value) {
					$documents[$key]['filePath'] = FCPATH."assets/uploads/".$value['up_filename'];

					$uploadsTempDirPath = FCPATH."assets/uploads/temp/";
					if (!is_dir($uploadsTempDirPath)) {
					    @mkdir($uploadsTempDirPath, 0755, true);
					}
					$destinationFilename = $value['up_id']."-".$value['up_filename'];
					$destinationFilepath = FCPATH."assets/uploads/temp/".$destinationFilename;

					$documents[$key]['destinationFilename'] = $destinationFilename;
					$documents[$key]['destinationFilepath'] = $destinationFilepath;

					$allDocuments[] = $documents[$key];
				}
			}
		}
		// _pre($allDocuments);

		// ---------------------------------------------
		$totalPages = 0;
		$currentStartingPage = 1;
        foreach ($allDocuments as $key => $value) {
            $allDocuments[$key]['currentStartingPage'] = $currentStartingPage;
            $currentStartingPage += $value['up_number_of_pages'];
            $totalPages += $value['up_number_of_pages'];
        }
		// _pre($allDocuments, 0);
		// _pre($totalPages);
		// exit;

		// ---------------------------------------------
        $tempData = array('allDocuments' => $allDocuments, 'totalPages' => $totalPages);
		$pdfData = $this->fpdflib->modifyPdf($tempData);
		$status = true;

		// ---------------------------------------------
		$responseData = array(
			'allDocuments' => $allDocuments
		);
		$resultData = array(
            'status' => $status,
            'message' => $message,
            'data' => $responseData,
        );
        return $resultData;
	}
	private function _getVersionControls($category, $adId){
		$whereArray = array('block_id' => $adId, 'block_category_id' => $category);
		$ptBlocksVersionControlsData = $this->PtBlocksVersionControlsModel->getData($whereArray, '', 'ASC');
		return $ptBlocksVersionControlsData;
	}

	private function _getRepeatSectionAdditionalData($category, $blockId){
		$ptBlocksCategoryData = $this->PtBlocksCategoryModel->getDataByAdParent($category);
		if($ptBlocksCategoryData){
			foreach ($ptBlocksCategoryData as $key => $value) {
	            $adId = $value['ad_id'];
	            $whereArray = array('pt_block_id' => $blockId, 'pt_blocks_category_id' => $adId);
        		$ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
        		if($ptBlocksMultipleDatas){
        			$ptBlocksCategoryData[$key]['ptBlocksMultipleDatas'] = $ptBlocksMultipleDatas;
        		}
			}
		}
		return $ptBlocksCategoryData;
	}
	private function _getBalconyAdditionalData($adId){
		$whereArray = array('pt_block_id' => $adId);
        $ptBlocksMultipleDatas = $this->PtBlocksMultipleDatas->getData($whereArray, '', 'asc');
        return $ptBlocksMultipleDatas;
	}
	private function _replaceCurlyBracketsData($html, $data){
		$field_data['column']   =  'instructingclient';
     	$field_data['table_id'] =  26;
     	$field_data['field_id'] =  66;
     	$field_data['listing']  = $data['listing'];
     	$clientName = Modules::run('autocomplete/autocomplete/get_value', $field_data);

		$tempAddressArray = array(
            $data['listing']['ad_DevelopmentName'],
            $data['listing']['ad_No'],
            $data['listing']['ad_Street'],
            $data['listing']['ad_Town'],
            $data['listing']['ad_Postcode']
        );
        $tempAddressString = implode(', ', array_filter($tempAddressArray));

		$keyReplace = 'client_name';
		if(!empty($clientName)){
			$html = str_replace('{' . $keyReplace . '}', $clientName, $html);
        }
        else{
			$html = str_replace('{' . $keyReplace . '}', '', $html);
        }

		$keyReplace = 'building_name';
		if(!empty($tempAddressString)){
			$html = str_replace('{' . $keyReplace . '}', $tempAddressString, $html);
        }
        else{
			$html = str_replace('{' . $keyReplace . '}', '', $html);
        }
        return $html;
	}

	public function generate_quote($category, $ad_id, $contact_id, $order=0, $stream = false, $attachState = false, $format = 'pdf', $batch = NULL, $template = NULL, $returnFrom='', $increment = true){
		//containerEnd view has most of the JS in it

		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);

		// $GLOBALS['test'][123] = 5;

		// _pre($GLOBALS);

		$data = array();
		$data['format'] = $format;

		$data['width'] = 6;
		$data['ad_id'] = 0;
		$data['listing'] = '';

		$data['preview'] = true;

		$data['date'] = date('d/m/y',strtotime('now'));

		$data['logo'] = $this->Quotesmodel->getSetting('logo');
		
		$html = '';
		$housesShort = '';
		
		$data['contactID'] = $contact_id;

		$data['listing'] = $this->Advertcategorymodel->getAdvert($ad_id,  $this->default_table);	

		//template override 
		if(is_null($template)){
			$custom_path = 'pdf/custom/'.str_replace('pt_','',$this->default_table).'/';
			$path = 'application/views/'.$custom_path;
			$vpath = 'pdf/';
		}else{

			$custom_path = 'pdf/custom/'.$template.'/';
			$path = 'application/views/'.$custom_path;
			$vpath = 'pdf/';
		
		}

		if(is_dir($path)){
			$vpath = $custom_path;
		}

		//$job = Jobmodel::find($data['listing']['ad_job_id']);
		//$data['taken_by'] = UserEl::find($job->taken_by);

		//appendix part
		/*
		$this->db->select('*');
		$this->db->where('rd_client', $houses['instructing_client']);
		$this->db->where('rd_property_type', $property_type['pt_id']);
		$id_of_template = $this->db->get('pt_required_documents_template')->row_array();
		$id_of_template = $id_of_template['rd_id'];
		
		
		$this->db->select('*');
		$this->db->where('td_template_id', $id_of_template);
		$this->db->join('pt_uploads', 'up_id=td_upload_id');
		$this->db->order_by('td_order', 'ASC');
		$docs = $this->db->get('pt_template_docs')->result_array();
		*/
		$docs = array();

		$data['ref'] = date('dmy',strtotime($data['listing']['ad_added'])).'/'.$data['listing']['ad_rand'];


		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$this->db->order_by('ad_order','ASC');
		$this->db->where('ad_hide',0);

		$result = $this->db->get($this->default_category_table)->result_array();

		// _pre($data);
		$html.=$this->load->view($vpath.'quotes_top',$data,true);

		// _pre($data);


		$datasourceColumns = $this->Entriesmodel->getDatasourceColumns();
		//var_dump($datasourceColumns);
		$cost = 0;
		$retail = 0;
		$vat = 0;

		$quote_entry = array();

		
		if(isset($cost)){
			$quote_entry['cost'] = $cost;

			//echo '##'.$quote_entry['cost'].'##';
			$data['cost']=$cost;
			$data['retail']=$retail;
			$data['vat']=$vat;	
		}
		$data['count'] = 1;

		$rows = '';

		$count = 0; 
		$lastpage = false;
		$length = sizeof($result)-1;

		switch($category){
			default:
				foreach($result as $r){
					$r['listing'] = $data['listing'];
					if($count==$length){
						$lastpage = true;
					}
					$r['lastpage'] = $lastpage;
					$rows.=$this->load->view($vpath.'quotes_title',$r, true);

					$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);	
					//print_r($data['listing']);
					//print_r($data['fields']);


					$rows.=$this->load->view($vpath.'quotes_row',$data,true);
					$data['count']++;
					$count++;
				}
			break;
		}

		$html.=$rows;
		//exit();
		/*
		$data['retail'] = $retail;
		//some calcs
		$data['total'] = $retail;
		*/
		if($data['listing']['ad_vat']==1){
			$data['retail'] = $vat;
			//some calcs
			$data['total'] = $vat;
		}else{
			$data['retail'] = $retail;
			//some calcs
			$data['total'] = $retail;
		}	

		$quote_entry['retail_ex_vat'] = $retail;
		$quote_entry['retail_inc_vat'] = $vat;
		//start additions and discounts
		/*
		//additional fields
		$r['ad_name'] = 'Additional Items';
		
		$html.=$this->load->view('pdf/quotes_title',$r, true);

		$this->db->select('*');
		$this->db->where('adi_table',$this->default_table);
		$this->db->where('adi_ad_id',$data['listing']['ad_id']);
		$this->db->where('adi_type',1);
		$additional_fields_update = $this->db->get('pt_additional_items')->result_array();

		$additionals = 0;
		$vatRate = $this->Quotesmodel->getSetting('vat');
		foreach($additional_fields_update as $a){

			$r['item'] = $a['adi_text'];
			
			if($data['listing']['ad_vat']==1){

				$multiplier = ($vatRate+100)/100;
				$value = $a['adi_value']*$multiplier;
				$r['price'] = $value;
				$additionals+=$value;

			}else{

				$r['price'] = $a['adi_value'];
				$additionals+=$a['adi_value'];
				
			}

			$html.=$this->load->view('pdf/quote_row_generic',$r, true);
		}
		//additional fields end

		
		//discounts start
		$r['ad_name'] = 'Discounts';

		$html.=$this->load->view('pdf/quotes_title',$r, true);

		$this->db->select('*');
		$this->db->where('adi_table',$this->default_table);
		$this->db->where('adi_ad_id',$data['listing']['ad_id']);
		$this->db->where('adi_type',0);
		$discounts_update = $this->db->get('pt_additional_items')->result_array();

		$discounts = 0;
		foreach($discounts_update as $a){
			$r['item'] = $a['adi_text'];
			$r['price'] = $a['adi_value'];

			$discounts+=$a['adi_value'];

			$html.=$this->load->view('pdf/quote_row_generic',$r, true);
		}
		//discounts end

		$data['retail']+=$additionals; 
		$data['total']+=$additionals; 
		$data['vat']+=$additionals; 

		$data['discount'] = $discounts;

		$quote_entry['vat'] = $data['vat'];

		//end additions and discounts

		//print_r($data);

		$html.=$this->load->view('pdf/quotes_price',$data,true);
		*/


		$html.=$this->load->view($vpath.'quote_footer',$data,true);

		//echo $html;
		//exit();

		$data['user'] = $this->data;

		
		//subtract any discounts
		if(isset($data['discount'])){
			$data['total'] = $data['total']-$data['discount'];
			//echo $data['total'].'#';
		}


		$this->db->select('*');
		$this->db->where('quh_ref',$data['ref']);
		$this->db->from('quote_history');
		$quote = $this->db->get()->result_array();
		$number = sizeof($quote);

		$quote_number = $number+1;
		$filename = str_replace('/','_',$data['ref']);
		if($increment){
			$filename = $filename.'_'.$quote_number;
		}
		$siteaddress = $data['listing']['ad_id'];
		$siteaddress = str_replace(' ','_',$siteaddress);
		$siteaddress = str_replace(',','',$siteaddress);
		$filename=$siteaddress.$filename;
		//echo $filename;
		//exit();
		/*
		$data['cost']=$cost;
		$data['retail']=$retail;
		$data['vat']=$vat;	

		$data['is_vat'] = 0;
		if($r['listing']['ad_vat']==1){
			$data['is_vat'] = 1;
		}
		if($data['is_vat']==1){
			$price = $data['vat'];
		}else{
			$price = $data['retail'];
		}
		*/
		$price = $data['total'];
		//echo $price;
		
		$tenPercent = ($price*0.1);
		//echo '#'.$tenPercent;
		$stage1 = ($tenPercent - $this->config->item('deposit_price'));
		$adjustment1 = 0;
		if($stage1<=0){
			$adjustment1 = $stage1;
			$stage1 = 0;
		}


		$data['deposit'] = number_format($this->config->item('deposit_price'),2);
		$data['stage1'] = number_format($stage1,2);
		$data['stage2'] = number_format(($tenPercent*4)+$adjustment1,2);
		$data['stage3'] = number_format(($tenPercent*4),2);
		$data['stage4'] = number_format($tenPercent,2);

		//$html.=$this->load->view('pdf/quotes_payment_schedule',$data,true);

		$insert['quh_ref'] = $data['ref'];
		$insert['quh_filename'] = $filename;
		$insert['quh_gen_date'] = date('Y-m-d H:i:s',strtotime('now'));
		/*
		$insert['quh_cost_price'] = $data['cost'];
		$insert['quh_retail_price'] = $data['retail']+$additionals-$discounts;
		$insert['quh_margin_price'] = $data['retail']-$data['cost'];
		*/
		$insert['quh_cost_price'] = $quote_entry['cost'];
		$insert['quh_retail_price'] = $quote_entry['retail_ex_vat'];
		$insert['quh_margin_price'] = $quote_entry['retail_ex_vat']-$quote_entry['cost'];

		/*
		if($data['is_vat']==1){
			//$insert['quh_vat_price'] = $data['retail'];
			$vatRate = $this->getSetting('vat');
			$vatRate = $vatRate+100/100;
			$insert['quh_vat_price'] = $data['total'];
		}*/
		if($data['is_vat']==1){
			$insert['quh_vat_price'] = $quote_entry['vat'];
		}
		$insert['quh_table'] = $this->default_table;
		$insert['quh_gen_by_id'] = $this->session->userdata("us_id");

		$insert['quh_order'] = $order;


		$insert['quh_deposit'] = str_replace(',','',$data['deposit']);
		$insert['quh_stage1'] = str_replace(',','',$data['stage1']);
		$insert['quh_stage2'] = str_replace(',','',$data['stage2']);
		$insert['quh_stage3'] = str_replace(',','',$data['stage3']);
		$insert['quh_stage4'] = str_replace(',','',$data['stage4']);

		$insert['quh_prospect_id'] = $contact_id;

		$this->db->insert('quote_history', $insert);
		//echo $this->db->last_query();
		
		$quote_id = $this->db->insert_id();
		//update the most recent quote as the main one
		$update_quote['ad_quote_id'] = $quote_id;
		$this->db->where('ad_id', $ad_id);
		$this->db->update($this->default_table, $update_quote);
		//echo $this->db->last_query();
		$filename = str_replace('/', '-', $filename);
		$filename = str_replace('__', '_', $filename);

		if(!$increment){
			$filename = str_replace('_', '', $filename);
		}

		//echo $filename;
		//echo $html;
		//echo sizeof($docs);
		//exit();

		//print_r($docs);

		if($format=='pdf'){
			//$dompdf = new Dompdf();
			$dompdf = new Dompdf(array('enable_remote' => true));
			$dompdf->set_option("isPhpEnabled", true);

			//$dompdf = new Dompdf();
			//echo $html;
			//exit();
			$dompdf->load_html($html);
			$dompdf->render();

			//ini_set('display_errors', 1); 
			//ini_set('display_startup_errors', 1); 
			//error_reporting(E_ALL);

			$font = $dompdf->getFontMetrics()->get_font("helvetica", "bold");
			$dompdf->getCanvas()->page_text(400, 775, $filename, $font, 6, array(0,0,0));
			$dompdf->getCanvas()->page_text(270, 775, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));

			$canvas = $dompdf->get_canvas();
		    $font = $dompdf->getFontMetrics()->get_font("helvetica", "normal");
		    $canvas->page_text(520, 805, "Page {PAGE_NUM}", $font, 9, array(0.4, 0.4, 0.4));

		    //print_r($GLOBALS['entity_page']);
		    $GLOBALS["x"] = 100; // X-coordinate
			$GLOBALS["y"] = 100; // Y-coordinate
		    $toc = 0;
		    $tocx = 0;
		    $GLOBALS["tocContent"] = '';
		    $GLOBALS["tocContentx"] = '';

	    	$tocContent = '';
	    	
	    	if (isset($GLOBALS['entity_page']) && (is_array($GLOBALS['entity_page']) || is_object($GLOBALS['entity_page']))) {
				foreach ($GLOBALS['entity_page'] as $section) {
					if (strpos($section[0], '*') !== 0) {
						$toc++;
				    	$GLOBALS["tocContent"] .=number_format($toc, 2).' '. $section[0] . '-' . $section[1] . "||";
					}else{
						$tocx++;
				    	$GLOBALS["tocContentx"] .=str_replace('*', '', $section[0]) . '-' . $section[1] . "||";
					}
				}
	    	}
			//print_r($GLOBALS["tocContentx"]);

			if(is_null($template)){

	            $canvas->page_script('
	            	if ($PAGE_NUM == 2) {
				    	// Set the font, font size, and color variables
				    	$font = "helvetica";
				    	$fontSize = 12;
				    	$color = array(0, 0, 0); // RGB color: black

				    	// Position the table of contents
				    	$x = 40;
				    	$y = 150;
				    	$xp = 525;

				    	// Write the table of contents content on the first page
				    
				    	$parts = explode("||", $GLOBALS["tocContent"]);
				    	foreach($parts as $p){
				    		$p2 = explode("-", $p);
				    		if(isset($p2[0])){
				    			$pdf->text($x, $y, $p2[0], $font, $fontSize, $color);
				    		}
				    		if(isset($p2[1])){

				    			if(strlen($p2[1])==1){

				    				$pdf->text($x+$xp, $y, "   ".$p2[1], $font, $fontSize, $color);

				    			}else if((strlen($p2[1])==2)){

				    				$pdf->text($x+$xp, $y, " ".$p2[1], $font, $fontSize, $color);

				    			}else{

				    				$pdf->text($x+$xp, $y, $p2[1], $font, $fontSize, $color);

				    			}
				   
				    		}
				    		$y += 20;
						}
					}
				');

			}
			else{

	            $canvas->page_script('
	            	if ($PAGE_NUM == 3) {
				    	// Set the font, font size, and color variables
				    	$font = "helvetica";
				    	$fontSize = 10;
				    	$color = array(0, 0, 0); // RGB color: black

				    	// Position the table of contents
				    	$x = 40;
				    	$y = 80;
				    	$xp = 725;

				    	// Write the table of contents content on the first page
				    	$pdf->text($x, $y-20, \'Contents\', $font, 12, $color);
				    	$parts = explode("||", $GLOBALS["tocContent"]);
				    	foreach($parts as $p){
				    		$p2 = explode("-", $p);
				    		if(isset($p2[0])){
				    			$pdf->text($x, $y, strip_tags($p2[0]), $font, $fontSize, $color);
				    		}
				    		if(isset($p2[1])){

				    			if(strlen($p2[1])==1){

				    				$pdf->text($x+$xp, $y, "   ".$p2[1], $font, $fontSize, $color);

				    			}else if((strlen($p2[1])==2)){

				    				$pdf->text($x+$xp, $y, " ".$p2[1], $font, $fontSize, $color);

				    			}else{

				    				$pdf->text($x+$xp, $y, $p2[1], $font, $fontSize, $color);

				    			}
				   
				    		}
				    		$y += 20;
						}
					}
				');

	            $show_appendices = false;
	            if( (!is_null($data['listing']['ad_fraewappendixatitle'])) || 
				(!is_null($data['listing']['ad_fraewappendixbtitle'])) || 
				(!is_null($data['listing']['ad_fraewappendixctitle'])) || 
				(!is_null($data['listing']['ad_fraewappendixdtitle'])) || 
				(!is_null($data['listing']['ad_fraewappendixetitle'])) || 
				(!is_null($data['listing']['ad_fraewappendixftitle'])) ||
				(!is_null($data['listing']['ad_fraewappendixgtitle'])) ||
				(!is_null($data['listing']['ad_fraewappendixhtitle']))){

	            	$show_appendices = true;

	            }
	            if((!is_null($data['listing']['ad_fraewappendixititle']) && ($data['listing']['ad_fraewappendixititle'] !== ''))){
	            	$show_appendices = true;
	            }

	            if($show_appendices){
	            	
				$canvas->page_script('
	            	if ($PAGE_NUM == 4) {
				    	// Set the font, font size, and color variables
				    	$font = "helvetica";
				    	$fontSize = 10;
				    	$color = array(0, 0, 0); // RGB color: black

				    	// Position the table of contents
				    	$x = 40;
				    	$y = 80;
				    	$xp = 725;

				   		// Write the table of contents content on the first page
				    	$pdf->text($x, $y-20, \'APPENDICES\', $font, 12, $color);
				    	$parts = explode("||", $GLOBALS["tocContentx"]);
				    	foreach($parts as $p){
				    		$p2 = explode("-", $p);
				    		if(isset($p2[0])){
				    			$pdf->text($x, $y, $p2[0], $font, $fontSize, $color);
				    		}
				    		if(isset($p2[1])){

				    			if(strlen($p2[1])==1){

				    				$pdf->text($x+$xp, $y, "   ".$p2[1], $font, $fontSize, $color);

				    			}else if((strlen($p2[1])==2)){

				    				$pdf->text($x+$xp, $y, " ".$p2[1], $font, $fontSize, $color);

				    			}else{

				    				$pdf->text($x+$xp, $y, $p2[1], $font, $fontSize, $color);

				    			}
				   
				    		}
				    		$y += 20;
						}
					}
				');

				}
			}
			
    		// Set the text position on the page
		
    		//exit();


			//get the new document type
			//$data['listing']['ad_PropertyType2'];
			//$data['listing']['ad_skeletons'];

			
			if($stream==true){
				//return $dompdf->stream($filename.".pdf", array("Attachment" => $attachState));
				//exit();
				$output = $dompdf->output();


				// Original date string
				$originalDate = $data['listing']['ad_dateofinvoice'];

				// Create a DateTime object from the original date string
				$date = DateTime::createFromFormat('l, F d, Y', $originalDate);

				// Check if the date was parsed correctly
				if ($date !== false) {
				    // Format the date to "mm-yyyy"
				    $formattedDate = $date->format('m-Y');
				}

				mkdir('assets/quotes/'.$formattedDate);

				file_put_contents('assets/quotes/'.$formattedDate.'/'.$filename.'.pdf', $output);

				if($returnFrom == 'downloadPdfBeforeMerge'){
					$returnArray = array(
						'status' => true,
						'data' => array(
							'fileName' => $filename.'.pdf',
							'path' => FCPATH.'assets/quotes/'.$formattedDate.'/'.$filename.'.pdf'
						)
					);
					return $returnArray;
				}

				// _pre($GLOBALS);

				// echo "hi";
				// exit;

				/*$setPageNumbersInPdfsData = array(
					'ad_id' => $ad_id,
					'mainPdf' => $filename.'.pdf',
					'mainPdfPath' => FCPATH."assets/quotes/".$filename.'.pdf'
				);
				// _pre($setPageNumbersInPdfsData);
				$pageNumbersInPdfsResult = $this->_setPageNumbersInPdfs($setPageNumbersInPdfsData);
				$allDocuments = $pageNumbersInPdfsResult['data']['allDocuments'];*/
				
				// _pre($allDocuments);
				// echo "completed";
				// exit;


				//File Merge section

				
				if(!is_null($template)){

					$merge_files = [];
					$Advertfieldsmodel = new Advertfieldsmodel();

					$merge_fields = $Advertfieldsmodel->where('adv_field_type', 28)->where('adv_table', $this->default_table)->get();
					//print_r($merge_fields);
					
					$postData = array(
					    'main_pdf' => new CURLFILE('assets/quotes/' . $filename . '.pdf'),
					);

					$totalPagesOfAllAdditionalPdfs = 0;

					$count = 0;
					foreach($merge_fields as $m){
						$documents = $this->Advertcategorymodel->getDocuments($ad_id, $m->adv_id);
						foreach($documents as $d){
							$totalPagesOfAllAdditionalPdfs += $d['up_number_of_pages'];

							$postData['additional_pdfs['.$count.']'] = new CURLFILE('assets/uploads/'.$d['up_filename']);
							$count++;
						}
					}
					/*if(isset($allDocuments) && !empty($allDocuments)){
						foreach ($allDocuments as $allDocumentsKey => $allDocumentsValue) {
							if($allDocumentsKey > 0){ // $allDocumentsKey = 0 is the main pdf file
								$postData['additional_pdfs['.$count.']'] = new CURLFILE('assets/uploads/temp/'.$allDocumentsValue['destinationFilename']);
								$count++;
							}
						}
					}*/
					// _pre($postData);
					//print_r($postData);
					
					if($totalPagesOfAllAdditionalPdfs > 0){
					
						$key = $this->db->where('id', 1)->get('keys')->row();

						$this->config->load('rest');
						$logins = $this->config->item('rest_valid_logins');
						//echo 'https://admin:'.$logins['admin'].'@'.str_replace('https://', '', $this->config->item('base_url')).'/Api/upload_pdfs';
						$curl = curl_init();

						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://admin:'.$logins['admin'].'@'.str_replace('https://', '', $this->config->item('base_url')).'/Api/upload_pdfs',
						  
						  // CURLOPT_URL => 'http://localhost/cladding-working/Api/upload_pdfs',
						  // CURLOPT_URL => 'http://admin:0nqwJwVTV1OHghi@localhost/cladding-working/Api/upload_pdfs',
						  // CURLOPT_USERPWD => "admin:0nqwJwVTV1OHghi",
						  
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'POST',
						  CURLOPT_POSTFIELDS => $postData,
						  CURLOPT_HTTPHEADER => array(
						    'X-API-KEY: '.$key->key
						  ),
						));

						$response = curl_exec($curl);
						//print_r($response);
						curl_close($curl);
						$file = json_decode($response);
						//print_r($file);
						//exit();
						//header("Location:".$file->main_pdf);

						$main_pdf_path = $file->main_pdf_path;

						$fileName = basename($main_pdf_path);
						$filePath = $main_pdf_path;
						
						$destinationFileName = basename($main_pdf_path);
						$destinationFilepath = $main_pdf_path;

						$dompdfPageGlobal = $GLOBALS['dompdfPage'];

						$dataModifyFinalPdf = array(
							'up_filename' => $fileName,
							'filePath' => $filePath,
							'destinationFilepath' => $destinationFilepath,
							'dompdfPageGlobal' => $dompdfPageGlobal,
							'totalPagesOfAllAdditionalPdfs' => $totalPagesOfAllAdditionalPdfs,
						);
						//print_r($dataModifyFinalPdf);
						$this->fpdflib->modifyFinalPdf($dataModifyFinalPdf);
						// _pre($GLOBALS);
						// $this->_deleteTempPdfFiles($allDocuments);

						if($returnFrom == 'getResponsePdfAfterMerge'){
							$returnArray = array(
								'status' => true,
								'data' => array(
									'fileName' => $fileName,
									'random_string' => $file->random_string,
									'main_pdf' => $file->main_pdf,
									'main_pdf_path' => $main_pdf_path,
								)
							);
							return $returnArray;
						}

						//print_r($response);
						header('Location:'.$file->main_pdf);
						exit();
						
					}
				}

				///File merge

				//$this->db->order_by('up_order', 'DESC');
				//$data = $this->db->get('pt_uploads')->result_array();

				$path = $this->path.$filename.'.pdf';
				//some issue with overwriting
				$output_path = $this->path.$filename.'_1.pdf';

				$command = $path.' ';
				foreach($docs as $d){ 
					$command.='/var/www/pre-install/assets/uploads/'.$d['up_filename'].' ';
				}
				//echo 'pdftk '.$command.' cat output '.$path;
				if(sizeof($docs)>0){
					$o = shell_exec('pdftk '.$command.' cat output '.$output_path);
				}
				//echo $o;
				//echo "<pre>$o</pre>";
				//exit();

				header("Content-Description: File Transfer"); 
				header("Content-Type: application/octet-stream"); 
				header("Content-Disposition: attachment; filename=".$filename.".pdf"); 
				if(sizeof($docs)>0){
					readfile("assets/quotes/".$filename."_1.pdf");
				}else{
					readfile("assets/quotes/".$filename.".pdf");
				}


				exit();

			//}
				
			}else{
				if(!file_exists('assets/quotes/'.$filename.'.pdf')){
					$output = $dompdf->output();
					file_put_contents('assets/quotes/'.$filename.'.pdf', $output);
				}

				return $quote_id;	
			}

		}else if($format=='doc'){

			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			require_once 'application/third_party/phpdocx-basic-8.5/classes/CreateDocx.php';

			if($category==35){

				$docx = new CreateDocxFromTemplate('./assets/templates/form_5_completion_certificate_submission.docx');
				$data['listing'] = $this->Advertcategorymodel->getAdvert($ad_id);	


				$docx->replaceVariableByText($data['listing']);

				$filename = str_replace('/','_',$data['ref']);
				$filename = $filename.'_'.$quote_number;

				$siteaddress = $data['listing']['ad_Address'].'_'.$data['listing']['ad_Postcode'];
				$siteaddress = str_replace(' ','_',$siteaddress);
				$siteaddress = str_replace(',','',$siteaddress);
				$filename='Form5_'.$siteaddress.$filename;

				//$docx->createDocx('/var/www/pre-install/assets/form5/'.$filename);
				$docx->createDocxAndDownload('/var/www/pre-install/assets/form5/'.$filename);
			
			}else{


				if($batch!=NULL){
					//echo $batch.'#';

					$housesShort = NULL;

					//print_r($full_list_of_properties);
					//echo $full_list_of_properties[$batch];
					//exit();

					$updated_listing = $this->getDataFromEsterByPRID($full_list_of_properties[$batch]);

					//print_r($updated_listing);

					$data['listing'] = array_merge($data['listing'], $updated_listing);

					//print_r($data['listing']);
					//exit();

					$filename = str_replace('/','_',$data['ref']);
					$filename = $filename.'_'.$quote_number;

					$siteaddress = $data['listing']['ad_SiteAddress'];
					$siteaddress = str_replace(' ','_',$siteaddress);
					$siteaddress = str_replace(',','',$siteaddress);
					$filename=$siteaddress.$filename;

				}

				require_once 'application/third_party/phpdocx-basic-8.5/classes/CreateDocx.php';

				$data['count'] = 1;

				$styleOptions = array(
				    'borderColor' => '000000',
				    'cellMargin' => array('top' => 400, 'left' => 150),
				);
				
				//$docx = new CreateDocx();
				$docx = new CreateDocxFromTemplate('./assets/templates/template_watermark_mac_0_margin.docx');

				$docx->setDefaultFont('Arial');

				$imageOptions = array(
				    'src' => './assets/img/rics.png',
				    'dpi' => 300,  
				);
				$footerImage = new WordFragment($docx, 'defaultFooter');
				$footerImage->addImage($imageOptions);

				$numbering = new WordFragment($docx, 'defaultFooter');
				$options = array(
				    'textAlign' => 'center',
				    'sz' => 14,
				);
				$numbering->addPageNumber('page-of', $options);

				$valuesTable = array(
				array(
						array('value' => $footerImage, 'vAlign' => 'left'),
						array('value' => $numbering, 'vAlign' => 'center'),
						array('value' => $filename, 'vAlign' => 'right'),
				    ),
				);
				$widthTableCols = array(
				    3792,
				    3792,
				    3792
				);
				$paramsTable = array(
				    'border' => 'nil',
				    'columnWidths' => $widthTableCols,
				    'fitText' => true,
				    'textProperties' => array('fontSize'=>10),
				);
				$footerTable = new WordFragment($docx, 'defaultFooter');
				$footerTable->addTable($valuesTable, $paramsTable);
				$docx->addFooter(array('default' => $footerTable));


				//heading at start
				$site_address = explode(',', $data['listing']['ad_SiteAddress']);
				foreach($site_address as $a){
					$docx->addHeading($a, 1, array('textAlign' => 'center'));	
				}
				$docx->addHeading(' ', 1, array('textAlign' => 'center'));
				
				$tableProperties = array(
	    			'border' => 'single',
	    			'borderColor' => '#000000',
	    			'borderWidth' => 10,
	    			'tableAlign' => 'center',
	    			'textProperties' => array('fontSize' => 11, 'textAlign' => 'center'),
	    			'columnWidths' => array(5688, 5688),
	    			'tableWidth' => array('type' => 'dxa', 'value' => 11376),
	    			'cellMargin' => 100,
	    			'tableLayout' => 'fixed',
				);

				//properties table
				$houses_string = '';
				$street_string = $key;
				$values = array();
				//echo '###';
				//print_r($housesShort);
				//echo sizeof($housesShort);
				//echo $housesShort;
				//exit();

				$docx->addBreak(array('type' => 'page'));
				//page 1 end

				//letterhead
				$options = array(
					'src' => './assets/img/address2.png',
					'imageAlign' => 'right',
					'scaling' => 50,
					'spacingTop' => 10,
					'spacingBottom' => 0,
					'spacingLeft' => 0,
					'spacingRight' => 20,
					'textWrap' => 0,
					'borderStyle' => 'solid',
					'borderWidth' => 0,
					'borderColor' => 'ffffff',
				);
				$docx->addImage($options);


				//client address
				$address = explode(',', $data['listing']['ad_Client']);
				$paragraphOptions = array(
					'tab' => 1,
				);
				
				foreach($address as $a){
					$docx->addText(trim($a), $paragraphOptions);	
				}
				$docx->addText(' ', $paragraphOptions);
				//$docx->addBreak(array('type' => 'page'));

				$docx->createTableStyle('myTableStyle', $styleOptions);

				$values = array();
				//tables 
				$tableProperties = array(
	    			'border' => 'single',
	    			'borderColor' => '#000000',
	    			'borderWidth' => 10,
	    			'tableAlign' => 'center',
	    			'textProperties' => array('fontSize' => 11, 'textAlign' => 'center'),
	    			'columnWidths' => array(5688, 5688),
	    			'tableWidth' => array('type' => 'dxa', 'value' => 11376),
	    			'cellMargin' => 100,
	    			'tableLayout' => 'fixed',
				);




				$docx->addBreak(array('type' => 'page'));

				$listing = $data['listing'];

				foreach($result as $r){
					$r['listing'] = $data['listing'];

					$textFragment = new WordFragment($docx);
					$text = array();
					$text[] =
							array(
								'text' => strtoupper($r['ad_name']),
							);
					$paragraphOptions = array(
							'bold'	=> true,
							'textAlign' => 'center',
					);
					$textFragment->addText($text, $paragraphOptions);

					$col_1_1 = array(
					    'colspan' => 3, 
					    'value' => $textFragment, 
					    'backgroundColor' => 'e2ffe2',
					    'bold' => true
					);

					$values = array();
					//adding title
					$values[] = array($col_1_1);

					$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);	



					foreach($data['fields'] as $f){

					$data_text = '';

					if ($f['fi_type'] != 'checkboxes'){

						if ($f['adv_datasourceId'] != 0){

							$value = $this->Advertcategorymodel->getDataValue($listing['ad_' . $f['adv_column']]);
							$pricing = $this->Advertcategorymodel->getDataValuePricing($listing['ad_' . $f['adv_column']]);

							if ($value['ds_value'] != ''){

								$data_text = $value['ds_value'];
							
							}

							if ($f['adv_associated_fieldId'] != '0'){

								$column = $this->Advertcategorymodel->getColumnById($f['adv_associated_fieldId']);
								$value = $this->Advertcategorymodel->getDataValue($listing['ad_' . $column]);

								if ($value['ds_value'] != ''){
									$data_text = $value['ds_value'];
								}
							}

						} else {
							if ($f['fi_type'] == 'radioBool'){

								switch ($listing['ad_' . $f['adv_column']]){
									case 0:
										$data_text = 'No';
									break;
									case 1:
										$data_text = 'Yes';
									break;
								}
							
							} else if ($f['fi_type'] == 'image'){

								$images = $this->Advertcategorymodel->getImages($listing['ad_id'], $f['adv_id']);
								$i = 0;
								$c = 0;

								foreach($images as $i){

									list($width, $height, $type, $attr) = getimagesize('assets/uploaded_images/' . $i['up_filename']);
									if ($width < 500){

										$data_text.= '<img src="assets/uploaded_images/' . $i['up_filename'] . '"><div style="clear:both;"><br />' . $i['up_caption'] . '</div>';

										$html = new WordFragment($docx, 'document');
										$html->embedHTML($data_text);
										$col_2_3 = array(
											'value' => $html, 
											'backgroundColor' => 'ffffff',
											'colspan' => 2
										);
										$values[] = array($col_2_3);

									}else{
										
										$data_text.= '<img src="assets/uploaded_images/' . $i['up_filename'] . '" style="width:500px;"><div style="clear:both;"><br />' . $i['up_caption'] . '</div>';
										$html = new WordFragment($docx, 'document');
										$html->embedHTML($data_text);
										$col_2_3 = array(
											'value' => $html, 
											'backgroundColor' => 'ffffff',
											'colspan' => 2
										);
										$values[] = array($col_2_3);

									}

									$i++;
									$c++;

								}
							} else {

								if ($listing['ad_' . $f['adv_column']] != ''){
									$data_text = $listing['ad_' . $f['adv_column']];
								}
							}
						}

					} else {

						$ds = explode(',', $listing['ad_' . $f['adv_column']]);

						// var_dump($ds);

						$size = sizeof($ds);
						$count = 0;
						foreach($ds as $d){

							$value = $this->Advertcategorymodel->getDataValue($d);
							if ($value['ds_value'] != ''){

								$data_text.= $value['ds_value'];
								$count++;
								if ($count < ($size - 1)){
									$data_text.= ', ';
								}
							}
						}
					}

					// echo '#'.$data_text.'<br />';

					if ($f['fi_type'] == 'image') {

					} else {
						if ($f['adv_column'] == 'IncludeSignature') {
							if ($data_text == 'Yes') {

									$textFragment = new WordFragment($docx);
									$text = array();
									$text[] =
											array(
												'text' => $data['count'].'.'.$c.' Signature',
											);
									$paragraphOptions = array(
											'textAlign' => 'left',
									    	'bold'	=> true
									);
									$textFragment->addText($text, $paragraphOptions);


									$col_2_2 = array(
									    'value' => $textFragment, 
									    'backgroundColor' => 'e2ffe2',
									);

									$imageOptions = array(
									    'src' 		=> './assets/img/phil_signature.jpg',
									    'height' 	=> 55,
									    'width'		=> 100,
									);
									$sig = new WordFragment($docx);
									$sig->addImage($imageOptions);


									$col_2_3 = array(
									    'value' => $sig, 
									    'backgroundColor' => 'ffffff',
									);
									$values[] = array($col_2_2, $col_2_3);

								}
							}
						  else
							{
							
								if($data_text!=''){


									$textFragment = new WordFragment($docx);
									$text = array();
									$text[] =
									    array(
									        'text' => $data['count'].'.'.$c.' '.$f['adv_text'],
									    );
									$paragraphOptions = array(
									    'textAlign' => 'left',
									    'bold'	=> true
									);
									$textFragment->addText($text, $paragraphOptions);


						        	$col_2_2 = array(
									    //'value' => $data['count'].'.'.$c.' '.$f['adv_text'], 
									    'value'	=> $textFragment,
									    'backgroundColor' => 'e2ffe2',
									);

						        	//echo '###'.htmlentities($data_text).'###';
									$doc = new DOMDocument();
									//$doc->loadHTML(htmlentities($data_text));
									$data_text = str_replace('&nbsp;', ' ', $data_text);
									$data_text = str_replace('&', '&amp;', $data_text);
									
									$doc->loadHTML($data_text);
									//echo $doc->saveHTML();

									$html = new WordFragment($docx, 'document');
									$html->embedHTML($doc->saveHTML());
									//exit();
									$col_2_3 = array(
									    'value' => $html, 
									    'backgroundColor' => 'ffffff',
									);
									$values[] = array($col_2_2, $col_2_3);
								}
							}
						}
						if($data_text!=''){
							$c++;
						}
					}

					$data['count']++;
					$c = 1;

					$docx->addTable($values, $tableProperties);
					$docx->addBreak(array('type' => 'page'));
				}

				//exit();
				if($batch!=NULL){
					
					$folder_name = str_replace(' ', '_', $skeletons['sk_name']);

					if(!file_exists($this->path.$folder_name.'/')){
						mkdir($this->path.$folder_name.'/');
					}
					$docx->createDocx($this->path.$folder_name.'/'.$filename);
				}else{
					$docx->createDocxAndDownload($this->path.$filename);
				}
			
			}
			exit();
		
		}else if($format=='html'){

			echo $html;
			exit();
			
		}
		//$dompdf->stream($filename.".pdf");
	}


	function getDataFromEsterByPRID($id){

			$otherdb = $this->load->database('ester', TRUE);
			
			$otherdb->where('id',$id);
			$primary_house = $otherdb->get('dm_houses')->row_array();

			//get client 
			$otherdb->where('c_id',$primary_house['instructing_client']);
			$instructing_client = $otherdb->get('dm_client')->row_array();

			$primary_house_a['ad_companyName'] = $instructing_client['companyName'].', '.$instructing_client['companyAddress'].', '.$instructing_client['companyAddress2'].','.$instructing_client['postcode'];

			//get construction type 
			
			$otherdb->where('pc_id',$primary_house['construction_type_surveyor']);
			$construction_type = $otherdb->get('dm_construction_type')->row_array();
			$primary_house_a['ad_Propertyconstruction'] = $construction_type['pc_name'];
			$primary_house_a['ad_constructionType2'] = $construction_type['pc_name'];
			
			$otherdb->where('pt_id',$primary_house['property_type_surveyor']);
			$construction_type = $otherdb->get('dm_property_type')->row_array();
			$primary_house_a['ad_PropertyType2'] = $construction_type['pt_name'];
			

			$address = '';
			if($primary_house['flatNumber']!=''){
				$address.='Flat '.$primary_house['flatNumber'].' ';
			}
			$address.=$primary_house['house_number'].' '.trim($primary_house['street_name']).', '.trim($primary_house['town']).', '.trim($primary_house['postcode']);
			$primary_house_a['ad_SiteAddress'] = $address;

			//some hard coded stuff
			$primary_house_a['ad_InspectionCompany'] = 'Diamond & Co, Chartered Building Surveyors';
			$primary_house_a['ad_Inspector'] = 'Mr Philip S Diamond MRICS, Chartered Building Surveyor';

			//$primary_house_a['ad_Created'] = date('d/m/Y',strtotime($primary_house['date_added']));

			//$primary_house_a['ad_LastUpdated'] = date('d/m/Y',strtotime($primary_house['last_edited']));
			
			//$primary_house_a['ad_Date'] = date('d/m/Y');
			
			$primary_house_a['ad_NameofSurveyor'] = 'Mr Philip S Diamond MRICS, Chartered Building Surveyor';

			$primary_house_a['ad_Company'] = 'Diamond & Co, Chartered Building Surveyors';

			$primary_house_a['ad_RCISMembershipNumber'] = '0087625';

			$primary_house_a['ad_Noofstoreys'] = $primary_house['story'];

			$primary_house_a['ad_Noofbedrooms'] = $primary_house['bedrooms'];

			$primary_house_a['ad_Approximateyearofconstruction'] = $primary_house['circa'];

			//$primary_house_a['ad_Date'] = date('d/m/Y', strtotime($primary_house['date_surveyed']));
			//$primary_house_a['ad_SigningDate'] = date('d/m/Y', strtotime($primary_house['date_report_issue']));
			
			return $primary_house_a;
			
	}


	public function quote_latest($ref){
		$ref = str_replace('_','/',$ref);
		$this->db->select('*');
		$this->db->where('quh_ref',$ref);
		$this->db->order_by('quh_gen_date','DESC');
		$this->db->limit(1);
		$row = $this->db->get('quote_history')->row_array();
		//echo $this->db->last_query();
		return $row;
	}

	public function get_Additional_Items($ad_id, $table, $type){
		//additionals
		$this->db->select_sum('adi_value');
		$this->db->where('adi_ad_id', $ad_id);
		$this->db->where('adi_table', $table);
		$this->db->where('adi_type',$type);
		$additionals = $this->db->get('pt_additional_items')->row_array();
		//echo $this->db->last_query();
		return $additionals['adi_value'];
	}

	public function getSetting($name){
		$this->db->select('se_value');
		$this->db->where('se_key',$name);
		$value = $this->db->get('pt_settings')->row_array();
		return $value['se_value'];
	}

	public function generate_build_form($category, $ad_id, $contact_id, $order=0, $table, $catTable, $build_order){
		//containerEnd view has most of the JS in it
		$data = '';
		$data['width'] = 6;
		$data['ad_id'] = 0;
		$data['listing'] = '';

		$data['preview'] = true;

		$data['date'] = date('d/m/y',strtotime('now'));

		
		$data['contactID'] = $contact_id;
		

		$data['listing'] = $this->Advertcategorymodel->getAdvert($ad_id, $table);	

		//get contact
		//$contact = $this->Contactsmodel->selectProspect($data['listing']['ad_contact']);
		//$data['contact'] = $contact;
		//$data['ref'] = substr($contact['pr_first_name'], 0,1).substr($contact['pr_surname'], 0,1).'/'.date('dmy',strtotime($data['listing']['ad_added'])).'/'.$data['listing']['ad_rand'];
		$data['ref'] = date('dmy',strtotime($data['listing']['ad_added'])).'/'.$data['listing']['ad_rand'];


		$this->db->select('*');
		$this->db->where('ad_parent',$category);
		$this->db->order_by('ad_order','ASC');

		$result = $this->db->get($catTable)->result_array();


		$data['section'] = 'View Quote';

		$data['is_vat'] = 0;
		if($data['listing']['ad_vat']==1){
			$data['is_vat'] = 1;
		}
		
		$data['order'] = $this->Ordersmodel->getOrder($build_order);
		//print_r($data['order']);

		//quote
    	$this->db->where('quh_id', $order);
    	$data['quote_history'] = $this->db->get('quote_history')->row_array();

		$data['user'] = $this->Usermodel->selectUser($data['order']['bo_user_id']);
		$html.=$this->load->view('pdf/build_order_top',$data,true);

		$datasourceColumns = $this->Entriesmodel->getDatasourceColumns();
		$cost = 0;
		$retail = 0;
		$vat = 0;

		$quote_entry = '';

		foreach($datasourceColumns as $col){
			$price = $this->Advertcategorymodel->getDataValuePricing($data['listing']['ad_'.$col['adv_column']]);
			$cost+=$price['ds_cost_price'];
			$retail+=$price['ds_retail_price'];
			$vat+=$price['ds_vat_price'];

			
		}
		
		$quote_entry['cost'] = $cost;

		$data['cost']=$cost;
		$data['retail']=$retail;
		$data['vat']=$vat;	

		switch($category){
			default:
				foreach($result as $r){
					$r['listing'] = $data['listing'];

					$html.=$this->load->view('pdf/quotes_title',$r, true);

					$data['fields'] = $this->Advertcategorymodel->getFields($r['ad_id'],true, $data['listing'],false);	

					$html.=$this->load->view('pdf/quotes_row_no_price',$data,true);
				}
			break;
		}
		//start additions and discounts
		//additional fields
		$r['ad_name'] = 'Additional Items';
		
		$html.=$this->load->view('pdf/quotes_title',$r, true);

		$this->db->select('*');
		$this->db->where('adi_table',$this->default_table);
		$this->db->where('adi_ad_id',$data['listing']['ad_id']);
		$this->db->where('adi_type',1);
		$additional_fields_update = $this->db->get('pt_additional_items')->result_array();

		$additionals = 0;
		foreach($additional_fields_update as $a){
			/*
			$r['item'] = $a['adi_text'];
			$r['price'] = $a['adi_value'];

			$additionals+=$a['adi_value'];
			*/
			$r['item'] = $a['adi_text'];
			
			if($data['listing']['ad_vat']==1){

				$multiplier = ($vatRate+100)/100;
				$value = $a['adi_value']*$multiplier;
				$r['price'] = $value;
				$additionals+=$value;

			}else{

				$r['price'] = $a['adi_value'];
				$additionals+=$a['adi_value'];
				
			}

			$html.=$this->load->view('pdf/quote_row_generic_no_price',$r, true);
		}
		

		$filename = str_replace('/','_',$data['ref']);
		$filename = $filename.'_Build_Order';
		
		$dompdf = new Dompdf();
		$dompdf->load_html($html);
		$dompdf->render();
		//its fine to overwrite it
		//if(!file_exists('assets/quotes/'.$filename.'.pdf')){
			$output = $dompdf->output();
			file_put_contents('assets/quotes/'.$filename.'.pdf', $output);
		//}
		$this->db->select('*');
		$this->db->where('do_order_id', $build_order);
		$this->db->where('do_type', 'build_order');
		$docs = $this->db->get('pt_documents')->row_array();
		if(sizeof($docs)==0){
			$bo_insert['do_name'] = $filename;
			$bo_insert['do_order_id'] = $build_order;
			$bo_insert['do_type'] = 'build_order';
			$this->db->insert('pt_documents',$bo_insert);
		}
		return $quote_id;
		//$dompdf->stream($filename.".pdf");

	}

	public function generate_contract($order_id){
		//build order

    	$this->db->where('bo_order_id',$order_id);
    	$data['build_order'] = $this->db->get('pt_build_order')->row_array();

    	//quote
    	$this->db->where('quh_id', $order_id);
    	$data['quote_history'] = $this->db->get('quote_history')->row_array();
    	
    	//detail and listing
    	$this->db->where('ad_quote_id', $order_id);
    	$data['listing'] = $this->db->get($data['quote_history']['quh_table'])->row_array();

    	//customer
    	$this->db->where('pr_id', $data['listing']['ad_contact']);
    	$data['contact'] = $this->db->get('pt_prospect')->row_array();


    	//print_r($data['build_order']);
    	//print_r($data['quote_history']);
    	//print_r($data['listing']);
    	//print_r($data['contact']);

    	$html = $this->load->view('pdf/pdf_contract',$data,true);
    	//echo $html;
    	
    	$html.=$this->load->view('pdf/quote_footer',$data,true);

    	$filename = 'Contract_'.$data['quote_history']['quh_filename'];

		$dompdf = new Dompdf();
		$dompdf->set_paper('A4','portrait'); //Changed A4 to A3
		$dompdf->load_html($html);
		$dompdf->render();
		//if(!file_exists('assets/quotes/'.$filename.'.pdf')){
			$output = $dompdf->output();
			file_put_contents('assets/quotes/'.$filename.'.pdf', $output);
		//}
		//$dompdf->stream($filename.".pdf");

		$this->db->select('*');
		$this->db->where('do_order_id', $order_id);
		$this->db->where('do_type', 'contract');
		$docs = $this->db->get('pt_documents')->row_array();
		if(sizeof($docs)==0){
			$bo_insert['do_name'] = $filename;
			$bo_insert['do_order_id'] = $order_id;
			$bo_insert['do_type'] = 'contract';
			$this->db->insert('pt_documents',$bo_insert);
		}
		//return $quote_id;
	}
}
