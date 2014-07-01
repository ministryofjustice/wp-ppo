<div class="wrap" id= "wpsync-content">
    
    <h2 class="logo"><?php _e('Sidejump'); ?></h2>
    
    <div class="wrapper">

        <div class="mainWrapper">
            <div class="leftHolder">

                <div class="errorNumber">
                    <span><?php echo "Ooooopps......" ?></span>
                    <p> <?php echo "Something happened. Please try again. "; ?></p>
                </div> 
            </div>
            <div class="rightHolder">
                <div class="message"><p><?php echo $errMessage; ?></p></div>

                <div class="robotik"><img src="<?php echo plugins_url('sidejump/admin/assets/images/robotik.png'); ?>" alt="error"  id="robot"></div>
                <div class="clear"></div>
                <div class="back-link"><a href="javascript:history.go(-1)">Go Back</a></div>
            </div>

        </div>

    </div>
</div>
<!-- end .wrapper -->
