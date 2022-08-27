<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("setting"); ?>">Settings</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> Share  Details</a></li>
               <!--  <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-share_issuance_category">Share Prices</a></li> -->
                <!-- <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-share_call">Share  Calls</a></li> -->
                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-share-product-fees">Share  Fees</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div class="panel-title pull-right">
                            <?php if (in_array('3', $share_issuance_privilege)) { ?>
                                <a href="#add_share_issuance-modal" data-bind="click: initialize_edit" data-toggle="modal"  class="btn btn-default btn-sm">
                                    <i class="fa fa-pencil"></i> Edit</a>
                            <?php } ?>
                            <?php
                            $modalTitle = "Edit Share Issuance Info";
                            $saveButton = "Update";
                            $this->load->view('setting/shares/share_issuance/add_share_issuance');
                            ?>
                        </div>
                        <table class="table table-user-information table-stripped  m-t-md">
                            <tbody data-bind="with: product">
                                <tr>
                                    <td colspan="6">

                                            <div class="form-group row">
                                                 <label class="col-lg-2 col-form-label"><strong>Total Shares to Issue </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (share_to_issue)?share_to_issue:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Date of Issue </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (date_of_issue)? date_of_issue:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Closing Date </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (closing_date)? closing_date:'None'"></div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="hr-line-dashed"> </div>
                                        
                                        <fieldset class="col-lg-12" data-bind="">     
                                            <legend>Lock in period</legend>
                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (default_lock_in_period)?default_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (min_lock_in_period)?min_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (max_lock_in_period)?max_lock_in_period:'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Locked in</strong></label>
                                                <div class="col-lg-1">
                                                    <div data-bind="text: (lock_in_period_id)?made_every_name:'None'">
                                                    </div>
                                                </div>

                                            </div>
                                        </fieldset>
                                        <div class="hr-line-dashed"> </div>
                                        <fieldset class="col-lg-12 ">
                                            <div class="form-group row">  
                                                <label class="col-lg-3 col-form-label"><strong>Allow Inactive Clients Dividends</strong></label>
                                                <div class="col-lg-3">
                                                    <div data-bind="text: (allow_inactive_clients_dividends)?(allow_inactive_clients_dividends==1)?'Yes':'No':'None'"></div>
                                                </div>
                                                <label class="col-lg-3 col-form-label"><strong>Refund Deadline</strong></label>
                                                <div class="col-lg-3">
                                                    <div data-bind="text: (refund_deadline)?refund_deadline:'None'"></div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-share-product-fees" class="tab-pane">
                    <div class="panel-body">
                        <div><strong>Share Issuance Fees</strong> <?php if (in_array('1', $share_issuance_privilege)) { ?> <a data-toggle="modal" href="#add_share_issuance_fees-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> New fee </a>  <?php } ?></div>
                        <div class="table-responsive">
                            <table id="tblShare_issuance_fees" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fee Name</th>
                                        <th>Amount Calculated As</th>
                                        <th>Amount/Rate</th>
                                        <th>Income Account</th>
                                        <th>Receivable Account</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive--> 
                    </div>
                </div><!--End of Fees section-->
         <?php //$this->load->view("setting/shares/share_calls/tab_view"); ?>
         <?php // $this->load->view("setting/shares/issuance_categories/tab_view"); ?>
         <?php //$this->load->view("setting/shares/share_calls/add_modal"); ?>
         <?php //$this->load->view("setting/shares/issuance_categories/add_modal"); ?>
            </div>
        </div>
    </div>
</div>
<?php echo $add_share_issuance_fees_modal; ?>
<script>
    var dTable = {};
    var savePdtDetailModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $('form#formShare_issuance').validator().on('submit', saveData);
        $('form#formShare_issuance_category').validate({submitHandler: saveData2});  

        $('form#formShare_issuance_fees').validator().on('submit', saveData);
        $('form#formShare_call').validator().on('submit', saveData);

        var SavePdtDetailModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.product = ko.observable(<?php echo json_encode($share_issuance); ?>);
            self.repayment_made_every_options = ko.observableArray(<?php echo json_encode($repayment_made_every); ?>);
            self.active_period = ko.observable();
            self.active_lock_period = ko.observable();
            self.share_fees = ko.observable(<?php echo json_encode($share_fee); ?>);
            self.feename = ko.observable();

            self.share_categories = ko.observable(<?php echo json_encode($share_categories); ?>);
            self.category_id = ko.observable();
            self.initialize_edit = function () {
                edit_data(self.product(), "formShare_issuance");

            }
             self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };


            self.select2accounts = function (sub_category_id) {
                //its possible to send multiple subcategories as the parameter
            var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                return Array.isArray(sub_category_id)?(check_in_array(account.sub_category_id,sub_category_id)):(account.sub_category_id == sub_category_id);
            });
            return filtered_accounts;
        };
            
               self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);

            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };
            
            self.select2accounts = function (sub_category_id) {
                //its possible to send multiple subcategories as the parameter
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                    return Array.isArray(sub_category_id)?(check_in_array(account.sub_category_id,sub_category_id)):(account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };

        };
        var handleDataTableButtons = function (tabClicked) {
            <?php $this->view('setting/shares/share_issuance_fees/table_js'); ?>
            <?php //$this->load->view("setting/shares/share_calls/table_js"); ?>
            <?php // $this->load->view("setting/shares/issuance_categories/table_js"); ?>

        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init();


        savePdtDetailModel = new SavePdtDetailModel();
        ko.applyBindings(savePdtDetailModel);
    });
     function display_footer_sum(api, columns) {
        $.each(columns, function (key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html((overall_total===parseInt(100))?overall_total:'<font color="red">'+overall_total+'    {Percentage is less than 100}</font>');
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
    }
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formShare_issuance":
                savePdtDetailModel.product(response.share_issuance);
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
