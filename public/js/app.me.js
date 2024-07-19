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

$( document ).ready(function() {
	$('.message_form').on('submit', function(e){ 
		e.preventDefault();
		//alert('hit');
		var a = $(this).serializeArray();
		//alert(a[0].value);
		//console.log(a);
		var l = Ladda.create(document.querySelector('#saveButton_'+a[0].value));
	 	l.start();

		console.log($(this).serialize());
		$.ajax({
			type: "POST",
			url: base_url+'messages/update_messages',
			data: $(this).serialize(),
			dataType:'json',
			success: function(data)
			{
				l.stop();
			}
		});
	});
});

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

jQuery(document).ready(function() { 

    $('#add_user_form').on('submit', function(e){ 
        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_user'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'user/save/',
            data: data,
            dataType:'json',
            success: function(data)
            {
                l.stop();
                if(data.status==1){
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });

    })
});
jQuery(document).ready(function() { 

    $('#add_role_form').on('submit', function(e){ 
        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_role'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'roles/save/',
            data: data,
            dataType:'json',
            success: function(data)
            {
                l.stop();
                if(data.status==1){
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });

    })
});
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
jQuery(document).ready(function() { 

    $('#add_personnel_form').on('submit', function(e){ 

        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_personnel'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'personnel/save/',
            data: data,
            dataType:'json',
            success: function(data)
            {
                l.stop();
                if(data.status==1){
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });

    })
});
jQuery( document ).ready(function() {
  //CKEDITOR.replace()
  
  	$(document).on("click",".save_text",function(){
  		//alert('hit');
  		let data = {
  			'field_id': 	$('#field_id').val(), 
  			'id': 			$('#id').val(),
				'name': 		$('#prefill_name').val()
  		};
			var textarea = $('#editor1');
			if (textarea.length) {
				data.prefill = CKEDITOR.instances.editor1.getData();
			}
			else {
				data.prefill = $("#id_json_string").val().trim();
			}
  		$.ajax({
			type: "POST",
			url: base_url+'Prefilltext/save',
			data,
			dataType:'json',
			success: function(data)
			{
				//paginate(currentPage);
				window.location.reload();
			}
		});

  	});

  	$(document).on("click",".edit_row",function(e){
  		e.preventDefault();
  		var id = $(this).data('id');
  		$('#id').val(id);

  		var name = $('.name_'+id).val();
  		$('#prefill_name').val(name.trim());

  		var textarea = $('#editor1');
			if (textarea.length) {
				var html = $('.data_'+id).html();
  			CKEDITOR.instances.editor1.setData(html);
			}
			else {
				var content = $('.data_'+id).html().trim();
				$("#id_json_string").val(content);
			}

  		window.scrollTo(0, 0);
  		//window.location.reload();
  	});

  	$(document).on("click",".delete_row",function(e){
  		e.preventDefault();
		var id = $(this).data('id');
		$.ajax({
			type: "POST",
			url: base_url+'Prefilltext/delete/'+id,
			dataType:'json',
			success: function(data)
			{
				//paginate(currentPage);
				window.location.reload();
			}
		});
  	});	
});
$(document).ready(function() {
    $(document).on("submit","#mapping_form",function(e) {
      e.preventDefault();
      //var l = Ladda.create($('#submitButton'));
      var l = Ladda.create(document.querySelector('#import_button'));
      l.start();

      var url = base_url + "import/save_mapping/"+ $('#form_id').val(); 
      $.ajax({
           type: "POST",
           url: url,
           data: $("#mapping_form").serialize(), // serializes the form's elements.
           dataType:'json',
           success: function(data)
           {
             if(data.status==1){
                  
                toastr.success(data.msg);
                window.location = base_url+'entries/show/1?formID='+$('#form_id').val();

             }else{
                
                toastr.error(data.msg);
             
             }
             l.stop();
           }
         });

      return false;
    });

});
if($('#client-div').length>0){


	var storeTable = 
	Ext.create('Ext.data.JsonStore', {
		pageSize: 25,
    	fields:['c_id', 'clientName', 'companyName', 'companyAddress', 'companyAddress2', 'postcode', 'telephoneNumber', 'email'],
		proxy: {
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'store',
				totalProperty: 'totalProperty'
			}
    	},
    	autoLoad : true,
     		/*data:{'items':[{ 
			'id': '',  
			'name':'',
			'house_number':'',
			'street_name':'',
			'town':'',
			'postcode':'',
			'pt_name':'',
			'pc_name':'',
			'companyName':'',
			'tagname':'',
		}]}*/
	}); 

var pluginExpanded = true;
var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
	id:'gridPanelHourly',
	hidden:true,
    title: 'Manage Clients',
    store: storeTable,
	listeners : {
		itemdblclick: function(dv, record, item, index, e) {
			//alert('working');
			var c_id = gridPanelHourlyVar.store.getAt(index).data.c_id;
			console.log(c_id);
			window.location.href = base_url+"client/add/"+c_id;
		}
	},

    columns: [
        { text: 'id', dataIndex: 'c_id', flex: 1, hidden: true, hideable: false },
		{ text: 'Client Name', dataIndex: 'clientName', flex: 1 },
		{ text: 'Company Name', dataIndex: 'companyName', flex: 1 },
		{ text: 'Company Address', dataIndex: 'companyAddress', flex: 1 },
		{ text: 'Company Address 2', dataIndex: 'companyAddress2', flex: 1 },
		{ text: 'Postcode ', dataIndex: 'postcode', flex: 1 },
		{ text: 'Email', dataIndex: 'email', flex: 1 },
		{
                xtype: 'actioncolumn',
				text:'Delete',
                width: 60,
                items: [{
                    icon: base_url+'assets/images/icons/cancel.png',
                    handler: function(grid, rowIndex, colindex) {
						var id = grid.store.getAt(rowIndex).data.c_id;
						Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
							if(btn === 'yes'){
								//some code
								
								$.ajax({
								   type: "POST",
								   url: base_url+'client/delete',
								   data: {'client_id': id},
								   dataType:'json',
								   success: function(data)
								   {
									   if(data.status=='1'){
										   var grid = Ext.getCmp('gridPanelHourly');
											var selection = grid.getView().getSelectionModel().getSelection()[0];
											console.log(selection);
											if (selection) {
												storeTable.remove(selection);
											}
									   }else{
										   Ext.Msg.alert('Error', data.msg);
									   }
								   }
								});
							}else{
								//some code
							}
 						});
                    }
                }]
            }

    ],
    height: $(document).height(),
	width:'100%',
	// paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTable,
            displayInfo: true,
            displayMsg: 'Displaying houses {0} - {1} of {2}',
            emptyMsg: "No houses to display",
        }),
    renderTo: 'client-div'
});

					storeTable.loadData([],false);
					storeTable.getProxy().url = base_url+'client/results';
					storeTable.load();
					var panel = Ext.getCmp('gridPanelHourly');
					panel.show();
					panel.doLayout(); 

}
  $( document ).ready(function() {

  	$(document).on('click', '.archive', function(){
        var l = Ladda.create( document.querySelector('.archive'));
        var id = $(this).attr('data-id');
        l.start();

        $.ajax({
            'url': url+'job/archive',
            'method': 'POST',
            'dataType':'json',
            'data': {'id': id, 'archive': $('#archive').val()}
        }).done(function (data) {
            l.stop();
            if(data.status==1){
            	toastr.success(data.msg);
            	window.location.reload();
            }else{
            	toastr.warning(data.msg);
            }
        });
    
    });

    $(document).on('click', '.issue', function(){
        var tableid = $(this).attr('data-tableid');
        var entryid = $(this).attr('data-entryid');

        $('#iss_entryid').val(entryid);
        $('#iss_tableid').val(tableid);
        
        $.ajax({
            'url': url+'messages/get_by_document/'+tableid,
            'method': 'POST',
            'dataType':'json',
        }).done(function (data) {
            $('#iss_subject').val(data.subject);
            $('#iss_message').val(data.message);
            $('#issueModal').modal('show');
        });
    });

    $(document).on('click', '.send_document', function(){
        var l = Ladda.create( document.querySelector('.send_document'));
        l.start();

        var data = $('#email_send').serializeArray();

        //$('#iss_entryid').val(entryid);
        //$('#iss_tableid').val(tableid);

        $.ajax({
            'url': url+'messages/send_message?formID='+$('#iss_tableid').val(),
            'method': 'POST',
            'dataType':'json',
            'data': data
        }).done(function (data) {
            l.stop();
            if(data.status==1){
              $('#issueModal').modal('hide');
            }
        });

    });

    
    $(document).on('change', '#pick_email', function(){
      
      var tokens = $('#email_list').tokenfield('getTokens');
      console.log(tokens);

      var email_list = Array();
      for (index = 0; index < tokens.length; ++index) {
          email_list.push(tokens[index].value);
      }

      var val         = $(this).val();
      if(email_list.indexOf(val)===-1){
        
        /*
        var email_list  = $('#email_list').val();
        $('#email_list').val(email_list+','+val);
        $('#email_list').tokenfield()
        */
        $('#email_list').tokenfield('createToken', val);
      }
    });
});
jQuery(document).ready(function() { 

  $('#add_link_form').on('submit', function(e){ 
    e.preventDefault();
    var data = $(this).serializeArray();
    var l = Ladda.create(document.querySelector('#save_link'));
      l.start();

      jQuery.ajax({
      type: "POST",
      url: url+'documents/save_link/',
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

$(document).on("change","#nature_of_instruction",function() {
  $('#standard_items').val('');
});

$(document).on("change","#standard_items",function() {
  $('#nature_of_instruction').val('');
});

});
jQuery(document).ready(function() { 

  $('#add_link_form').on('submit', function(e){ 
    e.preventDefault();
    var data = $(this).serializeArray();
    var l = Ladda.create(document.querySelector('#save_email_link'));
      l.start();

      jQuery.ajax({
      type: "POST",
      url: url+'documents/save_email_link/',
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
  
});
if($('#users-list').length>0){

var pageSize = 25;
var searchUrl = '';
var count = 0;
Ext.onReady(function () {

  // setup the state provider, all state information will be saved to a cookie
  //Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
  if (Ext.supports.LocalStorage) {
    //alert('Localstorage Store');
    Ext.state.Manager.setProvider(
      Ext.create('Ext.state.LocalStorageProvider')
    );
  } else {
    //Ext.state.Manager.setProvider(
    //  Ext.create('Ext.state.CookieProvider')
    //);
    var thirtyDays = new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30));
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
      expires: thirtyDays
    }));
  }

  var globals = {
    currentEditId: '',
    totalPages: '',
    loaded: false,
    fromRecord: '',
    toRecord: '',
    storeLoaded: false,
    visibleColumns: '',
    indexID: '',
    color: ''
  }
  Ext.namespace("Ext.ux");

  Ext.Loader.setConfig({
    enabled: true,
    paths: {
      Ext: '.',
      'Ext.ux': '../public/extjs/ux'
    }
  });

  Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.ux.grid.Printer',
    'Ext.ux.RowExpander'
  ]);

  Ext.ux.comboBoxRendererWarrant = function (combo) {
    return function (value) {
      return value;
    }
  }
  Ext.ux.comboBoxRenderer = function (combo) {
    return function (value) {
      var idx = combo.store.find(combo.valueField, value);
      var rec = combo.store.getAt(idx);
      return (rec == null ? '' : rec.get(combo.displayField));
    };
  }

  var storeTable = Ext.create('Ext.data.JsonStore', {
    count: 0,
    listeners: {
      load: function () {
        console.log(this);
      }
    },
    sorters: [{
      property: 'us_lastlogin',
      direction: 'DESC'
    }],
    sortRoot: 'us_lastlogin',
    sortOnLoad: false,
    remoteSort: true,
    pageSize: pageSize,
    fields: ['us_id', 'us_firstName', 'us_surname', 'us_company', 'us_joined', 'us_lastlogin', 'us_verified', 'us_blocked', 'role_name'],
    proxy: {
      type: 'ajax',
      reader: {
        type: 'json',
        root: 'store',
        totalProperty: 'totalProperty'
      }
    },
  });

  storeTable.currentPage = '1';
  storeTable.loadData([], false);
  storeTable.getProxy().url = base_url + 'user/results/' + searchUrl;
  console.log(storeTable.getProxy().url);

  storeTable.load();
  console.log(storeTable.count);


  var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
    id: 'gridPanelHourly',
    width: '100%',
    height: $(document).height(),
    hidden: true,
    title: 'Users',
    store: storeTable,
    stateful: true,
    XStateful: true,
    selModel: new Ext.selection.RowModel({
      mode: "MULTI"
    }),
    stateId: 'stateGridUsers',
    viewConfig: {
      getRowClass: function (record) {
      }
    },
    listeners: {
      itemdblclick: function (dv, record, item, index, e) {
        //alert('working');
        var id = gridPanelHourlyVar.store.getAt(index).data.us_id;
        console.log(id);
        window.location.href = base_url+"user/add/" + id;
      },
      
      itemclick: function (dv, record, item, index, e) {
        console.log(index);
        globals.currentEditId = gridPanelHourlyVar.store.getAt(index).data.us_id;
        globals.indexID = index;
      }
    
    },
    plugins: [
      Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
      })
    ],
    columns: [{
        text: 'id',
        dataIndex: 'us_id',
        flex: 1,
        //hidden: true,
        //hideable: false
      },
      {
        text: 'First Name',
        dataIndex: 'us_firstName',
        flex: 1,
      },
      {
        text: 'Surname',
        dataIndex: 'us_surname',
        flex: 1,
      },
      {
        text: 'Company',
        dataIndex: 'us_company',
        flex: 1,
      },
      {
        text: 'Joined',
        dataIndex: 'us_joined',
        flex: 1,
      },
      {
        text: 'Last Login',
        dataIndex: 'us_lastlogin',
        flex: 1,
      },
      {
        text: 'Verified',
        dataIndex: 'us_verified',
        flex: 1,
        renderer:function(value, metaData, record, row, col, store, gridView){
          if(value=='0'){
            return 'N';
          }else{
            return 'Y';
          }
        } 
      },
      {
        text: 'Blocked',
        dataIndex: 'us_blocked',
        flex: 1,
        renderer:function(value, metaData, record, row, col, store, gridView){
          if(value=='0'){
            return 'N';
          }else{
            return 'Y';
          }
        } 
      },
      {
        text: 'Role',
        dataIndex: 'role_name',
        flex: 1,
      },
    ],
    renderTo: 'users-list'

  });

  var panel = Ext.getCmp('gridPanelHourly');
  panel.show();
  panel.doLayout();
  //Ext.getCmp('pagingToolBar').moveFirst();
});

}
jQuery(document).ready(function() { 

    $('#add_staff_form').on('submit', function(e){ 
        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_staff'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'staff/save/',
            data: data,
            dataType:'json',
            success: function(data)
            {
                l.stop();
                if(data.status==1){
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });

    })
});
if($('#staffs-list').length>0){

var pageSize = 25;
var searchUrl = '';
var count = 0;
Ext.onReady(function () {

  // setup the state provider, all state information will be saved to a cookie
  //Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
  if (Ext.supports.LocalStorage) {
    //alert('Localstorage Store');
    Ext.state.Manager.setProvider(
      Ext.create('Ext.state.LocalStorageProvider')
    );
  } else {
    //Ext.state.Manager.setProvider(
    //  Ext.create('Ext.state.CookieProvider')
    //);
    var thirtyDays = new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30));
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
      expires: thirtyDays
    }));
  }

  var globals = {
    currentEditId: '',
    totalPages: '',
    loaded: false,
    fromRecord: '',
    toRecord: '',
    storeLoaded: false,
    visibleColumns: '',
    indexID: '',
    color: ''
  }
  Ext.namespace("Ext.ux");

  Ext.Loader.setConfig({
    enabled: true,
    paths: {
      Ext: '.',
      'Ext.ux': '../public/extjs/ux'
    }
  });

  Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.ux.grid.Printer',
    'Ext.ux.RowExpander'
  ]);

  Ext.ux.comboBoxRendererWarrant = function (combo) {
    return function (value) {
      return value;
    }
  }
  Ext.ux.comboBoxRenderer = function (combo) {
    return function (value) {
      var idx = combo.store.find(combo.valueField, value);
      var rec = combo.store.getAt(idx);
      return (rec == null ? '' : rec.get(combo.displayField));
    };
  }

  var storeTable = Ext.create('Ext.data.JsonStore', {
    count: 0,
    listeners: {
      load: function () {
        console.log(this);
      }
    },
    sorters: [{
      property: 'ad_id',
      direction: 'DESC'
    }],
    sortRoot: 'ad_id',
    sortOnLoad: false,
    remoteSort: true,
    pageSize: pageSize,
    fields: ['ad_id', 'ad_firstname', 'ad_lastname', 'email'],
    proxy: {
      type: 'ajax',
      reader: {
        type: 'json',
        root: 'store',
        totalProperty: 'totalProperty'
      }
    },
  });

  storeTable.currentPage = '1';
  storeTable.loadData([], false);
  storeTable.getProxy().url = base_url + 'staff/results/' + searchUrl;
  console.log(storeTable.getProxy().url);

  storeTable.load();
  console.log(storeTable.count);


  var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
    id: 'gridPanelHourly',
    width: '100%',
    height: $(document).height(),
    hidden: true,
    title: 'Users',
    store: storeTable,
    stateful: true,
    XStateful: true,
    selModel: new Ext.selection.RowModel({
      mode: "MULTI"
    }),
    stateId: 'stateGridUsers',
    viewConfig: {
      getRowClass: function (record) {
      }
    },
    listeners: {
      itemdblclick: function (dv, record, item, index, e) {
        //alert('working');
        var id = gridPanelHourlyVar.store.getAt(index).data.ad_id;
        console.log(id);
        window.location.href = base_url+"staff/add/" + id;
      },
      
      itemclick: function (dv, record, item, index, e) {
        console.log(index);
        globals.currentEditId = gridPanelHourlyVar.store.getAt(index).data.ad_id;
        globals.indexID = index;
      }
    
    },
    plugins: [
      Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
      })
    ],
    columns: [{
        text: 'id',
        dataIndex: 'ad_id',
        flex: 1,
        //hidden: true,
        //hideable: false
      },
      {
        text: 'First Name',
        dataIndex: 'ad_firstname',
        flex: 1,
      },
      {
        text: 'Last Name',
        dataIndex: 'ad_lastname',
        flex: 1,
      },
    ],
    renderTo: 'staffs-list'

  });

  var panel = Ext.getCmp('gridPanelHourly');
  panel.show();
  panel.doLayout();
  //Ext.getCmp('pagingToolBar').moveFirst();
});

}
/*
 Sticky-kit v1.1.3 | MIT | Leaf Corcoran 2015 | http://leafo.net
*/
(function(){var c,f;c=window.jQuery;f=c(window);c.fn.stick_in_parent=function(b){var A,w,J,n,B,K,p,q,L,k,E,t;null==b&&(b={});t=b.sticky_class;B=b.inner_scrolling;E=b.recalc_every;k=b.parent;q=b.offset_top;p=b.spacer;w=b.bottoming;null==q&&(q=0);null==k&&(k=void 0);null==B&&(B=!0);null==t&&(t="is_stuck");A=c(document);null==w&&(w=!0);L=function(a){var b;return window.getComputedStyle?(a=window.getComputedStyle(a[0]),b=parseFloat(a.getPropertyValue("width"))+parseFloat(a.getPropertyValue("margin-left"))+
parseFloat(a.getPropertyValue("margin-right")),"border-box"!==a.getPropertyValue("box-sizing")&&(b+=parseFloat(a.getPropertyValue("border-left-width"))+parseFloat(a.getPropertyValue("border-right-width"))+parseFloat(a.getPropertyValue("padding-left"))+parseFloat(a.getPropertyValue("padding-right"))),b):a.outerWidth(!0)};J=function(a,b,n,C,F,u,r,G){var v,H,m,D,I,d,g,x,y,z,h,l;if(!a.data("sticky_kit")){a.data("sticky_kit",!0);I=A.height();g=a.parent();null!=k&&(g=g.closest(k));if(!g.length)throw"failed to find stick parent";
v=m=!1;(h=null!=p?p&&a.closest(p):c("<div />"))&&h.css("position",a.css("position"));x=function(){var d,f,e;if(!G&&(I=A.height(),d=parseInt(g.css("border-top-width"),10),f=parseInt(g.css("padding-top"),10),b=parseInt(g.css("padding-bottom"),10),n=g.offset().top+d+f,C=g.height(),m&&(v=m=!1,null==p&&(a.insertAfter(h),h.detach()),a.css({position:"",top:"",width:"",bottom:""}).removeClass(t),e=!0),F=a.offset().top-(parseInt(a.css("margin-top"),10)||0)-q,u=a.outerHeight(!0),r=a.css("float"),h&&h.css({width:L(a),
height:u,display:a.css("display"),"vertical-align":a.css("vertical-align"),"float":r}),e))return l()};x();if(u!==C)return D=void 0,d=q,z=E,l=function(){var c,l,e,k;if(!G&&(e=!1,null!=z&&(--z,0>=z&&(z=E,x(),e=!0)),e||A.height()===I||x(),e=f.scrollTop(),null!=D&&(l=e-D),D=e,m?(w&&(k=e+u+d>C+n,v&&!k&&(v=!1,a.css({position:"fixed",bottom:"",top:d}).trigger("sticky_kit:unbottom"))),e<F&&(m=!1,d=q,null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.detach()),c={position:"",width:"",top:""},a.css(c).removeClass(t).trigger("sticky_kit:unstick")),
B&&(c=f.height(),u+q>c&&!v&&(d-=l,d=Math.max(c-u,d),d=Math.min(q,d),m&&a.css({top:d+"px"})))):e>F&&(m=!0,c={position:"fixed",top:d},c.width="border-box"===a.css("box-sizing")?a.outerWidth()+"px":a.width()+"px",a.css(c).addClass(t),null==p&&(a.after(h),"left"!==r&&"right"!==r||h.append(a)),a.trigger("sticky_kit:stick")),m&&w&&(null==k&&(k=e+u+d>C+n),!v&&k)))return v=!0,"static"===g.css("position")&&g.css({position:"relative"}),a.css({position:"absolute",bottom:b,top:"auto"}).trigger("sticky_kit:bottom")},
y=function(){x();return l()},H=function(){G=!0;f.off("touchmove",l);f.off("scroll",l);f.off("resize",y);c(document.body).off("sticky_kit:recalc",y);a.off("sticky_kit:detach",H);a.removeData("sticky_kit");a.css({position:"",bottom:"",top:"",width:""});g.position("position","");if(m)return null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.remove()),a.removeClass(t)},f.on("touchmove",l),f.on("scroll",l),f.on("resize",y),c(document.body).on("sticky_kit:recalc",y),a.on("sticky_kit:detach",H),setTimeout(l,
0)}};n=0;for(K=this.length;n<K;n++)b=this[n],J(c(b));return this}}).call(this);

$(function(){"use strict";$(function(){$(".preloader").fadeOut()}),jQuery(document).on("click",".mega-dropdown",function(i){i.stopPropagation()});var i=function(){var i=window.innerWidth>0?window.innerWidth:this.screen.width,e=70;500>i?($("body").addClass("mini-sidebar"),$(".navbar-brand span").hide(),$(".scroll-sidebar, .slimScrollDiv").css("overflow-x","visible").parent().css("overflow","visible"),$(".sidebartoggler i").addClass("ti-menu")):($("body").removeClass("mini-sidebar"),$(".navbar-brand span").show(),$(".sidebartoggler i").removeClass("ti-menu"));var s=(window.innerHeight>0?window.innerHeight:this.screen.height)-1;s-=e,1>s&&(s=1),s>e&&$(".page-wrapper").css("min-height",s+"px")};$(window).ready(i),$(window).on("resize",i),$(".fix-header .topbar").stick_in_parent({}),$(".nav-toggler").click(function(){$("body").toggleClass("show-sidebar"),$(".nav-toggler i").toggleClass("ti-menu"),$(".nav-toggler i").addClass("ti-close")}),$(".sidebartoggler").on("click",function(){$(".sidebartoggler i").toggleClass("ti-menu")}),$(function(){for(var i=window.location,e=$("ul#sidebarnav a").filter(function(){return this.href==i}).addClass("active").parent().addClass("active");;){if(!e.is("li"))break;e=e.parent().addClass("in").parent().addClass("active")}}),$(function(){$("#sidebarnav").metisMenu()}),$(".scroll-sidebar").slimScroll({position:"left",size:"5px",height:"100%",color:"#dcdcdc"}),$("body").trigger("resize")});
!function(e){e.fn.extend({slimScroll:function(i){var o={width:"auto",height:"250px",size:"7px",color:"#000",position:"right",distance:"1px",start:"top",opacity:.4,alwaysVisible:!1,disableFadeOut:!1,railVisible:!1,railColor:"#333",railOpacity:.2,railDraggable:!0,railClass:"slimScrollRail",barClass:"slimScrollBar",wrapperClass:"slimScrollDiv",allowPageScroll:!1,wheelStep:20,touchScrollStep:200,borderRadius:"7px",railBorderRadius:"7px"},s=e.extend(o,i);return this.each(function(){function o(t){if(h){var t=t||window.event,i=0;t.wheelDelta&&(i=-t.wheelDelta/120),t.detail&&(i=t.detail/3);var o=t.target||t.srcTarget||t.srcElement;e(o).closest("."+s.wrapperClass).is(x.parent())&&r(i,!0),t.preventDefault&&!y&&t.preventDefault(),y||(t.returnValue=!1)}}function r(e,t,i){y=!1;var o=e,r=x.outerHeight()-R.outerHeight();if(t&&(o=parseInt(R.css("top"))+e*parseInt(s.wheelStep)/100*R.outerHeight(),o=Math.min(Math.max(o,0),r),o=e>0?Math.ceil(o):Math.floor(o),R.css({top:o+"px"})),v=parseInt(R.css("top"))/(x.outerHeight()-R.outerHeight()),o=v*(x[0].scrollHeight-x.outerHeight()),i){o=e;var a=o/x[0].scrollHeight*x.outerHeight();a=Math.min(Math.max(a,0),r),R.css({top:a+"px"})}x.scrollTop(o),x.trigger("slimscrolling",~~o),n(),c()}function a(e){window.addEventListener?(e.addEventListener("DOMMouseScroll",o,!1),e.addEventListener("mousewheel",o,!1)):document.attachEvent("onmousewheel",o)}function l(){f=Math.max(x.outerHeight()/x[0].scrollHeight*x.outerHeight(),m),R.css({height:f+"px"});var e=f==x.outerHeight()?"none":"block";R.css({display:e})}function n(){if(l(),clearTimeout(p),v==~~v){if(y=s.allowPageScroll,b!=v){var e=0==~~v?"top":"bottom";x.trigger("slimscroll",e)}}else y=!1;return b=v,f>=x.outerHeight()?void(y=!0):(R.stop(!0,!0).fadeIn("fast"),void(s.railVisible&&E.stop(!0,!0).fadeIn("fast")))}function c(){s.alwaysVisible||(p=setTimeout(function(){s.disableFadeOut&&h||u||d||(R.fadeOut("slow"),E.fadeOut("slow"))},1e3))}var h,u,d,p,g,f,v,b,w="<div></div>",m=30,y=!1,x=e(this);if(x.parent().hasClass(s.wrapperClass)){var C=x.scrollTop();if(R=x.closest("."+s.barClass),E=x.closest("."+s.railClass),l(),e.isPlainObject(i)){if("height"in i&&"auto"==i.height){x.parent().css("height","auto"),x.css("height","auto");var H=x.parent().parent().height();x.parent().css("height",H),x.css("height",H)}if("scrollTo"in i)C=parseInt(s.scrollTo);else if("scrollBy"in i)C+=parseInt(s.scrollBy);else if("destroy"in i)return R.remove(),E.remove(),void x.unwrap();r(C,!1,!0)}}else if(!(e.isPlainObject(i)&&"destroy"in i)){s.height="auto"==s.height?x.parent().height():s.height;var S=e(w).addClass(s.wrapperClass).css({position:"relative",overflow:"hidden",width:s.width,height:s.height});x.css({overflow:"hidden",width:s.width,height:s.height});var E=e(w).addClass(s.railClass).css({width:s.size,height:"100%",position:"absolute",top:0,display:s.alwaysVisible&&s.railVisible?"block":"none","border-radius":s.railBorderRadius,background:s.railColor,opacity:s.railOpacity,zIndex:90}),R=e(w).addClass(s.barClass).css({background:s.color,width:s.size,position:"absolute",top:0,opacity:s.opacity,display:s.alwaysVisible?"block":"none","border-radius":s.borderRadius,BorderRadius:s.borderRadius,MozBorderRadius:s.borderRadius,WebkitBorderRadius:s.borderRadius,zIndex:99}),D="right"==s.position?{right:s.distance}:{left:s.distance};E.css(D),R.css(D),x.wrap(S),x.parent().append(R),x.parent().append(E),s.railDraggable&&R.bind("mousedown",function(i){var o=e(document);return d=!0,t=parseFloat(R.css("top")),pageY=i.pageY,o.bind("mousemove.slimscroll",function(e){currTop=t+e.pageY-pageY,R.css("top",currTop),r(0,R.position().top,!1)}),o.bind("mouseup.slimscroll",function(e){d=!1,c(),o.unbind(".slimscroll")}),!1}).bind("selectstart.slimscroll",function(e){return e.stopPropagation(),e.preventDefault(),!1}),E.hover(function(){n()},function(){c()}),R.hover(function(){u=!0},function(){u=!1}),x.hover(function(){h=!0,n(),c()},function(){h=!1,c()}),x.bind("touchstart",function(e,t){e.originalEvent.touches.length&&(g=e.originalEvent.touches[0].pageY)}),x.bind("touchmove",function(e){if(y||e.originalEvent.preventDefault(),e.originalEvent.touches.length){var t=(g-e.originalEvent.touches[0].pageY)/s.touchScrollStep;r(t,!0),g=e.originalEvent.touches[0].pageY}}),l(),"bottom"===s.start?(R.css({top:x.outerHeight()-R.outerHeight()}),r(0,!0)):"top"!==s.start&&(r(e(s.start).position().top,null,!0),s.alwaysVisible||R.hide()),a(this)}}),this}}),e.fn.extend({slimscroll:e.fn.slimScroll})}(jQuery);
/*
Template Name: Monster Admin
Author: Themedesigner
Email: niravjoshi87@gmail.com
File: js
*/
(function (global, factory) {
  if (typeof define === "function" && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports !== "undefined") {
    factory(require('jquery'));
  } else {
    var mod = {
      exports: {}
    };
    factory(global.jquery);
    global.metisMenu = mod.exports;
  }
})(this, function (_jquery) {
  'use strict';

  var _jquery2 = _interopRequireDefault(_jquery);

  function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
      default: obj
    };
  }

  var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  };

  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  var Util = function ($) {
    var transition = false;

    var TransitionEndEvent = {
      WebkitTransition: 'webkitTransitionEnd',
      MozTransition: 'transitionend',
      OTransition: 'oTransitionEnd otransitionend',
      transition: 'transitionend'
    };

    function getSpecialTransitionEndEvent() {
      return {
        bindType: transition.end,
        delegateType: transition.end,
        handle: function handle(event) {
          if ($(event.target).is(this)) {
            return event.handleObj.handler.apply(this, arguments);
          }
          return undefined;
        }
      };
    }

    function transitionEndTest() {
      if (window.QUnit) {
        return false;
      }

      var el = document.createElement('mm');

      for (var name in TransitionEndEvent) {
        if (el.style[name] !== undefined) {
          return {
            end: TransitionEndEvent[name]
          };
        }
      }

      return false;
    }

    function transitionEndEmulator(duration) {
      var _this2 = this;

      var called = false;

      $(this).one(Util.TRANSITION_END, function () {
        called = true;
      });

      setTimeout(function () {
        if (!called) {
          Util.triggerTransitionEnd(_this2);
        }
      }, duration);

      return this;
    }

    function setTransitionEndSupport() {
      transition = transitionEndTest();
      $.fn.emulateTransitionEnd = transitionEndEmulator;

      if (Util.supportsTransitionEnd()) {
        $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
      }
    }

    var Util = {
      TRANSITION_END: 'mmTransitionEnd',

      triggerTransitionEnd: function triggerTransitionEnd(element) {
        $(element).trigger(transition.end);
      },
      supportsTransitionEnd: function supportsTransitionEnd() {
        return Boolean(transition);
      }
    };

    setTransitionEndSupport();

    return Util;
  }(jQuery);

  var MetisMenu = function ($) {

    var NAME = 'metisMenu';
    var DATA_KEY = 'metisMenu';
    var EVENT_KEY = '.' + DATA_KEY;
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var TRANSITION_DURATION = 350;

    var Default = {
      toggle: true,
      preventDefault: true,
      activeClass: 'active',
      collapseClass: 'collapse',
      collapseInClass: 'in',
      collapsingClass: 'collapsing',
      triggerElement: 'a',
      parentTrigger: 'li',
      subMenu: 'ul'
    };

    var Event = {
      SHOW: 'show' + EVENT_KEY,
      SHOWN: 'shown' + EVENT_KEY,
      HIDE: 'hide' + EVENT_KEY,
      HIDDEN: 'hidden' + EVENT_KEY,
      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
    };

    var MetisMenu = function () {
      function MetisMenu(element, config) {
        _classCallCheck(this, MetisMenu);

        this._element = element;
        this._config = this._getConfig(config);
        this._transitioning = null;

        this.init();
      }

      MetisMenu.prototype.init = function init() {
        var self = this;
        $(this._element).find(this._config.parentTrigger + '.' + this._config.activeClass).has(this._config.subMenu).children(this._config.subMenu).attr('aria-expanded', true).addClass(this._config.collapseClass + ' ' + this._config.collapseInClass);

        $(this._element).find(this._config.parentTrigger).not('.' + this._config.activeClass).has(this._config.subMenu).children(this._config.subMenu).attr('aria-expanded', false).addClass(this._config.collapseClass);

        $(this._element).find(this._config.parentTrigger).has(this._config.subMenu).children(this._config.triggerElement).on(Event.CLICK_DATA_API, function (e) {
          var _this = $(this);
          var _parent = _this.parent(self._config.parentTrigger);
          var _siblings = _parent.siblings(self._config.parentTrigger).children(self._config.triggerElement);
          var _list = _parent.children(self._config.subMenu);
          if (self._config.preventDefault) {
            e.preventDefault();
          }
          if (_this.attr('aria-disabled') === 'true') {
            return;
          }
          if (_parent.hasClass(self._config.activeClass)) {
            _this.attr('aria-expanded', false);
            self._hide(_list);
          } else {
            self._show(_list);
            _this.attr('aria-expanded', true);
            if (self._config.toggle) {
              _siblings.attr('aria-expanded', false);
            }
          }

          if (self._config.onTransitionStart) {
            self._config.onTransitionStart(e);
          }
        });
      };

      MetisMenu.prototype._show = function _show(element) {
        if (this._transitioning || $(element).hasClass(this._config.collapsingClass)) {
          return;
        }
        var _this = this;
        var _el = $(element);

        var startEvent = $.Event(Event.SHOW);
        _el.trigger(startEvent);

        if (startEvent.isDefaultPrevented()) {
          return;
        }

        _el.parent(this._config.parentTrigger).addClass(this._config.activeClass);

        if (this._config.toggle) {
          this._hide(_el.parent(this._config.parentTrigger).siblings().children(this._config.subMenu + '.' + this._config.collapseInClass).attr('aria-expanded', false));
        }

        _el.removeClass(this._config.collapseClass).addClass(this._config.collapsingClass).height(0);

        this.setTransitioning(true);

        var complete = function complete() {

          _el.removeClass(_this._config.collapsingClass).addClass(_this._config.collapseClass + ' ' + _this._config.collapseInClass).height('').attr('aria-expanded', true);

          _this.setTransitioning(false);

          _el.trigger(Event.SHOWN);
        };

        if (!Util.supportsTransitionEnd()) {
          complete();
          return;
        }

        _el.height(_el[0].scrollHeight).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
      };

      MetisMenu.prototype._hide = function _hide(element) {

        if (this._transitioning || !$(element).hasClass(this._config.collapseInClass)) {
          return;
        }
        var _this = this;
        var _el = $(element);

        var startEvent = $.Event(Event.HIDE);
        _el.trigger(startEvent);

        if (startEvent.isDefaultPrevented()) {
          return;
        }

        _el.parent(this._config.parentTrigger).removeClass(this._config.activeClass);
        _el.height(_el.height())[0].offsetHeight;

        _el.addClass(this._config.collapsingClass).removeClass(this._config.collapseClass).removeClass(this._config.collapseInClass);

        this.setTransitioning(true);

        var complete = function complete() {
          if (_this._transitioning && _this._config.onTransitionEnd) {
            _this._config.onTransitionEnd();
          }

          _this.setTransitioning(false);
          _el.trigger(Event.HIDDEN);

          _el.removeClass(_this._config.collapsingClass).addClass(_this._config.collapseClass).attr('aria-expanded', false);
        };

        if (!Util.supportsTransitionEnd()) {
          complete();
          return;
        }

        _el.height() == 0 || _el.css('display') == 'none' ? complete() : _el.height(0).one(Util.TRANSITION_END, complete).emulateTransitionEnd(TRANSITION_DURATION);
      };

      MetisMenu.prototype.setTransitioning = function setTransitioning(isTransitioning) {
        this._transitioning = isTransitioning;
      };

      MetisMenu.prototype.dispose = function dispose() {
        $.removeData(this._element, DATA_KEY);

        $(this._element).find(this._config.parentTrigger).has(this._config.subMenu).children(this._config.triggerElement).off('click');

        this._transitioning = null;
        this._config = null;
        this._element = null;
      };

      MetisMenu.prototype._getConfig = function _getConfig(config) {
        config = $.extend({}, Default, config);
        return config;
      };

      MetisMenu._jQueryInterface = function _jQueryInterface(config) {
        return this.each(function () {
          var $this = $(this);
          var data = $this.data(DATA_KEY);
          var _config = $.extend({}, Default, $this.data(), (typeof config === 'undefined' ? 'undefined' : _typeof(config)) === 'object' && config);

          if (!data && /dispose/.test(config)) {
            this.dispose();
          }

          if (!data) {
            data = new MetisMenu(this, _config);
            $this.data(DATA_KEY, data);
          }

          if (typeof config === 'string') {
            if (data[config] === undefined) {
              throw new Error('No method named "' + config + '"');
            }
            data[config]();
          }
        });
      };

      return MetisMenu;
    }();

    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */

    $.fn[NAME] = MetisMenu._jQueryInterface;
    $.fn[NAME].Constructor = MetisMenu;
    $.fn[NAME].noConflict = function () {
      $.fn[NAME] = JQUERY_NO_CONFLICT;
      return MetisMenu._jQueryInterface;
    };
    return MetisMenu;
  }(jQuery);
});
!function(t){"use strict";function e(t){return null!==t&&t===t.window}function n(t){return e(t)?t:9===t.nodeType&&t.defaultView}function a(t){var e,a,i={top:0,left:0},o=t&&t.ownerDocument;return e=o.documentElement,"undefined"!=typeof t.getBoundingClientRect&&(i=t.getBoundingClientRect()),a=n(o),{top:i.top+a.pageYOffset-e.clientTop,left:i.left+a.pageXOffset-e.clientLeft}}function i(t){var e="";for(var n in t)t.hasOwnProperty(n)&&(e+=n+":"+t[n]+";");return e}function o(t){if(d.allowEvent(t)===!1)return null;for(var e=null,n=t.target||t.srcElement;null!==n.parentElement;){if(!(n instanceof SVGElement||-1===n.className.indexOf("waves-effect"))){e=n;break}if(n.classList.contains("waves-effect")){e=n;break}n=n.parentElement}return e}function r(e){var n=o(e);null!==n&&(c.show(e,n),"ontouchstart"in t&&(n.addEventListener("touchend",c.hide,!1),n.addEventListener("touchcancel",c.hide,!1)),n.addEventListener("mouseup",c.hide,!1),n.addEventListener("mouseleave",c.hide,!1))}var s=s||{},u=document.querySelectorAll.bind(document),c={duration:750,show:function(t,e){if(2===t.button)return!1;var n=e||this,o=document.createElement("div");o.className="waves-ripple",n.appendChild(o);var r=a(n),s=t.pageY-r.top,u=t.pageX-r.left,d="scale("+n.clientWidth/100*10+")";"touches"in t&&(s=t.touches[0].pageY-r.top,u=t.touches[0].pageX-r.left),o.setAttribute("data-hold",Date.now()),o.setAttribute("data-scale",d),o.setAttribute("data-x",u),o.setAttribute("data-y",s);var l={top:s+"px",left:u+"px"};o.className=o.className+" waves-notransition",o.setAttribute("style",i(l)),o.className=o.className.replace("waves-notransition",""),l["-webkit-transform"]=d,l["-moz-transform"]=d,l["-ms-transform"]=d,l["-o-transform"]=d,l.transform=d,l.opacity="1",l["-webkit-transition-duration"]=c.duration+"ms",l["-moz-transition-duration"]=c.duration+"ms",l["-o-transition-duration"]=c.duration+"ms",l["transition-duration"]=c.duration+"ms",l["-webkit-transition-timing-function"]="cubic-bezier(0.250, 0.460, 0.450, 0.940)",l["-moz-transition-timing-function"]="cubic-bezier(0.250, 0.460, 0.450, 0.940)",l["-o-transition-timing-function"]="cubic-bezier(0.250, 0.460, 0.450, 0.940)",l["transition-timing-function"]="cubic-bezier(0.250, 0.460, 0.450, 0.940)",o.setAttribute("style",i(l))},hide:function(t){d.touchup(t);var e=this,n=(1.4*e.clientWidth,null),a=e.getElementsByClassName("waves-ripple");if(!(a.length>0))return!1;n=a[a.length-1];var o=n.getAttribute("data-x"),r=n.getAttribute("data-y"),s=n.getAttribute("data-scale"),u=Date.now()-Number(n.getAttribute("data-hold")),l=350-u;0>l&&(l=0),setTimeout(function(){var t={top:r+"px",left:o+"px",opacity:"0","-webkit-transition-duration":c.duration+"ms","-moz-transition-duration":c.duration+"ms","-o-transition-duration":c.duration+"ms","transition-duration":c.duration+"ms","-webkit-transform":s,"-moz-transform":s,"-ms-transform":s,"-o-transform":s,transform:s};n.setAttribute("style",i(t)),setTimeout(function(){try{e.removeChild(n)}catch(t){return!1}},c.duration)},l)},wrapInput:function(t){for(var e=0;e<t.length;e++){var n=t[e];if("input"===n.tagName.toLowerCase()){var a=n.parentNode;if("i"===a.tagName.toLowerCase()&&-1!==a.className.indexOf("waves-effect"))continue;var i=document.createElement("i");i.className=n.className+" waves-input-wrapper";var o=n.getAttribute("style");o||(o=""),i.setAttribute("style",o),n.className="waves-button-input",n.removeAttribute("style"),a.replaceChild(i,n),i.appendChild(n)}}}},d={touches:0,allowEvent:function(t){var e=!0;return"touchstart"===t.type?d.touches+=1:"touchend"===t.type||"touchcancel"===t.type?setTimeout(function(){d.touches>0&&(d.touches-=1)},500):"mousedown"===t.type&&d.touches>0&&(e=!1),e},touchup:function(t){d.allowEvent(t)}};s.displayEffect=function(e){e=e||{},"duration"in e&&(c.duration=e.duration),c.wrapInput(u(".waves-effect")),"ontouchstart"in t&&document.body.addEventListener("touchstart",r,!1),document.body.addEventListener("mousedown",r,!1)},s.attach=function(e){"input"===e.tagName.toLowerCase()&&(c.wrapInput([e]),e=e.parentElement),"ontouchstart"in t&&e.addEventListener("touchstart",r,!1),e.addEventListener("mousedown",r,!1)},t.Waves=s,document.addEventListener("DOMContentLoaded",function(){s.displayEffect()},!1)}(window);
!function(t,e){"function"==typeof define&&define.amd?define(e):"object"==typeof exports?module.exports=e(require,exports,module):t.Tether=e()}(this,function(t,e,o){"use strict";function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function i(t){var e=t.getBoundingClientRect(),o={};for(var n in e)o[n]=e[n];if(t.ownerDocument!==document){var r=t.ownerDocument.defaultView.frameElement;if(r){var s=i(r);o.top+=s.top,o.bottom+=s.top,o.left+=s.left,o.right+=s.left}}return o}function r(t){var e=getComputedStyle(t)||{},o=e.position,n=[];if("fixed"===o)return[t];for(var i=t;(i=i.parentNode)&&i&&1===i.nodeType;){var r=void 0;try{r=getComputedStyle(i)}catch(s){}if("undefined"==typeof r||null===r)return n.push(i),n;var a=r,f=a.overflow,l=a.overflowX,h=a.overflowY;/(auto|scroll)/.test(f+h+l)&&("absolute"!==o||["relative","absolute","fixed"].indexOf(r.position)>=0)&&n.push(i)}return n.push(t.ownerDocument.body),t.ownerDocument!==document&&n.push(t.ownerDocument.defaultView),n}function s(){A&&document.body.removeChild(A),A=null}function a(t){var e=void 0;t===document?(e=document,t=document.documentElement):e=t.ownerDocument;var o=e.documentElement,n=i(t),r=P();return n.top-=r.top,n.left-=r.left,"undefined"==typeof n.width&&(n.width=document.body.scrollWidth-n.left-n.right),"undefined"==typeof n.height&&(n.height=document.body.scrollHeight-n.top-n.bottom),n.top=n.top-o.clientTop,n.left=n.left-o.clientLeft,n.right=e.body.clientWidth-n.width-n.left,n.bottom=e.body.clientHeight-n.height-n.top,n}function f(t){return t.offsetParent||document.documentElement}function l(){if(M)return M;var t=document.createElement("div");t.style.width="100%",t.style.height="200px";var e=document.createElement("div");h(e.style,{position:"absolute",top:0,left:0,pointerEvents:"none",visibility:"hidden",width:"200px",height:"150px",overflow:"hidden"}),e.appendChild(t),document.body.appendChild(e);var o=t.offsetWidth;e.style.overflow="scroll";var n=t.offsetWidth;o===n&&(n=e.clientWidth),document.body.removeChild(e);var i=o-n;return M={width:i,height:i}}function h(){var t=arguments.length<=0||void 0===arguments[0]?{}:arguments[0],e=[];return Array.prototype.push.apply(e,arguments),e.slice(1).forEach(function(e){if(e)for(var o in e)({}).hasOwnProperty.call(e,o)&&(t[o]=e[o])}),t}function d(t,e){if("undefined"!=typeof t.classList)e.split(" ").forEach(function(e){e.trim()&&t.classList.remove(e)});else{var o=new RegExp("(^| )"+e.split(" ").join("|")+"( |$)","gi"),n=c(t).replace(o," ");g(t,n)}}function u(t,e){if("undefined"!=typeof t.classList)e.split(" ").forEach(function(e){e.trim()&&t.classList.add(e)});else{d(t,e);var o=c(t)+(" "+e);g(t,o)}}function p(t,e){if("undefined"!=typeof t.classList)return t.classList.contains(e);var o=c(t);return new RegExp("(^| )"+e+"( |$)","gi").test(o)}function c(t){return t.className instanceof t.ownerDocument.defaultView.SVGAnimatedString?t.className.baseVal:t.className}function g(t,e){t.setAttribute("class",e)}function m(t,e,o){o.forEach(function(o){-1===e.indexOf(o)&&p(t,o)&&d(t,o)}),e.forEach(function(e){p(t,e)||u(t,e)})}function n(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function v(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function, not "+typeof e);t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,enumerable:!1,writable:!0,configurable:!0}}),e&&(Object.setPrototypeOf?Object.setPrototypeOf(t,e):t.__proto__=e)}function y(t,e){var o=arguments.length<=2||void 0===arguments[2]?1:arguments[2];return t+o>=e&&e>=t-o}function b(){return"undefined"!=typeof performance&&"undefined"!=typeof performance.now?performance.now():+new Date}function w(){for(var t={top:0,left:0},e=arguments.length,o=Array(e),n=0;e>n;n++)o[n]=arguments[n];return o.forEach(function(e){var o=e.top,n=e.left;"string"==typeof o&&(o=parseFloat(o,10)),"string"==typeof n&&(n=parseFloat(n,10)),t.top+=o,t.left+=n}),t}function C(t,e){return"string"==typeof t.left&&-1!==t.left.indexOf("%")&&(t.left=parseFloat(t.left,10)/100*e.width),"string"==typeof t.top&&-1!==t.top.indexOf("%")&&(t.top=parseFloat(t.top,10)/100*e.height),t}function O(t,e){return"scrollParent"===e?e=t.scrollParents[0]:"window"===e&&(e=[pageXOffset,pageYOffset,innerWidth+pageXOffset,innerHeight+pageYOffset]),e===document&&(e=e.documentElement),"undefined"!=typeof e.nodeType&&!function(){var t=e,o=a(e),n=o,i=getComputedStyle(e);if(e=[n.left,n.top,o.width+n.left,o.height+n.top],t.ownerDocument!==document){var r=t.ownerDocument.defaultView;e[0]+=r.pageXOffset,e[1]+=r.pageYOffset,e[2]+=r.pageXOffset,e[3]+=r.pageYOffset}G.forEach(function(t,o){t=t[0].toUpperCase()+t.substr(1),"Top"===t||"Left"===t?e[o]+=parseFloat(i["border"+t+"Width"]):e[o]-=parseFloat(i["border"+t+"Width"])})}(),e}var E=function(){function t(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}return function(e,o,n){return o&&t(e.prototype,o),n&&t(e,n),e}}(),x=void 0;"undefined"==typeof x&&(x={modules:[]});var A=null,T=function(){var t=0;return function(){return++t}}(),S={},P=function(){var t=A;t||(t=document.createElement("div"),t.setAttribute("data-tether-id",T()),h(t.style,{top:0,left:0,position:"absolute"}),document.body.appendChild(t),A=t);var e=t.getAttribute("data-tether-id");return"undefined"==typeof S[e]&&(S[e]=i(t),k(function(){delete S[e]})),S[e]},M=null,W=[],k=function(t){W.push(t)},_=function(){for(var t=void 0;t=W.pop();)t()},B=function(){function t(){n(this,t)}return E(t,[{key:"on",value:function(t,e,o){var n=arguments.length<=3||void 0===arguments[3]?!1:arguments[3];"undefined"==typeof this.bindings&&(this.bindings={}),"undefined"==typeof this.bindings[t]&&(this.bindings[t]=[]),this.bindings[t].push({handler:e,ctx:o,once:n})}},{key:"once",value:function(t,e,o){this.on(t,e,o,!0)}},{key:"off",value:function(t,e){if("undefined"!=typeof this.bindings&&"undefined"!=typeof this.bindings[t])if("undefined"==typeof e)delete this.bindings[t];else for(var o=0;o<this.bindings[t].length;)this.bindings[t][o].handler===e?this.bindings[t].splice(o,1):++o}},{key:"trigger",value:function(t){if("undefined"!=typeof this.bindings&&this.bindings[t]){for(var e=0,o=arguments.length,n=Array(o>1?o-1:0),i=1;o>i;i++)n[i-1]=arguments[i];for(;e<this.bindings[t].length;){var r=this.bindings[t][e],s=r.handler,a=r.ctx,f=r.once,l=a;"undefined"==typeof l&&(l=this),s.apply(l,n),f?this.bindings[t].splice(e,1):++e}}}}]),t}();x.Utils={getActualBoundingClientRect:i,getScrollParents:r,getBounds:a,getOffsetParent:f,extend:h,addClass:u,removeClass:d,hasClass:p,updateClasses:m,defer:k,flush:_,uniqueId:T,Evented:B,getScrollBarSize:l,removeUtilElements:s};var z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),E=function(){function t(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}return function(e,o,n){return o&&t(e.prototype,o),n&&t(e,n),e}}(),j=function(t,e,o){for(var n=!0;n;){var i=t,r=e,s=o;n=!1,null===i&&(i=Function.prototype);var a=Object.getOwnPropertyDescriptor(i,r);if(void 0!==a){if("value"in a)return a.value;var f=a.get;if(void 0===f)return;return f.call(s)}var l=Object.getPrototypeOf(i);if(null===l)return;t=l,e=r,o=s,n=!0,a=l=void 0}};if("undefined"==typeof x)throw new Error("You must include the utils.js file before tether.js");var Y=x.Utils,r=Y.getScrollParents,a=Y.getBounds,f=Y.getOffsetParent,h=Y.extend,u=Y.addClass,d=Y.removeClass,m=Y.updateClasses,k=Y.defer,_=Y.flush,l=Y.getScrollBarSize,s=Y.removeUtilElements,L=function(){if("undefined"==typeof document)return"";for(var t=document.createElement("div"),e=["transform","WebkitTransform","OTransform","MozTransform","msTransform"],o=0;o<e.length;++o){var n=e[o];if(void 0!==t.style[n])return n}}(),D=[],X=function(){D.forEach(function(t){t.position(!1)}),_()};!function(){var t=null,e=null,o=null,n=function i(){return"undefined"!=typeof e&&e>16?(e=Math.min(e-16,250),void(o=setTimeout(i,250))):void("undefined"!=typeof t&&b()-t<10||(null!=o&&(clearTimeout(o),o=null),t=b(),X(),e=b()-t))};"undefined"!=typeof window&&"undefined"!=typeof window.addEventListener&&["resize","scroll","touchmove"].forEach(function(t){window.addEventListener(t,n)})}();var F={center:"center",left:"right",right:"left"},H={middle:"middle",top:"bottom",bottom:"top"},N={top:0,left:0,middle:"50%",center:"50%",bottom:"100%",right:"100%"},U=function(t,e){var o=t.left,n=t.top;return"auto"===o&&(o=F[e.left]),"auto"===n&&(n=H[e.top]),{left:o,top:n}},V=function(t){var e=t.left,o=t.top;return"undefined"!=typeof N[t.left]&&(e=N[t.left]),"undefined"!=typeof N[t.top]&&(o=N[t.top]),{left:e,top:o}},R=function(t){var e=t.split(" "),o=z(e,2),n=o[0],i=o[1];return{top:n,left:i}},q=R,I=function(t){function e(t){var o=this;n(this,e),j(Object.getPrototypeOf(e.prototype),"constructor",this).call(this),this.position=this.position.bind(this),D.push(this),this.history=[],this.setOptions(t,!1),x.modules.forEach(function(t){"undefined"!=typeof t.initialize&&t.initialize.call(o)}),this.position()}return v(e,t),E(e,[{key:"getClass",value:function(){var t=arguments.length<=0||void 0===arguments[0]?"":arguments[0],e=this.options.classes;return"undefined"!=typeof e&&e[t]?this.options.classes[t]:this.options.classPrefix?this.options.classPrefix+"-"+t:t}},{key:"setOptions",value:function(t){var e=this,o=arguments.length<=1||void 0===arguments[1]?!0:arguments[1],n={offset:"0 0",targetOffset:"0 0",targetAttachment:"auto auto",classPrefix:"tether"};this.options=h(n,t);var i=this.options,s=i.element,a=i.target,f=i.targetModifier;if(this.element=s,this.target=a,this.targetModifier=f,"viewport"===this.target?(this.target=document.body,this.targetModifier="visible"):"scroll-handle"===this.target&&(this.target=document.body,this.targetModifier="scroll-handle"),["element","target"].forEach(function(t){if("undefined"==typeof e[t])throw new Error("Tether Error: Both element and target must be defined");"undefined"!=typeof e[t].jquery?e[t]=e[t][0]:"string"==typeof e[t]&&(e[t]=document.querySelector(e[t]))}),u(this.element,this.getClass("element")),this.options.addTargetClasses!==!1&&u(this.target,this.getClass("target")),!this.options.attachment)throw new Error("Tether Error: You must provide an attachment");this.targetAttachment=q(this.options.targetAttachment),this.attachment=q(this.options.attachment),this.offset=R(this.options.offset),this.targetOffset=R(this.options.targetOffset),"undefined"!=typeof this.scrollParents&&this.disable(),"scroll-handle"===this.targetModifier?this.scrollParents=[this.target]:this.scrollParents=r(this.target),this.options.enabled!==!1&&this.enable(o)}},{key:"getTargetBounds",value:function(){if("undefined"==typeof this.targetModifier)return a(this.target);if("visible"===this.targetModifier){if(this.target===document.body)return{top:pageYOffset,left:pageXOffset,height:innerHeight,width:innerWidth};var t=a(this.target),e={height:t.height,width:t.width,top:t.top,left:t.left};return e.height=Math.min(e.height,t.height-(pageYOffset-t.top)),e.height=Math.min(e.height,t.height-(t.top+t.height-(pageYOffset+innerHeight))),e.height=Math.min(innerHeight,e.height),e.height-=2,e.width=Math.min(e.width,t.width-(pageXOffset-t.left)),e.width=Math.min(e.width,t.width-(t.left+t.width-(pageXOffset+innerWidth))),e.width=Math.min(innerWidth,e.width),e.width-=2,e.top<pageYOffset&&(e.top=pageYOffset),e.left<pageXOffset&&(e.left=pageXOffset),e}if("scroll-handle"===this.targetModifier){var t=void 0,o=this.target;o===document.body?(o=document.documentElement,t={left:pageXOffset,top:pageYOffset,height:innerHeight,width:innerWidth}):t=a(o);var n=getComputedStyle(o),i=o.scrollWidth>o.clientWidth||[n.overflow,n.overflowX].indexOf("scroll")>=0||this.target!==document.body,r=0;i&&(r=15);var s=t.height-parseFloat(n.borderTopWidth)-parseFloat(n.borderBottomWidth)-r,e={width:15,height:.975*s*(s/o.scrollHeight),left:t.left+t.width-parseFloat(n.borderLeftWidth)-15},f=0;408>s&&this.target===document.body&&(f=-11e-5*Math.pow(s,2)-.00727*s+22.58),this.target!==document.body&&(e.height=Math.max(e.height,24));var l=this.target.scrollTop/(o.scrollHeight-s);return e.top=l*(s-e.height-f)+t.top+parseFloat(n.borderTopWidth),this.target===document.body&&(e.height=Math.max(e.height,24)),e}}},{key:"clearCache",value:function(){this._cache={}}},{key:"cache",value:function(t,e){return"undefined"==typeof this._cache&&(this._cache={}),"undefined"==typeof this._cache[t]&&(this._cache[t]=e.call(this)),this._cache[t]}},{key:"enable",value:function(){var t=this,e=arguments.length<=0||void 0===arguments[0]?!0:arguments[0];this.options.addTargetClasses!==!1&&u(this.target,this.getClass("enabled")),u(this.element,this.getClass("enabled")),this.enabled=!0,this.scrollParents.forEach(function(e){e!==t.target.ownerDocument&&e.addEventListener("scroll",t.position)}),e&&this.position()}},{key:"disable",value:function(){var t=this;d(this.target,this.getClass("enabled")),d(this.element,this.getClass("enabled")),this.enabled=!1,"undefined"!=typeof this.scrollParents&&this.scrollParents.forEach(function(e){e.removeEventListener("scroll",t.position)})}},{key:"destroy",value:function(){var t=this;this.disable(),D.forEach(function(e,o){e===t&&D.splice(o,1)}),0===D.length&&s()}},{key:"updateAttachClasses",value:function(t,e){var o=this;t=t||this.attachment,e=e||this.targetAttachment;var n=["left","top","bottom","right","middle","center"];"undefined"!=typeof this._addAttachClasses&&this._addAttachClasses.length&&this._addAttachClasses.splice(0,this._addAttachClasses.length),"undefined"==typeof this._addAttachClasses&&(this._addAttachClasses=[]);var i=this._addAttachClasses;t.top&&i.push(this.getClass("element-attached")+"-"+t.top),t.left&&i.push(this.getClass("element-attached")+"-"+t.left),e.top&&i.push(this.getClass("target-attached")+"-"+e.top),e.left&&i.push(this.getClass("target-attached")+"-"+e.left);var r=[];n.forEach(function(t){r.push(o.getClass("element-attached")+"-"+t),r.push(o.getClass("target-attached")+"-"+t)}),k(function(){"undefined"!=typeof o._addAttachClasses&&(m(o.element,o._addAttachClasses,r),o.options.addTargetClasses!==!1&&m(o.target,o._addAttachClasses,r),delete o._addAttachClasses)})}},{key:"position",value:function(){var t=this,e=arguments.length<=0||void 0===arguments[0]?!0:arguments[0];if(this.enabled){this.clearCache();var o=U(this.targetAttachment,this.attachment);this.updateAttachClasses(this.attachment,o);var n=this.cache("element-bounds",function(){return a(t.element)}),i=n.width,r=n.height;if(0===i&&0===r&&"undefined"!=typeof this.lastSize){var s=this.lastSize;i=s.width,r=s.height}else this.lastSize={width:i,height:r};var h=this.cache("target-bounds",function(){return t.getTargetBounds()}),d=h,u=C(V(this.attachment),{width:i,height:r}),p=C(V(o),d),c=C(this.offset,{width:i,height:r}),g=C(this.targetOffset,d);u=w(u,c),p=w(p,g);for(var m=h.left+p.left-u.left,v=h.top+p.top-u.top,y=0;y<x.modules.length;++y){var b=x.modules[y],O=b.position.call(this,{left:m,top:v,targetAttachment:o,targetPos:h,elementPos:n,offset:u,targetOffset:p,manualOffset:c,manualTargetOffset:g,scrollbarSize:S,attachment:this.attachment});if(O===!1)return!1;"undefined"!=typeof O&&"object"==typeof O&&(v=O.top,m=O.left)}var E={page:{top:v,left:m},viewport:{top:v-pageYOffset,bottom:pageYOffset-v-r+innerHeight,left:m-pageXOffset,right:pageXOffset-m-i+innerWidth}},A=this.target.ownerDocument,T=A.defaultView,S=void 0;return T.innerHeight>A.documentElement.clientHeight&&(S=this.cache("scrollbar-size",l),E.viewport.bottom-=S.height),T.innerWidth>A.documentElement.clientWidth&&(S=this.cache("scrollbar-size",l),E.viewport.right-=S.width),(-1===["","static"].indexOf(A.body.style.position)||-1===["","static"].indexOf(A.body.parentElement.style.position))&&(E.page.bottom=A.body.scrollHeight-v-r,E.page.right=A.body.scrollWidth-m-i),"undefined"!=typeof this.options.optimizations&&this.options.optimizations.moveElement!==!1&&"undefined"==typeof this.targetModifier&&!function(){var e=t.cache("target-offsetparent",function(){return f(t.target)}),o=t.cache("target-offsetparent-bounds",function(){return a(e)}),n=getComputedStyle(e),i=o,r={};if(["Top","Left","Bottom","Right"].forEach(function(t){r[t.toLowerCase()]=parseFloat(n["border"+t+"Width"])}),o.right=A.body.scrollWidth-o.left-i.width+r.right,o.bottom=A.body.scrollHeight-o.top-i.height+r.bottom,E.page.top>=o.top+r.top&&E.page.bottom>=o.bottom&&E.page.left>=o.left+r.left&&E.page.right>=o.right){var s=e.scrollTop,l=e.scrollLeft;E.offset={top:E.page.top-o.top+s-r.top,left:E.page.left-o.left+l-r.left}}}(),this.move(E),this.history.unshift(E),this.history.length>3&&this.history.pop(),e&&_(),!0}}},{key:"move",value:function(t){var e=this;if("undefined"!=typeof this.element.parentNode){var o={};for(var n in t){o[n]={};for(var i in t[n]){for(var r=!1,s=0;s<this.history.length;++s){var a=this.history[s];if("undefined"!=typeof a[n]&&!y(a[n][i],t[n][i])){r=!0;break}}r||(o[n][i]=!0)}}var l={top:"",left:"",right:"",bottom:""},d=function(t,o){var n="undefined"!=typeof e.options.optimizations,i=n?e.options.optimizations.gpu:null;if(i!==!1){var r=void 0,s=void 0;if(t.top?(l.top=0,r=o.top):(l.bottom=0,r=-o.bottom),t.left?(l.left=0,s=o.left):(l.right=0,s=-o.right),window.matchMedia){var a=window.matchMedia("only screen and (min-resolution: 1.3dppx)").matches||window.matchMedia("only screen and (-webkit-min-device-pixel-ratio: 1.3)").matches;a||(s=Math.round(s),r=Math.round(r))}l[L]="translateX("+s+"px) translateY("+r+"px)","msTransform"!==L&&(l[L]+=" translateZ(0)")}else t.top?l.top=o.top+"px":l.bottom=o.bottom+"px",t.left?l.left=o.left+"px":l.right=o.right+"px"},u=!1;if((o.page.top||o.page.bottom)&&(o.page.left||o.page.right)?(l.position="absolute",d(o.page,t.page)):(o.viewport.top||o.viewport.bottom)&&(o.viewport.left||o.viewport.right)?(l.position="fixed",d(o.viewport,t.viewport)):"undefined"!=typeof o.offset&&o.offset.top&&o.offset.left?!function(){l.position="absolute";var n=e.cache("target-offsetparent",function(){return f(e.target)});f(e.element)!==n&&k(function(){e.element.parentNode.removeChild(e.element),n.appendChild(e.element)}),d(o.offset,t.offset),u=!0}():(l.position="absolute",d({top:!0,left:!0},t.page)),!u){for(var p=!0,c=this.element.parentNode;c&&1===c.nodeType&&"BODY"!==c.tagName;){if("static"!==getComputedStyle(c).position){p=!1;break}c=c.parentNode}p||(this.element.parentNode.removeChild(this.element),this.element.ownerDocument.body.appendChild(this.element))}var g={},m=!1;for(var i in l){var v=l[i],b=this.element.style[i];b!==v&&(m=!0,g[i]=v)}m&&k(function(){h(e.element.style,g),e.trigger("repositioned")})}}}]),e}(B);I.modules=[],x.position=X;var $=h(I,x),z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}(),Y=x.Utils,a=Y.getBounds,h=Y.extend,m=Y.updateClasses,k=Y.defer,G=["left","top","right","bottom"];x.modules.push({position:function(t){var e=this,o=t.top,n=t.left,i=t.targetAttachment;if(!this.options.constraints)return!0;var r=this.cache("element-bounds",function(){return a(e.element)}),s=r.height,f=r.width;if(0===f&&0===s&&"undefined"!=typeof this.lastSize){var l=this.lastSize;f=l.width,s=l.height}var d=this.cache("target-bounds",function(){return e.getTargetBounds()}),u=d.height,p=d.width,c=[this.getClass("pinned"),this.getClass("out-of-bounds")];this.options.constraints.forEach(function(t){var e=t.outOfBoundsClass,o=t.pinnedClass;e&&c.push(e),o&&c.push(o)}),c.forEach(function(t){["left","top","right","bottom"].forEach(function(e){c.push(t+"-"+e)})});var g=[],v=h({},i),y=h({},this.attachment);return this.options.constraints.forEach(function(t){var r=t.to,a=t.attachment,l=t.pin;"undefined"==typeof a&&(a="");var h=void 0,d=void 0;if(a.indexOf(" ")>=0){var c=a.split(" "),m=z(c,2);d=m[0],h=m[1]}else h=d=a;var b=O(e,r);("target"===d||"both"===d)&&(o<b[1]&&"top"===v.top&&(o+=u,v.top="bottom"),o+s>b[3]&&"bottom"===v.top&&(o-=u,v.top="top")),"together"===d&&("top"===v.top&&("bottom"===y.top&&o<b[1]?(o+=u,v.top="bottom",o+=s,y.top="top"):"top"===y.top&&o+s>b[3]&&o-(s-u)>=b[1]&&(o-=s-u,v.top="bottom",y.top="bottom")),"bottom"===v.top&&("top"===y.top&&o+s>b[3]?(o-=u,v.top="top",o-=s,y.top="bottom"):"bottom"===y.top&&o<b[1]&&o+(2*s-u)<=b[3]&&(o+=s-u,v.top="top",y.top="top")),"middle"===v.top&&(o+s>b[3]&&"top"===y.top?(o-=s,y.top="bottom"):o<b[1]&&"bottom"===y.top&&(o+=s,y.top="top"))),("target"===h||"both"===h)&&(n<b[0]&&"left"===v.left&&(n+=p,v.left="right"),n+f>b[2]&&"right"===v.left&&(n-=p,v.left="left")),"together"===h&&(n<b[0]&&"left"===v.left?"right"===y.left?(n+=p,v.left="right",n+=f,y.left="left"):"left"===y.left&&(n+=p,v.left="right",n-=f,y.left="right"):n+f>b[2]&&"right"===v.left?"left"===y.left?(n-=p,v.left="left",n-=f,y.left="right"):"right"===y.left&&(n-=p,v.left="left",n+=f,y.left="left"):"center"===v.left&&(n+f>b[2]&&"left"===y.left?(n-=f,y.left="right"):n<b[0]&&"right"===y.left&&(n+=f,y.left="left"))),("element"===d||"both"===d)&&(o<b[1]&&"bottom"===y.top&&(o+=s,y.top="top"),o+s>b[3]&&"top"===y.top&&(o-=s,y.top="bottom")),("element"===h||"both"===h)&&(n<b[0]&&("right"===y.left?(n+=f,y.left="left"):"center"===y.left&&(n+=f/2,y.left="left")),n+f>b[2]&&("left"===y.left?(n-=f,y.left="right"):"center"===y.left&&(n-=f/2,y.left="right"))),"string"==typeof l?l=l.split(",").map(function(t){return t.trim()}):l===!0&&(l=["top","left","right","bottom"]),l=l||[];var w=[],C=[];o<b[1]&&(l.indexOf("top")>=0?(o=b[1],w.push("top")):C.push("top")),o+s>b[3]&&(l.indexOf("bottom")>=0?(o=b[3]-s,w.push("bottom")):C.push("bottom")),n<b[0]&&(l.indexOf("left")>=0?(n=b[0],w.push("left")):C.push("left")),n+f>b[2]&&(l.indexOf("right")>=0?(n=b[2]-f,w.push("right")):C.push("right")),w.length&&!function(){var t=void 0;t="undefined"!=typeof e.options.pinnedClass?e.options.pinnedClass:e.getClass("pinned"),g.push(t),w.forEach(function(e){g.push(t+"-"+e)})}(),C.length&&!function(){var t=void 0;t="undefined"!=typeof e.options.outOfBoundsClass?e.options.outOfBoundsClass:e.getClass("out-of-bounds"),g.push(t),C.forEach(function(e){g.push(t+"-"+e)})}(),(w.indexOf("left")>=0||w.indexOf("right")>=0)&&(y.left=v.left=!1),(w.indexOf("top")>=0||w.indexOf("bottom")>=0)&&(y.top=v.top=!1),(v.top!==i.top||v.left!==i.left||y.top!==e.attachment.top||y.left!==e.attachment.left)&&(e.updateAttachClasses(y,v),e.trigger("update",{attachment:y,targetAttachment:v}))}),k(function(){e.options.addTargetClasses!==!1&&m(e.target,g,c),m(e.element,g,c)}),{top:o,left:n}}});var Y=x.Utils,a=Y.getBounds,m=Y.updateClasses,k=Y.defer;x.modules.push({position:function(t){var e=this,o=t.top,n=t.left,i=this.cache("element-bounds",function(){return a(e.element)}),r=i.height,s=i.width,f=this.getTargetBounds(),l=o+r,h=n+s,d=[];o<=f.bottom&&l>=f.top&&["left","right"].forEach(function(t){var e=f[t];(e===n||e===h)&&d.push(t)}),n<=f.right&&h>=f.left&&["top","bottom"].forEach(function(t){var e=f[t];(e===o||e===l)&&d.push(t)});var u=[],p=[],c=["left","top","right","bottom"];return u.push(this.getClass("abutted")),c.forEach(function(t){u.push(e.getClass("abutted")+"-"+t)}),d.length&&p.push(this.getClass("abutted")),d.forEach(function(t){p.push(e.getClass("abutted")+"-"+t)}),k(function(){e.options.addTargetClasses!==!1&&m(e.target,p,u),m(e.element,p,u)}),!0}});var z=function(){function t(t,e){var o=[],n=!0,i=!1,r=void 0;try{for(var s,a=t[Symbol.iterator]();!(n=(s=a.next()).done)&&(o.push(s.value),!e||o.length!==e);n=!0);}catch(f){i=!0,r=f}finally{try{!n&&a["return"]&&a["return"]()}finally{if(i)throw r}}return o}return function(e,o){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return t(e,o);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}();return x.modules.push({position:function(t){var e=t.top,o=t.left;if(this.options.shift){var n=this.options.shift;"function"==typeof this.options.shift&&(n=this.options.shift.call(this,{top:e,left:o}));var i=void 0,r=void 0;if("string"==typeof n){n=n.split(" "),n[1]=n[1]||n[0];var s=n,a=z(s,2);i=a[0],r=a[1],i=parseFloat(i,10),r=parseFloat(r,10)}else i=n.top,r=n.left;return e+=i,o+=r,{top:e,left:o}}}}),$});
/*!
 * Signature Pad v3.0.0-beta.3 | https://github.com/szimek/signature_pad
 * (c) 2018 Szymon Nowak | Released under the MIT license
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global.SignaturePad = factory());
}(this, (function () { 'use strict';

  var Point = (function () {
      function Point(x, y, time) {
          this.x = x;
          this.y = y;
          this.time = time || Date.now();
      }
      Point.prototype.distanceTo = function (start) {
          return Math.sqrt(Math.pow(this.x - start.x, 2) + Math.pow(this.y - start.y, 2));
      };
      Point.prototype.equals = function (other) {
          return this.x === other.x && this.y === other.y && this.time === other.time;
      };
      Point.prototype.velocityFrom = function (start) {
          return this.time !== start.time
              ? this.distanceTo(start) / (this.time - start.time)
              : 0;
      };
      return Point;
  }());

  var Bezier = (function () {
      function Bezier(startPoint, control2, control1, endPoint, startWidth, endWidth) {
          this.startPoint = startPoint;
          this.control2 = control2;
          this.control1 = control1;
          this.endPoint = endPoint;
          this.startWidth = startWidth;
          this.endWidth = endWidth;
      }
      Bezier.fromPoints = function (points, widths) {
          var c2 = this.calculateControlPoints(points[0], points[1], points[2]).c2;
          var c3 = this.calculateControlPoints(points[1], points[2], points[3]).c1;
          return new Bezier(points[1], c2, c3, points[2], widths.start, widths.end);
      };
      Bezier.calculateControlPoints = function (s1, s2, s3) {
          var dx1 = s1.x - s2.x;
          var dy1 = s1.y - s2.y;
          var dx2 = s2.x - s3.x;
          var dy2 = s2.y - s3.y;
          var m1 = { x: (s1.x + s2.x) / 2.0, y: (s1.y + s2.y) / 2.0 };
          var m2 = { x: (s2.x + s3.x) / 2.0, y: (s2.y + s3.y) / 2.0 };
          var l1 = Math.sqrt(dx1 * dx1 + dy1 * dy1);
          var l2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);
          var dxm = m1.x - m2.x;
          var dym = m1.y - m2.y;
          var k = l2 / (l1 + l2);
          var cm = { x: m2.x + dxm * k, y: m2.y + dym * k };
          var tx = s2.x - cm.x;
          var ty = s2.y - cm.y;
          return {
              c1: new Point(m1.x + tx, m1.y + ty),
              c2: new Point(m2.x + tx, m2.y + ty)
          };
      };
      Bezier.prototype.length = function () {
          var steps = 10;
          var length = 0;
          var px;
          var py;
          for (var i = 0; i <= steps; i += 1) {
              var t = i / steps;
              var cx = this.point(t, this.startPoint.x, this.control1.x, this.control2.x, this.endPoint.x);
              var cy = this.point(t, this.startPoint.y, this.control1.y, this.control2.y, this.endPoint.y);
              if (i > 0) {
                  var xdiff = cx - px;
                  var ydiff = cy - py;
                  length += Math.sqrt(xdiff * xdiff + ydiff * ydiff);
              }
              px = cx;
              py = cy;
          }
          return length;
      };
      Bezier.prototype.point = function (t, start, c1, c2, end) {
          return (start * (1.0 - t) * (1.0 - t) * (1.0 - t))
              + (3.0 * c1 * (1.0 - t) * (1.0 - t) * t)
              + (3.0 * c2 * (1.0 - t) * t * t)
              + (end * t * t * t);
      };
      return Bezier;
  }());

  function throttle(fn, wait) {
      if (wait === void 0) { wait = 250; }
      var previous = 0;
      var timeout = null;
      var result;
      var storedContext;
      var storedArgs;
      var later = function () {
          previous = Date.now();
          timeout = null;
          result = fn.apply(storedContext, storedArgs);
          if (!timeout) {
              storedContext = null;
              storedArgs = [];
          }
      };
      return function wrapper() {
          var args = [];
          for (var _i = 0; _i < arguments.length; _i++) {
              args[_i] = arguments[_i];
          }
          var now = Date.now();
          var remaining = wait - (now - previous);
          storedContext = this;
          storedArgs = args;
          if (remaining <= 0 || remaining > wait) {
              if (timeout) {
                  clearTimeout(timeout);
                  timeout = null;
              }
              previous = now;
              result = fn.apply(storedContext, storedArgs);
              if (!timeout) {
                  storedContext = null;
                  storedArgs = [];
              }
          }
          else if (!timeout) {
              timeout = window.setTimeout(later, remaining);
          }
          return result;
      };
  }

  var SignaturePad = (function () {
      function SignaturePad(canvas, options) {
          if (options === void 0) { options = {}; }
          var _this = this;
          this.canvas = canvas;
          this.options = options;
          this._handleMouseDown = function (event) {
              if (event.which === 1) {
                  _this._mouseButtonDown = true;
                  _this._strokeBegin(event);
              }
          };
          this._handleMouseMove = function (event) {
              if (_this._mouseButtonDown) {
                  _this._strokeMoveUpdate(event);
              }
          };
          this._handleMouseUp = function (event) {
              if (event.which === 1 && _this._mouseButtonDown) {
                  _this._mouseButtonDown = false;
                  _this._strokeEnd(event);
              }
          };
          this._handleTouchStart = function (event) {
              event.preventDefault();
              if (event.targetTouches.length === 1) {
                  var touch = event.changedTouches[0];
                  _this._strokeBegin(touch);
              }
          };
          this._handleTouchMove = function (event) {
              event.preventDefault();
              var touch = event.targetTouches[0];
              _this._strokeMoveUpdate(touch);
          };
          this._handleTouchEnd = function (event) {
              var wasCanvasTouched = event.target === _this.canvas;
              if (wasCanvasTouched) {
                  event.preventDefault();
                  var touch = event.changedTouches[0];
                  _this._strokeEnd(touch);
              }
          };
          this.velocityFilterWeight = options.velocityFilterWeight || 0.7;
          this.minWidth = options.minWidth || 0.5;
          this.maxWidth = options.maxWidth || 2.5;
          this.throttle = ('throttle' in options ? options.throttle : 16);
          this.minDistance = ('minDistance' in options
              ? options.minDistance
              : 5);
          if (this.throttle) {
              this._strokeMoveUpdate = throttle(SignaturePad.prototype._strokeUpdate, this.throttle);
          }
          else {
              this._strokeMoveUpdate = SignaturePad.prototype._strokeUpdate;
          }
          this.dotSize =
              options.dotSize ||
                  function dotSize() {
                      return (this.minWidth + this.maxWidth) / 2;
                  };
          this.penColor = options.penColor || 'black';
          this.backgroundColor = options.backgroundColor || 'rgba(0,0,0,0)';
          this.onBegin = options.onBegin;
          this.onEnd = options.onEnd;
          this._ctx = canvas.getContext('2d');
          this.clear();
          this.on();
      }
      SignaturePad.prototype.clear = function () {
          var ctx = this._ctx;
          var canvas = this.canvas;
          ctx.fillStyle = this.backgroundColor;
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          ctx.fillRect(0, 0, canvas.width, canvas.height);
          this._data = [];
          this._reset();
          this._isEmpty = true;
      };
      SignaturePad.prototype.fromDataURL = function (dataUrl, options, callback) {
          var _this = this;
          if (options === void 0) { options = {}; }
          var image = new Image();
          var ratio = options.ratio || window.devicePixelRatio || 1;
          var width = options.width || this.canvas.width / ratio;
          var height = options.height || this.canvas.height / ratio;
          this._reset();
          image.onload = function () {
              _this._ctx.drawImage(image, 0, 0, width, height);
              if (callback) {
                  callback();
              }
          };
          image.onerror = function (error) {
              if (callback) {
                  callback(error);
              }
          };
          image.src = dataUrl;
          this._isEmpty = false;
      };
      SignaturePad.prototype.toDataURL = function (type, encoderOptions) {
          if (type === void 0) { type = 'image/png'; }
          switch (type) {
              case 'image/svg+xml':
                  return this._toSVG();
              default:
                  return this.canvas.toDataURL(type, encoderOptions);
          }
      };
      SignaturePad.prototype.on = function () {
          this.canvas.style.touchAction = 'none';
          this.canvas.style.msTouchAction = 'none';
          if (window.PointerEvent) {
              this._handlePointerEvents();
          }
          else {
              this._handleMouseEvents();
              if ('ontouchstart' in window) {
                  this._handleTouchEvents();
              }
          }
      };
      SignaturePad.prototype.off = function () {
          this.canvas.style.touchAction = 'auto';
          this.canvas.style.msTouchAction = 'auto';
          this.canvas.removeEventListener('pointerdown', this._handleMouseDown);
          this.canvas.removeEventListener('pointermove', this._handleMouseMove);
          document.removeEventListener('pointerup', this._handleMouseUp);
          this.canvas.removeEventListener('mousedown', this._handleMouseDown);
          this.canvas.removeEventListener('mousemove', this._handleMouseMove);
          document.removeEventListener('mouseup', this._handleMouseUp);
          this.canvas.removeEventListener('touchstart', this._handleTouchStart);
          this.canvas.removeEventListener('touchmove', this._handleTouchMove);
          this.canvas.removeEventListener('touchend', this._handleTouchEnd);
      };
      SignaturePad.prototype.isEmpty = function () {
          return this._isEmpty;
      };
      SignaturePad.prototype.fromData = function (pointGroups) {
          var _this = this;
          this.clear();
          this._fromData(pointGroups, function (_a) {
              var color = _a.color, curve = _a.curve;
              return _this._drawCurve({ color: color, curve: curve });
          }, function (_a) {
              var color = _a.color, point = _a.point;
              return _this._drawDot({ color: color, point: point });
          });
          this._data = pointGroups;
      };
      SignaturePad.prototype.toData = function () {
          return this._data;
      };
      SignaturePad.prototype._strokeBegin = function (event) {
          var newPointGroup = {
              color: this.penColor,
              points: []
          };
          if (typeof this.onBegin === 'function') {
              this.onBegin(event);
          }
          this._data.push(newPointGroup);
          this._reset();
          this._strokeUpdate(event);
      };
      SignaturePad.prototype._strokeUpdate = function (event) {
          var x = event.clientX;
          var y = event.clientY;
          var point = this._createPoint(x, y);
          var lastPointGroup = this._data[this._data.length - 1];
          var lastPoints = lastPointGroup.points;
          var lastPoint = lastPoints.length > 0 && lastPoints[lastPoints.length - 1];
          var isLastPointTooClose = lastPoint
              ? point.distanceTo(lastPoint) <= this.minDistance
              : false;
          var color = lastPointGroup.color;
          if (!lastPoint || !(lastPoint && isLastPointTooClose)) {
              var curve = this._addPoint(point);
              if (!lastPoint) {
                  this._drawDot({ color: color, point: point });
              }
              else if (curve) {
                  this._drawCurve({ color: color, curve: curve });
              }
              lastPoints.push({
                  time: point.time,
                  x: point.x,
                  y: point.y
              });
          }
      };
      SignaturePad.prototype._strokeEnd = function (event) {
          this._strokeUpdate(event);
          if (typeof this.onEnd === 'function') {
              this.onEnd(event);
          }
      };
      SignaturePad.prototype._handlePointerEvents = function () {
          this._mouseButtonDown = false;
          this.canvas.addEventListener('pointerdown', this._handleMouseDown);
          this.canvas.addEventListener('pointermove', this._handleMouseMove);
          document.addEventListener('pointerup', this._handleMouseUp);
      };
      SignaturePad.prototype._handleMouseEvents = function () {
          this._mouseButtonDown = false;
          this.canvas.addEventListener('mousedown', this._handleMouseDown);
          this.canvas.addEventListener('mousemove', this._handleMouseMove);
          document.addEventListener('mouseup', this._handleMouseUp);
      };
      SignaturePad.prototype._handleTouchEvents = function () {
          this.canvas.addEventListener('touchstart', this._handleTouchStart);
          this.canvas.addEventListener('touchmove', this._handleTouchMove);
          this.canvas.addEventListener('touchend', this._handleTouchEnd);
      };
      SignaturePad.prototype._reset = function () {
          this._lastPoints = [];
          this._lastVelocity = 0;
          this._lastWidth = (this.minWidth + this.maxWidth) / 2;
          this._ctx.fillStyle = this.penColor;
      };
      SignaturePad.prototype._createPoint = function (x, y) {
          var rect = this.canvas.getBoundingClientRect();
          return new Point(x - rect.left, y - rect.top, new Date().getTime());
      };
      SignaturePad.prototype._addPoint = function (point) {
          var _lastPoints = this._lastPoints;
          _lastPoints.push(point);
          if (_lastPoints.length > 2) {
              if (_lastPoints.length === 3) {
                  _lastPoints.unshift(_lastPoints[0]);
              }
              var widths = this._calculateCurveWidths(_lastPoints[1], _lastPoints[2]);
              var curve = Bezier.fromPoints(_lastPoints, widths);
              _lastPoints.shift();
              return curve;
          }
          return null;
      };
      SignaturePad.prototype._calculateCurveWidths = function (startPoint, endPoint) {
          var velocity = this.velocityFilterWeight * endPoint.velocityFrom(startPoint) +
              (1 - this.velocityFilterWeight) * this._lastVelocity;
          var newWidth = this._strokeWidth(velocity);
          var widths = {
              end: newWidth,
              start: this._lastWidth
          };
          this._lastVelocity = velocity;
          this._lastWidth = newWidth;
          return widths;
      };
      SignaturePad.prototype._strokeWidth = function (velocity) {
          return Math.max(this.maxWidth / (velocity + 1), this.minWidth);
      };
      SignaturePad.prototype._drawCurveSegment = function (x, y, width) {
          var ctx = this._ctx;
          ctx.moveTo(x, y);
          ctx.arc(x, y, width, 0, 2 * Math.PI, false);
          this._isEmpty = false;
      };
      SignaturePad.prototype._drawCurve = function (_a) {
          var color = _a.color, curve = _a.curve;
          var ctx = this._ctx;
          var widthDelta = curve.endWidth - curve.startWidth;
          var drawSteps = Math.floor(curve.length()) * 2;
          ctx.beginPath();
          ctx.fillStyle = color;
          for (var i = 0; i < drawSteps; i += 1) {
              var t = i / drawSteps;
              var tt = t * t;
              var ttt = tt * t;
              var u = 1 - t;
              var uu = u * u;
              var uuu = uu * u;
              var x = uuu * curve.startPoint.x;
              x += 3 * uu * t * curve.control1.x;
              x += 3 * u * tt * curve.control2.x;
              x += ttt * curve.endPoint.x;
              var y = uuu * curve.startPoint.y;
              y += 3 * uu * t * curve.control1.y;
              y += 3 * u * tt * curve.control2.y;
              y += ttt * curve.endPoint.y;
              var width = curve.startWidth + ttt * widthDelta;
              this._drawCurveSegment(x, y, width);
          }
          ctx.closePath();
          ctx.fill();
      };
      SignaturePad.prototype._drawDot = function (_a) {
          var color = _a.color, point = _a.point;
          var ctx = this._ctx;
          var width = typeof this.dotSize === 'function' ? this.dotSize() : this.dotSize;
          ctx.beginPath();
          this._drawCurveSegment(point.x, point.y, width);
          ctx.closePath();
          ctx.fillStyle = color;
          ctx.fill();
      };
      SignaturePad.prototype._fromData = function (pointGroups, drawCurve, drawDot) {
          for (var _i = 0, pointGroups_1 = pointGroups; _i < pointGroups_1.length; _i++) {
              var group = pointGroups_1[_i];
              var color = group.color, points = group.points;
              if (points.length > 1) {
                  for (var j = 0; j < points.length; j += 1) {
                      var basicPoint = points[j];
                      var point = new Point(basicPoint.x, basicPoint.y, basicPoint.time);
                      this.penColor = color;
                      if (j === 0) {
                          this._reset();
                      }
                      var curve = this._addPoint(point);
                      if (curve) {
                          drawCurve({ color: color, curve: curve });
                      }
                  }
              }
              else {
                  this._reset();
                  drawDot({
                      color: color,
                      point: points[0]
                  });
              }
          }
      };
      SignaturePad.prototype._toSVG = function () {
          var _this = this;
          var pointGroups = this._data;
          var ratio = Math.max(window.devicePixelRatio || 1, 1);
          var minX = 0;
          var minY = 0;
          var maxX = this.canvas.width / ratio;
          var maxY = this.canvas.height / ratio;
          var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
          svg.setAttribute('width', this.canvas.width.toString());
          svg.setAttribute('height', this.canvas.height.toString());
          this._fromData(pointGroups, function (_a) {
              var color = _a.color, curve = _a.curve;
              var path = document.createElement('path');
              if (!isNaN(curve.control1.x) &&
                  !isNaN(curve.control1.y) &&
                  !isNaN(curve.control2.x) &&
                  !isNaN(curve.control2.y)) {
                  var attr = "M " + curve.startPoint.x.toFixed(3) + "," + curve.startPoint.y.toFixed(3) + " " +
                      ("C " + curve.control1.x.toFixed(3) + "," + curve.control1.y.toFixed(3) + " ") +
                      (curve.control2.x.toFixed(3) + "," + curve.control2.y.toFixed(3) + " ") +
                      (curve.endPoint.x.toFixed(3) + "," + curve.endPoint.y.toFixed(3));
                  path.setAttribute('d', attr);
                  path.setAttribute('stroke-width', (curve.endWidth * 2.25).toFixed(3));
                  path.setAttribute('stroke', color);
                  path.setAttribute('fill', 'none');
                  path.setAttribute('stroke-linecap', 'round');
                  svg.appendChild(path);
              }
          }, function (_a) {
              var color = _a.color, point = _a.point;
              var circle = document.createElement('circle');
              var dotSize = typeof _this.dotSize === 'function' ? _this.dotSize() : _this.dotSize;
              circle.setAttribute('r', dotSize.toString());
              circle.setAttribute('cx', point.x.toString());
              circle.setAttribute('cy', point.y.toString());
              circle.setAttribute('fill', color);
              svg.appendChild(circle);
          });
          var prefix = 'data:image/svg+xml;base64,';
          var header = '<svg' +
              ' xmlns="http://www.w3.org/2000/svg"' +
              ' xmlns:xlink="http://www.w3.org/1999/xlink"' +
              (" viewBox=\"" + minX + " " + minY + " " + maxX + " " + maxY + "\"") +
              (" width=\"" + maxX + "\"") +
              (" height=\"" + maxY + "\"") +
              '>';
          var body = svg.innerHTML;
          if (body === undefined) {
              var dummy = document.createElement('dummy');
              var nodes = svg.childNodes;
              dummy.innerHTML = '';
              for (var i = 0; i < nodes.length; i += 1) {
                  dummy.appendChild(nodes[i].cloneNode(true));
              }
              body = dummy.innerHTML;
          }
          var footer = '</svg>';
          var data = header + body + footer;
          return prefix + btoa(data);
      };
      return SignaturePad;
  }());

  return SignaturePad;

})));

var wrapper = $(".signature-pad");
console.log(wrapper);
var canvases = Array();
var count = 0;

function show(id){
  $('.'+id+'_edit').hide();
  $('.'+id+'_show').removeClass('hide');
  $('#'+id).val('');
  resizeCanvas();
}
// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
  // When zoomed out to less than 100%, for some very strange reason,
  // some browsers report devicePixelRatio as less than 1
  // and only part of the canvas is cleared then.
  var ratio =  Math.max(window.devicePixelRatio || 1, 1);

  // This part causes the canvas to be cleared
  canvases.forEach(function(entry) {
    console.log(entry);
    entry.canvas.width = entry.canvas.offsetWidth * ratio;
    entry.canvas.height = entry.canvas.offsetHeight * ratio;
    entry.canvas.getContext("2d").scale(ratio, ratio);

    // This library does not listen for canvas changes, so after the canvas is automatically
    // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
    // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
    // that the state of this library is consistent with visual state of the canvas, you
    // have to clear it manually.
    
    //entry.signaturePad.clear();
  });
}

// On mobile devices it might make more sense to listen to orientation change,
// rather than window resize events.
window.onresize = resizeCanvas;
resizeCanvas();

$.each( wrapper, function( key, value ) {
    var data = {};
    data.id                = $(value).attr('data-id');
    data.clearButton       = $(value).find("[data-action=clear]");
    data.canvas            = $(value).find("canvas")[0];
    console.log(data.canvas);
    console.log('###');
    data.signaturePad = new SignaturePad(data.canvas, {
      // It's Necessary to use an opaque color when saving image as JPEG;
      // this option can be omitted if only saving as PNG or SVG
      backgroundColor: 'rgb(255, 255, 255)'
    });
    //console.log(sig_datas[count]);

    //signaturePad.fromDataURL(sig_datas[count]);
    //data.signaturePad = signaturePad;
    canvases.push(data);
    
    count++;
});

console.log(canvases);

function download(dataURL, filename) {
  if (navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") === -1) {
    window.open(dataURL);
  } else {
    var blob = dataURLToBlob(dataURL);
    var url = window.URL.createObjectURL(blob);

    var a = document.createElement("a");
    a.style = "display: none";
    a.href = url;
    a.download = filename;

    document.body.appendChild(a);
    a.click();

    window.URL.revokeObjectURL(url);
  }
}

// One could simply use Canvas#toBlob method instead, but it's just to show
// that it can be done using result of SignaturePad#toDataURL.
function dataURLToBlob(dataURL) {
  // Code taken from https://github.com/ebidel/filer.js
  var parts = dataURL.split(';base64,');
  var contentType = parts[0].split(":")[1];
  var raw = window.atob(parts[1]);
  var rawLength = raw.length;
  var uInt8Array = new Uint8Array(rawLength);

  for (var i = 0; i < rawLength; ++i) {
    uInt8Array[i] = raw.charCodeAt(i);
  }

  return new Blob([uInt8Array], { type: contentType });
}

function savePads(){
  canvases.forEach(function(entry) {
    console.log(entry);
    if($('#'+entry.id).val()===''){
      if (entry.signaturePad.isEmpty()) {
        alert("Please provide a signature first.");
      } else {
        var dataURL = entry.signaturePad.toDataURL();
        //download(dataURL, "signature.png");
        $('#'+entry.id).val(dataURL);
      }
    }
  });
}
/*
clearButton.addEventListener("click", function (event) {
  signaturePad.clear();
});

undoButton.addEventListener("click", function (event) {
  var data = signaturePad.toData();

  if (data) {
    data.pop(); // remove the last dot or line
    signaturePad.fromData(data);
  }
});

changeColorButton.addEventListener("click", function (event) {
  var r = Math.round(Math.random() * 255);
  var g = Math.round(Math.random() * 255);
  var b = Math.round(Math.random() * 255);
  var color = "rgb(" + r + "," + g + "," + b +")";

  signaturePad.penColor = color;
});

savePNGButton.addEventListener("click", function (event) {
  if (signaturePad.isEmpty()) {
    alert("Please provide a signature first.");
  } else {
    var dataURL = signaturePad.toDataURL();
    download(dataURL, "signature.png");
  }
});

saveJPGButton.addEventListener("click", function (event) {
  if (signaturePad.isEmpty()) {
    alert("Please provide a signature first.");
  } else {
    var dataURL = signaturePad.toDataURL("image/jpeg");
    download(dataURL, "signature.jpg");
  }
});

saveSVGButton.addEventListener("click", function (event) {
  if (signaturePad.isEmpty()) {
    alert("Please provide a signature first.");
  } else {
    var dataURL = signaturePad.toDataURL('image/svg+xml');
    download(dataURL, "signature.svg");
  }
});
*/

if($('#file-uploader').length){

	var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('file-uploader'),
            // path to server-side upload script
            action: base_url+'valums/upload_images/',
			params: {},
            onComplete: function (id, fileName, responseJSON) {
	            //alert(responseJSON['filename']);
				$('.qq-upload-list li').remove();
				$('.progress-bar').width(0);

				var str = responseJSON['filename'];
				str = str.replace(/\.(gif|jpg|jpeg|tiff|png)$/i, ""); 

				var str = responseJSON['filename'];
				console.log(responseJSON['error']);
				var form_id = $('#form_id').val();
				window.location = base_url+'import/mapping/'+form_id;

          },
		  onSubmit: function(id, fileName){

		  },
		  onProgress: function(id, fileName, loaded, total){

				var width = $('.progress').width()*(((loaded/total)*100)/100);
				$('.progress-bar').width(width);

		  },
		  onCancel: function(id, fileName){},
			onError: function(id, fileName, xhr){}
        });

		$('#file-uploader > .qq-uploader > .qq-upload-button').prepend('UPLOAD CSV');
		$('.qq-upload-button').addClass('btn');
		$('.qq-upload-button').addClass('btn-primary');
}


var ds_edit_field_id = '';
var fieldid = '';
function editDS(id, fieldid = null){
	$('#editDatasource').modal();	
	ds_edit_field_id = fieldid;
	$.ajax({
			type: "POST",
			url: base_url+'datasource/getDatasourceValues/'+id,
			dataType:'html',
			success: function(data)
			{
				$('#listDSValues').html(data);
			}
			
	});
}


$('#editDatasource').on('hidden.bs.modal', function () {
	var url = base_url+'datasource/refreshField/'+ds_edit_field_id+'/'+ad_id;

	$.ajax({
        type: "POST",
        url: url,
        dataType:'json',
        success: function(data)
        {
          $('#field_'+ds_edit_field_id).html(data.msg);
        }
    });
	
});

var deleteButtonLadda = '';
$(document.body).on('click', '.deleteValueButton' ,function(e){
    e.preventDefault();
    //alert('hit');
    var id = $(this).attr('data-id');
    //alert('saveValueButton_'+id);
    deleteButtonLadda = Ladda.create(document.querySelector('#deleteValueButton_'+id));
    deleteButtonLadda.start();
    $('#currentDeleteRow').val(id);
    $('#deleteConfirmationModal').modal('show');
  });

  $('#deleteConfirmationModal').on('hidden.bs.modal', function () {
    //alert('hit'); 
    //$('#currentDeleteRow').val(id); 
    deleteButtonLadda.stop();
  });

  $(document.body).on('click', '#deleteDSItemA' ,function(e){
    var l = Ladda.create(document.querySelector('#deleteDSItemA'));
    l.start();
    var id = $('#currentDeleteRow').val();
    //alert($('#ds_id_add').val());
    //alert(id);
    $.ajax({
        type: "POST",
        url: base_url+'datasource/deleteDSValue',
        data: {'ds_ds_id':$('#ds_id_add').val(),'ds_id':id},
        dataType:'json',
        success: function(data)
        {
          
          l.stop();
          if(data.status=='1'){
            $('#warningMessageDSItemDelete').hide();
            $('#successMessageDSItemDelete').hide();
            $('#successMessageDSItemDelete').html(data.msg);
            $('#successMessageDSItemDelete').show();
            setTimeout(function(){
               $('#successMessageDSItemDelete').hide();
               $('.bs-example-modal-sm').modal('hide');
               editDS($('#ds_id_add').val(), ds_edit_field_id);
            },2000);
          }else{
            $('#warningMessageDSItemDelete').hide();
            $('#successMessageDSItemDelete').hide();

            $('#warningMessageDSItemDelete').html(data.msg);
            $('#warningMessageDSItemDelete').show();
            setTimeout(function(){
               $('#warningMessageDSItemDelete').hide();
               $('.bs-example-modal-sm').modal('hide');
            },2000);
          }
        }
    });
  });
  
  $(document.body).on('click', '.saveValueButton' ,function(e){
		e.preventDefault();
		//alert('hit');
		var id = $(this).attr('data-id');
		//alert('saveValueButton_'+id);
		var l = Ladda.create(document.querySelector('#saveValueButton_'+id));
	 	l.start();
		if(id=='add'){
			//add
			$.ajax({
				type: "POST",
				url: base_url+'datasource/addDSValue',
				data: {'ds_id':$('#ds_id_'+id).val(),'value':$('#valueAdd').val()},
				dataType:'json',
				success: function(data)
				{
					editDS($('#ds_id_'+id).val(), ds_edit_field_id);
					l.stop();
				}
			});
		}else{
			//edit
      var checked = 0;
      if($('#manual_'+id).is(':checked')){
        checked = 1;
      }
			$.ajax({
				type: "POST",
				url: base_url+'datasource/editDSValue',
				data: {'id':$('#id_'+id).val(),'value':$('#value_'+id).val(),'cost_price':$('#cost_price_'+id).val(),'retail_price':$('#retail_price_'+id).val(),'manual':checked,'vat_price':$('#vat_price_'+id).val(),},
				dataType:'json',
				success: function(data)
				{
					l.stop();
				}
			});
		}
	});