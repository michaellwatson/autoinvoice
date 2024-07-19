$(document).ready(function() {

		$("#add_client_form").submit(function(e) { 
			e.preventDefault();
			//var l = Ladda.create($('#submitButton'));
			var l = Ladda.create(document.querySelector('#submitButton'));
	 		l.start();

			var url = base_url + "client/saveClientData"; 
			$.ajax({
				   type: "POST",
				   url: url,
				   data: $("#add_client_form").serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
					   if(data.status==1){
					   	   	
					   	   	toastr.success(data.msg);

						    if($('#idOfClient').val()==''){
						   		$("#surveyorForm").trigger("reset");
						   	}
					   }else{
						   	toastr.error(data.msg);
					   }
					   l.stop();
				   }
				 });

			return false;
		});
	});
