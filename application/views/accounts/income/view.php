<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
            <div class="pull-right add-record-btn">
            <?php if(in_array('3', $accounts_privilege)){ ?>
               <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_income-modal"><i class="fa fa-plus-circle"></i> Update Sale Details </button>
           <?php } ?>
           </div>
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-details"><i class="fa fa-list-alt"></i> Sale Details</a></li> 
                        <li><a class="nav-link" data-toggle="tab" href="#tab-accounts_details"><i class="fa fa-line-chart"></i> Accounts details</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-details" class="tab-pane active">
                            <?php $this->load->view('accounts/income/detail_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-accounts_details" class="tab-pane">
                            <?php $this->load->view('accounts/income/income_line/tab_view'); ?>
                        </div>
                    </div>
                </div>
        <?php //$this->load->view('accounts/income/add_modal'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $('form#formIncome_line').validate({submitHandler: saveData2});
        var ViewModel = function () {
            var self = this;
            self.income_detail = ko.observable(<?php echo json_encode($income); ?>);
            self.income_paid_amount = ko.observable(0);
            self.initialize_edit = function () {
                edit_data(self.income_detail(), "formIncome");
            };
        };

        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        var handleDataTableButtons = function () {
<?php $this->load->view("accounts/income/income_line/table_js"); ?>
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
        edit_data(data, 'formIncome');
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
            if (theData[0]['transaction_channel_id']) {//depreciation data array
                viewModel.income_paid_amount(sumUp(theData, 'amount'));
            }
        }
    }
</script>