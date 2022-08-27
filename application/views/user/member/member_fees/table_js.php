if ($('#tblApplied_member_fees').length && tabClicked === "tab-member_fees") {
                if(typeof dTable['tblApplied_member_fees'] !=='undefined'){
                    //$("#tab-member_fees").addClass("active");
                    dTable['tblApplied_member_fees'].ajax.reload(null,true);
                }else{
                    dTable['tblApplied_member_fees'] = $('#tblApplied_member_fees').DataTable({
                    "pageLength": 25,
                    "searching": false,
                    "paging": false,
                    // "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_privilege)){ ?> getBtnConfig('<?php echo $title; ?>- Member Fees'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('applied_member_fees/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){d.member_id = <?php echo $user['id']; ?>;}
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var amount_page = api.column(2, {page: 'current'}).data().sum();
                        var amount_overall = api.column(2).data().sum();                        
                        $(api.column(2).footer()).html(curr_format(amount_page) );
                    },
                    "columns": [
                        {"data": "transaction_no"},                    
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
                                    ret_txt += "<a href='<?php echo base_url(); ?>applied_member_fees/pdf/<?php echo $user['id']; ?>/"+full.transaction_no+"'  target = '_blank' class='btn btn-primary aquaBtn' ><i class='ti-printer'></i>&nbsp;Print reciept&nbsp;</a>";
                                   }else{
                                   ret_txt += '<a href="#add_member_fees-modal" data-toggle="modal"   title="Pay Fee" class="btn btn-xs btn-success edit_me"><i class="fa fa-money" ></i> Pay</a>';
                                }
                                <?php } if(in_array('4', $member_privilege)){ ?>
                                    ret_txt += '&nbsp;&nbsp; <a href="#" data-toggle="modal"   title="delete fee details" class="btn btn-xs btn-danger delete_me"> <i class="fa fa-trash"  ></i></a>';
                                <?php } ?>
                                return ret_txt;
                            }}
                    ]
                });
                }
            }
