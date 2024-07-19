			</div>
			</div>

		</div>
	</div>
	<div class="modal fade" id="idModalNotificationMobile">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body clsModalBodyNotificationMobile">
				</div>
			</div>
		</div>
	</div>
	<?php  if(isset($no_header) && !$no_header){ ?>
        <footer class="footer text-center <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'footer-small' : '';?>">

            Powered By <a href="https://rowe.ai" target="_blank"><img src="<?php echo base_url('assets/images/logo-black.png');?>" style="width:100px;"></a> © <?php echo date('Y');?>

        </footer>
    <?php } ?>

    <!--Innerwrapper Start--> 
<script src="<?php echo base_url('public/js/app.me.js');?>"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script type="text/javascript">
	function truncate(input) {
	   if (input.length > 5) {
	      return input.substring(0, 5) + '...';
	   }
	   return input;
	};
	$(document).ready(function(){
	    // Function to fetch and update notification count and list
	    function updateNotifications() {
	        $.ajax({
	            url: base_url+'Notifications/unread_count', // Specify URL to get notification count
	            type: 'GET',
	            dataType: 'json',
	            success: function(data) {
	            	if(parseInt(data.unread_count)>0){
						
						$('#notification-count').text(data.unread_count);
						$('#notification-count').removeClass('d-none');

						$('#notification-count-mobile').text(data.unread_count);
						$('#notification-count-mobile').removeClass('d-none');
	            	
	            	}else{

	            		$('#notification-count').addClass('d-none');

	            		$('#notification-count-mobile').addClass('d-none');
	            	}
	                
	            },
	            error: function(err) {
	                console.error("Failed to fetch notification count: ", err);
	            }
	        });

	        $.ajax({
	            url: base_url+'Notifications/unread_list', // Specify URL to get notification list
	            type: 'GET',
	            dataType: 'json',
	            success: function(data) {
	                // Assume each notification object has 'title' and 'body' properties
	                let notificationHtml = '';
	                data.notifications.forEach(notif => {
	                	notificationHtml += '<a href="'+base_url+'/notifications/view/'+notif.no_id+'">'
	                    notificationHtml += '<div style="padding:10px; border-bottom:1px solid #ccc;">';
	                    notificationHtml += '<strong>' + capitalizeFirstLetter(notif.no_notification_type) + '</strong><br>';
	                    notificationHtml += truncate(notif.no_notification_text);
	                    notificationHtml += '<br><small>'+notif.us_firstName+' '+notif.us_surname+'</small>';
	                    notificationHtml += '</div></a>';
	                });
	               	notificationHtml += '<span><a href="'+base_url+'notifications/view" class="view-all-btn">View All Notifications</a></span>';
	                $('#notification-list').html(notificationHtml);
	                $('.clsModalBodyNotificationMobile').html(notificationHtml);
	            },
	            error: function(err) {
	                console.error("Failed to fetch notification list: ", err);
	            }
	        });
	    }
	    
	    // Initial fetch and update of notifications
	    updateNotifications();

	    // Update notifications at a regular interval (e.g., every 10 seconds)
	    setInterval(updateNotifications, 30000);

	    // Toggle notification list visibility on bell click
	    $('#notification-bell').click(function() {
	        $('#notification-list').toggle();
	    });

	    var btn = $('#idNotificationButtonMobile');
	    btn.on('click', function(e) {
	        e.preventDefault();
	        $('#idModalNotificationMobile').modal('show');
	    });
	});
	function capitalizeFirstLetter(string) {
    	return string.charAt(0).toUpperCase() + string.slice(1);
	}
</script>

</body>
</html>