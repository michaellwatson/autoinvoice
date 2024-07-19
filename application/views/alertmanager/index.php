<style type="text/css">
.flot-x-axis{
	color: #a6b7bf;
}
.flot-y-axis{
	color: #a6b7bf;
}
</style>

<div class="container">
	<div class="row" style="padding:15px;">

        <div class="col-lg-12 col-md-12">
        	<h2>From <?php echo $start;?> To <?php echo $end;?></h2>
        </div>
        <div class="col-lg-12 col-md-12">
        	
			<?php echo $html;?>
            <a class="col-md-12 btn btn-success" href="<?php echo base_url("dashboard/choose_widget");?>">Add Widget</a>

        </div>
	</div>
</div>
