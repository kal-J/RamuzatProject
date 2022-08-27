    <div class="row">
          <div class="col-lg-12">
          <div class="ibox ">
                <div class="ibox-title">
                     <ul class="breadcrumb">
                        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                    </ul> 
                    <div class="ibox-tools">
                    <?php if(in_array('1', $staff_privilege)){ ?>
                      <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#add_staff-modal"><i class="fa fa-plus-circle"></i> Add Staff</button>
                    <?php } ?>
                  </div>
                </div>
                <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-active_staffs"> Active</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-inactive_staffs">Inactive</a></li>
                    </ul>

                    <div class="tab-content">
                      <div role="tabpanel" id="tab-active_staffs" class="tab-pane active">
                      <div class="hr-line-dashed"></div>
                      <div class="col-lg-12">
                        <div class="table-responsive">
                          <table class="table table-striped table-bordered table-hover dataTables-example" id="tblStaff" >
                            <thead>
                              <tr>
                                  <th>Staff #</th>
                                  <th>Names</th>
                                  <th>Branch</th>
                                  <th>Contact</th>
                                  <th>Position</th>
                                  <th>Email</th>
                                  <th style="width:90px;">Action</th>
                              </tr>
                            </thead>
                              <tbody>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        </div>

                        <div role="tabpanel" id="tab-inactive_staffs" class="tab-pane">
                          <div class="hr-line-dashed"></div>
                          <div class="col-lg-12">
                            <div class="table-responsive">
                              <table class="table table-striped table-bordered table-hover dataTables-example" id="tblInactiveStaff" >
                                <thead>
                                  <tr>
                                      <th>Staff #</th>
                                      <th>Names</th>
                                      <th>Branch</th>
                                      <th>Contact</th>
                                      <th>Position</th>
                                      <th>Email</th>
                                      <th>Action</th>
                                  </tr>
                                </thead>
                                  <tbody>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                        </div>

                      </div>

                  </div>
                </div>
            </div>
        </div>
    </div>

<?php require 'staff_user-modal.php';?>
<?php $this->load->view('user/staff/make_member.php'); ?>
<script type="text/javascript">
var dTable = {};
$(document).ready( function () {

        //**************************** Page View KO Model *********************************************************//
        var ViewModel = function () {
            var self = this;
            self.marital_status_id = ko.observable();
            self.initialize_edit = function () {
                edit_data(self.formatOptions(), "form");
            };
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        
var handleDataTableButtons = function(tabClicked) {
  if ($('#tblStaff').length && tabClicked === "tab-active_staffs") {
      if (typeof (dTable['tblStaff']) !== 'undefined') {
          $(".tab-pane").removeClass("active");
          $("#tab-active_staffs").addClass("active");
          dTable['tblStaff'].ajax.reload(null, true);
      } else {      
            dTable['tblStaff'] = $('#tblStaff').DataTable( {
              pageLength: 10,
              responsive: true,
              dom: '<"html5buttons"B>lTfgitp',
              buttons: <?php if(in_array('6', $staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
              ajax: {
                url:"<?php echo site_url("staff/jsonList");?>",
                dataType: 'JSON',
                type: 'POST',
                data: function(d){
                  d.status_id=1;
                }
              },
              columnDefs: [{
                          "targets": [6],
                          "orderable": false,
                          "searchable": false
                      }],
              columns:[
              { data: 'staff_no' },
              { data: 'salutation', render:function ( data, type, full, meta ) { 
                 return "<a href='<?php echo site_url("staff/staff_data"); ?>/" + full.id + "'>" + data + "  "+ full.firstname+" " + full.lastname + "  " + full.othernames + "</a>";}},
              { data: 'branch_name'},
              { data: 'mobile_number' },
              { data: 'position'},
              { data: 'email'},
              { data: 'id', render:function ( data, type, full, meta ) {
                var ret_txt ="<a href='<?php echo base_url();?>staff/staff_data/"+data+"'><i class='fa fa-edit'></i></a>";
                <?php if(in_array('4', $staff_privilege)){ ?>
                ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status'><i class='text-danger fa fa-ban'></i></a>";
                if(!full.client_no){
                ret_txt += "<a href='#add_member-modal' data-toggle='modal' class='btn btn-sm make_member'><i class='text-primary fa fa-plus-circle'></i></a>";
                }

                if(full.login_attempt<=0){
                ret_txt += "<a href='<?php echo base_url();?>staff/unblock_account/"+data+"'' data-toggle='modal' class='btn btn-sm unblock_account'><i class='fa fa-key' title='Unblock this account'></i></a>";
                }
                
                <?php } ?>
                return ret_txt;
              } 
            } 
            ]
              
            });
        }
      }

      if ($('#tblInactiveStaff').length && tabClicked === "tab-inactive_staffs") {
      if (typeof (dTable['tblInactiveStaff']) !== 'undefined') {
          $(".tab-pane").removeClass("active");
          $("#tab-inactive_staffs").addClass("active");
          dTable['tblInactiveStaff'].ajax.reload(null, true);
      } else {      
            dTable['tblInactiveStaff'] = $('#tblInactiveStaff').DataTable( {
              pageLength: 10,
              responsive: true,
              dom: '<"html5buttons"B>lTfgitp',
              buttons: <?php if(in_array('6', $staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
              ajax: {
                url:"<?php echo site_url("staff/jsonList");?>",
                dataType: 'JSON',
                type: 'POST',
                data: function(d){
                  d.status_id=2;
                }
              },
              columnDefs: [{
                          "targets": [6],
                          "orderable": false,
                          "searchable": false
                      }],
              columns:[
              { data: 'staff_no' },
              { data: 'salutation', render:function ( data, type, full, meta ) { 
                 return "<a href='<?php echo site_url("staff/staff_data"); ?>/" + full.id + "'>" + data + "  "+ full.firstname+" " + full.lastname + "  " + full.othernames + "</a>";}},
              { data: 'branch_name'},
              { data: 'mobile_number' },
              { data: 'position' },
              { data: 'email'},
              { data: 'id', render:function ( data, type, full, meta ) {
                var ret_txt ="<a href='<?php echo base_url();?>staff/staff_data/"+data+"'><i class='fa fa-edit'></i></a>";
                <?php if(in_array('4', $staff_privilege)){ ?>
                ret_txt += "<a href='#' data-href='<?php echo base_url(); ?>staff/change_status' data-toggle='modal' class='btn btn-sm change_status'><i class='text-danger fa fa-undo'></i></a>";
                <?php } ?>
                return ret_txt;
              } }
              ]
              
            });
        }
      }
    };
    TableManageButtons = function(){
    "use strict";
    return {
    init: function(tblClicked) {
      handleDataTableButtons(tblClicked);
    }
    };
    }();
    TableManageButtons.init("tab-active_staffs");
    $('#formStaff').validate({submitHandler: saveData2});
    $('#formMember').validate({submitHandler: saveData2});

    $('table tbody').on('click', 'tr .make_member', function (e) {
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
            var formId = tbl_id.replace("tblStaff", "formMember");
             edit_data(data, formId);

        });

  } );


function reload_data(formId, reponse_data)
    {
          switch (formId) {
            case "formMember":
                dTable['tblStaff'].ajax.reload(null, false);
                break;
            case "formStaff":
               if (typeof reponse_data.user !== 'undefined' ) {
                 window.location = "<?php  echo site_url('staff/staff_data/');?>"+reponse_data.user;
               }
                break;
            default:
                //nothing really to do here
                break;
        }
     
      
 }
</script>
