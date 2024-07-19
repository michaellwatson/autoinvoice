<div class="<?php echo (isset($_SESSION['two-col']) && (int)$_SESSION['two-col'] == 1) ? 'container-fluid' : 'container';?> solid headings" style="margin-bottom:15px;margin-top:20px;">
	<div class="col-sm-12 col-md-12 text-left">
		<h3><?php echo ucfirst($ad_name);?></h3> 
	</div>
</div>

<label class="headings-mobile"><?php echo strtoupper($ad_name);?></label> 