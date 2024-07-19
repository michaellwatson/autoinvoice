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

</head>


<body class="fix-header fix-sidebar card-no-border">
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
        <header class="topbar">
            <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo base_url($this->config->item('default_page'));?>">
                        <span>
                            <!-- dark Logo text -->
                            <img src="<?php echo base_url('assets/images/'.$logo);?>"  class="" style="max-width: 100%;"/>
                        </span>
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
                        <!--
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item hidden-sm-down">
                            <form class="app-search p-l-20">
                                <input type="text" class="form-control" placeholder="Search for..."> <a class="srh-btn"><i class="ti-search"></i></a>
                            </form>
                        </li>
                        -->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <?php
                            $user = (array)$user;
                            if($user!==''){
                            ?>
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
        ?>
        <aside class="left-sidebar">
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
                            
                            $create = $this->Usermodel->has_permission('create', $l->ft_id);
                            if($create){
                            ?>
                                <li>
                                    <a href="<?php echo base_url('Post/create_form/1?formID='.$l->ft_id);?>" class="waves-effect"><i class="fa <?php echo $l->ft_icon;?>  m-r-10" aria-hidden="true"></i>Add <?php echo $l->ft_name;?></a>
                                </li>
                            <?php } ?>
                            <?php
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
                        
                    </ul>
                
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <?php 
            }
        } ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">