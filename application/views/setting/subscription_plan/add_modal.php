<div class="modal inmodal fade" id="add_subscription_plan-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>subscription_plan/create"
                  id="formSubscription_plan">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                           echo $this->lang->line('cont_subscription'); 
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                                class="text-danger">*</span></small>
                </div>

                <div class="modal-body">

                    <div class="">
                        <input type="hidden" name="id">
                        <input type="hidden" name="organisation_id" value="<?php echo $_SESSION['organisation_id'] ?>">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Plan Name<span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <input placeholder="Plan name" required class="form-control" name="plan_name"
                                       type="text">
                            </div>
                        </div>
                        <fieldset class="col-lg-12">
                            <legend>Plan terms</legend>
                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label">Amount payable</label>
                                <div class="col-lg-8 form-group">
                                    <input placeholder="Amount payable" min="1" required class="form-control"
                                           name="amount_payable" type="number" id="amount_payable">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-4 col-form-label"> Frequency <small>(amount paid
                                        every)</small></label>
                                <div class="col-lg-4 form-group">
                                    <input placeholder="" required class="form-control" name="repayment_frequency"
                                           type="number" id="repayment_frequency">
                                </div>
                                <div class="col-lg-4 form-group">
                                    <select class="form-control" name="repayment_made_every" id="repayment_made_every"
                                            data-bind='options:repayment_made_every_options, optionsText: "made_every_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")'
                                            required data-msg-required="Select an option">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-4 col-form-label">When payment Starts</label>
                                <div class="col-lg-8 form-group">
                                    <select class="form-control" name="first_repayment_starts_upon"
                                            id="first_repayment_starts_upon">
                                        <option value="">--select--</option>
                                        <?php foreach ($repayment_start_options as $repayment_start_option): ?>
                                            <option value="<?php echo $repayment_start_option['id']; ?>"><?php echo $repayment_start_option['repayment_start_option_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-4 col-form-label">Income Account</label>
                                <div class="col-lg-8 form-group">
                                    <select class="form-control" name="income_account_id"
                                            data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                                            style="width: 100%" required data-msg-required="Select an option">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-4 col-form-label">Income Receivable Account</label>
                                <div class="col-lg-8 form-group">
                                    <select class="form-control" name="income_receivable_account_id"
                                            data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                                            style="width: 100%" required data-msg-required="Select an option">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-4 col-form-label">Note</label>
                                <div class="col-lg-8 form-group">
                                    <textarea class="form-control" name="notes" id="notes"
                                              placeholder=" notes. Optional...."></textarea>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $subscription_privilege)) || (in_array('3', $subscription_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
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
