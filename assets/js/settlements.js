(function ($) {
	"use strict";
	$(document).ready(function () {


		$( ".datepicker" ).datepicker({
			inline: true,
			format: 'dd/mm/yy'
		});
		$( ".datepicker" ).datepicker('option', 'dateFormat', "dd/mm/yy");
	

		jQuery('body').on('click', '.pagination li a', function(e){
			//alert($(this).attr('data-id'));
			e.preventDefault();
			paginate_settlements($(this).attr('data-id'));
	 	});

	 	jQuery('body').on('keyup', '#settlement_practice_name', function(e){
	 		paginate_settlements(0);
	 	});




	}); // End document ready
})(this.jQuery);

$(document).ready(function () {
	$(document).on("change","#settlement_presets",function() {
		var val = $(this).val();
		//alert(val);
		var res = val.split(":");
		$('#set_date_from').val(res[0]);
		$('#set_date_to').val(res[1]);

		$('#set_date_from').trigger('change');
		$('#set_date_to').trigger('change');
	});	

	jQuery('body').on('change', '#set_date_from', function(e){
	 	paginate_settlements(0);
	});
	
	jQuery('body').on('change', '#set_date_to', function(e){
	 	paginate_settlements(0);
	});


	jQuery('body').on('keyup', '#st_klarna_ref', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_practice_name', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_patient_first_name', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_patient_last_name', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_treatment', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_practice_reference', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('change', '#st_finance_product', function(e){
	 	paginate_settlements2(0);
	});
	jQuery('body').on('keyup', '#st_loan_value', function(e){
	 	paginate_settlements2(0);
	});
});

var currentPage = 0;
function paginate_settlements(page){
	currentPage = page;

	var practice_name 	= $('#settlement_practice_name').val();
	var date_from 		= $('#set_date_from').val();
	var date_to 		= $('#set_date_to').val();

	$.ajax({
		type: "POST",
		url: url+'settlements/ajax_search_settlements',
		data: {'page':page, 'practice_name':practice_name, 'from_date':date_from, 'to_date':date_to},
		dataType:'json',
		success: function(data)
		{
			$('#pagination').html(data.data.pagination);
			$('#settlementsList').html(data.data.rows);
			console.log(data.data.value);
			$('#total_transaction').html(data.data.value);
		}
	});
}

function paginate_settlements2(page){
	currentPage = page;

	var st_klarna_ref 			= $('#st_klarna_ref').val();
	var st_practice_name 		= $('#st_practice_name').val();
	var st_patient_first_name 	= $('#st_patient_first_name').val();
	var st_patient_last_name	= $('#st_patient_last_name').val();
	var st_treatment			= $('#st_treatment').val();
	var st_practice_reference	= $('#st_practice_reference').val();
	var st_finance_product		= $('#st_finance_product').val();
	var st_loan_value			= $('#st_loan_value').val();



	$.ajax({
		type: "POST",
		url: url+'settlements/ajax_search_settlements2',
		data: {
				'page':page, 
				'st_klarna_ref':st_klarna_ref, 
				'st_practice_name':st_practice_name, 
				'st_patient_first_name':st_patient_first_name, 
				'st_patient_last_name':st_patient_last_name, 
				'st_treatment':st_treatment, 
				'st_practice_reference':st_practice_reference, 
				'st_finance_product':st_finance_product, 
				'st_loan_value':st_loan_value
			},
		dataType:'json',
		success: function(data)
		{
			$('#pagination').html(data.data.pagination);
			$('#settlementsList').html(data.data.rows);
			console.log(data.data.value);
			$('#total_transaction').html(data.data.value);
		}
	});

}

paginate_settlements(0);
