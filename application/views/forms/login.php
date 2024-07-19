            <div class="row">

            <div class="col-md-12 ab_center_padding main-select-override">
            </div>
            
            <div class="col-md-2">
            </div>
            
            <div class="col-md-8 text-center text_shadow ">
                    <!--
                    <span class="syscap">SYSCAP</span>
                    <br>
                    -->
                    <span class="wimbledon wimbledon_white"></span>
                    <span class="body_copy">

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

                        <form class="form-signin text-center">

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
                            
                            <input type="text" class="form-control" placeholder="EMAIL / USERNAME" id="email" name="email">
                            <br>
                            <br>
                            <input type="password" class="form-control" placeholder="PASSWORD" id="password" name="password">
                            <br>
                            <br>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" checked>
                              <label class="form-check-label" for="flexCheckChecked">
                                Remember Me?
                              </label>
                            </div>
                            <button class="btn btn-success ladda-button" data-style="expand-right" id="submitButton" style="width:100%;">LOG IN</button>
                        </form>

                    </div>

                    <div class="text-center social">
                        <div class="social_padding"></div>
                        <a href="<?php echo base_url('login/forgot')?>" class="forgotten">OOPS I'VE FORGOTTEN MY PASSWORD</a>
                        <div class="social_padding"></div>
                        <a href="<?php echo base_url('login/resend')?>" class="forgotten">RESEND WELCOME EMAIL</a>
                    </div>
                </div>
            </div>
    