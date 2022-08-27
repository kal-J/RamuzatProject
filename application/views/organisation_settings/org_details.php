<div class="row">
    <div class="col-lg-12">
    <div class="ibox ">
     <div class="ibox-content">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-details"> Details</a></li>
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-branches">Branches</a></li>
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-modules">Modules</a></li>
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab_account_format">Number Formats</a></li>
                <!-- <li><a class="nav-link" data-toggle="tab" href="#tab-fiscal" data-bind="click: display_table"><i class="fa fa-sitemap"></i> Fiscal year</a></li> -->
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-payment_engine">Payment Engines</a></li>
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-sms_settings">SMS Settings</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-details" class="tab-pane active">
                    <div class="panel-body">
                    <table class="table table-stripped">
                            <tbody data-bind="with: organisation">
                                <tr>
                                    <td class="no-borders">
                                        <i class="fa fa-houzz text-navy"></i> Organisation Name
                                    </td>
                                    <td data-bind="text: name" class="no-borders">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-hashtag text-navy"></i> Initial
                                    </td>
                                    <td data-bind="text: org_initial">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-mobile-phone text-navy"></i> Description
                                    </td>
                                    <td data-bind="text: description">
                                    </td>
                                </tr>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-branches" class="tab-pane">
                <div class="hr-line-dashed"></div>
                            <?php if(in_array('1', $privileges)){ ?>
                            <div><a data-toggle="modal" href="#add_branch-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Branch</a></div>
                            <?php } ?>
                            <h3><center>Branches and Departments</center></h3>
                    <div class="panel-body">
                    <table id="tblBranch" class="table table-striped table-hover small m-t-md" width="100%">
                        <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Departments</th>
                                    <th>Telephone</th>
                                    <th>Email</th>
                                    <th>Physical Address</th>
                                    <th>Postal Address</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php $this->view('setting/organisation_format/tab_view'); ?>
                <?php //$this->view('setting/organisation_format/add_format'); ?>
                
                <?php $this->view('organisation_settings/modules/module_tab'); ?>

                <?php // $this->view('organisation_settings/fiscal/fiscal_year_tab'); ?>

                <?php $this->view('organisation_settings/payment/tab_view'); ?>
                <?php $this->view('organisation_settings/sms/tab_view'); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
    <?php echo $add_branch_modal; ?>
    <?php //$this->view('organisation_settings/fiscal/add_modal'); ?>
    <?php $this->view('organisation_settings/payment/add_modal'); ?>
    <?php $this->view('organisation_settings/sms/add_modal'); ?>
<script>
    var dTable = {};
    var organisationDetailModel = {};
    var TableManageButtons = {};
    $(document).ready(function() {
        $('form#formBranch').validator().on('submit', saveData);
        $('form#formModules').validator().on('submit', saveData);
        $('form#formOrganisationloan').validator().on('submit', saveData);
        $('form#formNumFormats').validator().on('submit', saveData);
        $('form#formPayment_engine').validator().on('submit', saveData);
        $('form#formSms_settings').validator().on('submit', saveData);
        $('form#formFiscal_year').validate({
            rules: {
              end_date: {
                required: true,
                remote: {
                  url: "<?php echo site_url("Fiscal_year/end_date_check"); ?>",
                  type: "post",
                  data:{
                        check: 1,
                        start_date:function(){
                          return $("#start_date").val();
                      }
                    }
                  }
                }
              }
            ,submitHandler: saveData2
        });
    /*********************************** Page Data Model (Knockout implementation) *****************************************/
    <?php //$this->view("setting/organisation_format/complex_formats_ko"); ?>

    const OrganisationDetailModel = function() {
        var self = this;
        self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
        };
        
        /*self.savings_account_format_model= new FormatsModel(1);
        self.loan_account_format_model= new FormatsModel(2);
        self.client_number_format_model= new FormatsModel(3);
        self.staff_number_format_model= new FormatsModel(4);
        self.group_number_format_model= new FormatsModel(5);*/
        
        self.org_modules= ko.observableArray(<?php echo json_encode($org_modules); ?>);
        self.organisation = ko.observable(<?php echo json_encode($organisation);?>);
        self.payment_engines = ko.observable(<?php echo json_encode($payment_engines); ?>);
        self.payment_engine = ko.observable();
        self.initialize_edit = function(){
            edit_data(self.organisation(),"formBranch");
        };
    };
    organisationDetailModel = new OrganisationDetailModel();
    ko.applyBindings(organisationDetailModel);

        var handleDataTableButtons = function(tabClicked) {
        <?php $this->view('organisation_settings/branch_js'); ?>
        <?php //$this->view('organisation_settings/fiscal/table_js'); ?>
        <?php $this->view('organisation_settings/payment/table_js'); ?>
        <?php $this->view('organisation_settings/sms/table_js'); ?>
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-details");
    });
function reload_data(form_id, response){
    switch(form_id){
        case "formBranch":
            organisationDetailModel.branch(response.branch);
            break;
        case "formModules":
            organisationDetailModel.org_modules(response.org_modules);
            break;
        default:
            //nothing really to do here
            break;
    }
}

</script>
<script type="text/html" id="formatTemplate">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-2"><label class="form-inline"> <input type="radio" class="form-control" name="format_type" value="1" data-bind="checked: format_type"/>Manual</label></div>
            <div class="col-md-2"><label class="form-inline"> <input type="radio" class="form-control" name="format_type" value="2" data-bind="checked: format_type"/>Dynamic</label></div>
            <div class="col-md-4" data-bind="if:format_type()==2"><button class="btn btn-sm btn-default-outline" data-bind="click: add_section, enable: format_sections().length<9">Add Section</button></div>
        </div>
        <div data-bind="if:format_type()==2">
        <!-- ko foreach: format_sections -->
        <div class="row">
            <div class="col-md-3">
                <select name="separator_id" class="form-control" data-bind="visible: $index, options:$parent.separators, optionsText:'label', optionsAfterRender: setOptionValue('value'),optionsValue:'value', optionsCaption: '--none--', value: section_seperator, ">
                </select>
            </div>
            <div class="col-md-4">
                <select name="format_id" class="form-control" data-bind="options:$parent.format_options, optionsText:'label', optionsAfterRender: setOptionValue('id'),optionsValue:'id', optionsCaption: '--select--', value: section_format, ">
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control" min="0" name="section_start" placeholder="Start" data-bind="attr: {type:section_format()==3?'number':'text'}, visible:section_format()<4, value: section_start"/>
            </div>
            <div class="col-md-2">
                <input type="number" min="0" class="form-control" name="section_length" placeholder="Length" data-bind="visible:section_format()==3, value: section_length"/>
            </div>
            <div class="col-md-1" data-bind="visible: $index"><a class="text-danger" data-bind="click: $parent.remove_section"><i class="fa fa-minus-circle"></i></a></div>
        </div>
        <!-- /ko -->
    </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group row">
            <div class="col-md-8" data-bind="if:format_type()==2">Sample: <strong data-bind="text: final_format"></strong></div>
            <div class="col-md-4"><button class="btn btn-sm btn-primary" data-bind="click: submit_format">Save</button></div>
        </div>
    </div>
</script>