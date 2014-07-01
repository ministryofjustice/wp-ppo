(function($) {
    "use strict";

    $(function() {

           // add multiple select / deselect functionality
        jQuery("#allthemes").click(function() {
            jQuery('.tname').attr('checked', this.checked);
        });

        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        jQuery(".tname").click(function() {

            if (jQuery(".tname").length == jQuery(".tname:checked").length) {
                jQuery("#allthemes").attr("checked", "checked");
            } else {
                jQuery("#allthemes").removeAttr("checked");
            }

        });




        // add multiple select / deselect functionality
        jQuery("#allplugins").click(function() {
            jQuery('.pname').attr('checked', this.checked);
        });

        // if all checkbox are selected, check the selectall checkbox
        // and viceversa
        jQuery(".pname").click(function() {

            if (jQuery(".pname").length == jQuery(".pname:checked").length) {
                jQuery("#allplugins").attr("checked", "checked");
            } else {
                jQuery("#allplugins").removeAttr("checked");
            }

        });


    });

}(jQuery));

// Function for validating target instance details
function validate_details() {
    
    // Getting values
    var target_name = jQuery('#target_name').val();
    var target_url = jQuery('#target_url').val();
    var f_host = jQuery('#ftp_host').val();
    var f_uname = jQuery('#ftp_uname').val();
    var f_pswd = jQuery('#ftp_pswd').val();
    var f_rp = jQuery('#ftp_root_path').val();
   
    // Return false if empty otherwise show message.
     if(target_name == ""){
       jQuery("#target_name").focus();
       jQuery("#target_name").css({"border-color": "red"});
       return false;
    }
     jQuery("#target_name").css({"border-color": ""});
    if(target_url == ""){
       jQuery("#target_url").focus();
       jQuery("#target_url").css({"border-color": "red"});
       return false;
    }
     jQuery("#target_url").css({"border-color": ""});
    if(f_host == ""){
       jQuery("#ftp_host").focus();
       jQuery("#ftp_host").css({"border-color": "red"});
       return false;
    }
   jQuery("#ftp_host").css({"border-color": ""});
    if(f_uname == ""){
       jQuery("#ftp_uname").focus();
       jQuery("#ftp_uname").css({"border-color": "red"});
       return false;
    }
    jQuery("#ftp_uname").css({"border-color": ""});
    if(f_pswd == ""){
       jQuery("#ftp_pswd").focus();
       jQuery("#ftp_pswd").css({"border-color": "red"});
       return false;
    }
   jQuery("#ftp_pswd").css({"border-color": ""});
    if(f_rp == ""){
       jQuery("#ftp_root_path").focus();
        jQuery("#ftp_root_path").css({"border-color": "red"});
       return false;
       
    }
    jQuery("#ftp_root_path").css({"border-color": ""});
    
    return true;
}