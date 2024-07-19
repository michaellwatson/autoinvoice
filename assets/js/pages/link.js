jQuery(document).ready(function() { 

  $('#add_link_form').on('submit', function(e){ 
    e.preventDefault();
    var data = $(this).serializeArray();
    var l = Ladda.create(document.querySelector('#save_link'));
      l.start();

      jQuery.ajax({
      type: "POST",
      url: url+'documents/save_link/',
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

$(document).on("change","#nature_of_instruction",function() {
  $('#standard_items').val('');
});

$(document).on("change","#standard_items",function() {
  $('#nature_of_instruction').val('');
});

});