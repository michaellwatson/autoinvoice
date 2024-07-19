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