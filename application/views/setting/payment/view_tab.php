<div role="tabpanel" id="tab-payment" class="tab-pane" >
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title  back-change">
                <h3 class="text-uppercase text-center">Payment Engine Requirements </h3>
                <div  class="text-center"><small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small></div>
            </div>
            <div class="ibox-content">
            <div class="row">
                <div class="col-lg-2">
                    
                </div>
                <div class="col-lg-10">
                    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('payment_engine/requirements_creation'); ?>" id="formPayment_engine" > 
                <input type="hidden" name="id" value="<?php echo $payment_engine_requirements['id'] ?>">
                 <fieldset class="col-lg-10">     
                    <legend><small><strong>Attach the channel of transaction which has the linked account</strong></small></legend>
                    <div class="form-group row"> 
                        <label class="col-lg-6 col-form-label">Transaction channel<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" 
                            data-bind='options: $root.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--",optionsValue:"id",optionsAfterRender: setOptionValue("id"),value:selected_channel' data-msg-required="Transaction channel  must be selected" style="width: 100%" required >
                            </select> 
                        </div>
                    </div>
                </fieldset>
                <?php if(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==4 ){?>
                <fieldset class="col-lg-10">     
                    <legend><small><strong>The Mula Payment engine Requirements</strong></small></legend>
                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">Client ID<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="client_id" type="text" value="<?php echo $payment_engine_requirements['client_id'] ?>">
                        </div>
                    </div>

                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">Client Secret<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="client_secret" type="text" value="<?php echo $payment_engine_requirements['client_secret'] ?>">
                        </div>
                    </div>

                    <div class="form-group row"> 
                        <label class="col-lg-6 col-form-label">Secret Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="secret_key" type="text" value="<?php echo $payment_engine_requirements['secret_key'] ?>">
                        </div> 
                    </div>  

                    <div class="form-group row">  
                        <label class="col-lg-6 col-form-label">IV Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="iv_key" type="text" value="<?php echo $payment_engine_requirements['iv_key'] ?>">
                        </div> 
                    </div>
                    <div class="form-group row">  
                        <label class="col-lg-6 col-form-label">Service Code<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="service_code" type="text" value="<?php echo $payment_engine_requirements['service_code'] ?>">
                        </div> 
                    </div>
                    <div class="form-group row">  
                        <label class="col-lg-6 col-form-label">Access Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="access_key" type="text" value="<?php echo $payment_engine_requirements['access_key'] ?>">
                        </div> 
                    </div>
                </fieldset>
            <?php }elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==2 ){?>
                <fieldset class="col-lg-10">     
                    <legend><small><strong>The Pesapal Payment engine Requirements</strong></small></legend>
                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">Consumer Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="consumer_key" type="text" value="<?php echo $payment_engine_requirements['consumer_key'] ?>">
                        </div>
                    </div>

                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">Consumer Secret<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="consumer_secret" type="text" value="<?php echo $payment_engine_requirements['consumer_secret'] ?>">
                        </div>
                    </div>
                </fieldset>
            <?php }elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==3 ){?>
                 <fieldset class="col-lg-10">     
                    <legend><small><strong>The Beyonic Payment engine Requirements</strong></small></legend>
                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">API Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="api_key" type="text" value="<?php echo $payment_engine_requirements['api_key'] ?>">
                        </div>
                    </div>
                </fieldset>
            <?php }elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==1 ){?>
                 <fieldset class="col-lg-10">     
                    <legend><small><strong>The SentePay Payment engine Requirements</strong></small></legend>
                    <div class="form-group row">     
                        <label class="col-lg-6 col-form-label">API Key<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input required class="form-control" name="api_key" type="text" value="<?php echo $payment_engine_requirements['api_key'] ?>">
                        </div>
                    </div>
                </fieldset>
            <?php } ?>

                <div class="col-lg-10 modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Save</button>
                </div>

            </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
</script>