<link rel="stylesheet" type="text/css" href="<?php echo base_url()."assets/plugins/datatables-1.10.21/css/jquery.dataTables.min.css" ?>">
<script src="<?php echo base_url()."assets/plugins/datatables-1.10.21/js/jquery.dataTables.min.js" ?>"></script>
<br />
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-2">
                        <label class="form-label">Document Type</label>
                        <select class="form-control" name="category" onchange="filterByData();">
                            <option value="">Select Document Type</option>
                            <option value="32">SBA</option>
                            <option value="56">FRAEW</option>
                        </select>
                    </div>
                </div>
				<table id="list_table_one" class="table table-bordered table-hover">
				    <thead>
				    <tr>
						<th>#</th>
						<th>Started</th>
                        <th>Template</th>
						<th>Filename</th>
						<th>Status</th>
						<th>Action</th>
				    </tr>
				    </thead>
				    <tbody>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    var list_table_one;
    var list_url = "<?php echo base_url('Post/getQuoteReportDownloadsDataAjax/'); ?>";
    $(document).ready(function () {
        list_table_one = $('#list_table_one').DataTable({
            // "bPaginate": false,
            // "bLengthChange": false,
            // "bFilter": true,
            // "bInfo": false,
            // "bAutoWidth": false,
            // "searching": false,
            // "ordering": false,
            "processing": true,
            "serverSide": true,
            order: [[1, 'desc']],
            "ajax": {
                "url": list_url,
                "type": "POST",
                "data":function (data){
                    data.category = $("select[name='category']").val();
                    data.entryId = "<?php echo $entry_id; ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 5],
                    "orderable": false,
                },
                // {
                //     "targets": 5,
                //     "className": "text-right",
                // }
            ],
            pageLength: 10,
            responsive: true,
            "autoWidth": false,
            "oLanguage": {
                "sEmptyTable": "No data available"
            }
        });
    });
    function reload_table() {
        list_table_one.ajax.reload(null, false); //reload datatable ajax
    }
    function filterByData(){
        list_table_one.ajax.reload();
    }
</script>