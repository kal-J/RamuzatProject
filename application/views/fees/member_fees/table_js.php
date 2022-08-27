if ($('#tblApplied_member_fees').length && tabClicked === "tab-member_fees") {
                if(typeof dTable['tblApplied_member_fees'] !=='undefined'){
                    //$("#tab-member_fees").addClass("active");
                    dTable['tblApplied_member_fees'].ajax.reload(null,true);
                }else{
                    dTable['tblApplied_member_fees'] = $('#tblApplied_member_fees').DataTable({
                    "pageLength": 25,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_privilege)){ ?> getBtnConfig('<?php echo $title; ?>- Member Fees'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('applied_member_fees/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){}
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(3, {page: 'current'}).data().sum();
                        var amount_overall = api.column(3).data().sum();                        
                        $(api.column(3).footer()).html(curr_format(amount_page) );
                    },
                    "columns": [
                        {"data": "transaction_no"},                    
                        {"data": "member_name"},                    
                        {"data": "feename"},
                        {"data": "amount", render: function(data, type, full, meta){ return data?curr_format(data*1):'';}},
                        { data: "payment_date", render:function( data, type, full, meta ){
                            return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'None';;
                        }},
                        {"data": "payment_mode"},
                        
                         {"data": "fee_paid", render: function(data, type,full ,meta ){
                            return (data==0)?'<a href="#" role="button" class="badge badge-danger" >Not Paid</a>':'<a href="#" role="button" class="badge badge-primary" >Paid</a>';
                        } },
                        {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                                <?php if(in_array('6', $member_privilege)){ ?>
                                    if(parseInt(full.fee_paid)===1){
                                    ret_txt += "<a href='<?php echo base_url(); ?>applied_member_fees/pdf/"+full.member_id+"/"+full.transaction_no+"'  target = '_blank' class='btn btn-primary aquaBtn' ><i class='ti-printer'></i>&nbsp;Print reciept&nbsp;</a>";
                                   }else{
                                   ret_txt += '<a href="#add_member_fees-modal" data-toggle="modal"   title="Pay Fee" class="btn btn-xs btn-success edit_me edit_me4"><i class="fa fa-money" ></i> Pay</a>';
                                }
                                <?php } if(in_array('4', $member_privilege)){ ?>
                                    <!-- ret_txt += '<a href="#" data-toggle="modal"   title="delete fee details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>'; -->
                                <?php } ?>
                                return ret_txt;
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
    get_user_savings_accounts(data.member_id);
});