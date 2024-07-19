
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Create/Edit a User</legend>
        
        <form class="form-horizontal" role="form" id="add_user_form">
          <?php 
          if(isset($user)){
            ?>
            <input type="hidden" id="id" name="id" value="<?php echo $user->us_id;?>">
            <?php
          }
          ?>
         <div class="form-row">
            <div class="form-group col-md-2">

              <label class="control-label">First Name</label>

            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" value="<?php if(isset($user->us_firstName)){ echo $user->us_firstName; }?>" autcomplete="false">
            
            </div>


            <div class="form-group col-md-2">

              <label class="control-label">Last Name</label>

            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="surname" name="surname" placeholder="Surname" value="<?php if(isset($user->us_surname)){ echo $user->us_surname; }?>" autcomplete="false">
            
            </div>


            
          </div>
          <div class="form-row">
          
            <div class="form-group col-md-2">

              <label class="control-label">Email</label>

            </div>
            <div class="col-md-4">
            
                <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?php if(isset($user->us_email)){ echo $user->us_email; }?>" autcomplete="false">
            
            </div>


              <div class="form-group col-md-2">
                <label class="control-label">Access</label>
              </div>
              <div class="form-group col-md-4">
                
                <select class="form-control" id="blocked" name="blocked">
                  <option>Please Select</option>

                  <option value="0" <?php if(isset($user->us_blocked)){ if($user->us_blocked==0){ echo 'selected="selected"'; } }?>>Not Blocked</option>
                  <option value="1" <?php if(isset($user->us_blocked)){ if($user->us_blocked==1){ echo 'selected="selected"'; } }?>>Blocked</option>

                </select>

              </div>
          
          </div>


          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Permission</label>

            </div>
            <div class="form-group col-md-10">
              <?php foreach($tables as $t){?>
                <h4><?php echo $t->ft_name;?></h4>

                  <ul class="twocolumn">
                  <?php foreach($t->permissions as $p){?>
                  <li>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="<?php echo $p['id'];?>" value="<?php echo $p['id'];?>" name="permissions[]" <?php 
                    if(isset($user_permissions)){ 
                      if(in_array($p['id'], $user_permissions)){ 
                        echo 'checked="checked"'; 
                      } 
                    } ?>>
                    <label class="form-check-label"><?php echo str_replace('_', ' ', $p['name']);?></label>
                  </div>
                  </li>
                  <?php } ?>
                  </ul>

              <?php } ?>

              <h4>Users</h4>

              <ul class="twocolumn">
              <?php foreach($permissions as $p){?>
              <li>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="<?php echo $p['id'];?>" value="<?php echo $p['id'];?>" name="permissions[]" <?php 
                if(isset($user_permissions)){ 
                  if(in_array($p['id'], $user_permissions)){ 
                    echo 'checked="checked"'; 
                  } 
                } ?>>
                <label class="form-check-label"><?php echo str_replace('_', ' ', $p['name']);?></label>
              </div>
              </li>
              <?php } ?>
              </ul>
            </div>

          </div>


          <div class="form-row">

            <div class="form-group col-md-2 ">
          
            <label class="control-label">Role</label>

            </div>
            <div class="form-group col-md-4">
              
               <select class="form-control" id="role" name="role">
                  <option>Please Select</option>
                  <?php foreach($roles as $r){?>
                  <option value="<?php echo $r['id'];?>" <?php if(isset($user->us_role)){ if($user->us_role==$r['id']){ echo 'selected="selected"'; } }?>><?php echo $r['name'];?></option>
                  <?php } ?>
                </select>

            </div>
            <div class="form-group col-md-4">
              <a href="#" class="btn btn-primary edit_role">Edit</a>
            </div>

          </div>

          <div class="form-row">


            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_user">
                <span class="ladda-label">Save</span>
              </button> 
            </div>

          </div>

        
        </div>

<script language="javascript">
jQuery(document).ready(function() { 

  
    jQuery('.edit_role').on('click', function(e){   
      window.location.href = url+'roles/'+$('#role').val();
    });

    jQuery('#add_user_form').find('#role').on('change', function(e){ 
        var id = $(this).val();

        jQuery.ajax({
          type: "POST",
          url: url+'roles/permissions/'+id,
          dataType:'json',
          success: function(data)
          {

            if(data.status==1){

              window.scrollTo(0, 0);

              $('input[type=checkbox]').each(function () {

                  var roles_array = jQuery.makeArray( data.msg );

                  //console.log(jQuery.inArray( parseInt($(this).val()), roles_array));
                  //console.log($(this).val());
                  //console.log(data.msg);
                  if(jQuery.inArray( parseInt($(this).val()), roles_array)!==-1){
                      $(this).prop('checked', true);
                  }else{
                      $(this).prop('checked', false);
                  }
              });

            }else{
              //alert(data.msg);
              toastr.error(data.msg);
            }

          }
        });

    });

});
</script>