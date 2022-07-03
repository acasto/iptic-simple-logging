function msgrebar_getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]).replace(/["']/g,"");
        }
    }
};

function msgrebar_get_view( id, attr = "" ) {
    jQuery.ajax({
        type: "POST",
        async: true,
        url: ( MSGREBAR['admin_url']+'admin-ajax.php'),
        data: ({
            action: 'msgrebar_get_view',
            security: MSGREBAR.nonce,
            view: id,
            attr: attr,
        }),
        // target_id: target_id,
        success: function (msg) {
            jQuery('#rebarModal .modal-body').empty().append(msg);
            jQuery('#rebarModal').modal({
                //backdrop: false
            });
            jQuery('#rebarModalAccept').removeAttr('onclick').attr('onclick', 'msgrebar_check_box("'+attr+'")');
        }
    });
}

function msgrebar_check_box( attr ) {
    jQuery('input[value="'+attr+'"]').prop("checked", true);
    jQuery('#rebarModal').modal('toggle');
}



jQuery(document).ready(function() {


});