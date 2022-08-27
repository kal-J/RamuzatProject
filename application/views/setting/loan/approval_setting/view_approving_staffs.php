<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title  back-change">
                <h3 class="text-uppercase text-center">Approval Setting
                    <small data-bind="text: 'Loan Amount Range '+curr_format($root.approval_setting().min_amount)+'-'+curr_format($root.approval_setting().max_amount)"></small></h3>
                <div  class="text-center"><h3><small class="text-danger" data-bind="text: ($root.approval_setting().num_of_attached_staff > 1)?('Attached staffs are '+$root.approval_setting().num_of_attached_staff+'. '):('Attached staff is '+$root.approval_setting().num_of_attached_staff+'. ')"></small></h3></div>
            </div>
            <div class="ibox-content">
            <?php if(in_array('1', $approval_privilege)){ ?>
                <div class="pull-right add-record-btn">
                    <a href="#add_approving_staff-modal" data-toggle="modal"  class="btn btn-default btn-sm pull-right">
                        <i class="fa fa-plus"></i> Assign Staff </a>
                </div>
            <?php } ?>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table  table-striped table-bordered table-hover" id="tblApproving_staff" width="100%" >
                            <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Gender</th>
                                    <th>Rank</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>                            
                            <!-- ko if: all_approving_staffs().length == 0 -->
                              <tbody>
                              <tr>
                                <td colspan="5">No staff attached yet</td>
                              </tr> 
                              </tbody>
                            <!--/ko-->
                            <tbody data-bind="foreach: all_approving_staffs">
                                <tr>
                                    <td data-bind="text: salutation+' '+firstname+' '+lastname+' '+othernames"></td>
                                    <td data-bind="text: (gender==1)?'Male':'Female'"></td>
                                    <td data-bind="text: (rank==0)?'Member':'Chair'"></td> 
                                    <td data-bind="text: (status_id==1)?'Active':'Inactive'"></td>
                                    <td>
                               <?php if(in_array('3', $approval_privilege)){ ?>
                                        <a href='#add_approving_staff-modal' data-bind='click: function(){edit(id,staff_id,rank);}' data-toggle='modal'  class='btn btn-sm pull-right'><i class="fa fa-edit"></i></a>
                               <?php } if(in_array('7', $approval_privilege)){ ?>
                                        <a href="#" data-toggle="modal" class="btn btn-sm" data-bind='click: function(data){change_status1(data.id,data.status_id,data.approval_setting_id);} '>
                                            <i class="text-danger fa " data-bind="attr:{title:(status_id==1)?'Deactive':'Reactivate'},css: {'fa-ban': (status_id==1),'fa-undo': (status_id==2)}"></i>
                                        </a>
                               <?php } ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $add_approving_staff_modal;?>
<script>
var viewModel = {};
$(document).ready(function() {
    $(".select2able").select2({
            allowClear: true
    });
    $('form#formApproving_staffs').validator().on('submit', saveData);
    var ViewModel = function() {
        var self = this;
        self.staffdata= ko.observable();
        self.approval_setting= ko.observable(<?php echo json_encode($approval_setting); ?>);
        self.all_approving_staffs= ko.observableArray(<?php echo json_encode($staffs); ?>);
        self.staffs = ko.observableArray(<?php echo json_encode($registered_staffs); ?>);
        self.staff= ko.observable();
        
    };
    viewModel = new ViewModel();
    ko.applyBindings(viewModel);
    });

function reload_data(form_id, response){
    switch(form_id){
        case "formApproving_staffs":
            viewModel.approval_setting(response.approval_setting);
            viewModel.all_approving_staffs(response.staffs);
            break;
        default:
            if (typeof response.approval_setting !== 'undefined') {
                viewModel.approval_setting(response.approval_setting);
            }
            if (typeof response.staffs !== 'undefined') {
                viewModel.all_approving_staffs(response.staffs);
            }
            //nothing really to do here
            break;
    }
}
    
function edit(approval_setting_id,staff_id,rank) {
    if (typeof rank !== 'undefined') {
        viewModel.approval_setting(<?php echo json_encode($approval_setting); ?>);
        var data={id: approval_setting_id,staff_id: staff_id,rank: rank};
        viewModel.staffdata(data);
        edit_data(viewModel.staffdata(),"formApproving_staffs");
    }
}

function change_status1(approving_staff_id,status_id,approval_setting_id) {
    if (typeof approving_staff_id !== 'undefined') {
        var tbl_id="tblApproving_staff";
        var controller="Approving_staff";
        var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/change_status";
        change_status({id:approving_staff_id,approval_setting_id:approval_setting_id, status_id:(parseInt(status_id)===1?2:1)}, url, tbl_id);
    }
}


            
</script>