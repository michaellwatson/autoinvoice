<div class="card">
	<div class="card-body">
		<h5 class="card-title">List Version Control</h5>
		<table class="table table-hover">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Version</th>
					<th scope="col">Implemented by</th>
					<th scope="col">Revision Date</th>
					<th scope="col">Approved by</th>
					<th scope="col">Reason</th>
					<th scope="col">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if($ptBlocksVersionControlsData){
						foreach ($ptBlocksVersionControlsData as $key => $value) {
				?>
							<tr>
								<th scope="row"><?php echo $key + 1; ?></th>
								<td><?php echo $value->version; ?></td>
								<td><?php echo $value->implemented_by; ?></td>
								<td><?php echo $value->revision_date; ?></td>
								<td><?php echo $value->approved_by; ?></td>
								<td><?php echo $value->reason; ?></td>
								<td>
									<button type="button" class="btn btn-primary clsEditVersionControl" data-id="<?php echo $value->id; ?>">Edit</button>
									<button type="button" class="btn btn-primary clsDeleteVersionControl" data-id="<?php echo $value->id; ?>">Delete</button>
								</td>
							</tr>
				<?php
						}
					}
					else{
				?>
							<tr>
								<th scope="row" colspan="7" class="text-center">No data available in table</th>
							</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	</div>
</div>