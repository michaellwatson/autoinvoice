<script language="javascript">

<?php
	$entry_id = $listing['ad_id'];

	$field_id = $field['adv_id'];
?>

var a = setTimeout(function(){

//jQuery(window).load(function() {

	$(window).off('beforeunload');
	var running = 0;  

	var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('file-uploader-documents_<?php echo $adv_column;?>'),
            // path to server-side upload script
            action: '<?php echo base_url('/valums/upload_filemanager/');//.$order_id);?>',
			params: {'entry_id': <?php echo $entry_id;?>, 'field_id': <?php echo $field_id;?>},
            onComplete: function (id, fileName, responseJSON) {
	            //alert(responseJSON['filename']);
				$('.qq-upload-list li').remove();
				$('.progress-bar_<?php echo $adv_column;?>').width(0);
				running--;
		        if(running==0){

		        	toastr.success('Files uploaded');
		        	setTimeout(function(){
		        		window.location.reload();
		        	}, 1000);  

		        }
          },
		  onSubmit: function(id, fileName){
				//$('.qq-upload-list').hide();	
				/*
				uploader.setParams({
		            file_name: $('#file_name').val() 
		        });
		        */
		       running++; 
		  },
		  onProgress: function(id, fileName, loaded, total){
				console.log(loaded);
				console.log(total);
				try {
					var width = $('.progress').width()*(((loaded/total)*100)/100);
					$('.progress-bar_<?php echo $adv_column;?>').width(width);
				}catch(err) {

				}
				/*var thenum = $('.qq-upload-size').html();
				var numOnly = thenum.replace( /^\D+/g, '');
				console.log(numOnly);*/
		  },
		  onCancel: function(id, fileName){},
		  onError: function(id, fileName, xhr){
		  	alert("error");
		  }
        });

		$('#file-uploader-documents_<?php echo $adv_column;?> > .qq-uploader > .qq-upload-button').prepend('UPLOAD');
		$('.qq-upload-button').addClass('btn');
		$('.qq-upload-button').addClass('btn-primary');



}, 3000);

</script>