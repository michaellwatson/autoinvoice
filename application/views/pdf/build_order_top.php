<style type="text/css">
.blue_text{
	color:#0e76bb;
	font-weight: bold;
}
table{
	font-family:Helvetica, Arial, Sans-Serif;
}
table tr td{
	padding:5px;
}
.dark_header{
	background-color: #02243b;
	text-align:center;
	color:#fff;
	font-family:Helvetica, Arial, Sans-Serif;
	font-size:24px;
}
.light_header{
	background-color: #0e76bb;
	text-align:left;
	color:#fff;
	font-family:Helvetica, Arial, Sans-Serif;
	font-size:20px;
	font-weight: bold;
}
.dark_header_small{
	background-color: #02243b;
	text-align:left;
	color:#fff;
	font-family:Helvetica, Arial, Sans-Serif;
	font-size:20px;
	font-weight: bold;
}
.alt_row{
	background-color: #f3f3f4;
}
.row{
	background-color: #e7e7e8;
}
.dashed{
	border-bottom: 1px dashed #949599;
}
.right{
	text-align:right;
}
footer { position: fixed; bottom: -300px; left: 0px; right: 0px; height: 300px; }
.signbox{
	border: 1px solid #02243b;
	width:100%;
	border-radius: 5px;
}
</style>
<table style="width:100%;" cellspacing="0" cellpadding="0">
<tr>
<td style="text-align:center;" colspan="3">
	<img src="assets/img/logo_white.png" style="width:300px;">
	<br>
	<br>
</td>
</tr>

<tr class="dark_header">
<td style="padding:12px;" colspan="3">
	ORDER FORM
	<small><?php echo $quote_history['quh_ref'];?></small>
</td>
</tr>
<tr class="row">
	<td width="33%" class="dashed">Build No.</td>
	<td width="33%" class="dashed"><?php echo $order['bo_build_no'];?></td>
	<td class="dashed right">
</tr>

<tr class="alt_row">
	<td width="33%" class="dashed">Date Created</td>
	<td width="33%" class="dashed"><?php echo date('d/m/Y',strtotime($order['bo_date_created']));?></td>
	<td class="dashed right">
</tr>

<tr class="row">
	<td width="33%" class="dashed">Primary Contact</td>
	<td width="33%" class="dashed"><?php echo $contact['pr_first_name'];?> <?php echo $contact['pr_surname'];?>, <?php echo $contact['pr_address_1'];?>, <?php echo $contact['pr_city'];?>, <?php echo $contact['pr_county'];?>, <?php echo $contact['pr_postcode'];?></td>
	<td class="dashed right">
</tr>

<tr class="alt_row">
	<td width="33%" class="dashed">Additional Customer</td>
	<td width="33%" class="dashed">-</td>
	<td class="dashed right">
</tr>

<tr class="row">
	<td width="33%" class="dashed">Estimated Delivery Date</td>
	<td width="33%" class="dashed"><?php echo date('d/m/Y',strtotime($order['bo_estimated_delivery_date']));?></td>
	<td class="dashed right">
</tr>

<tr class="alt_row">
	<td width="33%" class="dashed">Deliver To</td>
	<td width="33%" class="dashed"><?php
	switch($order['bo_deliver_to']){
		case 1:
			echo 'Mercia Marina';
		break;
		case 2:
			echo 'Hanbury Wharf';
		break;
	}
	?></td>
	<td class="dashed right">
</tr>

<tr class="row">
	<td width="33%" class="dashed">Off Site Location</td>
	<td width="33%" class="dashed"><?php
	switch($order['bo_off_site_location']){
		case 1:
			echo 'Yes';
		break;
		case 2:
			echo 'No';
		break;
	}
	?></td>
	<td class="dashed right">
</tr>

<tr class="alt_row">
	<td width="33%" class="dashed">Permanent Crane at Location</td>
	<td width="33%" class="dashed">
	<?php
	switch($order['bo_permanent_crane_location']){
		case 1:
			echo 'Yes';
		break;
		case 2:
			echo 'No';
		break;
	}
	?>
	</td>
	<td class="dashed right">
</tr>

<tr class="row">
	<td width="33%" class="dashed">Customer Approved</td>
	<td width="33%" class="dashed">
	<?php
	switch($order['bo_customer_approved']){
		case 1:
			echo 'Yes';
		break;
		case 2:
			echo 'No';
		break;
	}
	?>
	</td>
	<td class="dashed right">
</tr>

<tr class="alt_row">
	<td width="33%" class="dashed">NUBC Agent</td>
	<td width="33%" class="dashed"><?php echo $user['us_firstName'];?> <?php echo $user['us_surname'];?></td>
	<td class="dashed right">
</tr>

<tr>
	<td colspan="2">Customer Signed:</td>
	<td>Date:</td>
</tr>
<tr>
	<td colspan="2">
		<div class="signbox">
			<br>
			<br>
			<br>
		</div>
	</td>
	<td>
		<div class="signbox">
			<br>
			<br>
			<br>
		</div>
	</td>
</tr>
<tr>
	<td colspan="2">NUBC Agent Signed:</td>
	<td>Date:</td>
</tr>
<tr>
	<td colspan="2">
		<div class="signbox">
			<br>
			<br>
			<br>
		</div>
	</td>
	<td>
		<div class="signbox">
			<br>
			<br>
			<br>
		</div>
	</td>
</tr>
