<?php
//echo '00';
$ci =&get_instance();
//i put this in the header as its common on all pages
//although it would be better placed in the construct
$session_id = $this->session->userdata('session_guid');
//echo $session_id;
if($ci->session->userdata("us_id")!=''){
  $ci->Usermodel->setUpdateLoginEntry($ci->session->userdata("us_id"), $session_id);
}
//echo '11';
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <script language="javascript">
    var base_url = '<?php echo base_url();?>';
    var url = base_url;
  </script>
  <link rel="stylesheet" href="<?php echo base_url('public/css/app.css'); ?>" />
  <script src="<?php echo base_url('public/js/app.js');?>"></script>

  <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css');?>">
  <link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>

  <script src="<?php echo base_url('assets/js/main.js');?>"></script>
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Figtree:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <?php 
    $background = $login_background;
  ?>
  <style type="text/css">
    html{
      background-color: #7460ee;
      background: url('<?php echo $background;?>') no-repeat center center fixed; 
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }
  </style>
</head>
<body>
  <div class="container">
    <nav class="navbar navbar-inverse  navbar-default <?php if(!isset($custom_css)){?>transparent_header noShadow<?php }else{ ?>navbar-fixed-top<?php } ?>" role="navigation" <?php if(isset($custom_css)){?><?php } ?>>
        <!-- Brand and toggle get gr.ouped for better mobile display -->
        
        <div style="max-width:1111px;margin-left:auto;margin-right:auto;">
          <!--
          
          -->
          <?php if(isset($logged_in)){?>
          <div class="pull-right logout hidden-sm hidden-xs">
            <a href="<?php echo base_url('login/logout');?>">Log out</a>
          </div>
          <?php } ?>
          <div class="navbar-header bottomO">
            <button type="button" class="navbar-toggle" data-target="#navbar-collapse-1" data-toggle="collapse"> 
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            
          </div>
          <a class="navbar-brand" href="#"><img src="<?php echo base_url('assets/images/'.$logo);?>" class="logo-width"></a>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="<?php if(!isset($custom_css)){?>bottomO<?php } ?> noLeft collapse navbar-collapse" id="navbar-collapse-1">
            <?php if(isset($custom_css)){?>
            <ul class="nav navbar-nav navbar-left hidden-xs noLeft" >
              <li><a href="#" class="wimbledon white"></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" >

            </ul>
           <?php } ?>
          </div><!-- /.navbar-collapse -->
        </div>
    </nav>
  </div>
    <?php if(isset($custom_css)){?>
      <div class="main" style="height:100%;">
      <div class="container">
    <?php }else{ ?>
      <div class="main main-transparent" style="height:100%;">
      <div class="container">
    <?php } ?>