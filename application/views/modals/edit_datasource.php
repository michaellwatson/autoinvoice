<!--add edit ds values-->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editDatasource">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">Add/Edit Datasource</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            
        </div>
        <div class="modal-body">
        	<div id="listDSValues"></div>
        </div>
    </div>
  </div>
</div>  
<!--add edit ds values-->

<!--Delete modal-->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="deleteConfirmationModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Delete Datasource Item</h4>
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            
        </div>
        <div class="modal-body">
        <div id="successMessageDSItemDelete" class="alert alert-success" role="alert" style="display:none;"></div>
        <div id="warningMessageDSItemDelete" class="alert alert-danger" role="alert" style="display:none;"></div>
          Are you sure you wish to delete this datasource item?
          <br>
          <small>If the item is used in an entry, it cannot be deleted</small>
        </div>
        <div class="modal-footer">
          <button class="btn btn-maroon pull-right ladda-button" data-style="expand-right" id="deleteDSItemA">Confirm</button>
          <input type="hidden" id="currentDeleteRow">
        </div>
    </div>
  </div>
</div>
 <!-- end delete modal-->   