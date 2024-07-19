<div class="container clsTestDiv clsWaitme<?php echo $field['adv_id']; ?>">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
        <div class="col-md-12 col-sm-12 text-left">
            <button type="button" class="btn btn-primary clsAddRepeatsection" id="idAddRepeatsection<?php echo @$field['adv_id']; ?>" data-adv_category="<?php echo @$field['adv_category']; ?>" data-adv_id="<?php echo @$field['adv_id']; ?>" data-ad_id="<?php echo isset($listing) && isset($listing['ad_id']) ? $listing['ad_id'] : ''; ?>">+ Add</button>
        </div>
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php
                if(0){
            ?>
                <?php echo $field['adv_text']?>
                <?php if($field['adv_required']==1){ ?>
                <span style="color:red;">*</span>:&nbsp;
                <?php } ?>
            <?php
                }
            ?>
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">
            <?php
                if(0){
            ?>
            <div id="field_<?php echo $field['adv_id']?>">

                <input type="text"  name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" class="form-control" value="<?php echo  $listing['ad_'.$field['adv_column']];?>" autocomplete="off" placeholder="">
                <span class="<?php echo $field['adv_column'];?>_status"></span>
            </div> 
            <?php
                }
            ?>
        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>
<?php
    if($ptBlocksMultipleDatas){
?>
            <div class="container clsWaitme1<?php echo $field['adv_id']; ?> mb-2">
                <div class="row">
<?php
        foreach ($ptBlocksMultipleDatas as $keyPtBlocksMultipleDatas => $valuePtBlocksMultipleDatas) {
?>
                    <div class="col-lg-1 col-md-2 col-sm-3 text-left">
                        <button type="button" class="btn btn-primary clsEditBalconyRepeat<?php echo $field['adv_id']; ?>" data-adv_id="<?php echo $field['adv_id']; ?>" data-pt_blocks_multiple_datas_id="<?php echo $valuePtBlocksMultipleDatas->id; ?>">Edit <?php echo $keyPtBlocksMultipleDatas+1; ?></button>
                    </div>
<?php
        }
?>
                </div>
            </div>
<?php
    }
?>
<?php
    if($ptBlocksMultipleDatas){
?>
            <div class="container clsWaitme2<?php echo $field['adv_id']; ?>">
                <div class="row">
<?php
        foreach ($ptBlocksMultipleDatas as $keyPtBlocksMultipleDatas => $valuePtBlocksMultipleDatas) {
?>
                    <div class="col-lg-1 col-md-2 col-sm-3 text-left">
                        <button type="button" class="btn btn-primary clsDeleteBalconyRepeat<?php echo $field['adv_id']; ?>" data-adv_id="<?php echo $field['adv_id']; ?>" data-pt_blocks_multiple_datas_id="<?php echo $valuePtBlocksMultipleDatas->id; ?>">Delete <?php echo $keyPtBlocksMultipleDatas+1; ?></button>
                    </div>
<?php
        }
?>
                </div>
            </div>
<?php
    }
?>
<script type="text/javascript">
    var formID = "<?php echo $formID ?>";
    var currentEffect = "roundBounce";
    function runWaitMe(el, num, effect){
        text = 'Please wait...';
        fontSize = '';
        switch(num){
            case 1:
                maxSize = '';
                textPos = 'vertical';
                break;
            case 2:
                text = '';
                maxSize = 30;
                textPos = 'vertical';
                break;
            case 3:
                maxSize = 30;
                textPos = 'horizontal';
                fontSize = '18px';
                break;
        }
        el.waitMe({
            effect: effect,
            text: text,
            bg: 'rgba(255,255,255,0.7)',
            color: '#000',
            maxSize: maxSize,
            waitTime: -1,
            source: 'img.svg',
            textPos: textPos,
            fontSize: fontSize,
            onClose: function (el) {}
        });
    }
    dynamicHideWaitmeLoaderFunctions['hideWaitmeLoader_<?php echo $field['adv_id']; ?>'] = function() {
        let clsWaitMeClass = '<?php echo 'clsWaitme'.$field['adv_id']; ?>';
        let clsWaitMe1Class = '<?php echo 'clsWaitme1'.$field['adv_id']; ?>';
        let clsWaitMe2Class = '<?php echo 'clsWaitme2'.$field['adv_id']; ?>';
        $(`.${clsWaitMeClass}`).waitMe('hide');
        $(`.${clsWaitMe1Class}`).waitMe('hide');
        $(`.${clsWaitMe2Class}`).waitMe('hide');
    };
    /*function hideWaitmeLoader(){
        let clsWaitMeClass = '<?php echo 'clsWaitme'.$field['adv_id']; ?>';
        let clsWaitMe1Class = '<?php echo 'clsWaitme1'.$field['adv_id']; ?>';
        let clsWaitMe2Class = '<?php echo 'clsWaitme2'.$field['adv_id']; ?>';
        $(`.${clsWaitMeClass}`).waitMe('hide');
        $(`.${clsWaitMe1Class}`).waitMe('hide');
        $(`.${clsWaitMe2Class}`).waitMe('hide');
    }*/
    // $(document).on('click', '.clsAddRepeatsection', function(event){
    $(document).on('click', '#idAddRepeatsection<?php echo $field['adv_id']; ?>', function(event){
        let clsWaitMeClass = '<?php echo 'clsWaitme'.$field['adv_id']; ?>';
        runWaitMe($(`.${clsWaitMeClass}`), 1, currentEffect);
        let advId = $(event.target).data("adv_id");
        let advCategory = $(event.target).data("adv_category");
        let adId = $(event.target).data("ad_id");
        
        // let newId = $(`[id^="pos_${advCategory}_repeat_"]`).length + 1;

        // let data = { advId, newId };
        let data = { advId, adId, formID };
        let type = 'post';
        let url = "<?php echo base_url('repeatsection/addRepeatsection') . '/' ?>";
        let dataType = 'json';
        $.ajax({
            url, type, data, dataType,
            success: function (response) {
                if(response.status){
                    $('#idModalBalconies').modal('show');
                    // $(`${response.data.previousElementId}`).after(response.data.html);
                    $('.clsRepeatBalconies').html(response.data.html);
                    $('.clsRepeatModalHeader').html(response.data.htmlModalHeader);
                    dynamicHideWaitmeLoaderFunctions['hideWaitmeLoader_<?php echo $field['adv_id']; ?>']();
                }
                else{
                    // toastr.error(response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(`jqXHR: ${jqXHR}`);
                console.log(`textStatus: ${textStatus}`);
                console.log(`errorThrown: ${errorThrown}`);
            }
        });
    });
    $(document).on('click', '.clsEditBalconyRepeat<?php echo $field['adv_id']; ?>', function(event){
        let clsWaitMe1Class = '<?php echo 'clsWaitme1'.$field['adv_id']; ?>';
        runWaitMe($(`.${clsWaitMe1Class}`), 1, currentEffect);
        let advId = $(event.target).data("adv_id");
        let ptBlocksMultipleDatasId = $(event.target).data("pt_blocks_multiple_datas_id");
        let data = { advId, ptBlocksMultipleDatasId, formID };
        let type = 'post';
        let url = "<?php echo base_url('repeatsection/editRepeatsection') . '/' ?>";
        let dataType = 'json';
        $.ajax({
            url, type, data, dataType,
            success: function (response) {
                if(response.status){
                    $('#idModalBalconies').modal('show');
                    $('.clsRepeatBalconies').html(response.data.html);
                    $('.clsRepeatModalHeader').html(response.data.htmlModalHeader);
                    dynamicHideWaitmeLoaderFunctions['hideWaitmeLoader_<?php echo $field['adv_id']; ?>']();
                }
                else{
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(`jqXHR: ${jqXHR}`);
                console.log(`textStatus: ${textStatus}`);
                console.log(`errorThrown: ${errorThrown}`);
            }
        });
    });
    $(document).on('click', '.clsDeleteBalconyRepeat<?php echo $field['adv_id']; ?>', function(event){
        let clsWaitMe2Class = '<?php echo 'clsWaitme2'.$field['adv_id']; ?>';
        runWaitMe($(`.${clsWaitMe2Class}`), 1, currentEffect);
        let advId = $(event.target).data("adv_id");
        let ptBlocksMultipleDatasId = $(event.target).data("pt_blocks_multiple_datas_id");
        let data = { advId, ptBlocksMultipleDatasId, formID };
        let type = 'post';
        let url = "<?php echo base_url('repeatsection/deleteRepeatsection') . '/' ?>";
        let dataType = 'json';
        $.ajax({
            url, type, data, dataType,
            success: function (response) {
                if(response.status){
                    dynamicHideWaitmeLoaderFunctions['hideWaitmeLoader_<?php echo $field['adv_id']; ?>']();
                    window.location.reload();
                }
                else{
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(`jqXHR: ${jqXHR}`);
                console.log(`textStatus: ${textStatus}`);
                console.log(`errorThrown: ${errorThrown}`);
            }
        });
    });
</script>