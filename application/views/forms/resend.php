<div class="row">
    <div class="col-md-12 ab_center_padding main-select-override"></div>
    <div class="col-md-2"></div>
    <div class="col-md-8 text-center">
        <!--
                    <span class="syscap">SYSCAP</span><br>
                    -->
        <span class="wimbledon wimbledon_white">RESEND WELCOME EMAIL</span>
        <div style="clear:both;"></div>
        <span class="body_copy">
            <br>
                <br>
                        LOST YOUR WELCOME EMAIL? NO PROBLEM, JUST FILL IN YOUR EMAIL ADDRESS BELOW AND WE WILL RESEND. PLEASE ENSURE YOU USE THE SAME EMAIL ADDRESS THAT YOU REGISTERED WITH.
                        
                    <br>
                        <br>
                        IF YOU'VE ALREADY AUTHENTICATED YOUR ACCOUNT, PLEASE 
                            <a href="
                                <?php echo base_url('login/forgot');?>" class="reset">RESET YOUR PASSWORD HERE
                            </a>.

                    
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <div class="purple_boxes login-top">
                            <div id="successMessageResend" class="alert alert-success" role="alert" style="display:none;margin-top:15px;padding-bottom:15px;"></div>
                            <form class="form-resend">
                                <div id="warningMessageResend" class="alert alert-danger" role="alert" style="display:none;margin-top:15px;"></div>
                                <input type="text" class="form-control" placeholder="EMAIL" id="email" name="email"  required="required">
                                    <br>
                                    <br>

                                        <button class="btn btn-success ladda-button" data-style="expand-right" id="resendButton" style="width:100%;">RESEND WELCOME EMAIL</button>
                                    </form>
                                </div>
                                <div class="text-center social">
                                    <a href="
                                        <?php echo base_url('login');?>" class="forgotten">LOGIN
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>