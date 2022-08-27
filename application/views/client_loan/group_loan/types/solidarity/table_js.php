    if ($("#tblGroup_loan").length && tabClicked === "tab-solidarity_loan") {
        if (typeof (dTable['tblGroup_loan']) !== 'undefined') {
            $(".loans").removeClass("active");
            $("#tab-solidarity_loan").addClass("active");
            $("#tab-loans").addClass("active");
            dTable['tblGroup_loan'].ajax.reload(null, true);
        } else {
            dTable['tblGroup_loan'] = $('#tblGroup_loan').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                ajax: {
                    "url": "<?php  if(isset($group['id'])  && isset($client_type)){ 
                        echo site_url('group_loan/jsonList_per_group/').$group['id'];
                    }else{
                        echo site_url('group_loan/jsonList');
                    } ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.loan_type_id = '2';     //pending approval
                                <?php if (isset($user['id'])) { ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                <?php } ?>
                            }

                },
                "columnDefs": [{
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
      { data: 'group_loan_no', render:function(data, type, full, meta) {
        var link="<a href='<?php echo site_url('client_loan/index'); ?>/" + full.id + "' title='View the Individual Loans'>" + data + "</a>"
          return link;
      }  },
      { data: 'group_name', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("group/view");?>/"+full.group_id+"' title='Click to view full details'>"+data+"</a>";} },
      { data: 'requested_amount', render:function(data, type, full, meta) {
        return (data)?curr_format(data*1):data;
      } },
      { data: 'approved_amount', render:function(data, type, full, meta) {
        return (data)?curr_format(data*1):data;
      }},
      { data: 'firstname', render:function(data, type, full, meta) {
        return (full.othernames)?full.salutation+' '+data+' '+full.lastname+' '+full.othernames:full.salutation+' '+data+' '+full.lastname;
      }},
      { data: 'comment'},
      { data: 'id', render:function ( data, type, full, meta ) {
      var ret_txt="";
      <?php if(in_array('3', $group_loan_privilege)){ ?>
      ret_txt +="<a href='#add_pending_approval-modal' data-toggle='modal' class='edit_me' title='Edit this Loan details'><i class='text-primary fa fa-edit'></i></a>";
      <?php //} if(in_array('7', $group_loan_privilege)){ ?>
      <!-- ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status'><i class='text-danger fa fa-trash'></i></a>"; -->
      <?php } ?>
        return ret_txt;
      } }
      ],
                buttons: <?php if(in_array('6', $group_loan_privilege)){ ?> getBtnConfig('Solidarity Loan Applications'), <?php } else { echo "[],"; } ?>
                responsive: true
            });
        }
    }
