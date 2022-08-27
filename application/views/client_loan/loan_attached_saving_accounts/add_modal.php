<div class="modal inmodal fade" id="add_loan_detail_saving_acc-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_attached_saving_accounts/create" id="formLoan_detail_saving_accounts">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Attach savings account";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <input class="form-control" name="id" id="id" type="hidden">
                <input class="form-control" name="loan_id" value="<?php echo $loan_detail['id']; ?>" type="hidden">
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Account No(s).</th>
                                        <th>Amount available</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.attached_loan_saving_accounts'>
                                    <tr>
                                        <td>

                                            <select class="form-control"  data-bind='
                                            options: $root.available_loan_saving_accounts, 
                                            optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), 
                                            
                                            value: selected_fee' style="width: 100%"> </select>
                                        </td>
                                        <td data-bind="with: selected_fee">
                                            <label data-bind="text: cash_bal?curr_format(cash_bal):''"></label>                                          
                                            <!--input type="hidden" data-bind='attr:{name:"savingAccs["+$index()+"][amount]"}, value: cash_bal'/-->
                                            <input type="hidden" data-bind='attr:{name:"savingAccs["+$index()+"][saving_account_id]"}, value: id'/> 
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeSavingAcc'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bind='click: $root.addSavingAcc, enable:$root.available_loan_saving_accounts().length > 0' class="btn-white btn-sm"><i class="fa fa-plus"></i> Add another account</button>
                    <?php if((in_array('1', $client_loan_privilege))||(in_array('3', $client_loan_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.available_loan_saving_accounts().length > 0" type="submit">Attach Acc (s)</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>
