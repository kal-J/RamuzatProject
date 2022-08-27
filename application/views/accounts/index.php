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
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
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
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-transactions"><i class="fa fa-money"></i> Transactions</a></li>
                        <!-- 
                        <li><a class="nav-link" data-toggle="tab" href="#tab-assets" data-bind="click: display_table"><i class="fa fa-money"></i> Asset Management</a></li>

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Sales</a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-income"><i class="fa fa-money"></i> Sales </a></li>
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-invoice"><i class="fa fa-list"></i> Invoices</a></li>

                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-service_category"><i class="fa fa-list"></i>Categories</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Expenses</a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-expense"><i class="fa fa-money"></i> Expenses</a></li>
                               
                                <li><a class="nav-link" data-toggle="tab" href="#tab-bill" data-bind="click: display_table"><i class="fa fa-money"></i> Bills</a></li>
                                
                            </ul>
                        </li>
                        
                        <li><a class="nav-link" data-toggle="tab" href="#tab-supplier" data-bind="click: display_table"><i class="fa fa-money"></i> Suppliers</a></li>
                       -->
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-chart_of_accounts"><i class="fa fa-money"></i> Chart of Accounts</a></li>

                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-declarations"><i class="fa fa-money"></i>Dividend Declaration</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-fiscal"><i class="fa fa-calendar"></i> Fiscal Year</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-transactions_log"><i class="fa fa-money"></i>Reversed Transactions</a></li>
                        <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-sales"><i class="fa fa-shopping-cart"></i>Sales</a></li>
                        <?php } ?>



                    </ul>
                    <div class="tab-content">
                        <?php $this->view('accounts/chart_of_accounts_tab'); ?>
                        <?php $this->view('accounts/transaction/tab_view_1'); ?>
                        <?php $this->view('accounts/transaction/transaction_log_tab'); ?>
                        <?php $this->view('accounts/transaction/reverse_modal'); ?>
                        <?php //$this->view('accounts/transaction/loan_posting'); 
                        ?>
                        <?php $this->view('accounts/fixed_asset/tab_view'); ?>
                        <?php $this->view('accounts/expense/tab_view'); ?>
                        <?php $this->view('accounts/invoice/tab_view'); ?>
                        <?php $this->view('accounts/bill/tab_view'); ?>
                        <?php $this->view('accounts/sales/tab_view'); ?>
                        <?php $this->view('accounts/income/tab_view'); ?>
                        <?php $this->view('accounts/income/category/view'); ?>
                        <?php $this->view('accounts/dividend/declaration/tab_view'); ?>
                        <?php $this->view('accounts/supplier/tab_view'); ?>
                        <?php $this->view('accounts/fiscal/fiscal_year_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('accounts/add_account'); ?>
<?php $this->view('accounts/fiscal/add_modal');  ?>
<?php $this->view('accounts/sales/make_sales_modal');  ?>
<?php $this->view('accounts/closing/close_fiscal_data');  ?>
<?php $this->view('accounts/closing/undo_close_fiscal');  ?>
<?php //$this->load->view('accounts/fixed_asset/add_modal'); 
?>


<script>
    var dTable = {};
    var accountModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date, drp;
    var GeneralLedgerAccount = function() {

        var self = this;
        self.general_ledger_account = ko.observable();
        self.debit_amount = ko.observable();
        self.credit_amount = ko.observable();
        self.debit_focus = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 1));
        self.credit_focus = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 2));
        self.debit_focus1 = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 1));
        self.credit_focus1 = ko.observable((typeof self.general_ledger_account() !== 'undefined' && self.general_ledger_account().normal_balance_side === 2));
        self.narrative = ko.observable();
        self.id = ko.observable();
        //self.amount = ko.observable(0);
        self.credit_amount.subscribe(function(new_amount) {
            if (new_amount !== null) {
                self.debit_amount(null);
                //self.amount(new_amount);
            }
        });
        self.debit_amount.subscribe(function(new_amount) {
            if (new_amount !== null) {
                self.credit_amount(null);
                //self.amount(new_amount);
            }
        });
    };
    var ExpenseLine = function() {
        var self = this;
        self.selected_account = ko.observable();
        self.narrative = ko.observable();
        self.amount = ko.observable(0);
        self.id = ko.observable();
    };
    var BillLine = function() {
        var self = this;
        self.selected_account = ko.observable();
        self.narrative = ko.observable();
        self.amount = ko.observable(0);
        self.id = ko.observable();
    };
    var BillPaymentLine = function() {
        var self = this;
        self.supplier_account_id = ko.observable();
        self.due_amount = ko.observable(0);
        self.amount_paid = ko.observable(0);
        self.narrative = ko.observable();
        self.due_date = ko.observable();
        self.total_amount = ko.observable(0);
        self.bill_id = ko.observable(0);
        self.ref_no = ko.observable(0);
        self.id = ko.observable();
    };
    var IncomeLine = function() {
        var self = this;
        self.selected_account = ko.observable();
        self.narrative = ko.observable();
        self.amount = ko.observable(0);
        self.id = ko.observable();
    };
    var InvoiceLine = function() {
        var self = this;
        self.selected_account = ko.observable();
        self.narrative = ko.observable();
        self.amount = ko.observable(0);
        self.id = ko.observable();
    };
    var InvoicePaymentLine = function() {
        var self = this;
        self.receivable_account_id = ko.observable();
        self.due_amount = ko.observable(0);
        self.amount_paid = ko.observable(0);
        self.narrative = ko.observable();
        self.due_date = ko.observable();
        self.total_amount = ko.observable(0);
        self.invoice_id = ko.observable(0);
        self.ref_no = ko.observable(0);
        self.id = ko.observable();
    };
    $(document).ready(function() {
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");

        $(".select2able").select2({
            allowClear: false
        });

        $("#formClose_fiscal").steps({
            labels: {
                finish: "Close Fiscal Year",
                cancel: "Cancel"

            },
            bodyTag: "section",
            onInit: function(event, current) {
                //alert(current);
            },
            onFinishing: function(event, currentIndex) {
                var form = $(this);
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
            onFinished: function(event, currentIndex) {
                var form = $(this);
                // Submit form input
                saveData2(form);
            }
        });

        jQuery.validator.addMethod("valid_acc_no", function(value, element) {
            return this.optional(element) || /^[0-9]{1,5}$/.test(value);
        }, "Please provide digits for account no");
        $('#account_pay_with_id').select2({
            dropdownParent: $("#add_asset-modal")
        });
        $('.account_creation').select2({
            dropdownParent: $("#add_account-modal")
        });
        $('.asset_creation').select2({
            dropdownParent: $("#add_asset-modal")
        });
        $('.expense_category_modal').select2({
            dropdownParent: $("#add_expense_category-modal")
        });
        $('.expense_selects').select2({
            dropdownParent: $("#add_expense-modal")
        });
        $('.bill_selects').select2({
            dropdownParent: $("#add_bill-modal")
        });
        $('.service_category_modal').select2({
            dropdownParent: $("#add_service_category-modal")
        });
        $('.bill_payment_selects').select2({
            dropdownParent: $("#pay_bill-modal")
        });
        $('.supplier_selects').select2({
            dropdownParent: $("#add_supplier-modal")
        });
        $('.income_selects').select2({
            dropdownParent: $("#add_income-modal")
        });
        $('.invoice_selects').select2({
            dropdownParent: $("#add_invoice-modal")
        });
        $('.dividend_selects').select2({
            dropdownParent: $("#add_dividend_declaration-modal")
        });
        $('.invoice_payment_selects').select2({
            dropdownParent: $("#pay_invoice-modal")
        });
        $('.dividend_payment_selects').select2({
            dropdownParent: $("#pay_dividend-modal")
        });
        $('form#formAccounts').validate({
            rules: {
                new_account_code: {
                    required: true,
                    valid_acc_no: true,
                    remote: {
                        url: "<?php echo site_url("accounts/validate_acc_no"); ?>",
                        type: "post",
                        data: {
                            account_code: function() {
                                return $("#account_code").val();
                            },
                            id: function() {
                                return $("form#formAccounts input[name='id']").val();
                            }
                        }
                    }
                }
            },
            submitHandler: saveData2
        });
        $('form#formClose_fiscal').validate({
            submitHandler: saveData2
        });
        $('form#formUndo_close').validate({
            submitHandler: saveData2
        });
        $('form#formFixed_asset').validate({
            submitHandler: saveData2
        });
        $('form#formSales').validate({
            submitHandler: saveData2
        });
        $('form#formFiscal_year').validate({
            rules: {
                end_date: {
                    required: true,
                    remote: {
                        url: "<?php echo site_url("Fiscal_year/end_date_check"); ?>",
                        type: "post",
                        data: {
                            check: 1,
                            start_date: function() {
                                return $("#start_date").val();
                            }
                        }
                    }
                }
            },
            submitHandler: saveData2
        });
        $('form#formExpense').validate({
            submitHandler: saveData2
        });
        $('form#formBill').validate({
            submitHandler: saveData2
        });
        $('form#formBill_payment').validate({
            submitHandler: saveData2
        });
        $('form#formIncome').validate({
            submitHandler: saveData2
        });
        $('form#formInvoice').validate({
            submitHandler: saveData2
        });
        $('form#formService_category').validate({
            submitHandler: saveData2
        });
        $('form#formInvoice_payment').validate({
            submitHandler: saveData2
        });
        $('form#formSupplier').validator().on('submit', saveData);
        $('form#formDividend_declaration').validate({
            submitHandler: saveData2
        });
        $('form#formDividend_payment').validate({
            submitHandler: saveData2
        });
        $('form#formReverseJournal_transaction').validate({
            submitHandler: saveData2
        });

        $("form#formJournal_transaction").validate({
            rules: {
                fund_source_account: {
                    remote: {
                        url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                        type: "post",
                        data: {
                            amount: function() {
                                return $("form#formActive input[name='amount_approved']").val();
                            },
                            account_id: function() {
                                return $("form#formActive input[name='source_fund_account_id']").val();
                            }
                        }
                    }
                }

            },
            submitHandler: saveData2
        });

        // TEMPORARY  CODE FOR POSTING OLD LOANS
        // $("form#formLoanJournal_transaction").validate({
        // rules: {
        //         fund_source_account:{
        //             remote: {
        //             url: "<?php // echo site_url('journal_transaction_line/check_acc_balance'); 
                                ?>",
        //             type: "post",
        //             data: {
        //                 amount: function () {
        //                     return $("form#formActive input[name='amount_approved']").val();
        //                 },
        //                 account_id: function () {
        //                     return $("form#formActive input[name='source_fund_account_id']").val();
        //                 }
        //             }
        //         }
        //        }

        // },submitHandler: saveData2});
        // END TEMPORARY CODE

        get_member_savings_account();

        get_list_of_items();


        var AccountsModel = function() {
            var self = this;
            self.display_table = function(data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.pay_with = ko.observable([{
                "id": "Cash",
                "name": "Cash"
            }, {
                "id": "Bank",
                "name": "Bank"
            }, {
                "id": "Credit",
                "name": "Credit"
            }]);
            self.account_pay = ko.observable();

            self.account_name = ko.observable();
            self.account_name2 = ko.observable();

            self.subcat_list = ko.observableArray(<?php echo json_encode($subcat_list); ?>);
            self.journal_types = ko.observableArray(<?php echo json_encode($journal_types); ?>);
            self.fiscal_years = ko.observableArray(<?php echo json_encode($fiscal_years); ?>);
            self.fiscal_year = ko.observable();

            self.accounts_list = ko.observableArray();
            self.transaction_channels = ko.observableArray(<?php echo json_encode($transaction_channels); ?>);
            self.transaction_channel = ko.observable();
            self.countries = ko.observableArray(<?php echo json_encode($countries); ?>);
            self.supplier_list = ko.observableArray();
            self.supplier = ko.observableArray();
            self.tax_list = ko.observableArray();
            self.authorizer_list = ko.observableArray(<?php echo json_encode($staff_list); ?>);
            self.overall_total_credit = ko.observable(0);
            self.applied_tax = ko.observable();
            self.selected_supplier = ko.observable();
            self.paymentModeList = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            self.expense_lines = ko.observableArray([new ExpenseLine()]);
            self.bill_lines = ko.observableArray([new BillLine()]);
            self.bill_payment_lines = ko.observableArray([new BillPaymentLine()]);
            self.income_lines = ko.observableArray([new IncomeLine()]);
            self.invoice_lines = ko.observableArray([new InvoiceLine()]);
            self.invoice_payment_lines = ko.observableArray([new InvoicePaymentLine()]);
            self.paymentModeAccList = ko.observableArray();
            self.general_ledger_accounts = ko.observableArray([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
            self.general_ledger_account = ko.observable();
            //new account creation section
            self.parent_account = ko.observable();
            self.opening_balance_date = ko.observable("<?php echo date("d-m-Y"); ?>");
            self.account_sub_category = ko.observable();
            self.account_type_id = ko.observable();
            self.new_account_code = ko.observable();
            self.final_account_code = ko.computed(function() {
                if (typeof self.parent_account() !== 'undefined' && typeof self.new_account_code() !== 'undefined') {
                    return self.parent_account().account_code + "-" + self.new_account_code();
                }
                if (typeof self.account_sub_category() !== 'undefined' && typeof self.new_account_code() !== 'undefined') {
                    return self.account_sub_category().sub_cat_code + "-" + self.new_account_code();
                }
            });
            self.parent_accounts = ko.computed(function() {
                if (typeof self.account_sub_category() !== 'undefined') {
                    return ko.utils.arrayFilter(self.accounts_list(), function(single_account) {
                        //console.log(single_account.sub_category_id+" Yesss " + self.account_sub_category().id);
                        return single_account.sub_category_id == self.account_sub_category().id;
                    });
                }
                return [];
            });
            //end new account creation section
            self.form_origin = ko.observable(0);
            self.payment_mode = ko.observable();
            self.pay_label = ko.computed(function() {
                var payment_mode_acc_text = "Cash";
                if (typeof self.payment_mode() !== 'undefined' && self.payment_mode()) {
                    if (self.payment_mode().id == 2) {
                        payment_mode_acc_text = "Bank";
                    }
                    if (self.payment_mode().id == 3) {
                        payment_mode_acc_text = "Credit";
                    }
                }
                return payment_mode_acc_text;
            });

            self.filteredAccountToList = ko.computed(function() {
                if (self.account_name()) {
                    return ko.utils.arrayFilter(self.accounts_list(), function(accountto) {
                        return (parseInt(self.account_name().id) !== parseInt(accountto.id));
                    });
                }
            });
            //return concatenated names of the Account
            self.detail_accounts = ko.computed(function() {
                return ko.utils.arrayFilter(self.accounts_list(), function(account) {
                    //return account.account_type_id == 2;
                    return true;
                });
            });
            self.accountsList = ko.computed(function() {
                var data = $.map(self.accounts_list(), function(account) {
                    account.disabled = account.account_type_id == 1; // replace pk with your identifier
                    return account;
                });
                return data;
            });
            self.select2accounts = function(sub_category_id) {
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function(account) {
                    return Array.isArray(sub_category_id) ? (check_in_array(account.sub_category_id, sub_category_id)) : (account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };
            self.select2accountsById = function(id) {
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function(account) {
                    return Array.isArray(id) ? (check_in_array(account.id, id)) : (account.id == id);
                });
                return filtered_accounts;
            };
            self.formatAccount2 = function(account) {
                return account.account_code + " " + account.account_name;
            };
            self.initialize_edit = function() {
                edit_data(self.formatOptions(), "form");
            };
            self.reset_form = function() {
                self.general_ledger_accounts = ko.observableArray([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
            };
            self.addGeneralLedgerAccount = function() {
                self.general_ledger_accounts.push(new GeneralLedgerAccount());

                // add select2 to post_entry form
                const postEntrySelects = document.querySelectorAll('.acc_post_entry');
                postEntrySelects.forEach(select => {
                    $(select).select2();
                });
            };
            self.removeGeneralLedgerAccount = function(selected_account) {
                //removing the ones that were added by the user
                self.general_ledger_accounts.remove(selected_account);
            };
            //Dealing with expenses
            self.addExpenseLine = function() {
                self.expense_lines.push(new ExpenseLine());
            };
            self.removeExpenseLine = function(selected_expense_line) {
                //removing the ones that were added by the user
                self.expense_lines.remove(selected_expense_line);
            };
            self.addBillLine = function() {
                self.bill_lines.push(new ExpenseLine());
            };
            self.removeBillLine = function(selected_bill_line) {
                self.bill_lines.remove(selected_bill_line);
            };
            self.removeBillPaymentLine = function(selected_bill_payment_line) {
                self.bill_payment_lines.remove(selected_bill_payment_line);
            };
            //Dealing with Income
            self.addIncomeLine = function() {
                self.income_lines.push(new IncomeLine());
            };
            self.removeIncomeLine = function(selected_income_line) {
                //removing the ones that were added by the user
                self.income_lines.remove(selected_income_line);
            };
            self.addInvoiceLine = function() {
                self.invoice_lines.push(new InvoiceLine());
            };
            self.removeInvoiceLine = function(selected_invoice_line) {
                self.invoice_lines.remove(selected_invoice_line);
            };
            self.removeInvoicePaymentLine = function(selected_invoice_payment_line) {
                self.invoice_payment_lines.remove(selected_invoice_payment_line);
            };
            //SUMMATIONS
            //general journal transaction
            self.gjtotals = ko.computed(function() {
                var debit_total = 0,
                    credit_total = 0;
                $.each(self.general_ledger_accounts(), function(key, account) {
                    if (typeof account.general_ledger_account() !== undefined) {
                        debit_total += (typeof account.debit_amount() !== 'undefined' && account.debit_amount() !== null) ? parseFloat(account.debit_amount()) : 0;
                        credit_total += (typeof account.credit_amount() !== 'undefined' && account.credit_amount() !== null) ? parseFloat(account.credit_amount()) : 0;
                    }
                });
                return {
                    debit: debit_total,
                    credit: credit_total
                };
            });
            //Dealing with expenses
            self.expense_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.expense_lines(), function(key, account) {
                    if (typeof account.selected_account() !== undefined) {
                        total_amount += (account.amount() !== undefined && account.amount() !== null) ? parseFloat(account.amount()) : 0;
                    }
                });
                return total_amount;
            });
            self.bill_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.bill_lines(), function(key, bill_line) {
                    if (typeof bill_line.selected_account() !== undefined) {
                        total_amount += (bill_line.amount() !== undefined && bill_line.amount() !== null) ? parseFloat(bill_line.amount()) : 0;
                    }
                });
                return total_amount;
            });
            self.bill_payment_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.bill_payment_lines(), function(key, bill_payment_line) {
                    total_amount += (bill_payment_line.amount_paid() !== undefined && bill_payment_line.amount_paid() !== null) ? parseFloat(bill_payment_line.amount_paid()) : 0;
                });
                return total_amount;
            });
            //Dealing with Income
            self.income_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.income_lines(), function(key, account) {
                    if (typeof account.selected_account() !== undefined) {
                        total_amount += (account.amount() !== undefined && account.amount() !== null) ? parseFloat(account.amount()) : 0;
                    }
                });
                return total_amount;
            });
            self.invoice_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.invoice_lines(), function(key, invoice_line) {
                    if (typeof invoice_line.selected_account() !== undefined) {
                        total_amount += (invoice_line.amount() !== undefined && invoice_line.amount() !== null) ? parseFloat(invoice_line.amount()) : 0;
                    }
                });
                return total_amount;
            });
            self.invoice_payment_sum = ko.computed(function() {
                var total_amount = 0;
                $.each(self.invoice_payment_lines(), function(key, invoice_payment_line) {
                    total_amount += (invoice_payment_line.amount_paid() !== undefined && invoice_payment_line.amount_paid() !== null) ? parseFloat(invoice_payment_line.amount_paid()) : 0;
                });
                return total_amount;
            });

            //issuance account
            self.share_issuance = ko.observableArray(<?php echo json_encode($share_issuances); ?>);
            self.issuance = ko.observable();
            //shares section
            self.total_dividends = ko.observable(0);
            self.total_computed_share = ko.observable(0);
            self.dividend_per_share = ko.observable(0);
            self.profit_loss = ko.observable(0);
            self.no_shares = ko.observable(0);
            self.price_per_share = ko.observable(0);
            self.income_sums = ko.observable(0);
            self.expense_sums = ko.observable(0);
            self.asset_sums = ko.observable(0);
            self.liability_sums = ko.observable(0);
            self.equity_sums = ko.observable(0);
            self.credit_sums = ko.observable(0);
            self.debit_sums = ko.observable(0);
            self.total_shares = ko.observable(0);
            self.dividend_record_date = ko.observable();

            self.member_accounts = ko.observableArray();

            self.items_list = ko.observableArray();

            self.total_dividends.subscribe(function(new_amount) {
                if (new_amount !== null) {
                    self.dividend_per_share(round(new_amount / self.no_shares(), 2));
                }
            });
            self.total_computed_share.subscribe(function(new_amounts) {
                if (new_amounts !== null) {
                    self.dividend_per_share(round(new_amounts / self.no_shares(), 2));
                }
            });
            self.dividend_record_date.subscribe(function(record_date) {
                get_total_income(record_date);
            });
            self.dividend_per_share.subscribe(function(new_amount) {
                if (new_amount !== null) {
                    //self.total_dividends(round(new_amount*self.no_shares(),2));
                }
            });

        };
        accountsModel = new AccountsModel();
        ko.applyBindings(accountsModel);

        <?php //$this->view('includes/daterangepicker');  
        ?>
        //drp.setStartDate('03/01/2019');
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer();
        get_total_income();
        var handleDataTableButtons = function(tabClicked) {
            <?php $this->view('accounts/chart_of_accounts_js'); ?>
            <?php $this->view('accounts/fixed_asset/table_js'); ?>
            <?php $this->view('accounts/expense/table_js'); ?>
            <?php $this->view('accounts/bill/table_js'); ?>           
            <?php $this->view('accounts/sales/table_js'); ?>
            <?php $this->view('accounts/income/table_js'); ?>
            <?php $this->view('accounts/income/category/table_js'); ?>
            <?php $this->view('accounts/invoice/table_js'); ?>
            <?php $this->view('accounts/dividend/declaration/table_js');   ?>
            <?php $this->view('accounts/transaction/table_js_1'); ?>
            <?php $this->view('accounts/transaction/transaction_log_js'); ?>
            <?php $this->view('accounts/supplier/table_js'); ?>
            <?php $this->view('accounts/fiscal/table_js'); ?>
        };

        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-transactions");
        TableManageButtons.init("tab-supplier");
        TableManageButtons.init("tab-chart_of_accounts");
        //TableManageButtons.init("tab-expense_category");
        //TableManageButtons.init("tab-service_category");

        //Any edit to be done uses this code for modal popu-up
        $('table#tblBill tbody').on('click', 'tr .pay_bill', function(e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var dt = dTable["tblBill"];
            var data = dt.row(row).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof(data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            //lets get the form populated
            get_bill_payment_lines(data);
        });
        //Any edit to be done uses this code for modal popu-up
        $('table#tblInvoice tbody').on('click', 'tr .pay_invoice', function(e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var dt = dTable["tblInvoice"];
            var data = dt.row(row).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof(data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            //lets get the form populated
            get_invoice_payment_lines(data);
        });
        $('table#tblDividend_declaration tbody').on('click', 'tr .pay_dividend', function(e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var dt = dTable["tblDividend_declaration"];
            var data = dt.row(row).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof(data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            //lets get the form populated
            edit_data(data, "formDividend_payment");
        });

        // add select2 to post_entry form
        const postEntrySelects = document.querySelectorAll('.acc_post_entry');
        postEntrySelects.forEach(select => {
            $(select).select2();
        });

    });

    function get_member_savings_account() {
        var url = "<?php echo site_url("savings_account/jsonList2"); ?>";
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //clear the the other fields because we are starting the selection afresh
                accountsModel.member_accounts(null);

                accountsModel.member_accounts(response.accounts_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function get_list_of_items() {
        var url = "<?php echo site_url("sales/jsonList_items"); ?>";
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //clear the the other fields because we are starting the selection afresh
                accountsModel.items_list(null);

                accountsModel.items_list(response.data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function reload_data(formId, reponse_data) {
        switch (formId) {
            case "formAccounts":
                accountsModel.accounts_list.push(reponse_data.new_account);
                if (accountsModel.form_origin() == 1) { //click originated from the assets register form
                    $("#asset_account_id").val(reponse_data.new_account.id).trigger("change");
                }
                if (accountsModel.form_origin() == 2) { //click originated from the income form
                    $("#income_account_id").val(reponse_data.new_account.id).trigger("change");
                }
                if (accountsModel.form_origin() == 3) { //click originated from the income form
                    $("#expense_account_id").val(reponse_data.new_account.id).trigger("change");
                }
                break;
            case "formJournal_transaction":
                accountsModel.general_ledger_accounts([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
                break;
            case "formBill_payment":
                dTable['tblBill'].ajax.reload(null, false);
                break;
            case "formReverseJournal_transaction":
                dTable['tblJournal_transaction'].ajax.reload(null, false);
                break;
            case "formInvoice_payment":
                dTable['tblInvoice'].ajax.reload(null, false);
                break;
            case "formExpense":
                accountsModel.expense_lines([]);
                accountsModel.expense_lines([new ExpenseLine()]);
                break;
            case "formIncome":
                accountsModel.income_lines([]);
                accountsModel.income_lines([new IncomeLine()]);
                break;
            case "formInvoice_payment":
                dTable['tblInvoice'].ajax.reload(null, false);
                break;
            case "formFiscal_year":
            case "formClose_fiscal":
            case "formUndo_close":
                dTable['tblFiscal_year'].ajax.reload(null, false);
                accountsModel.general_ledger_accounts([new GeneralLedgerAccount(), new GeneralLedgerAccount()]);
                break;
            case "formSales":
                dTable['tblSales_transaction'].ajax.reload(null, false);
                break;
            case "formDividend_payment":
                dTable['tblDividend_declaration'].ajax.reload(null, false);
                break;
            default:
                //accountsModel.accounts_list.push(reponse_data.new_account);
                break;
        }
    }

    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        if (theData.length > 0) {
            if (theData[0]['account_name'] && theData[0]['account_code'] && theData[0]['normal_balance_side']) { //the accounts list array
                accountsModel.accounts_list(theData);
            }
            if (theData[0]['service_category_code'] && theData[0]['service_category_name']) { //if income categories
                accountsModel.incomeCategoryList(theData);
            }
            if (theData[0]['supplier_type_id'] && theData[0]['supplier_names']) { //if income categories
                accountsModel.supplier_list(theData);
            }
        }
    }

    function get_pay_with(data, url) {
        $.post(
            url,
            data,
            function(response) {
                accountsModel.paymentModeAccList(response.pay_with);
                if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                    accountsModel.payment_mode(ko.utils.arrayFirst(accountsModel.paymentModeAccList(), function(current_payment_mode) {
                        return (parseInt(data.pay_with_id) === parseInt(current_payment_mode.id));
                    }));
                    $('#account_pay_with_id').val(data.pay_with_id).trigger('change');
                }
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_total_income(record_date = false) {
        $.post(
            "<?php echo site_url('reports/profit_loss'); ?>", {
                fisc_date_from: "<?php echo $fiscal_year['start_date']; ?>",
                fisc_date_to: "<?php echo $fiscal_year['end_date']; ?>",
                record_date: record_date
            },
            function(response) {
                accountsModel.profit_loss(response.profit_loss);
                accountsModel.income_sums(response.total_income);
                accountsModel.expense_sums(response.total_expense);
                accountsModel.asset_sums(response.total_assets);
                accountsModel.liability_sums(response.total_liability);
                accountsModel.equity_sums(response.total_equity);
                accountsModel.credit_sums(response.total_credit_tr);
                accountsModel.debit_sums(response.total_debit_tr);
                accountsModel.total_shares(response.total_shares);
                accountsModel.price_per_share(response.price_per_share);
                accountsModel.no_shares(response.no_shares);
                accountsModel.total_dividends(response.profit_loss);
                accountsModel.dividend_per_share(response.profit_loss / response.no_shares);

            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_journal_transanction_lines(journal_transanction_id) {
        accountsModel.general_ledger_accounts([]);
        $.post(
            "<?php echo site_url("journal_transaction_line/jsonlist"); ?>", {
                journal_transaction_id: journal_transanction_id
            },
            function(response) {
                ko.utils.arrayForEach(response.data, function(journal_entry_line) {
                    var general_ledger_account = new GeneralLedgerAccount();
                    general_ledger_account.debit_amount(journal_entry_line.debit_amount);
                    general_ledger_account.credit_amount(journal_entry_line.credit_amount);
                    general_ledger_account.narrative(journal_entry_line.narrative);
                    general_ledger_account.id(journal_entry_line.id);
                    //let's get the particular account obj from the list of the accounts
                    general_ledger_account.general_ledger_account(get_account(journal_entry_line.account_id));
                    accountsModel.general_ledger_accounts.push(general_ledger_account);
                });
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_expense_lines(expense_id) {
        accountsModel.expense_lines([]);
        $.post(
            "<?php echo site_url("expense_line/jsonlist"); ?>", {
                expense_id: expense_id
            },
            function(response) {
                ko.utils.arrayForEach(response.data, function(server_expense_line) {
                    var expense_line = new ExpenseLine();
                    expense_line.amount(server_expense_line.amount);
                    expense_line.narrative(server_expense_line.narrative);
                    expense_line.id(server_expense_line.id);
                    //let's get the particular account obj from the list of the accounts
                    expense_line.selected_account(get_account(server_expense_line.account_id));
                    accountsModel.expense_lines.push(expense_line);
                });
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_bill_lines(bill_id) {
        accountsModel.bill_lines([]);
        $.post(
            "<?php echo site_url("bill_line/jsonlist"); ?>", {
                bill_id: bill_id
            },
            function(response) {
                ko.utils.arrayForEach(response.data, function(server_bill_line) {
                    var bill_line = new BillLine();
                    bill_line.amount(server_bill_line.amount);
                    bill_line.narrative(server_bill_line.narrative);
                    bill_line.id(server_bill_line.id);
                    //let's get the particular account obj from the list of the accounts
                    bill_line.selected_account(get_account(server_bill_line.account_id));
                    accountsModel.bill_lines.push(bill_line);
                });
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_bill_payment_lines(bills) {
        accountsModel.bill_payment_lines([]);
        if (typeof bills === 'object' && bills !== null) {
            add_bill_payment_line(bills);
        } else {
            if (Array.isArray(bills) && bills.length > 0) {
                ko.utils.arrayForEach(bills, function(single_bill) {
                    add_bill_payment_line(single_bill);
                });
            }
        }
        //edit_data(bills, "formBill_payment");
    }

    function add_bill_payment_line(single_bill) {
        var bill_payment_line = new BillPaymentLine();
        bill_payment_line.ref_no(single_bill.ref_no);
        bill_payment_line.supplier_account_id(single_bill.liability_account_id);
        var due_amount = parseFloat(single_bill.total_amount) - (single_bill.discount ? parseFloat(single_bill.discount) : 0) - (single_bill.amount_paid ? parseFloat(single_bill.amount_paid) : 0);
        bill_payment_line.due_amount(due_amount);
        bill_payment_line.due_date(single_bill.due_date);
        bill_payment_line.total_amount(single_bill.total_amount * 1);
        bill_payment_line.amount_paid(due_amount);
        bill_payment_line.bill_id(single_bill.id);
        accountsModel.bill_payment_lines.push(bill_payment_line);
    }

    function get_income_lines(income_id) {
        accountsModel.income_lines([]);
        $.post(
            "<?php echo site_url("income_line/jsonlist"); ?>", {
                income_id: income_id
            },
            function(response) {
                ko.utils.arrayForEach(response.data, function(server_income_line) {
                    var income_line = new ExpenseLine();
                    income_line.amount(server_income_line.amount);
                    income_line.narrative(server_income_line.narrative);
                    income_line.id(server_income_line.id);
                    //let's get the particular account obj from the list of the accounts
                    income_line.selected_account(get_account(server_income_line.account_id));
                    accountsModel.income_lines.push(income_line);
                });
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_invoice_lines(invoice_id) {
        accountsModel.invoice_lines([]);
        $.post(
            "<?php echo site_url("invoice_line/jsonlist"); ?>", {
                invoice_id: invoice_id
            },
            function(response) {
                ko.utils.arrayForEach(response.data, function(server_invoice_line) {
                    var invoice_line = new BillLine();
                    invoice_line.amount(server_invoice_line.amount);
                    invoice_line.narrative(server_invoice_line.narrative);
                    invoice_line.id(server_invoice_line.id);
                    //let's get the particular account obj from the list of the accounts
                    invoice_line.selected_account(get_account(server_invoice_line.account_id));
                    accountsModel.invoice_lines.push(invoice_line);
                });
            },
            'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function get_invoice_payment_lines(invoices) {
        accountsModel.invoice_payment_lines([]);
        if (typeof invoices === 'object' && invoices !== null) {
            add_invoice_payment_line(invoices);
        } else {
            if (Array.isArray(invoices) && invoices.length > 0) {
                ko.utils.arrayForEach(invoices, function(single_invoice) {
                    add_invoice_payment_line(single_invoice);
                });
            }
        }
        //edit_data(invoices, "formBill_payment");
    }

    function add_invoice_payment_line(single_invoice) {
        var invoice_payment_line = new InvoicePaymentLine();
        invoice_payment_line.ref_no(single_invoice.ref_no);
        invoice_payment_line.receivable_account_id(single_invoice.receivable_account_id);
        var due_amount = parseFloat(single_invoice.total_amount) - (single_invoice.discount ? parseFloat(single_invoice.discount) : 0) - (single_invoice.amount_paid ? parseFloat(single_invoice.amount_paid) : 0);
        invoice_payment_line.due_amount(due_amount);
        invoice_payment_line.due_date(single_invoice.due_date);
        invoice_payment_line.total_amount(single_invoice.total_amount * 1);
        invoice_payment_line.amount_paid(due_amount);
        invoice_payment_line.invoice_id(single_invoice.id);
        accountsModel.invoice_payment_lines.push(invoice_payment_line);
    }

    function get_account(account_id) {
        return ko.utils.arrayFirst(accountsModel.accounts_list(), function(current_account) {
            return current_account.id === account_id;
        });
    }

    function set_selects(data, formId) {
        switch (formId) {
            case 'formFixed_asset':
                accountsModel.cat_name(ko.utils.arrayFirst(accountsModel.subcategoryList(), function(subcategory) {
                    return (parseInt(data.pay_with_id) === parseInt(subcategory.id));
                }));
                $('#pay_with_id').val(data.pay_with_id).trigger('change');
                get_pay_with({
                    bank_or_cash: data.pay_with_id
                }, "<?php echo site_url("accounts/pay_with"); ?>");
                break;
            case 'formJournal_transaction':
                get_journal_transanction_lines(data.id);
                break;
            case 'formExpense':
                get_expense_lines(data.id);
                break;
            case 'formIncome':
                get_income_lines(data.id);
                break;
            case 'formBill':
                get_bill_lines(data.id);
                break;
            case 'formInvoice':
                get_invoice_lines(data.id);
                break;
        }
        edit_data(data, formId);
    }

    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);
        dTable['tblJournal_transaction'].ajax.reload(null, true);
    }

    
    $(document).ready(() => {

        $('#new_sales-modal').on('show.bs.modal', function(e) {
            $(".select2able").select2();

        });
    });
</script>