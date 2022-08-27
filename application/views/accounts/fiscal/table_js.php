    if ($("#tblFiscal_year").length && tabClicked === "tab-fiscal") {
                if (typeof (dTable['tblFiscal_year']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-fiscal").addClass("active");
                    dTable['tblFiscal_year'].ajax.reload(null, true);
                } else {
                    dTable['tblFiscal_year'] = $('#tblFiscal_year').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Fiscal_year/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
								e.organisation_id = <?php echo $_SESSION['organisation_id']; ?>;
								}
						},
                        "columnDefs": [{
                                "targets": [4],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                        {data: 'start_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                        }},
                        {data: 'end_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                        }},
                        {data: 'status_id', render: function (data, type, full, meta) {
                                var now = moment(new Date()); //todays date
                                var undo_start = moment(full.end_date);
                                var close_start = moment(full.start_date);
                                var duration_undo = moment.duration(now.diff(undo_start));
                                var duration = moment.duration(now.diff(close_start));
                                var undo = duration_undo.asDays();
                                var close = duration.asDays();
                          if((parseInt(data)===1) &&(parseInt(full.close_status)===1) &&(parseFloat(close)>345)){
                           <?php if(in_array('40', $fiscal_privilege)){ ?>
                            return "<center><a href='#close_fiscal-modal'  data-toggle='modal'  title='Close Fiscal Year' class='btn btn-xs btn-primary' >Close</a></center>";
                             <?php } else{ ?>
                                return "Access Denied!";
                            <?php }  ?>
                          } else if((parseInt(data)===1) &&(parseInt(full.close_status)===0)&&(parseFloat(undo)<200)){
                          <?php if(in_array('41', $fiscal_privilege)){ ?>
                            return "<center><a href='#undo_close-modal'  data-toggle='modal'  title='Undo Closed Fiscal Year' class='btn btn-xs btn-warning' >Undo Close</a></center>";
                            <?php } else{ ?>
                                return "Access Denied!";
                            <?php }  ?>
                          } else {
                           return "Not Applicable";
                          }
                        }},
                        {data: 'status_id', render:function ( data, type, full, meta ) {
                            if(data==="1"){
                                var stat="Active";
                                return '<a href="#" role="button" class="badge badge-success" >'+ stat +'</a>';
                            }else if(data==="2"){
                                var stat="Inactive";
                                return '<a href="#" role="button" class="badge badge-warning" >'+ stat +'</a>';
                            }else{
                                var stat ="Deactivated";
                                return '<a href="#" role="button" class="badge badge-danger" >'+ stat +'</a>';
                            }
                        }},
                        { data: 'id', render: function(data, type, full, meta) {
                            var display_btn = "<div class='btn-grp'>";
                            <?php if(in_array('3', $fiscal_privilege)){ ?>
                                
                            <?php } if(in_array('7', $fiscal_privilege)){ ?>
                                if(parseInt(full.status_id) ===2){
                                display_btn += '<a href="#" title="Activate Fiscal Year"><span class="fa fa-check-circle text-warning change_status_active"></span></a>';

                                display_btn += '<a href="#" style="padding-left:15px;" title="Deactivate Fiscal Year"><span class="fa fa-ban text-warning change_status_deactivate"></span></a>';
                                } else if (parseInt(full.status_id) ===1){
                                display_btn += '<a href="#"  style="padding-left:15px;" title="Make Fiscal Year Inactive"><span class="fa fa-undo text-danger change_status_inactivate"></span></a>';

                                display_btn += '<a href="#" style="padding-left:15px;"title="Deactivate Fiscal Year"><span class="fa fa-ban text-warning change_status_deactivate"></span></a>';
                                } else {
                                display_btn += '<a href="#" style="padding-left:15px;" title="Activate Fiscal Year"><span class="fa fa-check-circle text-warning change_status"></span></a>';
                                }

                             <?php }  if(in_array('7', $fiscal_privilege)){ ?>
                                <!-- display_btn += '<a href="#" style="padding-left:10px;" title="Delete  record"><span class="fa fa-trash text-danger delete_me"></span></a>'; -->
                             <?php } ?>
                                display_btn += "</div>";
                                return display_btn; 
                            }
                        }
                        ],
                        buttons: <?php if(in_array('6', $fiscal_privilege)){ ?> getBtnConfig('Fiscal Year'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

      $('table tbody').on('click', 'tr .change_status_active', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/change_status";

            change_status({id: data.id, status_id: 1}, url, tbl_id);
        });

        $('table tbody').on('click', 'tr .change_status_inactivate', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/inactivate";

            change_status({id: data.id, status_id: 2}, url, tbl_id);
        });

      $('table tbody').on('click', 'tr .change_status_deactivate', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/deactivate";

            change_status({id: data.id, status_id: (parseInt(data.status_id) === 3)}, url, tbl_id);
        });
