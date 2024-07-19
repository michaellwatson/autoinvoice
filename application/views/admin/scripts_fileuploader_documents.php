<style type="text/css">

#sortableDocuments_<?php echo $adv_column;?>{
	margin: 0px;
    padding: 0px;
}

#documentUploads_<?php echo $adv_column;?> ul li{
	display: inline;
	list-style-type: none;
	background-color: #fff;
	padding: 4px;
	margin-bottom: 10px;
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
	
	$.ajax({
		type: "POST",
		url: '<?php echo base_url('post/getDocuments')?>/<?php echo $entry_id?>/<?php echo $field_id?>',
		success: function(data)
		{
			$('#documentUploads_<?php echo $adv_column;?>').html('<ul id="sortableDocuments_<?php echo $adv_column;?>" class="ul_child">'+data+'</ul>');

			makesortable_<?php echo $adv_column;?>();
			save_button();
		}
	});

	var uploader = new qq.FileUploader({
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('file-uploader-documents_<?php echo $adv_column;?>'),
            // path to server-side upload script
            action: '<?php echo base_url('/valums/upload_documents/');//.$order_id);?>',
			params: {'entry_id': <?php echo $entry_id;?>, 'field_id': <?php echo $field_id;?>},
            onComplete: function (id, fileName, responseJSON) {
	            //alert(responseJSON['filename']);
				$('.qq-upload-list li').remove();
				$('.progress-bar_<?php echo $adv_column;?>').width(0);
				var str = responseJSON['filename'];
				str = str.replace(/\.(pdf)$/i, ""); 

				var str = responseJSON['filename'];
				console.log(responseJSON['error']);
				if(typeof responseJSON['error'] === 'undefined'){
					$.ajax({
						type: "POST",
						url: '<?php echo base_url('post/getDocuments')?>/<?php echo $entry_id?>/<?php echo $field_id?>',
						success: function(data)
						{
							$('#documentUploads_<?php echo $adv_column;?>').html('<ul id="sortableDocuments_<?php echo $adv_column;?>" class="ul_child">'+data+'</ul>');
							//alert('hit');
							makesortable_<?php echo $adv_column;?>();
						}
					});

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
				$('.progress-bar_<?php echo $adv_column;?>').width(width);
				/*var thenum = $('.qq-upload-size').html();
				var numOnly = thenum.replace( /^\D+/g, '');
				console.log(numOnly);*/
		  },
		  onCancel: function(id, fileName){},
			onError: function(id, fileName, xhr){}
        });
		$('#file-uploader-documents_<?php echo $adv_column;?> > .qq-uploader > .qq-upload-button').prepend('UPLOAD');
		$('.qq-upload-button').addClass('btn');
		$('.qq-upload-button').addClass('btn-primary');
//});


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

	$(document).on('click', '.btn_delete_document',function(e) {
		e.preventDefault();
		if(confirm_delete()){
			var dataid = $(this).attr('data-id');

			//alert(dataid);
			$.ajax({
				type: "POST",
				url: '<?php echo base_url('Post/deleteDocuments')?>',
				data: {'id':dataid},
				dataType:'json',
				success: function(data)
				{
					$('#document_'+dataid).remove();
				}
			});
		}
	});

function makesortable_<?php echo $adv_column;?>(){

	$("#sortableDocuments_<?php echo $adv_column;?>").sortable({
	    items: "li",
	    cursor: 'move',
	    opacity: 0.6,
	    update: function() {

	        var order = serializeList($("#sortableDocuments_<?php echo $adv_column;?>"));
	        //alert(order);

	        $.ajax({
	            type: "POST", 
	            dataType: "json", 
	            url: "<?php echo base_url('/Post/orderDocuments')?>",
	            data: {'order':order },
	            success: function(response) {
	            	deleteButton_<?php echo $adv_column;?>();
	            	save_button();
	            }
	        });

	    }
	});

}
</script>