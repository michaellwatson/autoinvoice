<div class="contentpanel">
      
      <div class="row"><!-- col-md-6 -->
        <?php if(isset($default_table_name)){?>
        You are editing <?php echo $default_table_name;?> <a href="<?php echo base_url('admin/advertAdmin');?>?formID=0">click here to change it</a>
        <br>
        <br>
        <?php } ?>
        <div class="col-md-12">
          <?php if($depth==1){?>
          <a href="javascript:showAddField();" style="float:right;"><i class="fa fa-plus"></i></a>
          <?php } ?>
          <h5 class="subtitle mb5"><?php echo $categoryName?></h5>
          
          <div class="table-responsive">
          <?php if($depth==0){?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Form Section Name</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="catfields">
			<?php 
			$this->load->view('templates/lists/partials/categories');?>
            </tbody>
          </table>
          <?php }else{ ?>
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Field Type</th>
                <th>Text</th>
                <th>HTML</th>
                <th>(Post text)</th>
                <th>Tooltip</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody id="fields">
			<?php 
			$this->load->view('templates/lists/partials/fields');?>
            </tbody>
          </table>
          <?php } ?>
		  <!--
          <ul class="pagination pagination-split">
                <li class="disabled"><a href="#"><i class="fa fa-angle-left"></i></a></li>
                <li><a href="#">1</a></li>
                <li class="active"><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
          </ul>
          -->
          </div><!-- table-responsive -->
        </div><!-- col-md-6 -->
        
  </div>
        
    </div><!-- contentpanel -->
    
  </div>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="addEditField">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">Add/Edit a field in the <?php echo $categoryName?> section</h4>
        </div>
        <div class="modal-body">
        <div id="fieldWarningMessage" class="alert alert-danger" role="alert" style="display:none;"></div>
        <form id="saveFieldForm" class="form-horizontal form-bordered">
        <input type="hidden" value="" name="datasourceId" id="datasourceId">
        <input type="hidden"  name="category" value="<?php echo $categoryId?>" id="category">
        	<div class="panel-body panel-body-nopadding">
                <div class="form-group">
                  <label class="col-sm-4 control-label">Type:</label>
                  <div class="col-sm-8">
                    <select id="fieldType" name="fieldType" class="form-control mb15">
                      <option value="">Please Select</option>
					  <?php foreach($fieldsList as $f){?>
                      <option value="<?php echo $f['fi_id'];?>:<?php echo $f['fi_hasDatasource'];?>"><?php echo $f['fi_name'];?></option>
                      <?php } ?>
                    </select>
                    
                  </div>

                    <div class="col-md-6 col-md-offset-4" id="hasDatasource" style="display:none;">
                    <button type="button" class="btn btn-default ladda-button" id="pickDatasourceButton">Pick Datasource</button>
                    <button type="button" class="btn btn-default ladda-button" id="createDatasourceButton">Create Datasource</button>
                    </div>
             	</div>

                <div class="form-group">
                  <label class="col-sm-4 control-label">Display Text:</label>
                  <div class="col-sm-8">
                    <input type="text" name="text" id="text" class="form-control" />
                  </div>
             	</div>
             
                <div class="form-group">
                  <label class="col-sm-4 control-label">After Text:</label>
                  <div class="col-sm-8">
                    <input type="text" name="post_text" class="form-control" />
                  </div>
             </div>
             
                <div class="form-group">
                  <label class="col-sm-4 control-label">Tooltip (more info):</label>
                      <div class="col-sm-8">
                        <textarea name="info" id="info" class="form-control"></textarea>
                      </div>
            	 </div>
             
                <div class="form-group">
                  <label class="col-sm-4 control-label">Required</label>
                 
                    <div class="row">
                		<div class="col-md-6">
                          <!--<div class="ckbox ckbox-default">-->
                            <input type="checkbox" value="1" id="required" name="required" />
                            <!--<label for="checkboxDefault"></label>-->
                          </div>
                       </div>
                  </div>
                  
                  <div class="form-group">
                  <label class="col-sm-4 control-label">More info link:</label>
                      <div class="col-sm-8">
                        <input type="text" name="link" id="link" class="form-control" />
                      </div>
            	 </div>
                  
             	</div>
             
             	<div class="panel-body panel-body-nopadding">
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-success ladda-button" id="saveFieldButton">Save</button>
                    </div>
                </div>
             </div>
             </div>
             <input type="hidden" id="editField" name="editField">
        </form>     
        </div>
    </div>
  </div>
</div>
<!--pick ds-->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="pickDatasource">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">Pick A Datasource</h4>
            
        </div>
        <div class="modal-body">
        	<div id="listDS"></div>
        </div>
    </div>
  </div>
</div>  
<!--end pick ds-->

<!--add edit ds values-->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editDatasource">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">Add/Edit Datasource</h4>
            
        </div>
        <div class="modal-body">
        	<div id="listDSValues"></div>
        </div>
    </div>
  </div>
</div>  
<!--add edit ds values-->




<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="addEditDatasource">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">Create A Datasource</h4>
            
        </div>
        <div class="modal-body">
        <div id="dsSuccessMessage" class="alert alert-success" role="alert" style="display:none;"></div>
        <div id="dsWarningMessage" class="alert alert-danger" role="alert" style="display:none;"></div>
        <form id="saveDatasourceNameForm" class="form-horizontal form-bordered">
        	<div class="form-group">
                  <label class="col-sm-4 control-label">Datasource Name:</label>
                  <div class="col-sm-8">
                    <input type="text" name="dataSourceName" id="dataSourceName" class="form-control" />
                  </div>
             </div>
             <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-success ladda-button" id="saveDatasourceNameButton">Save</button>
                    </div>
                </div>
            </form>
            <form id="populateDatasourceForm" class="form-horizontal form-bordered" style="display:none;">
            <input type="hidden" id="dsId" name="dsId">
        	<div class="form-group">
                  <label class="col-sm-4 control-label">Datasource Name:</label>
                  <div class="col-sm-8">
                  	<p style="padding-top: 9px;">
                    	<span id="dsName"></span>
                    </p>
                  </div>
             </div>
             <div class="form-group">
                  <label class="col-sm-4 control-label">Values, seperated by commas</label>
                  <div class="col-sm-8">
                  	<textarea class="form-control" rows="5" name="values" id="values"></textarea>
                  </div>
             </div>
             <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                    <button type="submit" class="btn btn-success ladda-button" id="saveDatasourceValuesButton">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>
        


<script language="javascript">
function reloadFields(){
	$.ajax({
		type: "POST",
		url: '<?php echo base_url('admin/getFieldsCategory/'.$categoryId);?>',
		dataType:'html',
		success: function(data)
		{
			$('#fields').html(data);
		}		   
	});
}
$('.modal').on('show.bs.modal', function(event) {
    var idx = $('.modal:visible').length;
    $(this).css('z-index', 1040 + (10 * idx));
});
$('.modal').on('shown.bs.modal', function(event) {
    var idx = ($('.modal:visible').length) -1; // raise backdrop after animation.
    $('.modal-backdrop').not('.stacked').css('z-index', 1039 + (10 * idx));
    $('.modal-backdrop').not('.stacked').addClass('stacked');
});
var deleteButtonLadda = '';
$( document ).ready(function() {

	$(document.body).on('click', '.dsRadios' ,function(){
		//alert($(this).val());	
		$('#datasourceId').val($(this).val());
		$('#pickDatasource').modal('hide');
		$('.forRemove').remove();
		$('#hasDatasource').prepend('<span class="label label-primary forRemove">'+$(this).attr('data-id')+'</span>');
	});
	$(document.body).on('change', '.secondFields' ,function(){
		//alert($(this).val());
		$.ajax({
				   type: "POST",
				   url: base_url+'datasource/updateSecondField',
				   data: {'data':$(this).val()}, // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
						reloadFields();
				   }
		});
	});
	
	$('#createDatasourceButton').on('click',function(){	
		$('#addEditDatasource').modal();	
	});
	
	$('#pickDatasourceButton').on('click',function(){	
		$('#pickDatasource').modal();		
		$.ajax({
				   type: "POST",
				   url: base_url+'datasource/listDatasources',
				   data: $("#populateDatasourceForm").serialize(), // serializes the form's elements.
				   dataType:'html',
				   success: function(data)
				   {
					   $('#listDS').html(data);
				   }
		});
	});
	$('#fieldType').on('change',function(){
		var val = $(this).val().split(':');
		if(val[1]==1){	
			$('#hasDatasource').show();
		}else{
			$('#hasDatasource').hide();
		}
    //check if there's any module functionality
    $.ajax({
       type: "POST",
       url: base_url+'documents/check_module',
       data: { 'fieldType': $(this).val() }, // serializes the form's elements.
       dataType:'json',
       success: function(data)
       {
          
       }
    });


	});
	//alert('hit');
	
	$('#populateDatasourceForm').submit(function(e){
		e.preventDefault();
		var l = Ladda.create(document.querySelector('#saveDatasourceValuesButton'));
	 	l.start();
		//save the datasource values
		$.ajax({
				   type: "POST",
				   url: base_url+'datasource/saveDataSourceValues',
				   data: $("#populateDatasourceForm").serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
					   if(data.status==1){
						   $('#addEditDatasource').modal('hide');
						   $('#populateDatasourceForm').hide();
						   $('#saveDatasourceNameForm').show();
						   $('#dataSourceName').val('');
						   $('#dsSuccessMessage').hide();
						   $('#dsWarningMessage').hide();
						   /*
						   $('#dsSuccessMessage').html(data.msg);
						   $('#dsSuccessMessage').show();
						   $('#dsWarningMessage').hide();
						   //hide the original form
						   $('#saveDatasourceNameForm').hide();
						   $('#populateDatasourceForm').show();
						   //alert(data.id);
						   $('#dsId').val(data.id);
						   $('#datasourceId').val(data.id);
						   $('#dsName').html(data.name);
						   */
						   
					   }else{
						   $('#dsWarningMessage').html(data.msg);
						   $('#dsWarningMessage').show();
						   $('#dsSuccessMessage').hide();
					   }
					   l.stop();
				   }
		});
	});
	$('#saveDatasourceNameForm').submit(function(e) {
		e.preventDefault();
		var l = Ladda.create(document.querySelector('#saveDatasourceNameButton'));
	 	l.start();
		$.ajax({
				   type: "POST",
				   url: base_url+'datasource/saveDataSource',
				   data: $("#saveDatasourceNameForm").serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
					   if(data.status==1){
						   $('#dsSuccessMessage').html(data.msg);
						   $('#dsSuccessMessage').show();
						   $('#dsWarningMessage').hide();
						   //hide the original form
						   $('#saveDatasourceNameForm').hide();
						   $('#populateDatasourceForm').show();
						   //alert(data.id);
						   $('#dsId').val(data.id);
						   $('#dsName').html(data.name);
						   $('#datasourceId').val(data.id);
						   $('.forRemove').remove();
						   $('#hasDatasource').prepend('<span class="label label-primary forRemove">'+data.name+'</span>');
						   $('#values').val('');
						   //alert('hit');
					   }else{
						   $('#dsWarningMessage').html(data.msg);
						   $('#dsWarningMessage').show();
						   $('#dsSuccessMessage').hide();
					   }
					   l.stop();
				   }
		});
	});
	$("#saveFieldForm").submit(function(e) { 
	//alert('hit');
			e.preventDefault();
			//var l = Ladda.create($('#submitButton'));
			var l = Ladda.create(document.querySelector('#saveFieldButton'));
	 		l.start();
			$.ajax({
				   type: "POST",
				   url: base_url+'admin/saveField',
				   data: $("#saveFieldForm").serialize(), // serializes the form's elements.
				   dataType:'json',
				   success: function(data)
				   {
					   if(data.status==1){
						   $('#addEditField').modal('hide');
						   //i didn't use reset to keep the category value
						   $('#datasourceId').val('');
						   $('#fieldType').val(0);
						   //$('#fieldType').attr('disabled',false);
						   $('#text').val('');
						   $('#post_text').val('');
						   $('#info').val('');
						   $('#editField').val('');
						   $('#required').attr('checked',false);
						   
						   $('.forRemove').remove();
						   $('#hasDatasource').hide();
						   $('#link').val('');
						   reloadFields();
					   }else{
						   $('#fieldWarningMessage').html(data.msg);
						   $('#fieldWarningMessage').show();
					   }
					   l.stop();
				   }
		});
	})
});
function showAddField(id){
	if(id!==undefined){
		//alert('hit');
		$('#editField').val(id);
		$.ajax({
			type: "POST",
			url: base_url+'admin/getField/'+id,
			dataType:'json',
			success: function(data)
			{
				if(data.status==1){
					//alert(data.data.adv_field_type);
					$('#fieldType').val(data.data.adv_field_type+':'+data.data.fi_hasDatasource);
					//$('#fieldType').attr('disabled',true);
					$('#text').val(data.data.adv_text);
					$('#post_text').val(data.data.adv_post_text);
					$('#info').val(data.data.adv_info);
					$('#link').val(data.data.adv_link);
					//alert(data.data.adv_link);
					if(data.data.adv_required==1){
						$('#required').attr('checked',true);
					}
					if(data.data.fi_hasDatasource==1){
						$('#hasDatasource').show();
						$('.forRemove').remove();
						$('#hasDatasource').prepend('<span class="label label-primary forRemove">'+data.data.dsName+'</span>');	
						//alert(data.data.dsId);
						$('#datasourceId').val(data.data.dsId);
					}
				}
			}
		});	
	}
	$('#addEditField').modal();	
}


function serializeList(container)
{
  var str = ''
  var n = 0
  var els = container.find('tr')
  for (var i = 0; i < els.length; ++i) {
    var el = els[i]
    var p = el.id;
    console.log(p);
    var x = p.split('_');
    /*if (p != -1) {
      if (str != '') str = str + '&'
      str = str + el.id.substring(0, p) + '[]=' + (n + 1)
      ++n
    }*/
    str+=x[1]+',';
  }
  return str
}
$("#fields").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            //alert('hit');
            var order = serializeList($("#fields"));
            console.log(order);
            $.ajax({
              type: "POST", 
              dataType: "json", 
              url: base_url+"/admin/updateOrder",
              data: {'order':order},
              success: function(response) {

              }
            });
        }
});

$("#catfields").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            //alert('hit');
            var order = serializeList($("#catfields"));
            console.log(order);
            $.ajax({
              type: "POST", 
              dataType: "json", 
              url: base_url+"/admin/updateCatOrder",
              data: {'order':order},
              success: function(response) {

              }
            });
        }
});

$(document.body).on('click', '#categoryAddButton' ,function(e){
    e.preventDefault();
    //alert('hit');
    var id = $(this).attr('data-id');
    //alert('saveValueButton_'+id);
    var l = Ladda.create(document.querySelector('#categoryAddButton'));
    l.start();

    $.ajax({
        type: "POST",
        url: base_url+'/admin/saveCategory',
        data: {'categoryId': '<?php echo $categoryId?>', 'value': jQuery('#valueAdd').val()},
        dataType:'json',
        success: function(data){
            l.stop();
            location.reload();
        }
    });
});

$(document.body).on('click', '.deleteField' ,function(e){
  //alert($(this).attr('data-id'));
  var id = $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: base_url+'/admin/delete_field',
    data: {'field_id': $(this).attr('data-id')},
    dataType:'json',
    success: function(data){

      $('#sort_'+id).remove();
    }
  });
});

$(document.body).on('click', '.deleteDataSource' ,function(e){
  //alert($(this).attr('data-id'));
  var id = $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: base_url+'/admin/delete_datasource',
    data: {'datasource_id': id},
    dataType:'json',
    success: function(data){

      $('#datasource_'+id).remove();
    }
  });
});

$(document.body).on('click', '.delete-row' ,function(e){
  //alert($(this).attr('data-id'));
  e.preventDefault();
  var id = $(this).attr('data-id');
  $.ajax({
    type: "POST",
    url: base_url+'/admin/delete_category',
    data: {'category_id': id},
    dataType:'json',
    success: function(data){
      $('#cat_'+id).remove();
    }
  });
});

jQuery(window).load(function() {
  jQuery(document).on('click', '.ds_manual', function() {
    var id = $(this).attr('data-id');
    //alert(id);
    if($(this).is(':checked')) {
      jQuery("#vat_price_"+id).prop('disabled', false);
    }else{
      jQuery("#vat_price_"+id).prop('disabled', true);
    }
  });
});

</script>