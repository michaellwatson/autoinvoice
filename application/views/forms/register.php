            <div class="row">

                <div class="col-md-12 ab_center_padding main-select-override">
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-8 text-center">
                    <!--
                    <span class="syscap">SYSCAP</span>
                    <br>
                    -->
                    <span class="wimbledon wimbledon_white">REGISTER</span>
                    <div style="clear:both;"></div>
                    <span class="body_copy">
                    Create an account below, then please request access from the administrator
                    <br>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <div class="purple_boxes login-top">
                        <div id="successMessageLogin" class="alert alert-success" role="alert" style="display:<?php 
                            if(isset($success)){
                                if($success==0){ 
                                    echo "none";
                                }else{
                                    echo "block";   
                                }
                            }else{
                                echo "none";    
                            }?>;margin-top:15px;padding-bottom:15px;">
                            <?php 
                            if(isset($msg)){
                                echo $msg;
                            }
                            ?>
                        </div>
                       <form class="form-horizontal form-register" role="form" method="post">
                            <div id="warningMessageLogin" class="alert alert-danger" role="alert" style="display:<?php 
                            if(isset($success)){
                                if($success==1){ 
                                    echo "none";
                                }else{
                                    echo "block";   
                                }
                            }else{
                                echo "none";    
                            }?>;margin-top:15px;">
                            <?php 
                            if(isset($msg)){
                                echo $msg;
                            }
                            ?>
                            </div>

                            <input type="text" class="form-control" value="" id="firstName" name="firstName" placeholder="FIRST NAME*" required="required" >
                            <br>
                            <br>
                            <input type="text" class="form-control" value="" id="surname" name="surname" placeholder="LAST NAME*" required="required" >
                            <br>
                            <br>
                            <input type="text" class="form-control" value="" id="company" name="company" placeholder="COMPANY">
                            <br>
                            <br>
                            <input type="text" class="form-control" value="" id="email" name="email"  required="required" placeholder="EMAIL*">
                            <br>
                            <br>
                            <input type="text" class="form-control" placeholder="USERNAME/NICKNAME*" id="username" name="username" required="required" >
                            <br>
                            <br>
                            <input type="password" class="form-control" value="" id="passwordAdd" name="passwordAdd" placeholder="PASSWORD*" required="required" >
                            <br>
                            <br>
                            <input type="password" class="form-control" value="" id="passwordConfirm" name="passwordConfirm" placeholder="CONFIRM PASSWORD*" required="required" >
                            <div class="mandatory_link">
                                <span class="mandatory">*MANDATORY FIELDS</span>
                            </div>
                            <button class="btn btn-success ladda-button" data-style="expand-right" id="submitButton" style="width:100%">REGISTER NOW</button>
                        </form>
                        
                    </div>
                    <div class="text-center social">
                            <a href="<?php echo base_url('login')?>" class="forgotten">ALREADY REGISTERED?</a>

                    </div>
                
                </div>
            </div>
        </div>

