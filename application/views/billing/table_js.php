if ($('#tblBilling').length && tabClicked === "tab-sms") {
if(typeof dTable['tblBilling'] !=='undefined'){
$(".tab-pane").removeClass("active");
$("#tab-sms").addClass("active");
dTable['tblBilling'].ajax.reload(null,true);
}else{
dTable['tblBilling'] = $('#tblBilling').DataTable({
"pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $billing_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Billing'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('billing/jsonList'); ?>",
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
    {"data": "client_no"},
    {"data": "member_name", render: function (data, type, full, meta) {
        return "<a href='<?php echo site_url("billing/sms_billing_details"); ?>/" + full.member_id + "'>" + data + "</a>";
    }},
    {"data": "mobile_number"},
    {"data": "no_of_msgs"},
    {"data": "cost", render:function( data, type, full, meta ){
        return curr_format(data * full.no_of_msgs);
    }}
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