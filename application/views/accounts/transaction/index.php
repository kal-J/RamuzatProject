<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-transactions"><i class="fa fa-line-chart"></i> Transactions</a></li> 
                    </ul>
                    <div class="tab-content">
                        <?php $this->load->view('accounts/transaction/tab_view.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var ledgerModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $('form#formLedger').validator().on('submit', saveData);
        var LedgerModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.accountFromList = ko.observableArray(<?php echo json_encode($accounts_from); ?>);

            self.account_name = ko.observable();
            self.account_name2 = ko.observable();
            self.filteredAccountToList = ko.computed(function () {
                if (self.account_name()) {
                    return ko.utils.arrayFilter(self.accountFromList(), function (accountto) {
                        return (parseInt(self.account_name().id) !== parseInt(accountto.id));
                    });
                }
            });
            self.initialize_edit = function () {
                edit_data(self.formatOptions(), "form");
            };
        };
        ledgerModel = new LedgerModel();
        ko.applyBindings(ledgerModel);
        var handleDataTableButtons = function (tabClicked) {
            <?php $this->view('accounts/transaction/table_js'); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-transactions");

    });
    function set_selects(data) {
        edit_data(data, 'formLedger');
        //set the account from accordingly
        $('#account_from_id').val(data.account_id).trigger('change');
        //as well as the account to object

        ledgerModel.account_to_id(ko.utils.arrayFirst(ledgerModel.accountToList(), function (accountto) {
            return (parseInt(data.second_account_id) === parseInt(accountto.id));
        }));
        $('#account_to_id').val(data.second_account_id).trigger('change');
    }
</script>

