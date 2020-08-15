/**
 * Module Namespace
 */
var ManagementModule = {};


ManagementModule.go = function(ajaxUrl) {
    if (ajaxUrl) {
        $('#dataTable').DataTable({
            "ajax": ajaxUrl,
            "order": [[0, "desc" ]]
        });
    } else {
        $('#dataTable').DataTable();
    }

    $('#managmentVP').dblclick(function(e) {
        e.preventDefault();
        $('#managmentTHEP').fadeToggle();
        setTimeout(function() {
          $('#managmentTHEP').fadeOut();
        }, 3000);
    });

};