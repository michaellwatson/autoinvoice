<?php if(!isset($alert)){
	$alert = 'warning';
}
?>
<?php if(!isset($display)){
	$display = 'none';
}
?>
<br>
<div class="alert alert-<?php if(isset($alert)){ echo $alert; }?>" role="alert" <?php if($alertid!=''){?>id="<?php echo $alertid;?>"<?php } ?>style="<?php if(isset($padding)){ if($padding==true){?>margin-left:10px;margin-right:10px;<?php } } ?>display:<?php echo $display;?>;"><?php if(isset($msg)){ echo $msg; }?></div>