
		<form class="form-horizontal" id="vat_form">
		
		<fieldset>

		<div class="form-group" style="margin-left:0px;margin-right: 0px;">

		<div id="warningMessage" class="alert alert-danger" role="alert" style="display:none;"></div>
		<div id="successMessage" class="alert alert-success" role="alert" style="display:none;"></div>

			<label class="col-sm-4 col-md-4 control-label" >Vat Percentage</label>
			<div class="col-md-4 col-sm-4">
				<input id="vatPercentage" name="vatPercentage" type="text" placeholder="Vat Percentage" class="form-control input-md" value="<?php echo $vatRate;?>">
			</div>

		</div>

		<div class="form-group" style="margin-left:0px;margin-right: 0px;">
            
            <label class="col-md-4 control-label" for="savebutton"></label>  
            <div class="col-md-4">
                <button class="btn btn-success ladda-button saveSettingsButton" type="submit" id="saveSettingsButton" data-style="expand-right" data-id="add">Save</button>
            </div>

        </div>

		</fieldset>

		</form>



