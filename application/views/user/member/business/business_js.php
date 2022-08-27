            if ($('#tblBusiness').length && tabClicked === "tab-business") {
                if(typeof(dTable['tblBusiness'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-business").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblBusiness'].ajax.reload(null,true);
                }else{
                    dTable['tblBusiness'] =
                        $('#tblBusiness').DataTable({
                    "pageLength": 25,
                    "searching": false,
                    "paging": false,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_privilege)){ ?> getBtnConfig('<?php echo $title; ?>-Businesses Held'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('business/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){
                        d.status_id=1,
                        d.member_id = <?php echo $user['id']; ?>;}
                    },
                    "columns": [
                        {"data": "businessname"},
                        {"data": "natureofbusiness"},
                        {"data": "businesslocation"},
                        {"data": "numberofemployees"},
                        {"data": "businessworth"},
                        {"data": "ursbnumber"},
                        {"data": "certificateofincorporation", render: function(data, type, full,meta){
                                if(full.certificateofincorporation!==""){
                                    var organisation_id=<?php echo $_SESSION['organisation_id'];?>;
                                    //var link="<a target='blank' href='http://docs.google.com/gview?url=<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/user_docs/certificate_of_incorporation/"+data+"&amp;embedded=true' width='500' height='250' style='border-style:none;'>view file </a>";
                                    var link= "<a target='blank' href='<?php echo site_url(); ?>uploads/organisation_"+organisation_id+"/user_docs/certificate_of_incorporation/"+data+"' title='View document details'>View File</a>";
                                    return link;
                                    }
                                return "No file";
                        }},
                        {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                              <?php if(in_array('3', $member_privilege)){ ?>
                                ret_txt += "<div class='btn-grp'><a href='#add_business-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update business details'><i class='fa fa-edit'></i></a>";
                                <?php } if(in_array('4', $member_privilege)){ ?>
                                ret_txt += '<a href="#" data-toggle="modal"   title="delete business details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
                                <?php } ?>
                                return ret_txt;
                            }}
                    ]
                });
                }
            }
