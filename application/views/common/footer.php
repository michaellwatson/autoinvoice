    
    </div>
    <div class="credit" style="position: absolute;bottom: 0; right:0px; cusrosr:pointer;" title="Login Screen Image Credit"><?php echo $login_background_credit;?></div>
	
  </div>

</body>
</html>
<script language="javascript">
$(document).ready(function() {
    //$('.main').css({'height':screen.height});
    $('.main').css({'height':$(document).height()});
    $( window ).resize(function() {
	  $('.main').css({'height':$(document).height()});
	  	<?php if(isset($custom_css)){?>
	  	if($(window).width() < 960){
		   $('.navbar-toggle').css({'display':'block'});
		}else {
		   $('.navbar-toggle').css({'display':'none'});
		}
		<?php } ?>
	})
	<?php if(isset($custom_css)){?>
    if($(window).width() < 960){
		$('.navbar-toggle').css({'display':'block'});
	}else {
		$('.navbar-toggle').css({'display':'none'});
	}
	<?php } ?>
});
</script>