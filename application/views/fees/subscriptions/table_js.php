 if ($('#tblClient_subscription').length && tabClicked === "tab-client_subscriptions") {
                if(typeof dTable['tblClient_subscription'] !=='undefined'){
                    $("#tab-client_subscriptions").addClass("active");
                    dTable['tblClient_subscription'].ajax.reload();
                }else{
                    dTable['tblClient_subscription'] = $('#tblClient_subscription').DataTable({
                    "pageLength": 25,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $subscription_privilege)){ ?> getBtnConfig('<?php echo $title; ?>-  Payments'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('client_subscription/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){d.status_id=1;}
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(4, {page: 'current'}).data().sum();
                        var amount_overall = api.column(4).data().sum();
                        
                        $(api.column(4).footer()).html(curr_format(amount_page) + "(" + curr_format(amount_overall) + ") ");
                    },
                    "columns": [
                        {"data": "transaction_no"},
                        {"data": "member_name", render: function (data, type, full, meta) {
                                    return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.client_id + "'>" + data + "</a>";
                                }},
                        {"data": "feename"},
                        {"data": "subscription_date", render: function(data, type, full,meta){
                                if(type ==="sort" || type==="filter"){
                                    return data?moment(data,'YYYY-MM-DD').format("X"):"";
                                }
                                return data?moment(data,'YYYY-MM-DD').format("DD-MMM-YYYY"):"";
                        }
                    },
                        
                        {"data": "amount", render: function(data, type, full, meta){ return data?curr_format(data):'';}},
                        
                        {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                              <?php if(in_array('4', $subscription_privilege)){ ?>
                                <!-- ret_txt += "<div class='btn-grp'><a href='#reverse_sub-modal' data-toggle='modal' class='btn btn-sm btn-danger edit_me2' title='Reverse Subscription Payment'><i class='fa fa-undo'></i></a>"; -->
                                <?php }  ?>
                                if(parseInt(full.sub_fee_paid)===0){
                                ret_txt += '<a href="#pay_sub_fee-modal" data-toggle="modal"   title="Pay Fee" class="btn btn-xs btn-success edit_me edit_me3"><i class="fa fa-money" ></i> Pay</a>';
                                 }
                                 if(parseInt(full.sub_fee_paid)===1){
                                    ret_txt = 'Paid';
                                 }
                            
                                return ret_txt;
                        }}
                    ]
                });
                }
            }


$('table tbody').on('click', 'tr .edit_me2', function (e) {
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
    var formId = tbl_id.replace("tbl", "formReverse");
    edit_data(data, formId);
});



$('table tbody').on('click', 'tr .edit_me3', function (e) {
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
    get_user_savings_accounts(data.client_id);

});
