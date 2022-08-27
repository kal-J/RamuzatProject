<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-share-assignment">Assignment</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#tab-share-application">Appplication</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-share-assignment" class="tab-pane active">
                    <div class="panel-body">
                        <div><strong>Assign Fees</strong> <?php if(in_array('1', $share_issuance_privilege)){ ?> <a data-toggle="modal" href="#add_share_assignment-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Assign share </a>  <?php } ?></div>
                        <div class="table-responsive">
                                <table id="tblShare_assignment" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Fee Name</th>
                                            <th>Fee Type</th>
                                            <th>Amount Calculated As</th>
                                            <th>Amount/Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive--> 
                    </div>
                </div><!--End of Fees section-->

                <div role="tabpanel" id="tab-share-application" class="tab-pane">
                    <div class="panel-body">
                        <div><strong>Apply share</strong> <?php if(in_array('1', $share_issuance_privilege)){ ?> <a data-toggle="modal" href="#add_share_application-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Apply share </a>  <?php } ?></div>
                        <div class="table-responsive">
                                <table id="tblShare_application" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Fee Name</th>
                                            <th>Fee Type</th>
                                            <th>Amount Calculated As</th>
                                            <th>Amount/Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive--> 
                    </div>
                </div><!--End of Fees section-->
            </div>
        </div>
    </div>
</div>
<?php echo $add_share_assignment_modal; ?>
<?php echo $add_share_application_modal; ?>
<script>
    var dTable = {};
    var loan_productDetailModel = {};
$(document).ready(function() {
  $('form#formShare_assignment').validator().on('submit', saveData);
  $('form#formShare_application').validator().on('submit', saveData);

     var handleDataTableButtons = function () {
<?php $this->view('setting/shares/share_assignment/table_js'); ?>
<?php $this->view('setting/shares/share_application/table_js'); ?>
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function () {
                    handleDataTableButtons();
                }
            };
        }();
        TableManageButtons.init();
    });
function reload_data(form_id, response){
    switch(form_id){
        case "formShare_assignment":
            share_assignmentDetailModel.share_assignment(response.share_assignment);
            break;
        case "formShare_application":
            share_applicationDetailModel.share_application(response.share_application);
            break;
        default:
            //nothing really to do here
            break;
    }
}
</script>
