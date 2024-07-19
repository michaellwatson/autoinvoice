<div class="container mt-5">
    <h2>Notifications</h2>
    <table id="notificationsTable" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Body</th>
                <th>Timestamp</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle">Modal title</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body" id="modalBody">
                <p id="modalTimestamp"></p>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#notificationsTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false, // Disabling the search functionality
            ajax: {
                url: base_url + "notifications/get_notifications_json",
                type: 'GET'
            },
            columns: [
                    { data: 'no_id' },
                    { data: 'no_notification_type' },
                    { 
                        "data": "no_notification_text",
                        "render": function(data, type, row) {
                            return abbreviateText(data, 50); // Abbreviate to 50 characters
                        }
                    },
                    {
                        data: 'no_time_stamp',
                        render: function (data) {
                            const date = new Date(data * 1000);
                            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                        }
                    },
                    { 
                        data: 'is_read',
                        render: function (data) {
                            return data == 1 ? 'Read' : 'Unread';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `<a href="#" class="notification-link" data-id="${data.no_id}">View</a>`;
                        }
                    }
            ],
            order: [[ 0, "desc" ]]  // Order by ID in descending order by default
        });

        // Handle the click event of the "View" link
        $('#notificationsTable tbody').on('click', '.notification-link', function (e) {
            e.preventDefault();
            let notificationId = $(this).data('id');
            showNotification(notificationId);
        });

        <?php if(!is_null($id)){ ?>
            showNotification(<?php echo $id;?>);
        <?php } ?>
    });

function abbreviateText(text, maxLength) {
    if(text.length > maxLength) {
        return text.substr(0, maxLength) + "...";
    } else {
        return text;
    }
}

function showNotification(notificationId){
    // AJAX call to fetch notification details
    $.ajax({
        url: base_url + 'Notifications/view_notification/' + notificationId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                // Populate and Open Modal
                $('#modalTitle').text(data.success.no_notification_type);
                $('#modalBody').html(data.success.no_notification_text);
                $('#modalTimestamp').text(new Date(data.success.no_time_stamp * 1000).toLocaleString());
                $('#notificationModal').modal('show');
            } else if(data.error) {
                // Handle Error
                alert(data.error);
            }
        },
        error: function() {
            // Handle general AJAX error
            alert('Failed to fetch notification details.');
        }
    });
}
</script>