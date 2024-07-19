          <h3><?php echo $title;?></h3>   


          <div class="row">
            <div class="alert alert-secondary" role="alert">
                Choose one of your forms below, when records are added or edited you can schedule that to create alerts based in the criteria defined in the next step
            </div>
          </div>

          <div class="card">
          <div class="card-block">

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

              $this->load->view('alertmanager/partials/forms_widgets');
            
            ?>
            </tbody>
          </table>
          
          </div><!-- table-responsive -->
        </div><!-- col-md-6 -->
        </div>
        

