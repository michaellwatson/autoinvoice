<div class="container">
    <div class="row" id="id_<?php echo $field['adv_id']?>">
      
        <label class="col-sm-6 col-md-6 control-label">
            <?php echo $field['adv_text']?>: 
        </label>
                            
        <div class="col-md-6 col-sm-6 text-left">

             <div id="field_<?php echo $field['adv_id']?>">
                <?php if(!empty($tokenRecord)){?>

                    <a href="<?php echo $this->config->item('client_portal_url');?>Login/token?token=<?php echo $tokenRecord->token;?>" target="_blank"><?php echo $this->config->item('client_portal_url');?>Login/token?token=<?php echo $tokenRecord->token;?></a>

                <?php }else{ ?>

                    <?php if(isset($listing['ad_id'])){?>
                    <a href="" name="<?php echo $field['adv_column'];?>" id="<?php echo $field['adv_column'];?>" class="btn btn-primary">Login to Client Portal as Client</a>
                    <?php }else{
                        echo "Create a client to use this feature";
                    } ?>

                <?php } ?>
            </div> 

        </div>
    
    </div>
    <div class="dashed-grey"></div>
</div>

<?php if(isset($listing['ad_id'])){?>
<script language="javascript">

    $( document ).ready(function() {
        
        $(document).on("click", "#<?php echo $field['adv_column'];?>", function(e) {
            e.preventDefault();
            //alert(base_url+"/tokengenerator/Tokengenerator/generate_token");
            // Send the data to the server using POST request

            $.ajax({
                type: "POST",
                url: base_url+"/tokengenerator/Tokengenerator/generate_token",
                data: {'entry_id' : <?php echo $listing['ad_id'];?> },
                dataType:'json',
                success: function(data){
                    window.location = '<?php echo $this->config->item("client_portal_url");?>Login/token?token='+data.token;
                } 
            });
            
        })

    });
</script>
<?php } ?>