<div id="loan_loss_provision-modal" class="modal inmodal fade"  tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5> Client Groups & Companies
                    <div class="pull-right add-record-btn">
                        <?php if(in_array('1', $group_privilege)){ ?>
                        <button type="button" data-toggle="modal" class="btn btn-sm btn-primary"
                            data-target="#add_group-modal"><i class="fa fa-plus-circle"></i> New Group</button>

                        <button type="button" data-toggle="modal" class="btn btn-sm btn-success"
                            data-target="#add_company-modal"><i class="fa fa-plus-circle"></i> New Company</button>
                        <?php } ?>
                    </div>
                </h5>
            </div>
            <div class="ibox-content">
                <div class="tabs-container mb-4 mt-2">

                    <ul class="nav nav-tabs" role="tablist">
                        <li><a id="groups-tab" class="nav-link active" data-toggle="tab"
                                href="#tab-groups"><i class="fa fa-users"></i> Groups</a></li>

                        <li><a id="companies-tab" class="nav-link" data-toggle="tab"
                                href="#tab-companies"><i class="fa fa-building"></i>Companies</a></li>

                    </ul>

                </div>
                <div class="tab-content">
                    <div role="tabpanel" id="tab-groups" class="tab-pane active">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tblGroup">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Group Name</th>
                                        <th>No. of members</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" id="tab-companies" class="tab-pane">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="tblCompanies">
                                <thead>
                                    <tr>
                                        <th>Company NO.</th>
                                        <th>Company Name</th>
                                        <th>Owner Name</th>
                                        <th>Phone Number</th>
                                        <th>NIN</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>No. of members</th>
                                        <th>Description</th>
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
                     

<?php $this->load->view("group/add_group_modal"); ?>
<?php $this->load->view("group/add_company_modal"); ?>
<?php $this->load->view("group/attach_company_director_modal"); ?>

<script>
    var dTable = {};
    $(document).ready(function () {
      
        $('form#formGroup').validator().on('submit', saveData);
        $('form#formCompany').validator().on('submit', saveData);
        /* PICK DATA FOR DATA TABLE  */
        var handleDataTableButtons = function (tabClicked) {
            <?php $this->load->view("group/group_js"); ?>
            <?php $this->load->view("group/companies_js"); ?>
            
        };

        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();

        TableManageButtons.init('tab-groups');

        $('#companies-tab').on('click', () => {
            handleDataTableButtons('tab-companies');
        });
        $('#groups-tab').on('click', () => {
            handleDataTableButtons('tab-groups');
        });


        setTimeout(() => {
               //console.log('File loaded');
               openModal();
        }, 2000);

    });
    
function reload_data(formId, reponse_data){
    if (typeof reponse_data.group_id !== 'undefined' ) {
        window.location = "<?php  echo site_url('group/view/');?>"+reponse_data.group_id;
    }
}
</script>





