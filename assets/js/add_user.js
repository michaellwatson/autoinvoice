jQuery(document).ready(function() {	
 	//alert('hit');
 	jQuery('#add_user_form').submit(function(event){
 		event.preventDefault();
        var data = $("#add_user_form :input").serializeArray();
    	//alert('Handler for .submit() called.');
    	var l = Ladda.create(document.querySelector('.saveValueButton'));
    	l.start();
    	jQuery.ajax({
			type: "POST",
			url: url+'users/createUser/',
			data: data,
			dataType:'json',
			success: function(data)
			{
				l.stop();
				if(data.status==1){
					toastr.success(data.msg);

					window.scrollTo(0, 0);

				}else{
					//alert(data.msg);
					toastr.error(data.msg);
				}

			}
		});
	});


/*
  var postDropZone = new Dropzone('#dZUpload', {

        //Dropzone.options.myDropzone = {
        uploadMultiple: false,
        parallelUploads: 1,
        timeout: 10000,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        url: base_url+'dropzone/upload/'+$('#user_id').val(),
        createImageThumbnails: true,
        autoProcessQueue: true,
        thumbnailWidth  : 100,   
        addRemoveLinks: true,
        dictRemoveFile : '×',
        dictCancelUpload : '...',
        clickable : '.logo_upload',
        //previewTemplate : document.querySelector('.template').innerHTML,
        previewsContainer: '#dropzonePreview',
        removedfile: function(file) {

                        file.previewElement.remove();
                        $.ajax({
                            url: 'delete_message_image',
                            type: 'POST',
                            data: {'file_id': file.previewElement.id},
                            dataType: 'json',
                            success: function (data) {

                            },
                            error: function () {

                            }
                        });

                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
        },
        uploadprogress: function(file, progress, bytesSent) {

                        $('.progress-bar').css({'width': progress+'%'});
                        if(progress==100){
                            setTimeout(function(){ 
                                $('.progress-bar').css({'width': '0%'});
                            }, 3000);
                        }
        },
        init: function () {

        },
        success: function (file, done) {
            console.log(file);
            console.log(done);
            $('#logo').val(done);
        },
        error: function(file, errorMessage, xhr){

        }   
        });

*/
 })
