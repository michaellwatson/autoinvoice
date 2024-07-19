<script type="text/javascript">
	$(document).ready(function () {
		
		let startDate = "<?php echo $startYmd ?>";
		let endDate = "<?php echo $endYmd ?>";
		let dataOfFilterWidget = {
			startDate, endDate
		};
		filterWidget(dataOfFilterWidget);

		$("input[name='daterange_filter']").daterangepicker({
			},
			function (start, end, label) {
				let startDate = start.format("YYYY-MM-DD").toString();
				let endDate = end.format("YYYY-MM-DD").toString();
				let dataOfFilterWidget = {
					startDate, endDate
				};
				filterWidget(dataOfFilterWidget);
			}
		);
	});
	function filterWidget(dataOfFilterWidget){
		let startDate = dataOfFilterWidget.startDate;
		let endDate = dataOfFilterWidget.endDate;
		
		let data = { startDate, endDate };
		let type = 'post';
		let url = "<?php echo base_url('Dashboard/getWidgetDataAjax') . '/' ?>";
		let dataType = 'json';
		$.ajax({
			url, type, data, dataType,
			success: function (response) {
				if(response.status){
					$('.clsWidgetHtml').html(response.data.html);
				}
				else{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
	}

	function startSend() {
	  let text = "This will send all the invoices, are you sure?";
	  if (confirm(text) == true) {
	    
	    var l = Ladda.create(document.querySelector('.sendInvoices'));
	    l.start();
	    
		let type = 'post';
		let url = "<?php echo base_url('Dashboard/send') . '/' ?>";
		let dataType = 'json';
		let data = '';
		$.ajax({
			url, type, data, dataType,
			contentType: false, cache: false, processData:false,
			success: function (response) {
				if(response.status){
					toastr.success(response.msg);
					$('.sendInvoices').attr('disabled', true);
					l.stop();
				}
				else{
					toastr.error(response.msg);
					l.stop();
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(`jqXHR: ${jqXHR}`);
				console.log(`textStatus: ${textStatus}`);
				console.log(`errorThrown: ${errorThrown}`);
			}
		});
		

	  } 
	}
</script>