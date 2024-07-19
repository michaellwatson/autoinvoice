<?php 
$CI =& get_instance();
$CI->load->model('Quotesmodel','',TRUE);
$logo = $CI->Quotesmodel->getSetting('logo');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Report Page</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">



<script language="javascript">
    var base_url = '<?php echo base_url();?>';
    var url = '<?php echo base_url();?>';
</script>

<link rel="stylesheet" href="<?php echo base_url('public/css/app.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/extjs/extjs/ext-all.css');?>" />

<script src="<?php echo base_url('public/js/app.js');?>"></script>

<script src="<?php echo base_url('script/bootstrap');?>"></script>
<script src="<?php echo base_url('assets/js/ckeditor/ckeditor.js');?>"></script>
<script src="<?php echo base_url('public/js/typeahead.bundle.min.js');?>"></script>

<link href="<?php echo base_url('assets/css/timeline.css');?>" id="theme" rel="stylesheet">
<link href="<?php echo base_url('assets/css/misc.css');?>" id="theme" rel="stylesheet">

<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Figtree:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

<?php 
if(isset($no_header) && $no_header){
?>
  <style type="text/css">
    .page-wrapper {
        margin-left: 0px !important;
    }
  </style>  
<?php
}
?>

<style>
body {
  font-family: "Nunito" !important;
}
.cls-notification-button-mobile {
    position: fixed;
    bottom: -40px;
    right: 40px;
    display: block;
    width: 50px;
    height: 50px;
    line-height: 50px;
    background: #335dff;
    color: #fff;
    text-align: center;
    text-decoration: none;
    border-radius: 50%;
    opacity: 0;
    -webkit-transform: scale(0.3);
    -ms-transform: scale(0.3);
    transform: scale(0.3);
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 9;
    -webkit-transition: all 0.3s;
    transition: all 0.3s;
}
.cls-notification-button-mobile:focus {
    color: #fff;
}
.cls-notification-button-mobile.show {
    bottom: 40px;
    right: 40px;
    opacity: 1;
    -webkit-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1);
}
.cls-notification-button-mobile.show:hover {
    color: #fff;
    bottom: 30px;
    opacity: 1;
}
@media (min-width: 480px) {
    .cls-notification-button-mobile {
        display: none;
    }
}
.modal-full {
    min-width: 100%;
    margin: 0;
}
.modal-full .modal-content {
    min-height: 100vh;
}
</style>
</head>


<body class="fix-header fix-sidebar card-no-border">
    <a href="javascript:void(0);" id="idNotificationButtonMobile" class="cls-notification-button-mobile show">
      <i class="fa fa-bell-o" style="font-size:30px;color:#fff;padding-right: 0px;padding-top: 10px;"></i>
      <span class="position-absolute badge rounded-pill bg-danger" id="notification-count-mobile"></span>
    </a>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->

    <!--
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    -->
    
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php 
        if(!isset($no_header)){
        ?>
        <header class="topbar">
            <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'navbar-header-small' : '';?>">
                    <a class="navbar-brand" href="<?php echo base_url($this->config->item('default_page'));?>">
                        <b class="logo-icon">
                            <!-- dark Logo text -->
                            <?php $logo =  ($this->data['user']['sidebar_minimized'] == 1) ? 'logosmall.png' : $logo;?>
                            <img src="<?php echo base_url('assets/images/'.$logo);?>"  class="logo-switcher" style="max-width: <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? '105' : '30';?>%;"/>
                        </b>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto mt-md-0 ">
                        <!-- This is  -->
                        
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>

                        
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item">
                            <div id="notification-bell" style="position:relative; cursor:pointer; max-width: 32px;">
                                <i class="fa fa-bell-o" aria-hidden="true" style="font-size:30px;color:#fff;padding-top: 19px;"></i>
                                <!-- Badge for notification count -->
                                <!--
                                <span id="notification-count" style="position:absolute; top:0; right:0; background-color:red; color:white; border-radius:50%; padding: 5px 10px;">0</span>
                                -->
                                <span class="position-absolute badge rounded-pill bg-danger d-none" id="notification-count" style="left: 18px; top: 10px;"></span>
                                <!-- Dropdown for notification list, initially hidden -->
                                <div id="notification-list" style="display:none; position:absolute; top:30px; right:0; background-color:white; border:1px solid #ccc; width:300px; max-height:300px; overflow-y:auto;">
                                <!-- Notifications will be injected here by jQuery -->
                                </div>
                            </div>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <?php
                            $user = (array)$user;
                            if($user!==''){
                            ?>
                            <a class="nav-link dropdown-toggle nav-color waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo base_url('assets/images/users/1.jpg');?>" alt="user" class="profile-pic m-r-5" /><?php echo $user['us_firstName'];?> <?php echo $user['us_surname'];?></a>
                            <?php } ?>

                            <!--
                            <div class="dropdown-menu dropdown-menu-right animated flipInY show">
                                <ul class="dropdown-user">
                                    <li>
                                        <div class="dw-user-box">
                                            <div class="u-img"><img src="../assets/images/users/1.jpg" alt="user"></div>
                                            <div class="u-text">
                                                <h4>Steave Jobs</h4>
                                                <p class="text-muted">varun@gmail.com</p><a href="profile.html" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                                        </div>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="ti-user"></i> My Profile</a></li>
                                    <li><a href="#"><i class="ti-wallet"></i> My Balance</a></li>
                                    <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                            -->
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <?php } ?>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <?php 
        $CI =& get_instance();
        $CI->load->model('Usermodel');

        $links = FormsTablesmodel::where('ft_settings', 0)->where('ft_hidden', 0)->get();
        //print_r( $this->data['links']);
        //echo '##';
        $settings = FormsTablesmodel::where('ft_settings', 1)->get();
        
        if($user!==''){
            //print_r($user);
        if(!isset($hide_menu)){
            if(!isset($no_header)){
            ?>
                <aside class="left-sidebar <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'left-sidebar-small' : '';?>" <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'style="width:50px;"' : '';?>>
                    <!-- Sidebar scroll-->
                    <div class="scroll-sidebar">
                        <!-- Sidebar navigation-->
                        <nav class="sidebar-nav">

                            <ul id="sidebarnav">
                                <?php if($user['us_role']==1){?>
                                <li>
                                    <a href="<?php echo base_url('dashboard');?>" class="waves-effect"><i class="fa fa-tachometer m-r-10" aria-hidden="true"></i>Dashboard</a>
                                </li>
                                <?php } ?>
                                <?php
                                foreach($links as $l){

                                    $permission = $this->Usermodel->has_permission('update', $this->input->get('formID'));

                                        $read = $this->Usermodel->has_permission('read', $l->ft_id);
                                        $update = $this->Usermodel->has_permission('update', $l->ft_id);
                                        if($read||$update){
                                    ?>
                                        <li>
                                            <a href="<?php echo base_url('entries/show/1?formID='.$l->ft_id);?>" class="waves-effect"><i class="fa <?php echo $l->ft_icon;?> m-r-10" aria-hidden="true"></i><?php echo $l->ft_name;?></a>
                                        </li>
                                    <?php } 

                                } ?>

                                <?php

                                $edit_users = $this->Usermodel->has_permission('edit_users');
                                $view_users = $this->Usermodel->has_permission('view_users');

                                if($edit_users||$view_users){ ?>
                                <li>
                                    <a href="<?php echo base_url('user/show');?>" class="waves-effect"><i class="fa fa-user m-r-10" aria-hidden="true"></i>Users</a>
                                </li>
                                <?php } ?>

                                <li>
                                    <a class="has-arrow waves-effect" href="#" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a>
                                    <ul aria-expanded="false" class="collapse" style="height: 0px;">
                                        <?php
                                            foreach($settings as $s){
                                        ?>
                                            <li><a href="<?php echo base_url('Post/create_form/1?formID='.$s->ft_id);?>"> <?php echo $s->ft_name;?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <li>
                                    <a href="<?php echo base_url('login/logout');?>" class="waves-effect"><i class="fa fa-info-circle m-r-10" aria-hidden="true"></i>Logout</a>
                                </li>
                                <li>
                                    <a href="#" class="<?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'maximise_menu' : 'minimise_menu';?>">
                                        <i class="fa fa-arrow-circle-<?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'right' : 'left';?> min-icon" aria-hidden="true"></i> 
                                    </a>
                                </li>
                            </ul>
                        
                        </nav>
                        <!-- End Sidebar navigation -->
                    </div>
                    <!-- End Sidebar scroll-->
                </aside>
                <script>
                    
                    $(document).ready(function () {
                        /*
                        let src = $('.logo-switcher').attr('src');
                        //alert(src);
                        let src_small = 'logosmall.png';

                        let imageUrlArray = src.split("/");
                        imageUrlArray.pop();
                        let newImageUrl = imageUrlArray.join("/");
                        newImageUrl = newImageUrl+'/'+src_small;
                        //alert(newImageUrl);
                        */
                        let page = "<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>";
                        $(document).on("click",".minimise_menu",function() {
                            /*
                            $('.page-wrapper').css({'margin-left' : '50px'});
                            $('.left-sidebar').css({'width' : '50px'});
                            $('.navbar-header').css({'width' : '50px'});
                            $('.footer').css({'left' : '50px'});
                            $('.min-icon').removeClass('fa-arrow-circle-left').addClass('fa-arrow-circle-right');
                            $(this).removeClass('minimise_menu').addClass('maximise_menu');
                            $('.logo-switcher').attr('src', newImageUrl);
                            */

                            var url = base_url + "User/sidebar_toggle"; 
                            $.ajax({
                                   type: "POST",
                                   url: url,
                                   data: {'min': 1}, // serializes the form's elements.
                                   dataType:'json',
                                   success: function(data)
                                   {
                                        if(data.status==1){
                                            
                                            window.location.href = page;

                                        }else{
                                            toastr.error(data.msg);
                                        }
                                   }
                                 });

                        });

                        $(document).on("click",".maximise_menu",function() {
                            /*
                            $('.page-wrapper').css({'margin-left' : '240px'});
                            $('.left-sidebar').css({'width' : '240px'});
                            $('.navbar-header').css({'width' : '240px'});
                            $('.footer').css({'left' : '240px'});
                            $('.min-icon').removeClass('fa-arrow-circle-right').addClass('fa-arrow-circle-left');
                            $(this).removeClass('maximise_menu').addClass('minimise_menu');
                            $('.logo-switcher').attr('src', src);
                            */
                            var url = base_url + "User/sidebar_toggle"; 
                            $.ajax({
                                   type: "POST",
                                   url: url,
                                   data: {'min': 0}, // serializes the form's elements.
                                   dataType:'json',
                                   success: function(data)
                                   {
                                        if(data.status==1){
                                            
                                            window.location.href = page;

                                        }else{
                                            toastr.error(data.msg);
                                        }
                                   }
                                 });

                        });

                    });

                </script>
        <?php 
                }
            }
        } ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->

        <div class="page-wrapper <?php echo ($this->data['user']['sidebar_minimized'] == 1) ? 'page-wrapper-small' : '';?>">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">