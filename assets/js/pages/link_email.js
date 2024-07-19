jQuery(document).ready(function() { 

  $('#add_link_form').on('submit', function(e){ 
    e.preventDefault();
    var data = $(this).serializeArray();
    var l = Ladda.create(document.querySelector('#save_email_link'));
      l.start();

      jQuery.ajax({
      type: "POST",
      url: url+'documents/save_email_link/',
      data: data,
      dataType:'json',
      success: function(data)
      {
        l.stop();
        if(data.status==1){
          //window.location.href = base_url+'job/list';
          toastr.success(data.msg);
        }else{
          toastr.error(data.msg);
        }
      }
    });
  })
  
});