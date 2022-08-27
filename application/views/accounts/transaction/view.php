<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("accounts"); ?>">Ledger Accounts</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped" width="100%" >
                        <tbody data-bind="with: transaction_details">
                            <tr>
                                <th>Transaction No.</th>
                                <td data-bind="text: id">##XVS</td>
                                <th>Ref No.</th>
                                <td data-bind="text: ref_no">##XVS</td>
                            </tr>
                            <tr>
                                <th>Journal type</th>
                                <td data-bind="text: type_name">description</td>
                                <th>Ref ID.</th>
                                <td data-bind="text: ref_id">####</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td data-bind="text: moment(transaction_date,'YYYY-MM-DD').format('DD-MMMM-YYYY')">description</td>
                                <th>Amount</th>
                                <td data-bind="text: curr_format(tt_amount*1)">##XVS</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td data-bind="text: description" colspan="3">description</td>
                                 
                            </tr>
                            <tr data-bind="visible:parseInt(status_id)==3">
                                 <th  >Status</th>
                                <td style="font-weight: bold;color: red;" >Reversed on { <span data-bind="text: moment(reversed_date,'YYYY-MM-DD').format('DD-MMMM-YYYY')"></span> }</td>
                                 <th>Reason</th>
                                <td  style="font-weight: bold;color: red;" data-bind="text: reverse_msg" >description</td>
                                 
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header">
                <h3>Transaction Items</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table-bordered display compact nowrap table-hover" id="tblJournal_transaction_line" width="100%" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Account</th>
                                <th>Date</th>
                                <th>Narrative</th>
                                <th>Debit </th>
                                <th>Credit </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Totals</th>
                                <th>Debit </th>
                                <th>Credit </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php //echo $this->view("accounts/transaction/post_entry"); ?>
<script>
    var dTable = {};
    var viewModel = {};
    $(document).ready(function () {
        $('form#formJournal_transaction').validator().on('submit', saveData);
        /*********************************** Page Data Model (Knockout implementation) *****************************************/
        var ViewModel = function () {
            var self = this;
            self.transaction_details = ko.observable(<?php echo json_encode($detail); ?>);
            self.initialize_edit = function () {
                edit_data(self.journal_transaction(), "formJournal_transaction");
            };
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);

        var handleDataTableButtons = function () {
<?php $this->view("accounts/transaction/lines/table_js"); ?>
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
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formJournal_transaction":
            journal_transactionDetailModel.journal_transaction(response.journal_transaction);
            break;
        default:
            //nothing really to do here
            break;
    }
}
</script>