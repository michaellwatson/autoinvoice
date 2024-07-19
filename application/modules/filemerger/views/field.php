<?php 
    $entry_id = 0;
    if(isset($listing)){

        // $entry_id = $listing['ad_id'];
        $entry_id = $listing['ad_id'] ?? null;

        if(isset($listing['ad_'.$field['adv_column']])){
            $value =  $listing['ad_'.$field['adv_column']];
        }
    }else{
        $value =  '';
    }

    $adv_column = $field['adv_column'];

    $field_id = $field['adv_id'];
    
?>
<style type="text/css">
.sortableDocuments_<?php echo $adv_column."_".$randomNumberFileMerger;?>{
    margin: 0px;
    padding: 0px;
}

.documentUploads_<?php echo $adv_column."_".$randomNumberFileMerger;?> ul li{
    display: inline;
    list-style-type: none;
    background-color: #fff;
    padding: 4px;
    margin-bottom: 10px;
}
</style>
<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>
            <?php if($field['adv_required']==1){ ?>
            <span style="color:red;">*</span>
            <?php } ?>:&nbsp;
        </label>

        <div class="col-md-6 col-sm-6">

            <div style="padding:15px;">

                <div class="progress active docBar">
                    <div class="progress-bar_<?php echo $adv_column."_".$randomNumberFileMerger;?> pdc_<?php echo $adv_column;?> progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;background-color:#00974A;">
                    </div>
                </div> 


                <div id="file-uploader-documents_<?php echo $adv_column."_".$randomNumberFileMerger;?>" style="padding-bottom:14px;">       
                    <noscript>          
                    <p>Please enable JavaScript to use file uploader.</p>
                    <!-- or put a simple form for upload here -->
                    </noscript>         
                </div>

                <div id="documentUploads_<?php echo $adv_column."_".$randomNumberFileMerger;?>" class="ul_parent">
                    <ul id="sortableDocuments_<?php echo $adv_column."_".$randomNumberFileMerger;?>" class="ul_child">
                    </ul>
                </div>

            </div>

        </div>

    </div>

</div>


<script language="javascript">

<?php
    $entry_id = $ad_id;

    $field_id = $field['adv_id'];
?>

/*
var entry_id    = <?php echo $entry_id;?>;
var adv_column  = "<?php echo $field['adv_column']; ?>";
var field_id    = <?php echo $field_id; ?>;
var formID      = <?php echo $formID;?>;
*/

var namespace<?php echo $field['adv_column']; ?> = {
    entry_id: <?php echo $entry_id; ?>,
    adv_column: "<?php echo $field['adv_column']."_".$randomNumberFileMerger; ?>",
    field_id: <?php echo $field_id; ?>,
    formID: <?php echo $formID; ?>
};

var a = setTimeout(function(){

    let getFileMergerData = {
        additionalFormType: '<?php echo $additionalFormType; ?>'
    };

    $.ajax({
        type: "POST",
        <?php
            if(isset($additionalFormType) && $additionalFormType == 'edit'){
        ?>        
        url: base_url+'/filemerger/get_files/' + namespace<?php echo $field['adv_column']; ?>.entry_id + '/' + namespace<?php echo $field['adv_column']; ?>.field_id + '/' + <?php echo $ptBlocksMultipleDatasId?>,
        <?php
            }
            else if(isset($additionalFormType) && $additionalFormType == 'add'){
        ?>        
        url: base_url+'/filemerger/get_files/0/0',
        <?php
            }
            else{
        ?>        
        url: base_url+'/filemerger/get_files/' + namespace<?php echo $field['adv_column']; ?>.entry_id + '/' + namespace<?php echo $field['adv_column']; ?>.field_id,
        <?php
            }
        ?>
        data: getFileMergerData,
        success: function(data) {
            $('#documentUploads_' + namespace<?php echo $field['adv_column']; ?>.adv_column).html('<ul id="sortableDocuments_' + namespace<?php echo $field['adv_column']; ?>.adv_column + '" class="ul_child">' + data + '</ul>');
            makesortable(namespace<?php echo $field['adv_column']; ?>.adv_column);
            save_button();
        }
    });

    var uploader = new qq.FileUploader({
        element: document.getElementById('file-uploader-documents_' + namespace<?php echo $field['adv_column']; ?>.adv_column),
        action: base_url+'/filemerger/upload_documents/',
        params: {'entry_id': namespace<?php echo $field['adv_column']; ?>.entry_id, 'field_id': namespace<?php echo $field['adv_column']; ?>.field_id, 'form_id': namespace<?php echo $field['adv_column']; ?>.formID},
        onComplete: function (id, fileName, responseJSON) {
            $('.qq-upload-list li').remove();
            $('.progress-bar_' + namespace<?php echo $field['adv_column']; ?>.adv_column).width(0);
            var str = responseJSON['filename'];
            str = str.replace(/\.(pdf)$/i, ""); 

            console.log(responseJSON['error']);

            <?php
                if(isset($additionalFormType) && $additionalFormType == 'add'){
            ?>
                let customName = '<?php echo @$customName ?>';
                if(customName != ''){
                    if ($('input[type="hidden"][name="hdnTempAddFileMerger"]').length > 0) {

                        const hiddenInputValue = $('input[type="hidden"][name="hdnTempAddFileMerger"]').val();

                        let idArray;
                        if(hiddenInputValue == ""){
                            idArray = [];
                        }
                        else{
                            idArray = JSON.parse(hiddenInputValue);
                        }
                        
                        idArray.push(responseJSON['doc_id']);
                        let hiddenInput = $('input[type="hidden"][name="hdnTempAddFileMerger"]');
                        hiddenInput.val(JSON.stringify(idArray));
                    }
                    else{
                    }
                }
            <?php
                }
            ?>

            if (typeof responseJSON['error'] === 'undefined') {

                <?php
                    if(isset($additionalFormType) && $additionalFormType == 'add'){
                ?>
                        let tempHdnTempAddImage = $('input[type="hidden"][name="hdnTempAddFileMerger"]').val();
                        let data = { hdnTempAddFileMerger: tempHdnTempAddImage, additionalFormType: getFileMergerData['additionalFormType'] };
                        let type = 'post';
                        let url = '<?php echo base_url('Filemerger/getFileMergerRepeaterAdd')?>/<?php echo $entry_id?>/<?php echo $field_id?>';
                        let dataType = 'json';
                        $.ajax({
                            url, type, data, dataType,
                            success: function (response) {
                                if(response.status){
                                    $('#documentUploads_<?php echo $adv_column."_".$randomNumberFileMerger;?>').html('<ul id="sortableDocuments_<?php echo $adv_column."_".$randomNumberFileMerger;?>" class="ul_child">'+response.data.html+'</ul>');
                                    //alert('hit');
                                    makesortable_<?php echo $adv_column."_".$randomNumberFileMerger;?>();
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
                            url = base_url+'/filemerger/get_files/' + namespace<?php echo $field['adv_column']; ?>.entry_id + '/' + namespace<?php echo $field['adv_column']; ?>.field_id + '/' + <?php echo $ptBlocksMultipleDatasId?>;
                <?php
                        }
                        else{
                ?>
                            url = base_url+'/filemerger/get_files/' + namespace<?php echo $field['adv_column']; ?>.entry_id + '/' + namespace<?php echo $field['adv_column']; ?>.field_id;
                <?php
                        }
                ?>
                        $.ajax({
                            type: "POST",
                            url,
                            success: function(data)
                            {
                                $('#documentUploads_<?php echo $adv_column."_".$randomNumberFileMerger;?>').html('<ul id="sortableDocuments_<?php echo $adv_column."_".$randomNumberFileMerger;?>" class="ul_child">'+data+'</ul>');
                                //alert('hit');
                                makesortable_<?php echo $adv_column."_".$randomNumberFileMerger;?>();
                            }
                        });
                <?php
                    }
                ?>

                /*$.ajax({
                    type: "POST",
                    url: base_url+'/filemerger/get_files/' + namespace<?php echo $field['adv_column']; ?>.entry_id + '/' + namespace<?php echo $field['adv_column']; ?>.field_id,
                    success: function(data) {
                        $('#documentUploads_' + namespace<?php echo $field['adv_column']; ?>.adv_column).html('<ul id="sortableDocuments_' + namespace<?php echo $field['adv_column']; ?>.adv_column + '" class="ul_child">' + data + '</ul>');
                        makesortable(adv_column);
                    }
                });*/

                let customName = '<?php echo @$customName ?>';
                // if(customName == "93[ad_photoupload_fraew]"){
                if(customName != ''){
                    if ($('input[type="hidden"][name="hdnBalconiesFileMerger"]').length > 0) {

                        const hiddenInputValue = $('input[type="hidden"][name="hdnBalconiesFileMerger"]').val();

                        let idArray;
                        if(hiddenInputValue == ""){
                            idArray = [];
                        }
                        else{
                            idArray = JSON.parse(hiddenInputValue);
                        }
                        
                        idArray.push(responseJSON['doc_id']);
                        let hiddenInput = $('input[type="hidden"][name="hdnBalconiesFileMerger"]');
                        hiddenInput.val(JSON.stringify(idArray));
                    }
                    else{
                    }
                }
            }

            
        },
        onSubmit: function(id, fileName){
            // Handle form submission here
        },
        onProgress: function(id, fileName, loaded, total){
            var width = $('.progress').width() * ((loaded / total) * 100 / 100);
            $('.progress-bar_' + namespace<?php echo $field['adv_column']; ?>.adv_column).width(width);
        },
        onCancel: function(id, fileName) {},
        onError: function(id, fileName, xhr) {}
    });
    $('#file-uploader-documents_' + namespace<?php echo $field['adv_column']; ?>.adv_column + ' > .qq-uploader > .qq-upload-button').prepend('UPLOAD');
    $('.qq-upload-button').addClass('btn');
    $('.qq-upload-button').addClass('btn-primary');

}, 1000);

if (typeof makesortable !== 'function') {
    function makesortable(adv_column) {
        $("#sortableDocuments_" + namespace<?php echo $field['adv_column']; ?>.adv_column).sortable({
            items: "li",
            cursor: 'move',
            opacity: 0.6,
            update: function () {
                var order = serializeList($("#sortableDocuments_" + namespace<?php echo $field['adv_column']; ?>.adv_column));

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/Post/orderDocuments", // Adjust the URL as needed
                    data: {'order': order },
                    success: function (response) {
                        deleteButton(namespace<?php echo $field['adv_column']; ?>.adv_column);
                        save_button();
                    }
                });
            }
        });
    }
}

$(document).on('click', '.btn_delete_document',function(e) {
    e.preventDefault();
    let advColumn = $(this).data("adv_column");
    if( advColumn == namespace<?php echo $field['adv_column']; ?>.adv_column ){
        if(confirm_delete()){
            var dataid = $(this).attr('data-id');

            //alert(dataid);
            $.ajax({
                type: "POST",
                url: base_url + 'Post/deleteDocuments',
                data: {'id':dataid, 'form_id': namespace<?php echo $field['adv_column']; ?>.formID},
                dataType:'json',
                success: function(data)
                {
                    $('#document_'+dataid).remove();
                }
            });
        }
    }
});

if (typeof serializeList !== 'function') {
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
}

</script>
