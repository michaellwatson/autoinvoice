
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
      <?php echo $freeholder['ad_NameofFreeholder'];?>
      <br>
      <?php echo ($freeholder['ad_AddressLine1']) ? $freeholder['ad_AddressLine1'].'<br>' : '' ;?>
      <?php echo (trim($freeholder['ad_AddressLine2'])!=='') ? $freeholder['ad_AddressLine2'].'<br>' : '' ;?>
      <?php echo (trim($freeholder['ad_AddressLine3'])!=='') ? $freeholder['ad_AddressLine3'].'<br>' : '' ;?>
      <?php echo (trim($freeholder['region'])!=='') ? $freeholder['region'].'<br>' : '' ;?>
      <?php echo (trim($freeholder['ad_Postcode'])!=='') ? $freeholder['ad_Postcode'].'<br>' : '' ;?>
      <?php echo (trim($freeholder['ad_Name'])!=='') ? $freeholder['ad_Name'].'<br>' : '' ;?>


        <table class="table" style="background-color:#fff;">
          <thead class="thead-light">
            <tr>
              <th scope="col">Item Code</th>
              <th scope="col">Description</th>
              <th scope="col">Quantity</th>
              <th scope="col">Unit Price</th>
              <th scope="col">Disc %</th>
              <th scope="col">Account</th>
              <th scope="col">Tax Rate</th>
              <th scope="col">Customer</th>
              <th scope="col">Cash</th>
              <th scope="col">Amount GBP</th>
            </tr>
          </thead>
          <tbody>

            <?php 
              foreach($survey_processes_approved as $s){
                $houses = count($s->project->schemes);
              ?>
              <tr>
                <td></td>
                <td>
                  Type: RA
                  <br>
                  Project: <?php echo $s->project->ad_TagName;?>
                  <br>
                  Description: <?php echo $s->project->schemes[0]->ad_AddressLine1;?>
                  <br>
                  Account: <?php echo $s->freeholder->ad_NameofFreeholder;?>
                  <br>
                </td>
                <td><?php echo $houses;?></td>
                <td><?php echo $freeholder->ad_RouteApproval;?></td>
                <td></td>
                <td><?php echo $s->fibreclient->ad_NameofFibreClient;?></td>
                <td><?php echo $this->config->item('vat'); ?></td>
                <td><?php echo $s->freeholder->ad_NameofFreeholder;?></td>
                <td><?php echo ($houses*$freeholder->ad_RouteApproval);?></td>
                <td><?php echo ($houses*$freeholder->ad_RouteApproval)*$this->config->item('vat_multi');?></td>
              </tr>
            <?php } ?>

            <?php foreach($survey_processes_installed as $s){?>
              <tr>
                <td></td>
                <td>
                  Type: Install
                  <br>
                  Project: <?php echo $s->project->ad_TagName;?>
                  <br>
                  Description: <?php echo $s->project->schemes[0]->ad_AddressLine1;?>
                  <br>
                  Account: <?php echo $s->freeholder->ad_NameofFreeholder;?>
                  <br>
                </td>
                <td><?php echo count($s->project->schemes);?></td>
                <td><?php echo $freeholder->ad_Install;?></td>
                <td></td>
                <td><?php echo $s->fibreclient->ad_NameofFibreClient;?></td>
                <td><?php echo $this->config->item('vat'); ?></td>
                <td><?php echo $s->freeholder->ad_NameofFreeholder;?></td>
                <td><?php echo ($houses*$freeholder->ad_Install);?></td>
                <td><?php echo ($houses*$freeholder->ad_Install)*$this->config->item('vat_multi');?></td>
              </tr>
            <?php } ?>

          </tbody>
        </table>


      </div>
    </div>