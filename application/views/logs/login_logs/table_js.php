if ($('#tblLogin_logs').length && tabClicked === "tab-login_log") {
if(typeof dTable['tblLogin_logs'] !=='undefined'){
$("#tab-login_log").addClass("active");
dTable['tblLogin_logs'].ajax.reload(null,true);
}
else{
dTable['tblLogin_logs'] = $('#tblLogin_logs').DataTable({
"pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $billing_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Login Log'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('ActivityLogs/get_login_log_list'); ?>",
    "dataType": "json",
    "type": "POST",
    },
    "footerCallback": function (tfoot, data, start, end, display){
        /*
    var api = this.api();
    var amount_page = api.column(5, {page: 'current'}).data().sum();
    var amount_overall = api.column(5).data().sum();
    $(api.column(5).footer()).html(curr_format(round(amount_page,2)) );
    */

    
    },

    "columns": [
    {"data": "member_name"},
    {"data": "username"},
    //{"data":"login_time"},
    
    {"data": "login_time", render:function( data, type, full, meta ){
        return (data) ? moment.unix(data,'DD-MMM-YYYY HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'None';
    }},
     
    
    {"data": "logout_time", render:function( data, type, full, meta ){
        return (data) ? moment.unix(data,'DD-MMM-YYYY HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'None';
    }},
    {"data": "forced_logout_time", render:function( data, type, full, meta ){
        return (data) ? moment.unix(data,'DD-MMM-YYYY HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'None';
    }},
    {"data": "status"}
               ]
    });
    }
    }

    $('table tbody').on('click', 'tr .edit_me4', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var tbl = row.parent().parent();
    var tbl_id = $(tbl).attr("id");
    var dt = dTable[tbl_id];
    var data = dt.row(row).data();
    if (typeof (data) === 'undefined') {
    data = dt.row($(row).prev()).data();
    if (typeof (data) === 'undefined') {
    data = dt.row($(row).prev().prev()).data();
    }
    }

    });