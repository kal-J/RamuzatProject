<div class="modal inmodal fade" id="reverse-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("Investiment/reverse_transaction"); ?>" id="formReverseTransaction">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" > Reverse Transaction </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div data-bind="with: $root.investment_data">
                    <input type="hidden"  data-bind="attr: {value:$root.investment_data().id}" name="id"/>
                    <input type="hidden"  data-bind="attr: {value:$root.investment_data().transaction_no}"name="transaction_no" />
                    <input type="hidden"  data-bind="attr: {value:$root.investment_data().transaction_type_id}" name="transaction_type_id" />
                 </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Reason?<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="reverse_msg" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $savings_privilege))) { ?>
                        <button class="btn btn-danger">Reverse
                            </button>
                         <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>