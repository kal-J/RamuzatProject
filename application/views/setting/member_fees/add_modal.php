<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_member_fees-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo site_url('index.php/Member_fees/create') ?>" id="formMember_fees" class="formValidate form-horizontal" method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h3 class="modal-title">Membership fees setup</h3>
                    <small class="font-bold">Please Make sure you enter all the required fields correctly</small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Fee&nbsp;Name<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <input placeholder="" required class="form-control" name="feename" id="feename" type="text">
                        </div>
                    </div>
                    <!--/row -->
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Rate/Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <input placeholder="" min="1" required class="form-control" name="amount" id="amount" type="number">
                        </div>
                    </div>
                    <!--/row -->
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Is this fee mandatory?<span class="text-danger"> *</span></label>
                        <div class="col-lg-8 form-group">
                            <select id='requiredfee' class="form-control required" name="requiredfee">
                                <option selected>--Select--</option>
                                <option value='1'> Yes </option>
                                <option value='0'> No </option>
                            </select>
                        </div>
                    </div>
                    <!--/row -->
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Income Account</label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control" name="income_account_id" data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Select an option">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Receivabe Account</label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control" name="receivable_account_id" data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' required data-msg-required="Select an option">
                            </select>
                        </div>
                    </div>

                    <div class=" form-group row">
                        <label class="col-lg-4 control-label">Trigger</label>
                        <div class="col-lg-8 form-group">
                            <select class="form-control" id="chargetrigger_id" name="chargetrigger_id" data-bind="options: myTriggerOptions, optionsText: 'name', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: myTrigger">
                            </select>
                        </div>
                        <!-- ko with: myTrigger -->
                        <label class="col-lg-4 control-label " data-bind='visible: id==2'>Frequency of Payment </label>
                        <div class="col-lg-4 form-group" data-bind='visible: id==2'>
                            <input placeholder="" class="form-control" name="repayment_frequency" type="number" id="repayment_frequency">
                        </div>
                        <div class="col-lg-4 form-group" data-bind='visible: id==2'>
                            <select class="form-control" name="repayment_made_every" id="repayment_made_every" data-bind='options:$root.repayment_made_every_options, optionsText: "made_every_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' data-msg-required="Select an option">
                            </select>
                        </div>                       
                        <!--/ko-->

                    </div>

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                            <textarea type="text" name="description" class="form-control m-b" required="required"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>