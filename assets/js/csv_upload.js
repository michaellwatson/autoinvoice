
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

