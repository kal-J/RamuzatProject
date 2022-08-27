<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-details"><i class="fa fa-list-alt"></i> Asset Details</a></li> 
                        <li><a class="nav-link" data-toggle="tab" href="#tab-depreciation"><i class="fa fa-line-chart"></i> Depreciation</a></li> 
                        <li><a class="nav-link" data-toggle="tab" href="#tab-asset_payment"><i class="fa fa-line-chart"></i> Payments</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-details" class="tab-pane active">
                            <?php $this->load->view('accounts/fixed_asset/details_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-depreciation" class="tab-pane">
                            <?php $this->load->view('accounts/fixed_asset/depreciation/tab_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-asset_payment" class="tab-pane">
                            <?php $this->load->view('accounts/fixed_asset/payments/tab_view'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $('form#formDepreciation').validate({submitHandler: saveData2});
        $('form#formAsset_payment').validate({submitHandler: saveData2});
        var ViewModel = function () {
            var self = this;
            self.fixed_asset_detail = ko.observable(<?php echo json_encode($fixed_asset); ?>);
            self.asset_paid_amount = ko.observable(0);
            self.depreciation_amount = ko.observable(0);
            self.transaction_channel = ko.observable();
            self.transaction_channels = ko.observableArray(<?php echo json_encode($transaction_channels);?>);
            self.pay_label = ko.computed(function () {
                var payment_mode_acc_text = "Cash";
                if (typeof self.fixed_asset_detail() !== 'undefined' && self.fixed_asset_detail()) {
                    if (self.fixed_asset_detail().payment_mode_id == 2) {
                        payment_mode_acc_text = "Bank";
                    }
                    if (self.fixed_asset_detail().payment_mode_id == 3) {
                        payment_mode_acc_text = "Credit";
                    }
                }
                return payment_mode_acc_text;
            });
            self.initialize_edit = function () {
                edit_data(self.fixed_asset_detail(), "formFixed_asset");
            };
        };

        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        var handleDataTableButtons = function () {
<?php $this->load->view("accounts/fixed_asset/depreciation/table_js"); ?>
<?php $this->load->view("accounts/fixed_asset/payments/table_js"); ?>
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
    function set_selects(data) {
        edit_data(data, 'formFixed_asset');
        //set the account from accordingly
        $('#account_from_id').val(data.account_id).trigger('change');
        //as well as the account to object

        viewModel.account_to_id(ko.utils.arrayFirst(viewModel.accountToList(), function (accountto) {
            return (parseInt(data.second_account_id) === parseInt(accountto.id));
        }));
        $('#account_to_id').val(data.second_account_id).trigger('change');
    }
    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        //compute the sums of the collateral or guarantors
        if (theData.length > 0) {
            if (theData[0]['financial_year_id']) {//depreciation data array
                viewModel.depreciation_amount(sumUp(theData, 'amount'));
            }
            if (theData[0]['transaction_channel_id']) {//depreciation data array
                viewModel.asset_paid_amount(sumUp(theData, 'amount'));
            }
        }
    }
</script>

