jQuery(document).ready(function() {	
	/*
	$('#date_of_instruction').datetimepicker({
		inline: true,
		format: 'dd/mm/yy'
	});
	*/
	jQuery('#date_of_instruction').datetimepicker({
		format:'d/m/Y',
		timepicker:false,
	});
	$('#fee_amount').mask('###0.00', {reverse: true});

	$('#add_job_form').on('submit', function(e){ 
		e.preventDefault();
		var data = $(this).serializeArray();
		var l = Ladda.create(document.querySelector('#save_job'));
    	l.start();

    	jQuery.ajax({
			type: "POST",
			url: url+'job/save/',
			data: data,
			dataType:'json',
			success: function(data)
			{
				l.stop();
				if(data.status==1){
					//window.location.href = base_url+'job/list';
					toastr.success(data.msg);
				}else{
					toastr.error(data.msg);
				}
			}
		});
	})

	$(document).on("change","#client",function() {

		var id = $(this).val();
		$.ajax({
			type: "POST",
			url: url+'personnel/dropdown/'+id,
			data: {'id': id},
			success: function(data)
			{
				$('#personnel').html(data);
			}
		});

	});
});