$(document).ready(function() {
    $(document).on("submit","#mapping_form",function(e) {
      e.preventDefault();
      //var l = Ladda.create($('#submitButton'));
      var l = Ladda.create(document.querySelector('#import_button'));
      l.start();

      var url = base_url + "import/save_mapping/"+ $('#form_id').val(); 
      $.ajax({
           type: "POST",
           url: url,
           data: $("#mapping_form").serialize(), // serializes the form's elements.
           dataType:'json',
           success: function(data)
           {
             if(data.status==1){
                  
                toastr.success(data.msg);
                window.location = base_url+'entries/show/1?formID='+$('#form_id').val();

             }else{
                
                toastr.error(data.msg);
             
             }
             l.stop();
           }
         });

      return false;
    });

});