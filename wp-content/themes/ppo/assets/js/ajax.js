/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    $("#loading-spinner").show();
    update_tiles(PPOAjax.queryParams);
});

function update_tiles(queryParams, clearData) {
    $.ajax({
        type: 'POST',
        url: PPOAjax.ajaxurl,
        async: true,
        cache: false,
        data: {
            action: 'update_tiles',
            queryParams: queryParams
        },
        success: function(results) {
            if (clearData === true) {
                jQuery(".live-results").html(results);
                curPage=1;
            } else {
                jQuery(".live-results").append(results);
            }
            $contentLoadTriggered = false;
            $("#loading-spinner").hide();
        },
        error: function(error) {
            console.log(error);
            $("#loading-spinner").hide();
        }
    });
}