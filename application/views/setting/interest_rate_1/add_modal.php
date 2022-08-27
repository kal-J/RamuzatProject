<!-- Deposit Product Fees -->
<div class="modal inmodal fade" id="add_interest_payment_method_modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">          
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Interest Rates</h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <form class="formValidate" id="formInterest_rate" action="Interest_rate/create">
                <div class="modal-body ">
                    <div class="row" >
                        <input type="hidden" name="id" id="id">
                        <label class="col-lg-4 control-label">Rate Source<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <select name="rate_source_id" id="rate_source_id"class="form-control" data-msg-required="Interest rate source is required" required>
                                <option value="">--select--</option>
                                <?php foreach ($interest_rate_sources as $interest_rate_source): ?>
                                <option value="<?php echo $interest_rate_source['id']; ?>"><?php echo $interest_rate_source['rate_source_name']; ?></option>
                                <?php endforeach;?>
                            </select> 
                        </div>
                        <label class="col-lg-4 control-label">Rate <span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <input type="text" name="rate" id="rate" placeholder="description" class="form-control" data-msg-required="Rate is required" required /> 
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="pull-right">
                    <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                        <button class="btn btn-sm btn-primary save" type="submit">Submit</button>
                    <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
