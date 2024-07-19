<h3>CHOOSE ALERT TYPE</h3>   

<div class="row">
	<div class="alert alert-secondary" role="alert">
  		Alerts are a way to schedule emails, or email sequences when records are created or edited in your tables, please choose an alert type below to get started
	</div>
</div>
<div class="row">

 <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Send an Email</h5>
        <h6 class="card-subtitle mb-2 text-muted">Send an email or series of emails when a record is added to a list</h6>
        <p class="card-text">Pick one of your tables as the information source, the system messages table is the default message source</p>
        <a href="<?php echo base_url("alertmanager/add_alert");?>" class="card-link">Choose</a>
      </div>
    </div>
  </div>
  <!--
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Widget Calc</h5>
        <h6 class="card-subtitle mb-2 text-muted">Equation based on other widgets</h6>
        <p class="card-text">Create a equation based on the results of other widgets (only available on counters)</p>
        <a href="<?php echo base_url("dashboard/add_widget");?>" class="card-link">Choose</a>
      </div>
    </div>
  </div>
  -->
</div>