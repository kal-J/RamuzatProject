if ($('#tblLogs').length && tabClicked === "tab-revenue-performance") {
if(typeof dTable['tblLogs'] !=='undefined'){
$("#tab-sms").addClass("active");
dTable['tblLogs'].ajax.reload(null,true);
}else{
dTable['tblLogs'] = $('#tblLogs').DataTable({
"pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $billing_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Billing'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('ActivityLogs/jsonList'); ?>",
    "dataType": "json",
    "type": "POST",
    "data": function(d){}
    },
    "footerCallback": function (tfoot, data, start, end, display) {
    /*

    var api = this.api();
    var amount_page = api.column(2, {page: 'current'}).data().sum();
    var amount_overall = api.column(2).data().sum();
    $(api.column(2).footer()).html(curr_format(amount_page) );

    */
    },

    "columns": [
    {"data": "Item"},
    {"data": "action"},
    {"data": "activity"},
    {"data": "module_name"},
    {"data": "reference_id"},
    {"data": "reference_number"},
    {"data": "date_created", render:function( data, type, full, meta ){
        return (data) ? moment.unix(data,'DD-MMM-YYYY HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'None';
    }},
    {"data": "reference_url"}
  
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