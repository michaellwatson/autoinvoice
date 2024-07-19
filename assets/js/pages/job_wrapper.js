  $( document ).ready(function() {

  	$(document).on('click', '.archive', function(){
        var l = Ladda.create( document.querySelector('.archive'));
        var id = $(this).attr('data-id');
        l.start();

        $.ajax({
            'url': url+'job/archive',
            'method': 'POST',
            'dataType':'json',
            'data': {'id': id, 'archive': $('#archive').val()}
        }).done(function (data) {
            l.stop();
            if(data.status==1){
            	toastr.success(data.msg);
            	window.location.reload();
            }else{
            	toastr.warning(data.msg);
            }
        });
    
    });

    $(document).on('click', '.issue', function(){
        var tableid = $(this).attr('data-tableid');
        var entryid = $(this).attr('data-entryid');

        $('#iss_entryid').val(entryid);
        $('#iss_tableid').val(tableid);
        
        $.ajax({
            'url': url+'messages/get_by_document/'+tableid,
            'method': 'POST',
            'dataType':'json',
        }).done(function (data) {
            $('#iss_subject').val(data.subject);
            $('#iss_message').val(data.message);
            $('#issueModal').modal('show');
        });
    });

    $(document).on('click', '.send_document', function(){
        var l = Ladda.create( document.querySelector('.send_document'));
        l.start();

        var data = $('#email_send').serializeArray();

        //$('#iss_entryid').val(entryid);
        //$('#iss_tableid').val(tableid);

        $.ajax({
            'url': url+'messages/send_message?formID='+$('#iss_tableid').val(),
            'method': 'POST',
            'dataType':'json',
            'data': data
        }).done(function (data) {
            l.stop();
            if(data.status==1){
              $('#issueModal').modal('hide');
            }
        });

    });

    
    $(document).on('change', '#pick_email', function(){
      
      var tokens = $('#email_list').tokenfield('getTokens');
      console.log(tokens);

      var email_list = Array();
      for (index = 0; index < tokens.length; ++index) {
          email_list.push(tokens[index].value);
      }

      var val         = $(this).val();
      if(email_list.indexOf(val)===-1){
        
        /*
        var email_list  = $('#email_list').val();
        $('#email_list').val(email_list+','+val);
        $('#email_list').tokenfield()
        */
        $('#email_list').tokenfield('createToken', val);
      }
    });
});