if ($("#tblHoliday").length && tabClicked === "tab-holiday") {
                if (typeof (dTable['tblHoliday']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-holiday").addClass("active");
                    dTable['tblHoliday'].ajax.reload(null, true);
                } else {
                    dTable['tblHoliday'] = $('#tblHoliday').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        ajax:{
                                 "url":  "<?php echo site_url('holiday/jsonList') ?>",
                                 "dataType": "json",
                                 "type": "POST",
                                 "data": function(d){
                                 d.organisation_id=1;
                                  }
                                  },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'holiday_date', render:function( data, type, full, meta ){
                                 if (type === "sort" || type === "filter") {
                                      return data;
                                  }

                                  if(moment(data).isSame(new Date(), 'year')){
                                    return moment(data,'YYYY-MM-DD').format('dddd, MMMM Do, YYYY');
                                  }else{
                                    return moment(data,'YYYY-MM-DD').add(1, 'year').format('dddd, MMMM Do, YYYY');
                                  }

                                }},
                            {data: 'holiday_name'},
                            {data: 'id', render:function ( data, type, full, meta ) {
                                var ret_txt="";
                                <?php if(in_array('3', $privileges)){ ?>
                                ret_txt +="<a href='#add_holiday-modal' data-toggle='modal' class='edit_me' ><i class='fa fa-edit'></i></a>";
                            <?php } if(in_array('4', $privileges)){ ?>
                                ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm delete_me'><i class='text-danger fa fa-trash'></i></a>";
                            <?php } ?>
                                return ret_txt;
                                      }
                            }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Organisation Holidays'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }