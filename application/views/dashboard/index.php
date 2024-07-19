<link rel="stylesheet" type="text/css" href="<?php echo base_url()."assets/plugins/daterangepicker-master/daterangepicker.css" ?>">
<script src="<?php echo base_url()."assets/plugins/daterangepicker-master/daterangepicker.js" ?>"></script>
<script src="<?php echo base_url()."assets/plugins/daterangepicker-master/moment.min.js" ?>"></script>
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
        	<!-- <h2>From <?php //echo $start;?> To <?php //echo $end;?></h2> -->
        	<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<form>
								<div class="form-group mb-3">
									<label>Select Date Range:</label>
									<input type="text" name="daterange_filter" class="form-control" value="<?php echo $start." - ".$end; ?>">
								</div>
							</form>
						</div>
					</div>
					<!-- end card -->
				</div>
			</div>
        </div>
        <div class="col-lg-12 col-md-12">

        	<div class="clsWidgetHtml">
        	</div>
			<?php //echo $html;?>

            <a class="col-md-12 btn btn-success" href="<?php echo base_url("dashboard/choose_widget");?>">Add Widget</a>

        </div>

		<div class="col-lg-12" style="padding-top:15px;">
			<div class="card">
				<div class="card-body">
					<h4 class="header-title">Send Invoices</h4>
					<form name="frmReset">
						<p>Clicking on the button below will send all unsent invoices</p>
						<?php if($send_cron->status == 1){?>Task is Running<br><br><?php } ?>
						<button type="button" class="btn btn-primary ladda-button sendInvoices" data-style="expand-right" onclick="startSend();" <?php if($send_cron->status == 1){?>disabled<?php } ?>>Send</button>
					</form>
				</div>
			</div>
		</div>

	</div>
</div>
<?php include_once("_scripts/_script_dashboard.php"); ?>