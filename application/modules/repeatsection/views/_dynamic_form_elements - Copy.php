<?php
foreach($fields as $f){
	//check its not related to a module
	$data['field']    = $f;
	$data['listing']  = $listing;
	$data['table']    = $f['adv_table'];
	$data['formID']   = $formID;
	//echo $data['table'];

	if($f['adv_hidden'] == 1){
?>
		<div style="display:none;">
<?php
	}

	$module_name = $f['fi_type'].'/'.ucfirst($f['fi_type']);
	$method ='/field';
	$result = NULL;

	try{
		$result = Modules::run($module_name.'/field', $data);
	}
	catch(Exception $e){
	}

	if(!is_null($result)){
		echo $result;
	}
	else{
	}

	if($f['adv_hidden'] == 1){
?>
		</div>
<?php
	}
}