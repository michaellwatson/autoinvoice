        <div class="row">   
            <div class="col-md-12 ab_center_padding select-override">
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-8 text-center">
                    <!--
                    <span class="syscap">SYSCAP</span>
                    <br>
                    -->
                    <span class="wimbledon wimbledon_white">Password Reset</span>
                    <span class="body_copy">
                    <br>
                    Reset your Password
                    <br>
                    <br>
                    </span>
                </div>

                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <div class="purple_boxes login-top">
  
            <!--form-->
       
            <div id="successMessageForgot" class="alert alert-success" role="alert" style="display:none;margin-top:15px;padding-bottom:15px;"></div>
            <div id="warningMessageForgot" class="alert alert-danger" role="alert" style="display:none;margin-top:15px;"></div>
            <form class="form-horizontal form-signin" role="form" method="post">

                <input type="password" class="form-control" value="" id="passwordAdd" name="passwordAdd" placeholder="Password" required="required">
                <br>
                <input type="password" class="form-control" value="" id="passwordConfirm" name="passwordConfirm" placeholder="Password Confirm" required="required">
                <br>
                <input type="text" class="form-control" value="" id="passwordHint" name="passwordHint" placeholder="password hint" required="required">
                <br>
				<input type="hidden" id="code" name="code" value="<?php echo $code?>">
                    	<!--<input type="submit" id="submitButton" class="imgClass" value=""/>-->
                        <button class="btn btn-success form-control ladda-button" data-style="expand-right" id="submitButton">
            				RESET PASSWORD
        				</button>
                
          	</form>
            <br>
        </div>
    </div>
    </div>
<link rel="stylesheet" href="<?php echo base_url('assets/css/ladda-themeless.min.css');?>">

<script src="<?php echo base_url('assets/js/spin.js');?>"></script>
<script src="<?php echo base_url('assets/js/ladda.js');?>"></script> 
<script src="<?php echo base_url('assets/js/prism.js');?>"></script> 

<script language="javascript">
$( document ).ready(function() {

	$(".form-signin").submit(function(e) { 
	//alert('hit');
			e.preventDefault();
			//var l = Ladda.create($('#submitButton'));
			var l = Ladda.create(document.querySelector('#submitButton'));
	 		l.start();
			var url = "<?php echo base_url('verify/resetPassword');?>"; 

			$.ajax({
				   type: "POST",
				   url: url,
				   data: $(".form-signin").serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
					   if(data.status==1){
						   $('#successMessageForgot').html(data.msg);
						   $('#successMessageForgot').show();
						   window.scrollTo(0, 0);
					   }else{
						   //alert(data.msg);
						   $('#warningMessageForgot').html(data.msg);
						   $('#warningMessageForgot').show();
						   setTimeout(function(){$('#warningMessageForgot').fadeOut();}, 2000);
						  
					   } 
					   l.stop();
				   }
			});
	});
});
</script>
