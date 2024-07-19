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