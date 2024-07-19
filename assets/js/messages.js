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
