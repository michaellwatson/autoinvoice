<!-- <img src="<?php //echo base_url('assets/uploaded_images/image-arrow.png'); ?>"> -->
<input type="hidden" name="hdnEntryId" value="<?php echo $entry_id; ?>" />
<input type="hidden" name="hdnFieldId" value="<?php echo $field_id; ?>" />

<input type="hidden" name="hdnImageData" value="" />
<input type="hidden" name="hdnAllArrowsPositions" value="" />
<input type="hidden" name="imgId" value="<?php echo $imageInfo->up_id; ?>" />

<div class="image-arrow-container">
    <div class="node" id="draggable1">
        <div id="node-1" class="overlay-verical-1 ui-draggable ui-draggable-handle" style="top: 65px;left: 0px;">
            <!-- <h3 class="subtitle is-1">Node 1</h3> -->
        </div>
        <div class="overlay-vertical-2 draggable" style="top: 323px; left: -125px; width: 400px;"></div>
    </div>
    <div class="node" id="draggable2">
        <div id="node-2" class="overlay-verical-1 ui-draggable ui-draggable-handle" style="top: 150px;left: 200px;">
            <!-- <h3 class="subtitle is-1">Node 2</h3> -->
        </div>
        <div class="overlay-vertical-2 draggable" style="top: 382px; left: 134px; width: 280px;"></div>
    </div>

    <div class="node" id="draggable3">
        <div id="node-3" class="overlay-verical-1 ui-draggable ui-draggable-handle" style="top: 235px;left: 400px;">
            <!-- <h3 class="subtitle is-1">Node 3</h3> -->
        </div>
        <div class="overlay-vertical-2 draggable" style="top: 422px; left: 374px; width: 200px;"></div>
    </div>

    <div class="node" id="draggable4">
        <div id="node-4" class="overlay-verical-1 ui-draggable ui-draggable-handle" style="top: 320px;left: 600px;">
            <!-- <h3 class="subtitle is-1">Node 4</h3> -->
        </div>
        <div class="overlay-vertical-2 draggable" style="top: 445px; left: 599px; width: 150px;"></div>
    </div>

    <div id="svg-arrows">
    </div>

    <br /><br /><br /><br /><br /><br />
    <br /><br /><br /><br /><br /><br />
    <br /><br /><br /><br /><br /><br />
    <br /><br /><br /><br /><br /><br />

    <!-- <div id="bottomDivImageArrow">
        <div class="bottomDivItemImageArrow">
            <h3>High</h3>
        </div>
        <div class="bottomDivItemImageArrow">
            <h3>Medium</h3>
        </div>
        <div class="bottomDivItemImageArrow">
            <h3>Low</h3>
        </div>
    </div> -->
</div>
<br />
<div style="">
    <button class="btn btn-orange ladda-button" data-style="expand-right" id="saveImageArrowButton">
    	SAVE
	</button>
</div>