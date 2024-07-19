<style type="text/css">
.btn-danger1 {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}
.btn-danger1:hover {
    color: #fff;
    background-color: #bb2d3b;
    border-color: #b02a37;
}

#sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>{
	margin: 0px;
    padding: 0px;
}

#documentUploads_<?php echo $adv_column."-".$randomNumber;?> ul li{
	/*display: inline;*/
	list-style-type: none;
	background-color: #fff;
}

</style>
<!--
<script type='text/javascript' src='<?php echo base_url('assets/js/fileuploader/fileuploader.js');?>'></script>
-->
<script language="javascript">

<?php
	$entry_id = $ad_id;
	$field_id = $adv_id;
?>

var a = setTimeout(function(){

//jQuery(window).load(function() {
	let getImagesData = {
		additionalFormType: '<?php echo $additionalFormType; ?>'
	};
	
	$.ajax({
		type: "POST",
		<?php
			if(isset($additionalFormType) && $additionalFormType == 'edit'){
		?>
		url: '<?php echo base_url('post/getImages')?>/<?php echo $entry_id?>/<?php echo $field_id?>/<?php echo $ptBlocksMultipleDatasId?>',
		<?php
			}
			else if(isset($additionalFormType) && $additionalFormType == 'add'){
		?>
		url: '<?php echo base_url('post/getImages')?>/0/0',
		<?php
			}
			else{
		?>
		url: '<?php echo base_url('post/getImages')?>/<?php echo $entry_id?>/<?php echo $field_id?>',
		<?php
			}
		?>
		data: getImagesData,
		success: function(data)
		{
			$('#documentUploads_<?php echo $adv_column."-".$randomNumber;?>').html('<ul id="sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>" class="ul_child">'+data+'</ul>');

			makesortable_<?php echo $adv_column."_".$randomNumber;?>();
			save_button();
		}
	});

	var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('file-uploader-documents_<?php echo $adv_column."-".$randomNumber;?>'),
            // path to server-side upload script
            action: '<?php echo base_url('/valums/upload_images/');?>',
			params: {'entry_id': <?php echo $entry_id;?>, 'field_id': <?php echo $field_id;?>},
            onComplete: function (id, fileName, responseJSON) {
	            //alert(responseJSON['filename']);
				$('.qq-upload-list li').remove();
				$('.progress-bar_<?php echo $adv_column."-".$randomNumber;?>').width(0);
				var str = responseJSON['filename'];
				str = str.replace(/\.(gif|jpg|jpeg|tiff|png)$/i, ""); 

				var str = responseJSON['filename'];
				console.log(responseJSON['error']);
				console.log(responseJSON);

				<?php
					if(isset($additionalFormType) && $additionalFormType == 'add'){
				?>
					let customName = '<?php echo @$customName ?>';
					if(customName != ''){
						if ($('input[type="hidden"][name="hdnTempAddImage"]').length > 0) {

							const hiddenInputValue = $('input[type="hidden"][name="hdnTempAddImage"]').val();

							let idArray;
							if(hiddenInputValue == ""){
								idArray = [];
							}
							else{
								idArray = JSON.parse(hiddenInputValue);
							}
							
							idArray.push(responseJSON['imgid']);
							let hiddenInput = $('input[type="hidden"][name="hdnTempAddImage"]');
							hiddenInput.val(JSON.stringify(idArray));
						}
						else{
						}
					}
				<?php
					}
				?>

				if(typeof responseJSON['error'] === 'undefined'){

					<?php
						if(isset($additionalFormType) && $additionalFormType == 'add'){
					?>
							let tempHdnTempAddImage = $('input[type="hidden"][name="hdnTempAddImage"]').val();
							let data = { hdnTempAddImage: tempHdnTempAddImage, additionalFormType: getImagesData['additionalFormType'] };
			        let type = 'post';
			        let url = '<?php echo base_url('post/getImagesRepeaterAdd')?>/<?php echo $entry_id?>/<?php echo $field_id?>';
			        let dataType = 'json';
			        $.ajax({
			            url, type, data, dataType,
			            success: function (response) {
			                if(response.status){
			                	$('#documentUploads_<?php echo $adv_column."-".$randomNumber;?>').html('<ul id="sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>" class="ul_child">'+response.data.html+'</ul>');
												//alert('hit');
												makesortable_<?php echo $adv_column."_".$randomNumber;?>();
			                }
			                else{
			                	// toastr.error(response.message);
			                }
			            },
			            error: function(jqXHR, textStatus, errorThrown){
			                console.log(`jqXHR: ${jqXHR}`); console.log(`textStatus: ${textStatus}`); console.log(`errorThrown: ${errorThrown}`);
			            }
			        });

					<?php
						}
						else{
					?>
							let url = '';
					<?php
							if(isset($additionalFormType) && $additionalFormType == 'edit'){
					?>
								url = '<?php echo base_url('post/getImages')?>/<?php echo $entry_id?>/<?php echo $field_id?>/<?php echo $ptBlocksMultipleDatasId?>';
					<?php
							}
							else{
					?>
								url = '<?php echo base_url('post/getImages')?>/<?php echo $entry_id?>/<?php echo $field_id?>';
					<?php
							}
					?>
							$.ajax({
								type: "POST",
								url,
								success: function(data)
								{
									$('#documentUploads_<?php echo $adv_column."-".$randomNumber;?>').html('<ul id="sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>" class="ul_child">'+data+'</ul>');
									//alert('hit');
									makesortable_<?php echo $adv_column."_".$randomNumber;?>();
								}
							});
					<?php
						}
					?>


					$('.crop_modal').modal('show');

					jcrop_api.setImage('<?php echo base_url();?>/assets/uploaded_images/'+responseJSON['filename']); 
					var l = Ladda.create(document.querySelector('#saveCropButton'));
					l.start();
					$('.jcrop-holder').find('img').bind('load',function(){
						//alert('hit');
						setTimeout(function(){
							var dim = jcrop_api.getBounds();
							var ratio = jcrop_api.getOptions().aspectRatio;
							var x = 0, y = 0, x_ = dim[0], y_ = dim[1];

							var x_r = (x_ / ratio) - y_;
							var y_r = (y_ / ratio) - x_;

							if (x_r > 0) {
							    x = x_r / 2;
							}
							if (y_r > 0) {
							    y = y_r / 2;
							}

							jcrop_api.setSelect([x, y, dim[0], dim[1]]);

							//jcrop_api.setSelect([140, 180, 160, 180]);
							//jcrop_api.destroy();
							l.stop();
						},2500);
					});
					
					//alert('hit');
					$('#img').val(responseJSON['filename']);
					$('#imgId').val(responseJSON['imgid']);

					let customName = '<?php echo @$customName ?>';
					// if(customName == "93[ad_photoupload_fraew]"){
					if(customName != ''){
						if ($('input[type="hidden"][name="hdnBalconiesPhotoUpload"]').length > 0) {

							const hiddenInputValue = $('input[type="hidden"][name="hdnBalconiesPhotoUpload"]').val();

							let idArray;
							if(hiddenInputValue == ""){
								idArray = [];
							}
							else{
								idArray = JSON.parse(hiddenInputValue);
							}
							
							idArray.push(responseJSON['imgid']);
							let hiddenInput = $('input[type="hidden"][name="hdnBalconiesPhotoUpload"]');
							hiddenInput.val(JSON.stringify(idArray));
						}
						else{
						}
					}

					$('#entry_id').val('<?php echo $entry_id?>');
					$('#field_id').val('<?php echo $field_id?>');
					$('#column').val('<?php echo $adv_column;?>');

				}
          },
		  onSubmit: function(id, fileName){
				//$('.qq-upload-list').hide();	
				/*
				uploader.setParams({
		            file_name: $('#file_name').val() 
		        });
		        */
		  },
		  onProgress: function(id, fileName, loaded, total){
				console.log(loaded);
				console.log(total);
				var width = $('.progress').width()*(((loaded/total)*100)/100);
				$('.progress-bar_<?php echo $adv_column."-".$randomNumber;?>').width(width);
				/*var thenum = $('.qq-upload-size').html();
				var numOnly = thenum.replace( /^\D+/g, '');
				console.log(numOnly);*/
		  },
		  onCancel: function(id, fileName){},
			onError: function(id, fileName, xhr){}
        });
		// if($('#file-uploader-documents_photoupload_fraew > .qq-uploader > .qq-upload-button').text() == 'UPLOAD'){
		// }
		// else{
			// $('#file-uploader-documents_<?php echo $adv_column;?> > .qq-uploader > .qq-upload-button').text('UPLOAD');
		// }

			$('#file-uploader-documents_<?php echo $adv_column."-".$randomNumber;?> > .qq-uploader > .qq-upload-button').prepend('UPLOAD');

			/*if ($('#file-uploader-documents_photoupload_fraew > .qq-uploader > .qq-upload-button').text().indexOf('UPLOAD') !== -1) {
			}
			else{
				$('#file-uploader-documents_<?php echo $adv_column;?> > .qq-uploader > .qq-upload-button').prepend('UPLOAD');
			}*/
		$('.qq-upload-button').addClass('btn');
		$('.qq-upload-button').addClass('btn-primary');
//});

		$(document).on("click", ".display_options_table_image", function(e) {
			let layout_id = $(this).attr('data-id');
			$(this).parent().parent().parent().find('.display_options_table_image').removeClass('image_selected');
			$(this).addClass('image_selected');
			//alert(dataid);
			<?php if(isset($listing['ad_id'])){?>

				$.ajax({
		        	type: "POST",
		            url: base_url+'Post/update_image_display_config',
		            data: {layout_id: layout_id, 'field': <?php echo $field_id;?>, 'entry_id': <?php echo $entry_id;?>}, // serializes the form's elements.
		            dataType:'json',
		            success: function(data){
		                //l.stop();
		                console.log(data);
		                toastr.success(data.msg);
		            } 
		        });

			<?php } ?>
		});

}, 3000);

function serializeList(container)
{
  var str = ''
  var n = 0
  var els = container.find('li')
  for (var i = 0; i < els.length; ++i) {
    var el = els[i]
    var p = el.id;
    console.log(p);
    var x = p.split('_');
    /*if (p != -1) {
      if (str != '') str = str + '&'
      str = str + el.id.substring(0, p) + '[]=' + (n + 1)
      ++n
    }*/
    str+=x[1]+',';
  }
  return str
}
function deleteButton(){
	<?php
		if(0){
	?>
			// $('.btn-danger').on('click',function(e){
			// $(document).on( 'click', '.delete-uploaded-image<?php echo $field_id ?>', function(e){
			// $('.delete-uploaded-image<?php echo $field_id ?>').on('click',function(e){
			// $('.deleteTempTemp').on('click',function(e){
			$(document).on( 'click', '.deleteTempTemp', function(e){
				e.preventDefault();
				if(confirm_delete()){

					<?php
						if(isset($additionalFormType) && $additionalFormType == 'edit'){
					?>
					let ptBlocksMultipleDatasId = "<?php echo $ptBlocksMultipleDatasId ?>";
					<?php
						}
						else{
					?>
					let ptBlocksMultipleDatasId = undefined;
					<?php
						}
					?>
					var dataid = $(this).attr('data-id');
					//alert(dataid);
					$.ajax({
						type: "POST",
						url: '<?php echo base_url('Post/deleteImages')?>',
						data: {'id':dataid, ptBlocksMultipleDatasId},
						dataType:'json',
						success: function(data)
						{
							//alert('hit');
							$('#image_'+dataid).remove();
						}
					});
				}
			});
	<?php
		}
	?>
}
function makesortable_<?php echo $adv_column."_".$randomNumber;?>(){

	$("#sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>").sortable({
	    items: "li",
	    cursor: 'move',
	    opacity: 0.6,
	    update: function() {

	        var order = serializeList($("#sortableDocuments_<?php echo $adv_column."-".$randomNumber;?>"));
	        //alert(order);

	        $.ajax({
	            type: "POST", 
	            dataType: "json", 
	            url: "<?php echo base_url('/Post/orderImages')?>",
	            data: {'order':order },
	            success: function(response) {
	            	deleteButton_<?php echo $adv_column."-".$randomNumber;?>();
	            	save_button();
	            }
	        });

	    }
	});
	deleteButton();

}
</script>