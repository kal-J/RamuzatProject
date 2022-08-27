<div class="modal inmodal fade" id="add_share_fee-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-md">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>share_fee/Create" id="formShare_fee">

<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Add New Share Fee";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

        <div class="modal-body"><!-- Start of the modal body -->
            <input type="hidden" name="id" id="id" >
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Fee&nbsp;Name<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <input placeholder="" required class="form-control" name="feename" id="feename" type="text">
                            </div> 
                        </div><!--/row -->  
                        <div class="form-group row">   
                            <label class="col-lg-4 col-form-label">Amount&nbsp;Calculated&nbsp;As<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <select id='amountcalculatedas_id' class="form-control required" name="amountcalculatedas_id" data-bind='options: amountCalOptions, optionsText: "amountcalculatedas", optionsCaption: "Select...", optionsAfterRender: setOptionValue("amountcalculatedas_id"), value: amountcalculatedasother' required data-msg-required="Calculation method is required" >
                                   
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" >
                            <label  class="col-lg-4 col-form-label">Rate /Amount<span class="text-danger">*</span></label>
                            <div class="col-lg-8 form-group">
                                <input placeholder=""  min="0.1" step="0.1" required class="number-separator form-control" name="amount" id="amount" type="text">
                            </div>   
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 control-label">Trigger</label>
                        <div class="col-lg-8 form-group" >
                            <select class="form-control" id="chargetrigger_id" name="chargetrigger_id"
                            data-bind="options: chargeTriggerOptions, optionsText: 'charge_trigger_name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: chargeTrigger" >
                            </select>                                   
                        </div>   
                        </div>
                        
                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                    <?php if((in_array('1', $share_issuance_privilege))||(in_array('3', $share_issuance_privilege))){ ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Save";
                                }
                             ?>
                        </button>
                    <?php } ?>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
