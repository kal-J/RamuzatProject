if ($('#tblBilling_sms').length && tabClicked === "tab-sms") {
if(typeof dTable['tblBilling_sms'] !=='undefined'){
//$("#tab-sms").addClass("active");
dTable['tblBilling_sms'].ajax.reload(null,true);
}else{
dTable['tblBilling_sms'] = $('#tblBilling_sms').DataTable({
"pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $billing_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Billing'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('billing/member_sms_jsonList' ); ?>",
    "dataType": "json",
    "type": "POST",
    "data": function(d){
        d.member_id=<?php echo $member_id; ?>
    }
    },
    "footerCallback": function (tfoot, data, start, end, display) {
    

    var api = this.api();
    var amount_page = api.column(5, {page: 'current'}).data().sum();
    var amount_overall = api.column(5).data().sum();
    $(api.column(5).footer()).html(curr_format(round(amount_page,2)) );

    
    },

    "columns": [
    {"data": "ref_no"},
    {"data": "message_type"},
    {"data": "message"},
    {"data": "date_created", render:function( data, type, full, meta ){
        return (data) ? moment.unix(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';
    }},
    {"data": "created_by"},
    {"data": "cost"}
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