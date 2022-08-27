<!-- Tax Rates -->
<div class="modal inmodal fade" id="add_tax_rate-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">          
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">New <?php echo $tax_rate_source['source'];?> Tax Rate</h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <form class="formValidate" id="formTax_rate" action="<?php echo site_url("tax_rate/create"); ?>">
                <div class="modal-body ">
                    <div class="row" >
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="tax_rate_source_id" id="tax_rate_source_id" value="<?php echo $tax_rate_source['id'];?>">
                        <label class="col-lg-4 control-label">Rate <span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <input type="number" name="rate" id="rate" placeholder="Tax rate" min="0" max="100" step="0.01" class="form-control" data-msg-required="Rate is required" data-msg-min="Rate is required" required />
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="clearfix"></div>
                        <label class="col-lg-4 control-label" for="start_date">Start Date <span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" value="<?php echo date('d-m-Y') ?>" name="start_date" id="start_date" placeholder="Start date" class="form-control" data-msg-required="Start date is required" required />
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="clearfix"></div>
                        <label class="col-lg-4 control-label" for="note">Note <span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <textarea class="form-control" rows="2" name="note" id="description"></textarea>
                            <div class="help-block with-errors"></div>
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
