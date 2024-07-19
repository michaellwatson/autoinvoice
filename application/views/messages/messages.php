<div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <fieldset>
      <legend>Messages</legend>

        <div class="card">
        <div class="card-block">

          <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Message</th>
                <th>Slug</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="messagesList">
            <?php foreach($messages as $m){?>			
                <form method="post" class="message_form">
                  <tr>
                      <td>
      				          <input type="hidden" name="id" id="id" value="<?php echo $m['me_id']?>">
      				          <input type="text" name="subject" id="subject" value="<?php echo $m['me_subject']?>" style="width:100%;" class="form-control">
      				        </td>
                      <td>
                        <textarea name="message" id="message" class="span12" rows="6" style="width:100%;"><?php echo $m['me_msg']?></textarea>
                      </td>
                      <td>
                        <?php echo $m['me_slug']?>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-success ladda-button" type="submit" id="saveButton_<?php echo $m['me_id']?>" data-style="expand-right">Save</button>
                      </td>
                  </tr>
                </form>
    			   <?php } ?>
            </tbody>
          </table>
              <div id="pagination">
              </div>
          </div><!-- table-responsive -->

          </div>
          </div>
          
        </div><!-- col-md-6 -->
        
        </fieldset>
      </div>
</div>
        
