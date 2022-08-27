<div class="modal inmodal fade" id="add_guarantor-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>client_loan_guarantor/Create" id="formClient_loan_guarantor">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Assign New Guarantor";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <table class="table table-bordered table-hover">
                    
                    <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">
                            <!--ko if:( parseFloat(( ( (($root.loan_detail().min_collateral*1.0)/100) * ( $root.loan_detail().requested_amount) ) - (guarantor_amount() + collateral_amount()) )) > 0 ) -->
                         Balance of Amount required to secure loan | <span style="color:red;" data-bind="text: curr_format(( (($root.loan_detail().min_collateral*1.0)/100) * ( $root.loan_detail().requested_amount ) ) - (guarantor_amount() + collateral_amount()))">
                         </span>
                        
                             <!--/ko-->
                             <!--ko if:( parseFloat(( ( (($root.loan_detail().min_collateral*1.0)/100) * ( $root.loan_detail().requested_amount) ) - (guarantor_amount() + collateral_amount()) )) <= 0 ) -->
                             Loan already secured
                             <!--/ko-->
                         </caption>
                    
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <input required class="form-control" name="id" id="id" type="hidden">
                <input required class="form-control" name="client_loan_id" type="hidden" value="<?php echo $loan_detail['id']; ?>" >
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Guarantor Savings account<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control required" id="savings_account_id" name="savings_account_id" data-bind='options: guarantors, optionsText: function(data){return data.account_no + " | " + data.member_name;}, optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: guarantor' style="width: 100%" data-msg-required="A savings account is required">
                            </select>
                        </div>
                    </div>
                    <!-- ko with: guarantor -->
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Amount to lock<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <input placeholder="" min='0' type='number' class="form-control required" name="amount_locked" type="text" data-bind='textInput: 0, attr: {"data-rule-min":((parseFloat(cash_bal)<0)?cash_bal:null), "data-rule-max": ((parseFloat(cash_bal)>0)?cash_bal:null), "data-msg-min":"Minimum amount entered is below the 0 limit ", "data-msg-max":"Maximum amount should be "+curr_format(parseInt(cash_bal))}' required />
                            <div class="blueText"><p>
                                    <span data-bind="visible: (parseFloat(0))">Min: </span>
                                    <span data-bind="visible: (parseFloat(0)), text: curr_format(parseInt(0))"></span> &nbsp;
                                    <span data-bind="visible: (parseFloat(cash_bal)>0)">Max: </span>
                                    <span data-bind="visible: (parseFloat(cash_bal)>0), text: curr_format(parseInt(cash_bal))"></span>
                            </div>
                        </div>
                    </div><!--/row -->
                    <!-- /ko -->



                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Relationship<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control" id="relationship_type_id" name="relationship_type_id" style="width: 100%" placeholder="Select a relationship type" required >
                                <option value="">--Select one--</option>
                                <?php
                                foreach ($relationship_types as $relationship_type) {
                                    echo "<option value='" . $relationship_type['id'] . "'>" . $relationship_type['relationship_type'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?></button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
