<!-- <script type="text/javascript" src="<?php //echo base_url('assets/plugins/html2canvas/html2canvas.min.js'); ?>"></script> -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/jquery-ui-1.13.2.custom/jquery-ui.min.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('assets/plugins/jquery-ui-1.13.2.custom/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/jquery-arrows-main/jquery.arrows.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/plugins/dom-to-image-master/dist/dom-to-image.min.js'); ?>"></script>

<script type="text/javascript">
<?php
	$entry_id = $ad_id;
	$field_id = $adv_id;
?>
$(document).on('click', '.clsUpload-<?php echo $adv_column;?>', function(e){
	let data = {};
	let type = 'post';
	let url = "<?php echo base_url('post/getImagesArrow')?>/<?php echo $entry_id?>/<?php echo $field_id?>";
	let dataType = 'json';
	$.ajax({
		url, type, data, dataType,
		success: function (response) {
			if(response.status){
				$('.clsModalBodyImageArrow').html(response.data.html);
				initializeImageArrow();
				$('.image_arrow_modal').modal('show');

				$(document).on("contextmenu", "#draggable4", function(e){
			        e.preventDefault(); // Prevent the default context menu from appearing

			        // Show a confirmation dialog
			        var confirmDelete = confirm("Are you sure you want to delete this element and its arrow?");
			        if (confirmDelete) {
			            // If the user clicks "Yes", delete #draggable4 and #arrow-3-4
			            $('#draggable4').remove(); // Remove the #draggable4 element
			            $('#arrow-3-4').remove(); // Remove the arrow with ID #arrow-3-4
			        }
			    });

			    $(document).on("contextmenu", "#draggable3", function(e){
			        e.preventDefault(); // Prevent the default context menu from appearing

			        // Show a confirmation dialog
			        var confirmDelete = confirm("Are you sure you want to delete this element and its sub arrows?");
			        if (confirmDelete) {
			            // If the user clicks "Yes", delete #draggable4 and #arrow-3-4
			            $('#draggable3').remove(); // Remove the #draggable4 element
			            $('#arrow-2-3').remove(); // Remove the arrow with ID #arrow-3-4

			         	$('#draggable4').remove(); // Remove the #draggable4 element
			            $('#arrow-3-4').remove(); // Remove the arrow with ID #arrow-3-4
			        }
			    });
			}
		},
		error: function(jqXHR, textStatus, errorThrown){
			console.log(`jqXHR: ${jqXHR}`);
			console.log(`textStatus: ${textStatus}`);
			console.log(`errorThrown: ${errorThrown}`);
		}
	});
});
function initializeImageArrow(){
	// $(function(){
	// 	$(".draggable").draggable({ snap: ".image-arrow-container", snapMode: "outer" });
	// });
	$( ".node" ).draggable({ axis: "x" }); // Make node boxes draggable with jQuery UI
	/*$("#draggable3").draggable({ 
	  axis: "x",
	  containment: [$("#draggable2").offset().left, 0, $("#draggable4").offset().left, 0]
	});*/
	/*$("#draggable3").draggable({
	  axis: "x",
	  drag: function (event, ui) {
	      var xStart = $("#draggable2").offset().left + $("#draggable2").outerWidth();
	      var xEnd = $("#draggable4").offset().left - $("#draggable3").outerWidth();
	      if (ui.position.left < xStart) {
	          ui.position.left = xStart;
	      } else if (ui.position.left > xEnd) {
	          ui.position.left = xEnd;
	      }
	  }
	});*/
	$().arrows({
	  within: '#svg-arrows', // Optional (container that will hold the created arrows - set to "body" if not specified)
	  id: 'arrow-1-2', // Optional (for unique element identification - set to "arrow-n" if not specified) 
	  class: 'arrow-class-1', // Optional (for arrow class identification and styling)
	  name: 'Fire performance', // Optional (no text is shown otherwise)
	  from: '#node-1', // MANDATORY - Any jQuery selector (allowing to choose multiple source elements at once).
	  to: '#node-2' // MANDATORY - Any jQuery selector (allowing to choose multiple destination elements at once).
	});
	$().arrows({
	  within: '#svg-arrows', // Optional (container that will hold the created arrows - set to "body" if not specified)
	  id: 'arrow-2-3', // Optional (for unique element identification - set to "arrow-n" if not specified) 
	  class: 'arrow-class-1', // Optional (for arrow class identification and styling)
	  name: 'Facade configuration', // Optional (no text is shown otherwise)
	  from: '#node-2', // MANDATORY - Any jQuery selector (allowing to choose multiple source elements at once).
	  to: '#node-3' // MANDATORY - Any jQuery selector (allowing to choose multiple destination elements at once).
	});
	$().arrows({
	  within: '#svg-arrows', 
	  id: 'arrow-3-4',
	  class: 'arrow-class-3',
	  name: 'Fire strategy/fire hazards',
	  from: '#node-3', 
	  to: '#node-4'
	});
	var arrows = $("arrow, inner");
	setInterval(function () {
	  arrows.arrows("update");
	}, 100);
}
var a = setTimeout(function(){
	$.ajax({
		type: "POST",
		url: '<?php echo base_url('post/getImages')?>/<?php echo $entry_id?>/<?php echo $field_id?>',
		success: function(data)
		{
			$('#documentUploads_<?php echo $adv_column;?>').html('<ul id="sortableDocuments_<?php echo $adv_column;?>" class="ul_child">'+data+'</ul>');

			makesortable_<?php echo $adv_column;?>();
			save_button();
		}
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
	$('.btn-danger').on('click',function(e){
		e.preventDefault();
		if(confirm_delete()){
			var dataid = $(this).attr('data-id');

			//alert(dataid);
			$.ajax({
				type: "POST",
				url: '<?php echo base_url('Post/deleteImages')?>',
				data: {'id':dataid},
				dataType:'json',
				success: function(data)
				{
					//alert('hit');
					$('#image_'+dataid).remove();
				}
			});
		}
	});
}
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
	            url: "<?php echo base_url('/Post/orderImages')?>",
	            data: {'order':order },
	            success: function(response) {
	            	deleteButton_<?php echo $adv_column;?>();
	            	save_button();
	            }
	        });

	    }
	});
	deleteButton();

}
</script>