
          <?php //if($noNav!=true){ ?>
          <div class="row" style="padding:15px;">
              <a class="btn btn-primary add_document_template">Add Document Template</a>
          </div>
          <?php //} ?>
   
          <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Templates</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
			      <?php 

              $this->load->view('templates/lists/partials/tables_quote');
            
            ?>
            </tbody>
          </table>
          
          </div><!-- table-responsive -->
        </div><!-- col-md-6 -->
        



<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" id="addTableModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create a new Document Template</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="saveTableForm">

              <div id="fieldWarningMessage" class="alert alert-danger" role="alert" style="display:none;"></div>
              
              <div class="form-row">
                <div class="form-group col-md-4">
                  Form Name:
                </div>
                <div class="col-sm-8">
                  <input type="text" name="form_name" id="form_name" class="form-control">
                </div>
              </div>

          </form>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary ladda-button" id="saveTableButton" data-style="expand-right">Save</button>
      </div>
    </div>
  </div>
</div>


<script language="javascript">


$('.modal').on('show.bs.modal', function(event) {
    var idx = $('.modal:visible').length;
    $(this).css('z-index', 1040 + (10 * idx));
});
$('.modal').on('shown.bs.modal', function(event) {
    var idx = ($('.modal:visible').length) -1; // raise backdrop after animation.
    $('.modal-backdrop').not('.stacked').css('z-index', 1039 + (10 * idx));
    $('.modal-backdrop').not('.stacked').addClass('stacked');
});

$('.add_document_template').on('click',function(){	
	$('#addTableModal').modal('show');	
});


$(document).on('click', '#saveTableButton', function(){
  $("#saveTableForm").submit();
});

$("#saveTableForm").submit(function(e) { 
  //alert('hit');
      e.preventDefault();
      //var l = Ladda.create($('#submitButton'));
      var l = Ladda.create(document.querySelector('#saveTableButton'));
      l.start();
      $.ajax({
           type: "POST",
           url: '<?php echo base_url('Documents/saveTable');?>',
           data: $("#saveTableForm").serialize(), // serializes the form's elements.
           dataType:'json',
           success: function(data)
           {
            if(data.status==1){
              $('#editDatasource').modal('hide');
              location.reload();

            }else{
              $('#fieldWarningMessage').html(data.msg);
              $('#fieldWarningMessage').show();
            }
            l.stop();
          }
    });
});



$(document.body).on('click', '.delete-row' ,function(e){
  //alert($(this).attr('data-id'));
  e.preventDefault();
  var id = $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: '<?php echo base_url();?>/admin/delete_table',
    data: {'table_id': id},
    dataType:'json',
    success: function(data){
      $('#tab_'+id).remove();
    }
  });
});
</script>