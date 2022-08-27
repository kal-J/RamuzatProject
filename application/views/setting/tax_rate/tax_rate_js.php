
    if ($("#tblTax_rate").length){
            dTable['tblTax_rate'] = $('#tblTax_rate').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: [[1, 'asc']],
                deferRender: true,
                ajax: {
                    "url": "<?php echo base_url('tax_rate/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                        function(d) {
                            //d.status_id = 1;
                            d.tax_rate_source_id = <?php echo $tax_rate_source['id'];?>;
                        }
                },
                "columnDefs": [{
                        "targets": [4],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'rate', render: function(data, type, full, meta){ return data?curr_format(data):'';}},
                    {data: 'start_date', render: function(data, type, full, meta){ 
                            if(type==="sort"){
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return moment(data, 'YYYY-MM-DD').format('D-M-YYYY');
                        }
                    },
                    {data: 'end_date', render: function(data, type, full, meta){
                            if(type==="sort"){
                                return data?moment(data, 'YYYY-MM-DD').format('X'):'';
                            }
                             return data?moment(data, 'YYYY-MM-DD').format('D-M-YYYY'):'';
                        }
                    },
                    {data: 'note'},
                    {data: 'id', render: function (data, type, full, meta) {
                           var display_btn ="";
                           display_btn += "<div class='btn-grp'>";
                          <?php if(in_array('3', $privileges)){ ?>
                            display_btn += "<a href='#add_tax_rate-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' title='Edit tax rate'><i class='fa fa-edit'></i></a>";
                            <?php } if(in_array('7', $privileges)){ ?> 
                            display_btn += '<a href="#" title="Deactivate tax rate" class="btn btn-sm btn-default change_status"><span class="fa fa-ban text-danger"></span></a>';
                            <?php }  ?> 
                            display_btn += "</div>";

                            return display_btn;
                        }
                    }
                ],
                buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('<?php echo $title; ?>-Tax rates'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }