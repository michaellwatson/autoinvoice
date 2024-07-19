<?php
$CI =& get_instance();
$CI->load->model('Advertcategorymodel');

ob_start(); //Start remembering everything that would normally be outputted, but don't quite do anything with it yet
$c = 0;
?>
<div class="row">

<div class="col-md-4">

<table class="table table-striped">
  <thead>
    <th></th>
    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Field to use as the trigger date"><b>Date Field <i class="fa fa-question-circle" aria-hidden="true"></i></b></th>
    <th class="text-center" data-toggle="tooltip" data-placement="top" title="Field to use as the contact email"><b>Email Field <i class="fa fa-question-circle" aria-hidden="true"></i></b></th>
  </thead>
<?php
foreach($fields as $f){ ?>
  <tr>
      <td data-id="<?php echo $f['adv_id'];?>">
          <?php echo $f['adv_text'];?>
      </td>

      <td class="text-center">
        <input class="form-check-input check" type="radio" value="<?php echo $f['adv_id'];?>" id="date" name="date" <?php if($f['fi_id']!==9){?>disabled<?php } ?>>
      </td>

      <td class="text-center">
        <input class="form-check-input check" type="radio" value="<?php echo $f['adv_id'];?>" id="email_field" name="email_field">
      </td>
  </tr>
<?php 
  $c++;
} 
?>
<?php foreach($spare_date_fields as $s){?>
  <tr>
      <td>
          <?php echo ucfirst(str_replace('_', ' ', preg_replace('/^ad_/', '', $s)));?>
      </td>

      <td class="text-center">
        <input class="form-check-input check" type="radio" value="<?php echo $s;?>" name="date">
      </td>
      <td colspan="3">
      </td>

  </tr>
<?php } ?>
</table>

</div>
<div class="col-md-4">

  <div class="mr-2">

    <div class="row">
      
      <label for="delay" data-toggle="tooltip" data-placement="top" title="How long from the trigger date to execute"><b>Delay <span class="required">*</span> <i class="fa fa-question-circle" aria-hidden="true"></i></b></label>
      <input type="text" class="form-control" id="delay" name="delay" value="" maxlength="100" required="" type="number">

    </div>
    <div class="row ">
                
      <select id="trigger_delay_period" name="trigger_delay_period" class="form-control trigger_delay_period">
        <option value="0">immediately</option>
        <option value="60">minutes(s)</option>
        <option value="3600">hour(s)</option>
        <option value="86400">day(s)</option>
        <option value="604800">week(s)</option>
      </select> 

    </div>

    </div>

  </div>

  <div class="col-md-4">
    <div class="row">
      
      <label for="delay" data-toggle="tooltip" data-placement="top" title="Message to send"><b>Message <span class="required">*</span> <i class="fa fa-question-circle" aria-hidden="true"></i></b></label>
      <select id="message_id" name="message_id" class="form-control message_id">
          <?php foreach ($messages as $m) { ?>
            <option value="<?php echo $m->ad_id;?>">(<?php echo $m->ad_id;?>) <?php echo $m->ad_subject;?></option>
          <?php } ?>
      </select> 
    </div>
  </div>

</div>

</div>
<?php
$output = ob_get_contents(); //Gives whatever has been "saved"
ob_end_clean(); //Stops saving things and discards whatever was saved
ob_flush();
?>
<div class="contentpanel">
  <div class="row"><!-- col-md-6 -->
      <div class="col-md-12">
          
          <form method="post" id="alert_form">

              <h3>ALERT CONFIG</h3>

              <div class="row">
                <div class="alert alert-secondary" role="alert">
                    Choose a trigger type, then fill in the details of the trigger. The first column is the base date to use as the trigger, for example if you wanted the alert to fire immediately when the record was created, choose the "added" field with 0 delay (immediately). If you wanted an email to go out 2 hours before a date specified, select the date column, set the delay to hours and the value to -2. The last column is to choose the email template that will get sent out.
                </div>
              </div>

              <div class="alert_config">

                <table class="table table-striped">
                  <tr>
                    <td>
                      Trigger Type
                    </td>
                    <td>
                      <select id="alert_type" name="alert_type" class="form-control alert_type">
                        <option value="" selected="selected">Please Select</option>
                        <option value="1" >Time value of a field</option>
                      </select> 
                    </td>
                  </tr>
                </table>

              </div> 
              <button type="submit" class="btn btn-primary ladda-button save_alert" data-style="expand-right" id="save_alert">Save</button> 
              
              <input type="hidden" name="form_id" id="form_id" value="<?php echo $form_id;?>">
          		
          </form>

		</div>
	</div>
</div>

<script language="javascript">
jQuery(document).ready(function() { 

  
  jQuery(document).on("change",".alert_type",function() {

      $('.alert_config').append(<?php echo json_encode($output);?>);
        $('[data-toggle="tooltip"]').tooltip()

  });

  jQuery(document).on("submit","#alert_form", function(e) {

  	  	e.preventDefault();
      	var alert_data = $(this).serialize();
      	console.log(alert_data);
        save_alert

        var l = Ladda.create(document.querySelector('.save_alert'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: base_url+'Alertmanager/save_alert',
            data: alert_data, // serializes the form's elements.
            dataType:'json',
            success: function(data)
            {
              toastr.success(data.msg);
              l.stop();
            }
        });

  });

  /*
    jQuery('.check').on('click', function(e){ 
      var id = $(this).attr('data-id');
      if($('#input_'+id).hasClass('d-none')){
        $('#input_'+id).removeClass('d-none'); 
      }else{
        $('#input_'+id).addClass('d-none');
        $('#input_'+id).val('');
      }
    });
  */

});
</script>