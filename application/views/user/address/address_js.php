            //  address javascript
            if ($('#tblAddress').length && tabClicked === "tab-address") {
                if(typeof(dTable['tblAddress'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-address").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblAddress'].ajax.reload(null,true);
                }else{
                    dTable['tblAddress'] =
                        $('#tblAddress').DataTable({
                    "pageLength": 25,
                    "searching": false,
                    "paging": false,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>-Addresses'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url("Address/jsonList"); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){d.user_id = <?php echo $user['user_id'] ?>;}
                    },
                    "columns": [
                        {"data": "address1"},
                        {"data": "address2"},
                        {"data": "village", render: function (data, type, full, meta) {
                                return ((full.district)?full.district.toUpperCase()+', ':'')+((full.subcounty)?full.subcounty.toUpperCase()+', ':'')+((full.parish)?full.parish.toUpperCase()+', ':'')+(data?data.toUpperCase():'');
                            }
                        }, 
                         {"data": "address_type_name"},                        
                        {"data": "start_date" , render:function ( data, type, full, meta ) {
                            var period = ret_txt = "";
                                if(moment(full.start_date, "YYYY-MM-DD", true).isValid()===true && moment(full.end_date, "YYYY-MM-DD", true).isValid()===true){
                                    var a = moment(full.end_date);
                                    var b = moment(full.start_date);
                                    var years = a.diff(b, 'year');
                                    b.add(years, 'year');
                                    var months = a.diff(b, 'months');
                                    b.add(months, 'month');
                                   period = ((years<=0)?'':(years>1)?years+' years ':years+' year') +' '+
                                    ((months<=0)?'Days':((months>1)?months+' months ':months+' month'));
                                    
                                     return ret_txt += "<span data-toggle='tooltip' class='' data-placement='top' title='From: "+ full.start_date+"  To: "+ full.end_date+"'>"+ period+"</span>";

                                }else if(moment(full.start_date, "YYYY-MM-DD", true).isValid()===true){
                                    return "<span data-toggle='tooltip' class='' data-placement='top' title='From: "+ full.start_date+"  To: Current'>Current</span>";
                                }else{
                                    return "Invalid Date";
                                }    
                            }
                         },
                        {"data": "id", render: function (data, type, full, meta) {
                         var ret_txt ="";
                         <?php if(in_array('3', $member_staff_privilege)){ ?>
                        ret_txt += "<a  data-toggle='modal' data-target='#add_address-modal' class='edit_me'><i class='fa fa-edit'></i></a>";
                         <?php } if(in_array('4', $member_staff_privilege)){ ?>
		                ret_txt +="&nbsp;&nbsp;<a  data-toggle='modal' data-target='#'><i class='fa fa-trash delete_me text-danger'></i></a>";
                         <?php } ?>
                                return ret_txt;
                            }}
                    ]

                });
                }
            }
