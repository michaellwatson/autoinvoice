jQuery(document).ready(function() { 

    $('#add_staff_form').on('submit', function(e){ 
        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_staff'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'staff/save/',
            data: data,
            dataType:'json',
            success: function(data)
            {
                l.stop();
                if(data.status==1){
                    toastr.success(data.msg);
                }else{
                    toastr.error(data.msg);
                }
            }
        });

    })
});