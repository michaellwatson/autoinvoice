<br>
<?php 
    //if($permissions['role']==1){ 
    ?>
    <!--timeline-->
    <div class="col-md-8"> 
        <!--timeline-->
          <div class="timeline-centered">
          <?php 
          foreach($events as $e){ ?>
              <article class="timeline-entry">
                 <div class="timeline-entry-inner">
                    <time class="timeline-time" datetime="2014-01-10T03:45">
                    <span><?php echo date('M d, Y', strtotime($e['created_at']));?></span> <!--<span>x days ago</span>-->
                    </time>

                    <div class="timeline-icon <?php if($e->status==1){?>timeline_success<?php }else if($e->status==2){?>timeline_warning<?php }else{ ?>timeline_primary<?php } ?>">
                      <i class="entypo-feather"></i>
                    </div>
                    <div class="timeline-label">
                      <h2><?php echo date('H:i A', strtotime($e['created_at']));?></h2>
                      <p>
                          <?php echo $e->note;?>
                          <br>
                          <small><?php echo $e->user->us_firstName;?> <?php echo $e->user->us_surname;?></small>
                      </p>
                    </div>

                 </div>
              </article>
          <?php } ?>
          </div>
      <!--timeline-->
<?php //} ?>