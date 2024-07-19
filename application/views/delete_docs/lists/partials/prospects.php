<?php 

foreach($allUsers as $users){?>			
            <tr>
                <td><a href="view_contact/<?php echo $users['pr_id'];?>"><?php echo $users['pr_first_name']?> <?php echo $users['pr_surname']?></a></td>
                <td>
                    <select id="selectbasic" name="contactStatus" class="form-control selectbasic" data-id="<?php echo $users['pr_id'];?>">
                      <option value="0" <?php if($users['pr_contact_type']==0){?>selected="selected"<?php } ?>>Prospect</option>
                      <option value="1" <?php if($users['pr_contact_type']==1){?>selected="selected"<?php } ?>>Client</option>
                    </select>
                </td>
                <!--
                <td><?php //buying order status?></td>
                <td><?php //interested in / purchased?></td>
                -->
                <td>
                    <button class="btn btn-success" id="View" data-id="view">View</button> <button class="btn btn-success" id="View" data-id="edit">Edit</button>
                </td>
              </tr>
<?php } ?>