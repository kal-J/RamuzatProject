<div id="sms_settings-modal" class="modal fade" role="dialog" aria-labelledby="modal_payment_engine" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
                    <div class="ibox-title">
                        <h2> <center> EDIT SMS SETTINGS </center> </h2>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                       
                    </div>
                     <?php echo form_open_multipart("payment_engine/create_sms", array('id' => 'formSms_settings', 'class' => 'formValidate', 'autocomplete'=>'off', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                    <div class="modal-body">
                    <hr>
                       
                        <input type="hidden" name="id" id="id" >
                        <input type="hidden" name="organisation_id" id="organisation_id"  value="<?php echo $organisation['id']; ?>">
                        <div class="form-group row">
                            <label class="col-lg-12 col-form-label">SMS Engine<span class="text-danger">*</span></label>
                            <div class="col-lg-12 form-group">
                        <input type="text" class="form-control" name="name" id="name"> 
                            </div>
                            </div>
                             <div class="form-group row">
                            <label class="col-lg-12 col-form-label">API KEY<span class="text-danger">*</span></label>
                            <div class="col-lg-12 form-group">
                        <input type="text" class="form-control" name="api_key" id="api_key"> 
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
