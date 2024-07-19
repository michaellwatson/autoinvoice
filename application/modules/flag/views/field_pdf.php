<?php 
if(isset($listing['ad_'.$field['adv_column']])){
	switch($listing['ad_'.$field['adv_column']]){
		case "1":
			echo '<img src="'.base_url('assets/images/icons/flag_red.png').'" class="small-icon">';
		break;
		case "2":
			echo '<img src="'.base_url('assets/images/icons/flag_yellow.png').'" class="small-icon">';
		break;
		case "3":
			echo '<img src="'.base_url('assets/images/icons/flag_green.png').'" class="small-icon">';
		break;
		case "4":
			echo '<img src="'.base_url('assets/images/icons/flag_blue.png').'" class="small-icon">';
		break;
		case "5":
			echo '<img src="'.base_url('assets/images/icons/flag_orange.png').'" class="small-icon">';
		break;
		case "6":
			echo '<img src="'.base_url('assets/images/icons/flag_pink.png').'" class="small-icon">';
		break;
		case "7":
			echo '<img src="'.base_url('assets/images/icons/flag_purple.png').'" class="small-icon">';
		break;
		case "8":
			echo '<img src="'.base_url('assets/images/icons/flag_white.png').'" class="small-icon">';
		break;
		default:
			echo 'None';
		break;
	}
}
?>
                