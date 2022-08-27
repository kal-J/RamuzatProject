<style type="text/css">
    section{
    overflow-y : auto;
    }
  
    @media (min-width: 992px) {
      .modal-lg,
      .modal-xl {
        max-width: 700px !important;
      }
    }

    @media (min-width: 1200px) {
      .modal-xl {
        max-width: 840px !important;
      }
    }
</style>
<div class="modal inmodal fade" id="post_entry-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo site_url("journal_transaction/create"); ?>" id="formJournal_transaction">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">
                       General Journal Entry
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="journal_type_id" value="1">
                <div class="form-group row">
                    <div class="form-group col-lg-6">
                    <label class="col-form-label">Date<span class="text-danger">*</span></label>

                        <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_year['start_date2'])); ?>" data-date-end-date="<?php echo isset($active_month)?((date('d-m-Y')<date('d-m-Y', strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((date('d-m-Y')<date('d-m-Y', strtotime($fiscal_year['end_date2']))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_year['end_date2'])))); ?>" >
                            <input type="text"  onkeydown="return false" autocomplete="off" class="form-control" name="transaction_date" placeholder="Transaction date" required/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                       <!--  <div class="form-group col-lg-5">
                       <label class="col-form-label">Journal Type<span class="text-danger">*</span></label>
                            <select class="form-control select2able m-b" id="sub_category_id" name="journal_type_id" data-bind='options: journal_types,  optionsText: function(data_item1){return data_item1.type_name;}, optionsCaption: "--Transaction Type--", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                        </div> -->
                    <div class="form-group col-lg-6">
                    <label class="col-form-label">Ref No</label>
                            <input  type="text" class="form-control" name="ref_no" placeholder="Ref No." />
                    </div>
                </div>     
                <div class="form-group row">
                    <div class="form-group col-lg-12">
                    <label class="col-form-label">Description</label>
                        <textarea  class="form-control" name="description" placeholder="Description"></textarea>
                    </div>
                </div>     
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-condensed table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Narrative</th>
                                    <th><button data-bind='click: addGeneralLedgerAccount' class="btn btn-white btn-sm" title="Add transaction entry"><i class="fa fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>

                            <!-- ko foreach: general_ledger_accounts-->
                                <tr>
                                    <td>
                                        <select  class="form-control m-b detail_accounts acc_post_entry" data-bind='options: $parent.accounts_list, optionsText: function(account){return account.account_code + " " + account.account_name}, value:general_ledger_account, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"journal_transaction_line["+$index()+"][account_id]"}' data-msg-required="Account must be selected" style="width: 100%" required>
                                        <!--select class="form-control m-b" id="account_from_id" data-bind='select2:$parent.select2accounts("#post_entry-modal"), value:general_ledger_account, attr:{name:"journal_transaction_line["+$index()+"][account_id]"}' data-msg-required="Account must be selected" style="width: 100%" required-->
                                        </select>
                                        <input type="hidden"  class="form-control m-b" data-bind='attr:{name:"journal_transaction_line["+$index()+"][id]",value:id}'/>
                                    </td>

                                    <td data-bind="with: general_ledger_account">
                                        <!--div data-bind='visible: (typeof normal_balance_side !== "undefined" && normal_balance_side == 1)'-->
                                        <input type="number" class="form-control" min="0" data-bind='textInput: $parent.debit_amount, attr:{name:"journal_transaction_line["+$index()+"][debit_amount]"},hasfocus: $parent.debit_focus'/>
                                        <!--/div-->
                                    </td>

                                    <td data-bind="with: general_ledger_account">
                                        <!--div data-bind='visible: (typeof normal_balance_side !== "undefined" && normal_balance_side == 2)'-->
                                            <input type="number" class="form-control" min="0" data-bind='textInput: $parent.credit_amount, attr:{name:"journal_transaction_line["+$index()+"][credit_amount]"},hasfocus: $parent.credit_focus' />
                                        <!--/div-->
                                    </td>

                                    <td>
                                        <input type="text" class="form-control" placeholder="Narrative" data-bind='value:narrative, attr:{name:"journal_transaction_line["+$index()+"][narrative]"}' />
                                    </td>

                                    <td>
                                    <span title="Remove item" class="btn text-danger" data-bind='click: $parent.removeGeneralLedgerAccount,visible:$index() > 1'><i class="fa fa-minus"></i></span>
                                    </td>
                                </tr>
                            <!--/ko-->

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th data-bind="text: curr_format(gjtotals().debit), css:{'text-danger':gjtotals().debit!=gjtotals().credit}">0</th>
                                    <th data-bind="text: curr_format(gjtotals().credit), css:{'text-danger':gjtotals().debit!=gjtotals().credit}">0</th>
                                    <th data-bind="text: (gjtotals().debit!=gjtotals().credit)?('Difference '+curr_format(Math.abs(gjtotals().credit - gjtotals().debit))):'', css:{'text-danger':gjtotals().debit!=gjtotals().credit}">&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                    <button type="submit" class="btn btn-primary" data-bind="enable: gjtotals().debit===gjtotals().credit && gjtotals().credit>0"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
