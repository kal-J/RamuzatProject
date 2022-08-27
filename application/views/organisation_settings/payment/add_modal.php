<div id="payment_engine-modal" class="modal fade" role="dialog" aria-labelledby="modal_payment_engine" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
                    <div class="ibox-title">
                        <h2> <center> Payment Engine </center> </h2>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                       
                    </div>
                     <?php echo form_open_multipart("payment_engine/create", array('id' => 'formPayment_engine', 'class' => 'formValidate', 'autocomplete'=>'off', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                    <div class="modal-body">
                    <hr>
                       
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="organisation_id" id="organisation_id"  value="<?php echo $organisation['id']; ?>">
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">Payment Engine<span class="text-danger">*</span></label>
                            <div class="col-lg-12 form-group">
                                <select  class="form-control" name="payment_id" data-bind='options: $root.payment_engines, optionsText: "name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.payment_engine' 
                                required data-msg-required="This field is required">
                                </select>

                            </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 ">
                                <button id="btn-submit" name="btn_submit" class="btn btn-primary pull-right btn-sm save_data"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                        </form>
                    </div><!--/.ibox-content -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
