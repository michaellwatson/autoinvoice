<?php
if( isset($id) && isset($ptBlocksVersionControlsData) ){ // edit form
?>
	<div class="card">
		<div class="card-body">
			<form class="row" name="frmVersionControl">
				<div class="col-md-12 mb-3">
					<label class="form-label">Version</label>
					<input type="text" class="form-control" name="txtVersion" value="<?php echo $ptBlocksVersionControlsData->version; ?>">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Implemented by</label>
					<input type="text" class="form-control" name="txtImplementedBy" value="<?php echo $ptBlocksVersionControlsData->implemented_by; ?>">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Revision Date</label>
					<input type="date" class="form-control" name="txtRevisionDate" value="<?php echo $ptBlocksVersionControlsData->revision_date; ?>">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Approved by</label>
					<input type="text" class="form-control" name="txtApprovedBy" value="<?php echo $ptBlocksVersionControlsData->approved_by; ?>">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Reason</label>
					<textarea class="form-control" name="txtReason" style="height: 100px;"><?php echo $ptBlocksVersionControlsData->reason; ?></textarea>
				</div>
				<div class="col-md-12">
					<div class="text-center">
						<button type="button" class="btn btn-primary clsSubmitVersionControl" data-id="<?php echo $ptBlocksVersionControlsData->id; ?>">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
}
else{ // add form
?>
	<div class="card">
		<div class="card-body">
			<form class="row" name="frmVersionControl">
				<div class="col-md-12 mb-3">
					<label class="form-label">Version</label>
					<input type="text" class="form-control" name="txtVersion">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Implemented by</label>
					<input type="text" class="form-control" name="txtImplementedBy">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Revision Date</label>
					<input type="date" class="form-control" name="txtRevisionDate">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Approved by</label>
					<input type="text" class="form-control" name="txtApprovedBy">
				</div>
				<div class="col-md-12 mb-3">
					<label class="form-label">Reason</label>
					<textarea class="form-control" name="txtReason" style="height: 100px;"></textarea>
				</div>
				<div class="col-md-12">
					<div class="text-center">
						<button type="button" class="btn btn-primary clsSubmitVersionControl">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
}
?>