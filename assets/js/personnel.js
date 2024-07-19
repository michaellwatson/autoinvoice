jQuery(document).ready(function() { 

    $('#add_personnel_form').on('submit', function(e){ 

        e.preventDefault();
        var data = $(this).serializeArray();
        var l = Ladda.create(document.querySelector('#save_personnel'));
        l.start();

        jQuery.ajax({
            type: "POST",
            url: url+'personnel/save/',
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