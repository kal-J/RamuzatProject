
if ($("#tblSavings_schedule").length && tabClicked === "tab-savings_schedule") {
        if (typeof (dTable['tblSavings_schedule']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-savings_schedule").addClass("active");
            dTable['tblSavings_schedule'].ajax.reload(null, true);
        } else {
            dTable['tblSavings_schedule'] = $('#tblSavings_schedule').DataTable({
                "lengthMenu": [[10, 25], [10, 25]],
                <!-- order: [], -->
                    "bInfo": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Savings Schedule Report '), <?php } else { echo "[],"; } ?>
                ajax:{
                    "url":  "<?php echo site_url('reports/savings_schedule') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.status_id = status_id;
                        d.product_id = product_id;
                        d.to_date = to_date;
                        d.from_date = from_date;
                    }
                    },
                columns: [
                    {data: "member_name"},
                   
                    {data: 'account_no'},

                    {data: 'productname'},
                    {data: 'from_date', render: function (data, type, full, meta) {
                        
                        return moment(data,'YYYY-MM-DD').format('D MMM YYYY')+" -- "+moment(full.to_date,'YYYY-MM-DD').format('D MMM YYYY');
                    }},
                    {data: 'fulfillment_code', render: function (data, type, full, meta) {
                    var ret_txt ="<div class='btn-group'>";
                        if(data==1){
                            ret_txt +="<i class='text-danger fa fa-times' style='font-size:14px'></i></div>";
                            return ret_txt;
                        }else if(data==2){
                            return 'Partially';
                        }else if(data==3){                            
                            ret_txt +="<i class='text-success fa fa-check' style='font-size:14px'></i></div>";
                            return ret_txt;
                        }
                        return data;
                    }}
                ],
                responsive: true
            });
        }
        }
