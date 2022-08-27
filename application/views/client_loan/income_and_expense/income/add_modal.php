<div class="modal inmodal fade" id="add_monthly_income-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo site_url();?>Client_loan_monthly_income/create" id="formClient_loan_monthly_income">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Income item";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

            <div class="modal-body">
                <input type="text" hidden name="id" id="id">
                <input type="text" hidden name="client_loan_id"  value="<?php echo $loan_detail['id']; ?>">
                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Income</label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <select name="income_id" data-bind='options: $root.income_items, optionsText: function(item){return item.income_type},  
                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id")' class="form-control"  style="width: 250px"> 
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Amount</label> 
                    <div class="col-lg-8">
                        <div class="input-group">
                            <input type="number" name="amount" id="amount"  class="form-control m-b" autocomplete="off" required="required">
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Description</label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <textarea name="description" id="description" class="form-control m-b" required="required" placeholder="Income Narrative"></textarea>
                        </div>
                    </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-flat" type="submit">Add item</button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm cancel">
                        <i class="fa fa-times"></i> Cancel</button>
                </div>
</form>
</div>
</div>
</div>
