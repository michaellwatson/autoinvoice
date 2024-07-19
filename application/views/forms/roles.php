
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
   		<legend>Create/Edit a Role</legend>
        
        <form class="form-horizontal" role="form" id="add_role_form">

          <input type="hidden" name="id" value="<?php echo $id;?>">
          <div class="form-row">
            
            <div class="form-group col-md-2 ">
          
            <label class="control-label">Can this role see all content or only content assigned to it</label>

            </div>
            <div class="form-group col-md-10">

              <ul class="twocolumn">
                <li>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" value="1" name="role_can_see_all_content" autocomplete="nope">
                  </div>
                </li>
              </ul>
                
            </div>
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
                    if(isset($role_permissions_ids)){ 
                      if(in_array($p['id'], $role_permissions_ids)){ 
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
                if(isset($role_permissions_ids)){ 
                  if(in_array($p['id'], $role_permissions_ids)){ 
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


            <div class="form-group col-md-4 offset-md-2">
              <button class="btn btn-primary ladda-button" data-style="expand-right" id="save_role">
                <span class="ladda-label">Save</span>
              </button> 
            </div>

          </div>

        
        </div>


          