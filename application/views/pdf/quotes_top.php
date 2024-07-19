<?php 
$CI =& get_instance();
$CI->load->model('Quotesmodel','',TRUE);
$logo = $CI->Quotesmodel->getSetting('logo');
?>
<html>
<style type="text/css">
table{
	font-family:Helvetica, Arial, Sans-Serif;
}
table tr td{
	padding:5px;
}
.dashed{
	border-bottom: 1px dashed #949599;
}
.right{
	text-align:right;
}
.body_style{
	font-size:14px;
	/*background-image:url('./assets/img/watermark.png');*/
  	background-repeat:no-repeat;
	width:100%;
	height:100%;
	background-position:-40px -5px;
	margin-top:-30px;
}

.bordered{
	border:1px solid #000;
}
header { position: fixed; top: 0px; left: 0px; right: 0px; background-color: lightblue; height: 50px; }
footer { position: fixed; bottom: -300px; left: 0px; right: 0px; height: 300px; text-align: left; font-size:8px; }

</style>

<header>
	<img src="assets/images/diamondandco_logo.jpg">

	<img src="assets/images/brookerdiamond_logo.jpg">
</header>

<body class="body_style">



<table style="width:100%;" cellspacing="0" cellpadding="0">
<tr>
	<td style="text-align: left;">
		
		<br>
		
		<br>
		
		<br>
		
	</td>
	<td style="text-align:right;">
		<?php 
            if($format=='doc'){
        ?>
        	<img src="<?php echo base_url('assets/images/'.$logo);?>" style="width:200px;">
        <?php
            }else{

    	        list($width, $height, $type, $attr) = getimagesize('assets/images/' . $logo);
                $path1 = 'assets/images/' . $logo;
                $type = pathinfo($path1, PATHINFO_EXTENSION);
                $data_i = file_get_contents($path1);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_i);
        ?>
            <img src="<?php echo $base64;?>" style="width:300px;">
        <?php } ?>
		<br>
	</td>
</tr>
</table>

