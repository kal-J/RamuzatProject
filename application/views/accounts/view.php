<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
         <div class="ibox-title">
             <ul class="breadcrumb">
                <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                <li><a href="<?php echo site_url("accounts"); ?>">Ledger Accounts</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
            <div class="pull-right" style="padding-left: 2%">
                <div id="reportrange" class="reportrange">
                    <i class="fa fa-calendar"></i>
                    <span></span> <b class="caret"></b>
                </div>
            </div>
        </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-transactions"><i class="fa fa-line-chart"></i> <?php echo $ledger_account['account_name']; ?></a></li> 
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
    var viewModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date;
    var GeneralLedgerAccount = function () {
        var self = this;
        self.general_ledger_account = ko.observable();
        self.debit_amount = ko.observable();
        self.credit_amount = ko.observable();
        self.debit_focus = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 1));
        self.credit_focus = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 2));
        self.narrative = ko.observable();
        self.id = ko.observable();
        //self.amount = ko.observable(0);
        self.credit_amount.subscribe(function (new_amount) {
            if (new_amount !== null) {
                self.debit_amount(null);
                //self.amount(new_amount);
            }
        });
        self.debit_amount.subscribe(function (new_amount) {
            if (new_amount !== null) {
                self.credit_amount(null);
                //self.amount(new_amount);
            }
        });
    };
    $(document).ready(function () {
        /*********************************** Page Data Model (KO implementation) *****************************************/
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); 
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        $("form#formJournal_transaction").validate({
        rules: {
                fund_source_account:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                        amount: function () {
                            return $("form#formActive input[name='amount_approved']").val();
                        },
                        account_id: function () {
                            return $("form#formActive input[name='source_fund_account_id']").val();
                        }
                    }
                }
               }
                
        },submitHandler: saveData2});
        var ViewModel = function () {
            var self = this;
            self.account_details = ko.observable(<?php echo json_encode($ledger_account); ?>);
            self.initialize_edit = function () {
                edit_data(self.formatOptions(), "form");
            };
            self.general_ledger_accounts = ko.observableArray([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
            self.general_ledger_account = ko.observable();

            self.account_name = ko.observable();
            self.account_name2 = ko.observable();

            self.subcat_list = ko.observableArray(<?php echo json_encode($subcat_list); ?>);

            self.accounts_list = ko.observableArray();
            self.select2accounts = function (sub_category_id) {
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                    return Array.isArray(sub_category_id) ? (check_in_array(account.sub_category_id, sub_category_id)) : (account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };
            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };
            self.addGeneralLedgerAccount = function () {
                self.general_ledger_accounts.push(new GeneralLedgerAccount());
            };
            self.removeGeneralLedgerAccount = function (selected_account) {
                //removing the ones that were added by the user
                self.general_ledger_accounts.remove(selected_account);
            };
              //SUMMATIONS
            //general journal transaction
            self.gjtotals = ko.computed(function () {
                var debit_total = 0, credit_total = 0;
                $.each(self.general_ledger_accounts(), function (key, account) {
                    if (typeof account.general_ledger_account() !== undefined) {
                        debit_total += (typeof account.debit_amount() !== 'undefined' && account.debit_amount() !== null) ? parseFloat(account.debit_amount()) : 0;
                        credit_total += (typeof account.credit_amount() !== 'undefined' && account.credit_amount() !== null) ? parseFloat(account.credit_amount()) : 0;
                    }
                });
                return {debit: debit_total, credit: credit_total};
            });
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        daterangepicker_initializer();
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

        TableManageButtons.init();

    });
     function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        //TableManageButtons.init(displayed_tab);
        dTable['tblJournal_transaction_line'].ajax.reload(null, true);
        $('#tab_title').html(`
        <center>Journal Transactions  { ${moment(start_date,'X').format('DD-MMM-YYYY')} - ${moment(end_date,'X').format('DD-MMM-YYYY')} }</center>
        `);
    }
     function reload_data(formId, reponse_data) {
        switch (formId) {
            case "formJournal_transaction":
                ViewModel.general_ledger_accounts([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
                break;
          
            default:
                //accountsModel.accounts_list.push(reponse_data.new_account);
                break;
        }
    }
    function get_journal_transanction_lines(journal_transanction_id) {
        ViewModel.general_ledger_accounts([]);
        $.post(
                "<?php echo site_url("journal_transaction_line/jsonlist"); ?>",
                {journal_transaction_id: journal_transanction_id},
                function (response) {
                    ko.utils.arrayForEach(response.data, function (journal_entry_line) {
                        var general_ledger_account = new GeneralLedgerAccount();
                        general_ledger_account.debit_amount(journal_entry_line.debit_amount);
                        general_ledger_account.credit_amount(journal_entry_line.credit_amount);
                        general_ledger_account.narrative(journal_entry_line.narrative);
                        general_ledger_account.id(journal_entry_line.id);
                        //let's get the particular account obj from the list of the accounts
                        general_ledger_account.general_ledger_account(get_account(journal_entry_line.account_id));
                        ViewModel.general_ledger_accounts.push(general_ledger_account);
                    });
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }
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

