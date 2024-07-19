<?php 
$text = 'Save';
//echo '#'.$order.'#';
if(isset($order)){ 
	if($order==1){
		$text = 'Save & Progress to Order';
	}
}?>

<input type="hidden" value="0" id="saveAndFinishLaterField" name="saveAndFinishLaterField">
<?php 
if(isset($order)){ 
    if($order==1){
        ?>
    <input type="hidden" name="order" id="order" value="1">
<?php }
}
?>
<br>
<?php

if($permissions['update']||$permissions['create']){ 
    if($show_button){
?>
<div class="<?php echo (isset($_SESSION['two-col']) && (int)$_SESSION['two-col'] == 1) ? 'container-fluid' : 'container';?>">
    <div class="row text-right justify-content-end button-row" style="margin-bottom:25px;">
    <button type="submit" class="btn btn-primary ladda-button pull-right submitButton" data-style="expand-right" id="submitButton" onclick="saveAndNext();"><span class="ladda-label"><?php echo $text;?></span></button>
    <br>
    <br>

    </div>
</div>
<?php
    }
} ?>

<?php 
if(sizeof($tabs)==0){
    if($permissions['role']==1){ 
    ?>
    <!--timeline-->
    <div class="col-md-8"> 
        <!--timeline-->
          <div class="timeline-centered">
          <?php 
          foreach($events as $e){ ?>
              <article class="timeline-entry">
                 <div class="timeline-entry-inner">
                    <time class="timeline-time" datetime="2014-01-10T03:45">
                    <span><?php echo date('M d, Y', strtotime($e['created_at']));?></span> <!--<span>x days ago</span>-->
                    </time>

                    <div class="timeline-icon <?php if($e->status==1){?>timeline_success<?php }else if($e->status==2){?>timeline_warning<?php }else{ ?>timeline_primary<?php } ?>">
                      <i class="entypo-feather"></i>
                    </div>
                    <div class="timeline-label">
                      <h2><?php echo date('H:i A', strtotime($e['created_at']));?></h2>
                      <p>
                          <?php echo $e->note;?>
                          <br>
                          <small><?php echo $e->user->us_firstName;?> <?php echo $e->user->us_surname;?></small>
                      </p>
                    </div>

                 </div>
              </article>
          <?php } ?>
          </div>
      <!--timeline-->
<?php } 
}
?>
  </div>

<?php 
    //if($user['user']['us_role']==1){ 
    ?>  
<script language="javascript">
<?php if(isset($id)){ ?>
var ad_id = '<?php echo $id;?>';  
<?php } ?>

function save_button(){

    $(document).on('click', '.save_button',function(e) {
        e.preventDefault();
        var dataid = $(this).attr('data-id');
        //alert(dataid);
        var value = jQuery('#caption_'+dataid).val();
        //alert(value);
        var l = Ladda.create(document.querySelector('#button_caption_'+dataid));
        l.start();
        
        jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/caption',
            data: {'image_id':dataid, 'caption':value}, // serializes the form's elements.
            dataType:'json',
            success: function(data)
            {
               l.stop();     
            }
        });
    }); 

}
function removeField(field){
    jQuery('#id_'+field).remove();

    var val = Number(field);
    if(String(val) === 'NaN') {
       console.log("true");
    }else{
        //its a number
        jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/remove_field',
            data: {'deleteID':field}, // serializes the form's elements.
            dataType:'json',
            success: function(data)
            {

            }
        });
    }
}
function saveAndFinishLater(){
	jQuery('textarea.ckeditor').each(function () {
	   var $textarea = $(this);
	   $textarea.val(CKEDITOR.instances[$textarea.attr('name')].getData());
	});
	jQuery('#saveAndFinishLaterField').val(1);
}
function saveAndNext(){
    savePads();
	jQuery('textarea.ckeditor').each(function () {
	   var $textarea = $(this);
	   $textarea.val(CKEDITOR.instances[$textarea.attr('name')].getData());
	});
	jQuery('#saveAndFinishLaterField').val(0);
    jQuery("#form-add").submit(); 
}
/*
function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}*/
jQuery(window).load(function() {    

    $(document).on("submit", "#send_msg_form", function (e) {
        e.preventDefault();
        var msg = CKEDITOR.instances['e_message'].getData();
        var slug = $('#e_slug').val();

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();

        today = dd + '/' + mm + '/' + yyyy;

        jQuery.ajax({
            type: "POST",
            url: base_url+'/Emailsending/send_email',
            data: jQuery(this).serialize(), // serializes the form's elements.
            dataType:'json',
            success: function(data)
            {
                if(data.status==1){
                    toastr.success(data.msg);
                    $('.send_msg_modal').modal('hide');
                    /*
                    120 MA Welcome, 
                    121 MA details sent to Fibre Provider, 
                    122 Welcome Pack sent to Freeholder,
                    123 Welcome Pack sent to MA,
                    124 Welcome Pack sent to Fibre Provider
                    */

                    switch(slug){ 
                        case "ma_welcome":
                
                            $('#WelcomeemailsenttoMA').val(117);
                            $('#DateSent').val(today);
                            $('#Status').val(120);
                
                        break;
                        case "ma_details_sent_to_fibre_provider":
                
                            $("#MAdetailssenttoFibreProvider_1").prop("checked", true);
                            $('#DateMAdetailssenttoFibreProvider').val(today);
                            $('#Status').val(121);
                
                        break;
                        case "welcome_pack_sent_to_freeholder":
                        
                            $("#SenttoFreeholder_1").prop("checked", true);
                            $('#DatesenttoFreeholder').val(today);
                            $('#Status').val(122);
                
                        break;
                        case "welcome_pack_sent_to_MA":

                            $("#SenttoManagingAgent_1").prop("checked", true);
                            $('#DatesenttoMA').val(today);
                            $('#Status').val(123);
                        
                        break;
                        case "pack_sent_to_fibre_provider":  

                            $("#SenttoFibreProvider").prop("checked", true);
                            $('#DatesenttoFibreProvider').val(today);
                            $('#Status').val(124);     
                        
                        break;
                    }
                    $()

                }else{
                    toastr.error(data.msg);
                }
            }
        });

    });

    


    jQuery('.uk_datepicker').datetimepicker({
        //defaultDate: new Date(),
        format:'d/m/Y'
    });

    jQuery('.role_select').change(function(){
        var id = jQuery(this).attr('data-id');
        var roleid = jQuery(this).val();

        //alert(id);
        //alert(roleid);
        jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/change_role',
            data: {'category_id':id, 'role_id':roleid}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                
                if(data.status==1){
                       
                }else{

                }
            }
        });
    }); 

    //jQuery('input[type="checkbox"]').bootstrapToggle();
    /*
    jQuery('input[type="checkbox"]').change(function() {
      var id = jQuery(this).attr('id');
      var val = jQuery(this).prop('checked');
      //alert(val);
      switch(val){
        case false:
            val = 0;
        break;
        case true:
            val = 1;
        break;
      }
      //alert(val);
      //preload perms
      jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/hide_category',
            data: {'id':id,'val':val}, // serializes the form's elements.
            dataType:'html',
            success: function(data)
            {
                //don't need to do anything
            }
        });

    });
    */



    //alert('hit1');
    var $document = jQuery(document),
    $element = jQuery('.pageheader'),
    className = 'hasScrolled';
/*
    $document.scroll(function() {
      $element.toggleClass(className, $document.scrollTop() >= 50);
    });
*/
    
	jQuery('#previousButton').click(function(e){
		var url = base_url+'post/create_form/'+jQuery(this).attr('data-category')+'/'+jQuery(this).attr('data-ad_id'); 	
		window.location = url;
	});
	jQuery('a.forTooltip').click(function(e)
	{
		// Special stuff to do when this link is clicked...
		// Cancel the default action
		e.preventDefault();
	});
	jQuery('.forTooltip').popover({html: true});
	/*
	$('textarea#Description').maxlength({
    	alwaysShow: true
    });
	*/

    jQuery("#submitButtonHeader").on('click',function(){
        jQuery("#form-add").submit(); 
    });

    $(document).ready(function() {
        let scrollPos = sessionStorage.getItem('scrollposRepeatsectionSave');
        if (scrollPos) {
            window.scrollTo(0, scrollPos);
            sessionStorage.removeItem('scrollposRepeatsectionSave');
        }
    });

	jQuery("#form-add").submit(function(e) { 
		//alert('hit');
		e.preventDefault();
        let category = "<?php echo $category; ?>";
        if(category == 56){
            let elementExists = $('.clsAddRepeatsection').length > 0;
            if(elementExists){
                if(balconiesRepeatData.length > 0){
                    /*var hiddenInput = $('<input>').attr({
                        type: 'hidden',
                        name: 'hdnBalconiesRepeat',
                        value: JSON.stringify(balconiesRepeatData)
                    });
                    $('.clsAddRepeatsection').after(hiddenInput);*/
                }
            }
            else{
            }
        }
		//var l = Ladda.create($('#submitButton'));
		var l = Ladda.create(document.querySelector('.submitButton'));
		//var l2 = Ladda.create(document.querySelector('#submitButton2'));
	 	l.start();
	 	//l2.start();
		<?php if($ad_id==0){?>
			var url = '<?php echo base_url('post/save_post/'.$category)?>';  
		<?php }else{ ?>
			var url = '<?php echo base_url('post/update_post/'.$category.'/'.$ad_id)?>'; 
		<?php } ?>
		jQuery.ajax({
				   type: "POST",
				   url: url,
				   data: jQuery(this).serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
                    //alert(data);
					   if(data.status==1){

                            l.stop();
                            toastr.success(data.msg);

                            if(data.repeatsectionSave){
                                sessionStorage.setItem('scrollposRepeatsectionSave', window.scrollY);
                                window.location = data.repeatsectionSaveData.redirectUrl;
                            }

                            if(!$('#stay_on_page').is(":checked")){

					   		//window.location='<?php echo base_url('post/create_form/'.$category.'/')?>'+data.ad_id+'?formID=<?php echo $_GET['formID'];?>';
                           
                                //we're in a frame
                                if(window.self !== window.top){
                                    parent.location.reload();
                                }else{

                                    <?php 
                                    if(isset($job_id)){
                                    ?>
                                        window.location='<?php echo base_url('job/add/'.$job_id)?>';
                                    <?php
                                    }else{
                                    if(isset($order)){ 
                                        if($order==1){
                                            ?>
                                            window.location='<?php echo base_url('entries/show')?>/<?php echo $category;?>?formID=<?php echo $formID;?>';
                                            <?php
                                        }else{
                                            ?>
                                            window.location='<?php echo base_url('entries/show')?>/<?php echo $category;?>?formID=<?php echo $formID;?>';
                                            <?php
                                        }
                                    }else{ ?>
                                        window.location='<?php echo base_url('entries/show')?>/<?php echo $category;?>?formID=<?php echo $formID;?>';
                                    <?php } 

                                    } ?>
                                    
                                }

                            }else if($('#force_timeout').val() == '1'){

                                window.location='<?php echo base_url('entries/show')?>/<?php echo $category;?>?formID=<?php echo $formID;?>';

                            }


					   }else{
                           toastr.error(data.msg);
						   window.scrollTo(0, 0);
						   jQuery('#warningMessage').html(data.msg);
						   jQuery('#warningMessage').show();
						   //setTimeout(function(){jQuery('#warningMessage').fadeOut();}, 2000);
						   l.stop();
						   //l2.stop();
					   }
				   }
			});
	});

    jQuery(document).on("click",".additional_fields",function() {
        //alert('hit');
        jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/additional_field',
            data: {'name':'additional_fields'}, // serializes the form's elements.
            dataType:'html',
            success: function(data)
            {
                jQuery('#additional_fields_entries').append(data);
            }
        });
    });

    jQuery(document).on("click",".discounts_fields",function() {
        //alert('hit');
        jQuery.ajax({
            type: "POST",
            url: base_url+'/Post/additional_field',
            data: {'name':'discount_fields'}, // serializes the form's elements.
            dataType:'html',
            success: function(data)
            {
                jQuery('#discounts_fields_entries').append(data);
            }
        });
    });
});


var jcrop_api; 

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

$(document).ready(function() {

    
    jQuery(document).on("click", ".prefill_button" ,function() {
        html = decodeHtml($(this).data('text'));
        //replace the values
        //html = '<a href="#">###</a><br>';

        var txt = $(this).parent().find('textarea');
        var editor = txt.attr('id');
        try {

            var d = CKEDITOR.instances[txt.attr('id')].getData();
            if(d!==''){

                html = d+'<br>'+html;

            }
            
            //console.log(data);
            //html = CKEDITOR.dom.element.createFromHtml(data);
            /*
            console.log(d);
            console.log(html);
            console.log(data);
            */
            CKEDITOR.instances[editor].setData('');
            $('.submitButton').attr('disabled', true);
            setTimeout(function(){
                console.log(html);
                CKEDITOR.instances[editor].insertHtml(html);

                setTimeout(function(){
                    
                    var editor = CKEDITOR.instances[txt.attr('id')]; 
                    var jqDocument = $(editor.document.$);
                    jqDocument.scrollTop(0);

                    // Simulate a key press event
                    var keyCode = 65; // ASCII code for 'A'
                    var fakeEvent = new CKEDITOR.dom.event(editor.document);
                    fakeEvent.$.keyCode = keyCode;
                    fakeEvent.$.which = keyCode;
                    fakeEvent.$.key = String.fromCharCode(keyCode);

                    // Trigger the key press event
                    editor.fire('key', { keyCode: fakeEvent.$.keyCode, domEvent: fakeEvent });
                    $('.submitButton').attr('disabled', false);

                }, 1000);

                //CKEDITOR.instances[editor].insertElement(html);
                //CKEDITOR.instances[editor].insertElement(html);
            }, 1000);
            
            //CKEDITOR.instances.body.insertElement(imgHtml);
            //CKEDITOR.instances[txt.attr('id')].insertElement(html);
            //CKEDITOR.instances[txt.attr('id')].insertElement(html);

        } catch(err) {
        
            var input = $(this).parent().find('input');
            
            input.val(html.replace(/(<([^>]+)>)/gi, ""));
        }

    })

    var browser = get_browser();
    //alert(browser);
    if((browser == 'IE')||(browser == 'MSIE')){
        //alert('hit');
            jcrop_api = jQuery.Jcrop('#cropbox', {
               /* aspectRatio: 1.3,*/
                onSelect: updateCoords,
                boxWidth: 700,
                boxHeight: 500
            });
    } else {
        $('#cropbox').Jcrop({
          /*aspectRatio: 1.3,*/
          onSelect: updateCoords,
          boxWidth: 700,
          boxHeight: 500
        },function(){
            jcrop_api = this;
        });
    }
});

function confirm_delete() {
  return confirm('Are you sure you want to delete?');
}

function updateCoords(c){
    $('#x').val(c.x);
    $('#y').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);
};

function get_browser() {
    var ua = navigator.userAgent,
        tem,
        M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if (/trident/i.test(M[1])) {
        tem =/\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE';
    }
    if (M[1] === 'Chrome') {
        tem = ua.match(/\bOPR\/(\d+)/)
        if (tem != null) {
            return 'Opera'
        }
    }
    M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
    if ((tem = ua.match(/version\/(\d+)/i)) !=null) {
        M.splice(1,1,tem[1]);
    }
    return M[0];
}

function rotate(direction){
    //showOverLay();
    $.ajax({
        type: "POST",
        url: base_url+'/Post/rotate/'+direction,
        data: $("#cropForm").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var d = new Date();
            var n = d.getTime();
            jcrop_api.setImage(base_url+'/assets/uploaded_images/'+$('#img').val()+'?='+n); 
            $.ajax({
                type: "POST",
                url: base_url+'post/getImages/'+$('#entry_id').val()+'/'+$('#field_id').val(),
                success: function(data)
                {
                    $('#documentUploads_'+$('#column').val()).html('<ul id="sortableDocuments_'+$('#column').val()+'" class="ul_child">'+data+'</ul>');

                    makesortable_<?php if(isset($adv_column)){ echo $adv_column; } ?>();
                }
            });
            //$('#overlay').remove();
        }
    }); 
}

function getAllArrowsPositions(){
    let topPositionNode1 = $('#node-1').css('top');
    let leftPositionNode1 = $('#node-1').css('left');
    let inlineStylesNode1 = $('#node-1').attr('style');

    let topPositionNode2 = $('#node-2').css('top');
    let leftPositionNode2 = $('#node-2').css('left');
    let inlineStylesNode2 = $('#node-2').attr('style');

    let topPositionNode3 = $('#node-3').css('top');
    let leftPositionNode3 = $('#node-3').css('left');
    let inlineStylesNode3 = $('#node-3').attr('style');

    let topPositionNode4 = $('#node-4').css('top');
    let leftPositionNode4 = $('#node-4').css('left');
    let inlineStylesNode4 = $('#node-4').attr('style');

    let allArrowsPositions = {
        'node-1': {
            'id': 'node-1',
            'top': topPositionNode1,
            'left': leftPositionNode1,
            'inlineStyles': inlineStylesNode1
        },
        'node-2': {
            'id': 'node-2',
            'top': topPositionNode2,
            'left': leftPositionNode2,
            'inlineStyles': inlineStylesNode2
        },
        'node-3': {
            'id': 'node-3',
            'top': topPositionNode3,
            'left': leftPositionNode3,
            'inlineStyles': inlineStylesNode3
        },
        'node-4': {
            'id': 'node-4',
            'top': topPositionNode4,
            'left': leftPositionNode4,
            'inlineStyles': inlineStylesNode4
        }
    };

    return allArrowsPositions;
}

$( document ).ready(function() {  
 
    $("#cropForm").submit(function(e) { 
        e.preventDefault();

        var l = Ladda.create(document.querySelector('#saveCropButton'));
        l.start();
        $.ajax({
            type: "POST",
            url: base_url+'Post/crop',
            data: $(this).serialize(), // serializes the form's elements.
            dataType:'json',
            success: function(data){
                l.stop();
                $('.bs-example-modal-lg').modal('hide');   

                $.ajax({
                    type: "POST",
                    url: base_url+'post/getImages/'+$('#entry_id').val()+'/'+$('#field_id').val(),
                    success: function(data)
                    {
                        $('#documentUploads_'+$('#column').val()).html('<ul id="sortableDocuments_'+$('#column').val()+'" class="ul_child">'+data+'</ul>');

                        makesortable_<?php if(isset($adv_column)){ echo $adv_column; } ?>();
                    }
                });       
            }
        });
        return false;
    });

    $("#frmImageArrow").submit(function(e) {
        e.preventDefault();
        const element = document.querySelector('.image-arrow-container');

        /*var svgElements = document.body.querySelectorAll('svg');
        svgElements.forEach(function(item) {
            item.setAttribute("width", item.getBoundingClientRect().width);
            item.setAttribute("height", item.getBoundingClientRect().height);
            item.style.width = null;
            item.style.height= null;
        });*/

        // html2canvas(element).then(function(canvas) {
        domtoimage.toPng(element).then(function(dataUrl){
            
            // const imgData = canvas.toDataURL('image/png');
            const imgData = dataUrl;

            let allArrowsPositions = getAllArrowsPositions();
            $('#frmImageArrow input[name="hdnImageData"]').val(imgData);
            $('#frmImageArrow input[name="hdnAllArrowsPositions"]').val(JSON.stringify(allArrowsPositions));
            console.log(JSON.stringify(allArrowsPositions));

            var l = Ladda.create(document.querySelector('#saveImageArrowButton'));
            l.start();
            $.ajax({
                type: 'POST',
                url: base_url+'Post/saveImageArrow',
                data: $("#frmImageArrow").serialize(), // serializes the form's elements.
                dataType:'json',
                success: function(response) {
                    if(response.status){
                        $('.image_arrow_modal').modal('hide');
                        $.ajax({
                            type: "POST",
                            url: base_url+'post/getImages/'+$('#entry_id').val()+'/'+response.data.field_id,
                            success: function(data)
                            {
                                $('#documentUploads_'+$('#column').val()).html('<ul id="sortableDocuments_'+$('#column').val()+'" class="ul_child">'+data+'</ul>');

                                makesortable_<?php if(isset($adv_column)){ echo $adv_column; } ?>();
                            }
                        });
                    }
                    l.stop();
                },
                error: function(xhr, status, error) {
                }
            });
            return false;
        }).catch(function (error) {
            console.error('oops, something went wrong!', error);
        });
        return false;
    });

    $(document).on("click", "#clockwise", function(e) { 
        e.preventDefault();
        rotate('clockwise');
    });
     $(document).on("click", "#anticlockwise", function(e) { 
        e.preventDefault();
        rotate('anticlockwise'); 
    });


    $(document).on('click', '.table_settings_save', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.table_settings_save'));
        l.start();
        e.preventDefault();

        var values = []; // Array to store input field values
        $('._field').each(function() {
          values.push($(this).val()); // Add input field values to the array
        });
        var ith = 0;
        $('.'+$('#table_header_table').val()).find('th').each(function( index ) {

            if(values[ith] !== undefined){
                let v = values[ith].replace(/(<([^>]+)>)/gi, "");
                $(this).html('<input type="text" style="max-width:35px;"><br>'+v);
            }
            ith++;
        });
        $.ajax({
            type: "POST",
            url: base_url+'table/Table/update_config',
            data: {columns: JSON.stringify(values), 'field': $('#table_header_id_field').val(), 'entry_id': $('#table_header_entry_id').val()}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                l.stop();
                console.log(data);
                toastr.success(data.msg);
                $("#table_settings").modal('hide');
            } 
        });
    });

});

</script>

<br>
<br>
<!--small modal-->
<div class="modal fade bs-example-modal-sm in warning" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">Warning</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            
        </div>
        <div class="modal-body other_user_warning">
            Warning, this entry is in use by another user!
        </div>
        <div class="modal-footer">
            <button class="btn btn-default pull-left" onclick="history.back()">Close</button>
        </div>
    </div>
  </div>
</div>

<!--small modal-->
<div class="modal fade bs-example-modal-sm in" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">Are you sure you want to Delete this <span id="fileType"></span>?</h4>
        </div>
        <div class="modal-footer">
        	<input type="hidden" id="deleteId">
        	<input type="hidden" id="fileTypeField">
            <button class="btn btn-default pull-left" data-dismiss="close" onclick="javascript:closeModal();">Cancel</button>
            <button class="btn btn-maroon pull-right" onclick="javascript:deleteImage();">Yes</button>
        </div>
    </div>
  </div>
</div>
<!--small modal end-->
<!--image modal-->
<div class="modal fade bs-example-modal-lg crop_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title cropTitle">Photo Editor</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>

        </div>
        <div id="successMessageCrop" class="alert alert-success" role="alert" style="display:none;">
        	Crop Successful
        </div>
        <div class="modal-body col-centered">
        <img id="cropbox"/>
        <form method="post" id="cropForm">
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
            <input type="hidden" id="img" name="img" />
            <input type="hidden" id="imgId" name="imgId" />

            <input type="hidden" id="entry_id" name="entry_id" value="<?php echo (isset($listing['ad_id'])) ? $listing['ad_id'] : '';?>"/>
            <input type="hidden" id="field_id" name="field_id" />
            <input type="hidden" id="column" name="column" />
            <!--
			<input type="submit" value="Crop Image"  class="btn btn-orange" id="cropButton"/> 
            -->
            <div style="padding-top:10px;">
            <button class="btn btn-orange ladda-button" data-style="expand-right" id="saveCropButton">
            	SAVE
        	</button>
            </div>
		</form>
			<div id="rotateButtons" style="float: right;top: -35px;position: relative;">
			    <button value="Rotate Anti-clockwise"  class="btn btn-grey" id="anticlockwise"><i class="fa fa-undo"></i></button>
	            <button value="Rotate clockwise"  class="btn btn-grey" id="clockwise"><i class="fa fa-repeat"></i></button>
        	</div>
            <div id="croppedImage" style="display:none;">
            	<img id="cropped">
            </div>
        </div>
    </div>
  </div>
</div>
<!-- <div class="modal fade bs-example-modal-lg image_arrow_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title cropTitle">Photo Editor</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>

        </div>
        <div class="modal-body col-centered">
            <form method="post" id="frmImageArrow">
                <div class="clsModalBodyImageArrow">
                </div>
            </form>
        </div>
    </div>
  </div>
</div> -->
<div class="modal modal-fullscreen1 fade image_arrow_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title cropTitle">Photo Editor</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <form method="post" id="frmImageArrow">
                <div class="clsModalBodyImageArrow">
                </div>
            </form>
        </div>
      </div>
    </div>
</div>
<!--image modal end-->

<!--image modal
<div class="modal fade send_msg_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        </div>
        <form method="post" id="send_msg_form">
        <div class="modal-body col-centered">
            

                <div class="container">

                    <div class="row">
                        <label class="col-sm-6 col-md-4 control-label">Email To:</label>
                                                  
                        <div class="col-md-6 col-md-8">
                            <input type="text" name="e_email_to" id="e_email_to" class="form-control"> 
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <label class="col-sm-6 col-md-4 control-label">Subject:</label>
                                                  
                        <div class="col-md-6 col-md-8">
                            <input type="text" name="e_subject" id="e_subject" class="form-control"> 
                        </div>
                    </div>

                    <br>
                    
                    <div class="row">
                        <label class="col-sm-6 col-md-4 control-label">Message:</label>
                                           
                        <div class="col-md-6 col-md-8">
                            <textarea name="e_message" id="e_message" class="form-control ckeditor" maxlength="400" rows="10"></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="e_slug" id="e_slug" class="form-control"> 
                    
                    <div class="dashed-grey"></div>

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Send</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </form>
    </div>
  </div>
</div>
image modal end-->


<!--table modal-->
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="table_settings" aria-hidden="true" id="table_settings"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title au_title">
                    Rename Columns
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button> 
            </div> 
            <div class="modal-body">

                <div id="table_settings_inputContainer">
                    <!-- Dynamic text input fields will be appended here -->
                </div>

            </div> 
            <div class="modal-footer">
                <input name="table_header_id_field" id="table_header_id_field" type="hidden">
                <input name="table_header_entry_id" id="table_header_entry_id" type="hidden">
                <input name="table_header_table" id="table_header_table" type="hidden">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary table_settings_save ladda-button" data-style="expand-right">Save</button>

            </div> 
        </div> 
    </div> 
</div>

<!--table column modal-->
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="table_columns" aria-hidden="true" id="table_columns"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title au_title">
                    How many Columns
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button> 
            </div> 
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    Changing the column count will reload the page, please ensure you've saved.
                </div>
                
                <label for="numOfInputs">Number of Inputs:</label>
                <input type="number" class="form-control table_columns_text" id="table_columns_num_cols" min="1" max="12">

                <div id="table_columns_inputContainer">
                    <!-- Dynamic text input fields will be appended here -->
                </div>

            </div> 
            <div class="modal-footer">

                <input name="table_columns_id_field" id="table_columns_id_field" type="hidden">
                <input name="table_columns_entry_id" id="table_columns_entry_id" type="hidden">
                <input name="table_columns_table" id="table_columns_table" type="hidden">
                <input name="table_columns_headers" id="table_columns_headers" type="hidden">
                <input name="table_columns_isEmpty" id="table_columns_isEmpty" type="hidden">
                <input name="table_columns_count" id="table_columns_count" type="hidden">
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary table_columns_save ladda-button" data-style="expand-right">Save</button>

            </div> 
        </div> 
    </div> 
</div>
<!--table modal end-->
<?php //} ?>

<script language="javascript">

$(document).ready(function() {

      // Add event listener for input change

      $('#table_columns_num_cols').on('input', function() {

        var isEmpty = parseInt($('#table_columns_isEmpty').val());

        var numOfInputs = parseInt($(this).val()); // Get the input value and parse it to an integer
        
        var columnsCount = parseInt($('#table_columns_count').val());

        if((isEmpty == 0) && (numOfInputs < columnsCount)){
            //the table is not empty and the columns are less that current, data loss will occur
            toastr.error('Action blocked: The table is not empty and the new column count is less that the current, data loss will occur');
        }else{

            let headers = JSON.parse($('#table_columns_headers').val());
            console.log(headers);

            
            var $inputContainer = $('#table_columns_inputContainer'); // Get the input container element
            $inputContainer.empty(); // Empty the container before appending new input fields
            if (numOfInputs > 0) {
              // Set a maximum limit of 10
              if (numOfInputs > 12) {
                numOfInputs = 12;
                $(this).val(12); // Update the input value to 10
              }
              for (var i = 0; i < numOfInputs; i++) {
                // Append new text input fields with class "form-control"
                $inputContainer.append('<br><input type="text" class="form-control table_columns_field" placeholder="Column Header" value="'+headers[i]+'"><br>');
              }
            }
        }
      });

    $(document).on('click', '.table_columns_save', function(e){
        var id = $(this).attr('data-id');

        var l = Ladda.create(document.querySelector('.table_columns_save'));
        l.start();
        e.preventDefault();

        var values = []; // Array to store input field values
        $('.table_columns_field').each(function() {
          values.push($(this).val()); // Add input field values to the array
        });

        $.ajax({
            type: "POST",
            url: base_url+'table/Table/update_config',
            //data: {columns: JSON.stringify(values), 'field': $('#table_columns_id_field').val()}, // serializes the form's elements.
            data: {columns: JSON.stringify(values), 'field': $('#table_columns_id_field').val(), 'entry_id': $('#table_columns_entry_id').val()}, // serializes the form's elements.
            dataType:'json',
            success: function(data){
                l.stop();
                console.log(data);
                toastr.success(data.msg);
                $("#table_columns").modal('hide');
                if (confirm("Page reload required for changes to take effect, reload now?") == true) {
                    window.location.reload();
                }
            } 
        });
    });
});

$(document).ready(function() {

    function lockRecord(recordId) {

        $.ajax({
            url: base_url+'/Recordaccess/lockrecord/' + recordId, // Replace with your actual URL
            type: 'POST',
            dataType: 'json',
            data: {formID: '<?php echo $formID;?>'},
            success: function(response) {
                if (response.status === 'success') {
                    console.log('Record locked successfully');
                    // Do something if the record is locked successfully
                } else {
                    console.log('Error: ' + response.message);
                    // Do something if an error occurred
                    $('.warning').modal('show');
                    $('.other_user_warning').html(response.message);
                    //alert(response.message);
                    $('btn').addClass('disabled');
                    
                }
            },
            error: function() {
                console.log('Error occurred');
            }
        });
    }

    function unlockRecord(recordId) {
        $.ajax({
            url: base_url+'/Recordaccess/unlockrecord/' + recordId, // Replace with your actual URL
            type: 'POST',
            dataType: 'json',
            data: {formID: '<?php echo $formID;?>'},
            success: function(response) {
                if (response.status === 'success') {
                    console.log('Record unlocked successfully');
                    // Do something if the record is unlocked successfully
                    if($('#force_timeout').val() == '1'){
                        saveAndNext();
                    }
                } else {
                    console.log('Error: ' + response.message);
                    // Do something if an error occurred
                }
            },
            error: function() {
                console.log('Error occurred');
            }
        });
    }
    
    // JavaScript code
    var recordId = ad_id; // Replace with the actual record ID
    var inactivityTimeout = 30 * 60 * 1000; // Inactivity timeout in milliseconds (e.g., 30 minutes)

    // Lock the record when the user opens the page
    lockRecord(recordId);

    var inactivityTimer = null;
    var countdownTimer = null;

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        clearTimeout(countdownTimer);

        inactivityTimer = setTimeout(function() {
            $('#force_timeout').val(1);
            unlockRecord(recordId);
        }, inactivityTimeout);

        // Start the countdown timer
        var remainingTime = inactivityTimeout / 1000;
        displayCountdown(remainingTime);

        countdownTimer = setInterval(function() {
            remainingTime--;
            displayCountdown(remainingTime);

            if (remainingTime <= 0) {
                clearInterval(countdownTimer);
            }
        }, 1000);
    }

    function displayCountdown(remainingTime) {
        var minutes = Math.floor(remainingTime / 60);
        var seconds = remainingTime % 60;

        // Display the countdown timer on the page
        $('#countdown').text('Time Left: ' + minutes + ' minutes ' + seconds + ' seconds');
    }

    // Call resetInactivityTimer on user activity events
    $(document).on('mousemove keydown', function() {
        resetInactivityTimer();
    });

    // Call unlockRecord when the user leaves the page
    $(window).on('beforeunload', function() {
        unlockRecord(recordId);
    });

    // Initial call to resetInactivityTimer
    resetInactivityTimer();

});
</script>
</div><!--end bodycontent-->
</div><!--end container-->

