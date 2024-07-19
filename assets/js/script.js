(function ($) {
	//"use strict";
	Ext.onReady(function () {
		$('input').attr('autocomplete', 'nope');
	});
	$(document).ready(function () {

		$('.datepicker').datepicker({
        	format: 'mm/dd/yyyy',
    	});

    	$('input').attr('autocomplete', 'nope');

		$(document).on("click",".remind_button",function() {

			var id = $(this).data('id');
			var mobile = $(this).data('mobile');
			var email = $(this).data('email');

			$('#resend_modal').modal('show');

			$('#resend_email').val(email);
			$('#resend_mobile').val(mobile);
			$('#resend_id').val(id);

		});

		$(document).on("click",".resend_application",function() {
			//alert(id);
			var l = Ladda.create( document.querySelector('.resend_application'));
			l.start();

			$.ajax({
		        'url': url+'dashboard/ajax_resend',
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'id' : $('#resend_id').val(), 'email': $('#resend_email').val(), 'mobile': $('#resend_mobile').val()}
		    }).done(function (response) {

		    	l.stop();
		    	paginate(currentPage);
		    	$('#resend_modal').modal('hide');

		    });
		});
		
		$(document).on("click",".cancel_order_button",function() {

			var orderid = $(this).data('orderid');
			var id = $(this).data('id');
			//alert(id);
			$.ajax({
		        'url': url+'cart/ajax_cancel/'+orderid,
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'id' : id}
		    }).done(function (response) {
		    	toastr.info(response.msg)
		    	paginate(0);
		    });

		});

		/*
		$( ".datepicker" ).datetimepicker({
			inline: true,
			format: 'dd/mm/yy'
		});
		$( ".datepicker" ).datetimepicker('option', 'dateFormat', "dd/mm/yy");
		*/
		//jQuery('#date_of_instruction').datetimepicker();

		$(document).on("change","#preset_dates",function() {
			var val = $(this).val();
			//alert(val);
			var res = val.split(":");
			$('#from_date').val(res[0]);
			$('#to_date').val(res[1]);

			$('#from_date').trigger('change');
			$('#to_date').trigger('change');
		});	
		

		$(document).on("click",".uncaptured",function() {

			var orderid = $(this).data('orderid');
			$('#past_captures').html();
			$.ajax({
		        'url': url+'cart/get_captures/'+orderid,
		        'method': 'POST',
		        'dataType':'html',
		    }).done(function (response) {
		    	$('#past_captures').html(response);
		    });

			
			//alert(orderid);
			$('#order_id').val(orderid);
			$('#capture_modal').modal('show');
		});

		$(document).on("click",".create_capture",function() {

			$.ajax({
		        'url': url+'cart/create_capture',
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'order_id' : $('#order_id').val(), 'capture_amount':$('#capture_amount').val(), 'description':$('#description').val()}
		    }).done(function (response) {
		    	$('#capture_modal').modal('hide');
		    	window.location.reload();
		    });
		});		


		$(document).on("click",".cancel_order",function() {
			var l = Ladda.create( document.querySelector('.cancel_order'));
        	l.start();
			$.ajax({
		        'url': url+'cart/cancel_order',
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'order_id' : $('#order_id').val()}
		    }).done(function (response) {
		    	l.stop();
		    	$('#cancel_modal').modal('hide');
		    	window.location.reload();
		    });
		});	


		$(document).on("click",".create_refund",function() {
			var l = Ladda.create( document.querySelector('.cancel_order'));
        	l.start();
			$.ajax({
		        'url': url+'cart/refund_order',
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'order_id' : $('#order_id').val(), 'refund_amount':$('#refund_amount').val() }
		    }).done(function (response) {
		    	l.stop();
		    	$('#cancel_modal').modal('hide');
		    	window.location.reload();
		    });
		});


		$(document).on("click",".create_release",function() {
			var l = Ladda.create( document.querySelector('.create_release'));
        	l.start();
			$.ajax({
		        'url': url+'cart/release_order',
		        'method': 'POST',
		        'dataType':'json',
		        'data': {'order_id' : $('#order_id').val()}
		    }).done(function (response) {
		    	l.stop();
		    	$('#release_modal').modal('hide');
		    	window.location.reload();
		    });
		});	

		jQuery('body').on('click', '.pagination li a', function(e){
			//alert($(this).attr('data-id'));
			e.preventDefault();
			paginate($(this).attr('data-id'));
	 	});

	 	jQuery('body').on('keyup', '#global_search', function(e){
	 		paginate(0);
	 	});

	 	jQuery('body').on('change', '#creation_date', function(e){
	 		paginate(0);
	 	});

	 	jQuery('body').on('change', '#expiry_date', function(e){
	 		paginate(0);
	 	});

	 	jQuery('body').on('keyup', '#practice_name', function(e){
	 		paginate(0);
	 	});

	 	jQuery('body').on('change', '#from_date', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#to_date', function(e){
	 		paginate(0);
	 	});
	 	/*
	 	jQuery('body').on('change', '#order_pending', function(e){
	 		paginate(0);
	 	});
	 	*/
	 	jQuery('body').on('change', '#order_uncaptured', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#order_paid', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#order_failed', function(e){
	 		paginate(0);
	 	});
	 	/*
	 	jQuery('body').on('change', '#order_cancelled', function(e){
	 		paginate(0);
	 	});
	 	*/
	 	jQuery('body').on('change', '#order_expired', function(e){
	 		paginate(0);
	 	});

	 	//new fields
	 	jQuery('body').on('change', '#part_captured', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#issued', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#authorized', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#not_submitted', function(e){
	 		paginate(0);
	 	});
	 	jQuery('body').on('change', '#order_refunded', function(e){
	 		paginate(0);
	 	});
	 	/*
	 	jQuery('body').on('change', '#captured', function(e){
	 		paginate(0);
	 	});
	 	*/
	 	//end new fields

	 	//searches
	 	jQuery('body').on('keyup', '#s_practice_name', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_klarna_ref', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_patient_first_name', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_patient_last_name', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_patient_email', function(e){
	 		paginate2(0);
	 	});

	 	jQuery('body').on('keyup', '#s_treatment_type', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_practice_reference', function(e){
	 		paginate2(0);
	 	}); 
	 	jQuery('body').on('keyup', '#s_paid_out_status', function(e){
	 		paginate2(0);
	 	});
	 	jQuery('body').on('keyup', '#s_order_overview_status', function(e){
	 		paginate2(0);
	 	}); 


	}); // End document ready
})(this.jQuery);

var currentPage = 0;

function paginate(page){
	currentPage = page;

	var global_search 	= $('#global_search').val();
	var creation_date 	= $('#creation_date').val();
	var expiry_date 	= $('#expiry_date').val();

	var practice_name	= $('#practice_name').val();
	var order_pending	= $('#order_pending:checked').val();
	var order_uncaptured= $('#order_uncaptured:checked').val();
	var order_paid		= $('#order_paid:checked').val();
	var order_failed	= $('#order_failed:checked').val();
	var order_cancelled = $('#order_cancelled:checked').val();
	var order_expired	= $('#order_expired:checked').val();

	var from_date	= $('#from_date').val();
	var to_date		= $('#to_date').val();

	var part_captured	= $('#part_captured:checked').val();
	var issued			= $('#issued:checked').val();
	var authorized		= $('#authorized:checked').val();
	var captured		= $('#captured:checked').val();

	var not_submitted	= $('#not_submitted:checked').val();
	var order_refunded	= $('#order_refunded:checked').val();

	$.ajax({
		type: "POST",
		url: url+'dashboard/ajax_search_transactions',
		data: {'page':page, 'global_search':global_search, 'creation_date':creation_date, 'expiry_date':expiry_date, 
		'practice_name':practice_name, 'order_pending':order_pending, 'order_uncaptured':order_uncaptured, 'order_paid':order_paid, 
		'order_failed':order_failed, 'order_cancelled':order_cancelled, 'order_expired':order_expired, 'from_date':from_date, 'to_date':to_date,
		'part_captured':part_captured, 'issued':issued, 'authorized':authorized, 'captured':captured, 'not_submitted':not_submitted, 'order_refunded':order_refunded
		},
		dataType:'json',
		success: function(data)
		{
			$('#pagination').html(data.data.pagination);
			$('#orderList').html(data.data.rows);
			console.log(data.data.value);
			$('#total_transaction').html(data.data.value);
		}
	});
}

function paginate2(page){
	currentPage = page;

	var s_practice_name 		= $('#s_practice_name').val();
	var s_klarna_ref 			= $('#s_klarna_ref').val();
	var s_patient_first_name 	= $('#s_patient_first_name').val();
	var s_patient_last_name		= $('#s_patient_last_name').val();
	var s_loan_value			= $('#s_loan_value').val();
	var s_treatment_type		= $('#s_treatment_type').val();
	var s_practice_reference	= $('#s_practice_reference').val();

	var s_paid_out_status		= $('#s_paid_out_status').val();
	var s_order_overview_status	= $('#s_order_overview_status').val();

	var s_patient_email			= $('#s_patient_email').val();


	$.ajax({
		type: "POST",
		url: url+'dashboard/ajax_search_transactions2',
		data: {'page':page, 's_practice_name':s_practice_name, 's_klarna_ref':s_klarna_ref, 's_patient_first_name':s_patient_first_name, 
		's_patient_last_name':s_patient_last_name, 's_loan_value':s_loan_value, 's_treatment_type':s_treatment_type, 
		's_practice_reference':s_practice_reference, 's_paid_out_status':s_paid_out_status, 's_order_overview_status':s_order_overview_status, 's_patient_email':s_patient_email},
		dataType:'json',
		success: function(data)
		{
			$('#pagination').html(data.data.pagination);
			$('#orderList').html(data.data.rows);
			console.log(data.data.value);
			$('#total_transaction').html(data.data.value);
		}
	});
}

//paginate(0);
