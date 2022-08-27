<div class="modal inmodal fade" id="add_supplier-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form  id="formSupplier" action="<?php echo site_url("supplier/create"); ?>" method="post" data-toggle="validator" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">New Supplier/Vendor</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id"  name="id" >
                    <div class="form-group row">
                        <label class="control-label col-md-2">Names</label>
                        <div class="form-group col-md-5">
                            <textarea name="supplier_names" id="supplier_names" placeholder="Supplier names" class="form-control" required></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-md-5">
                            <input type="text" name="supplier_short_name" id="supplier_short_name" class="form-control" placeholder="Short name" />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">TIN</label>
                        <div class="form-group col-md-5">
                            <input type="text" name="tin" id="tin" placeholder="TAX identification Number" class="form-control" />
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-md-5">
                            <label><input type="radio" name="supplier_type_id" value="1" />Supplier</label>
                            <label><input type="radio" name="supplier_type_id" value="2" />Vendor</label>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Phone contacts</label>
                        <div class="form-group col-md-5">
                            <input type="tel" name="phone1" pattern="^(0|\+\d{1,4})(\d{8,11})" class="form-control" placeholder="Primary phone no, required" data-error="Phone number is invalid" />
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-md-5">
                            <input type="tel" name="phone2" pattern="^(0|\+\d{1,4})(\d{8,11})" class="form-control" placeholder="Secondary phone no, optional" data-rule-phoneUg="true" data-msg-phoneUg="Wrong number format" />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Email contact</label>
                        <div class="form-group col-md-5">
                            <input type="email" name="email_contact1" class="form-control" placeholder="Primary email, optional" data-error="Email address is invalid" />
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group col-md-5">
                            <input type="email" name="email_contact2" class="form-control" placeholder="Secondary email, optional" data-error="Email address is invalid" />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Postal Address</label>
                        <div class="form-group col-md-10">
                            <textarea name="postal_address" class="form-control" placeholder="Optional" rows="2"></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Physical Address</label>
                        <div class="form-group col-md-10">
                            <textarea name="physical_address" class="form-control" rows="2" placeholder="Optional"></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-2">Country</label>
                        <div class="form-group col-md-5">
                            <select name="country_id" id="country_id" class="form-control supplier_selects" data-bind='options:countries, optionsText: "country_name", optionsCaption: "-- Select --", optionsAfterRender: setOptionValue("id")' style="width: 100%" required>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('2', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                    <button class="btn btn-default-outline" type="reset" >Reset </button>
                </div>
            </div>
</form>
    </div>
</div>