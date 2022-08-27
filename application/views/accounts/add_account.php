<div class="modal inmodal fade" id="add_account-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo base_url("accounts/create"); ?>" id="formAccounts">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "New Ledger Account";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Category<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control account_creation m-b" id="sub_category_id" name="sub_category_id" data-bind='options: subcat_list,  optionsText: function(data_item){return data_item.sub_cat_code +" " + data_item.sub_cat_name;}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:account_sub_category' style="width: 100%">
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Parent Account</label>
                        <div class="col-lg-4">
                        <select id='parent_account_id' name="parent_account_id" class="form-control account_creation"  data-bind='options: parent_accounts, optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),value:parent_account' style="width: 100%" >
                      </select>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Account Name<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input placeholder="" required class="form-control" name="account_name" type="text">
                        </div>
                        <label class="col-lg-2 col-form-label">Account No<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label type="text" class="input-group-text" data-bind='text: (typeof parent_account() !=="undefined"?parent_account().account_code:(typeof account_sub_category() !=="undefined"?account_sub_category().sub_cat_code:"")) +"-"'></label>
                                </div>
                            <input type="text" name="new_account_code" id="new_account_code" class="form-control" data-bind="value: new_account_code" required>
                            <input type="hidden" name="account_code" id="account_code" data-bind="value: final_account_code">
                            </div>
                        </div>
                        </div>
                    </div>
                  
                 <!-- ko with: account_sub_category  -->
                    <div class="form-group row" data-bind="visible: parseInt(category_id)<4" >
                    <label class="col-lg-2 col-form-label">Opening Balance</label>
                     <div class="col-lg-2"> 
                         <select class="form-control" required="required" id="normal_balance_side" name="normal_balance_side">
                          <option value="">Select</option>
                          <option value ="1">Dr</option>
                          <option value="2">Cr</option>
                        </select>
                     </div>
                        <div class="col-lg-3">
                            <input type="text" name="opening_balance" class="form-control">
                        </div>
                    <label class="col-lg-2 col-form-label">Opening Balance as at</label>
                        <div class="col-lg-3 form-group">
                            <div class="input-group date" data-date-end-date="+0d">
                                <input type="text" class="form-control" name="opening_balance_date" data-bind="datepicker: $parent.opening_balance_date, attr: {value:$parent.opening_balance_date}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                <!-- /ko-->
                <div class="form-group row">
                       <label class="col-lg-2 col-form-label">Allow Manual Entry?<span class="text-danger">*</span></label>
                       <div class="col-lg-4">
                           <select name="manual_entry" id="manual_entry" class="form-control" >
                               <option value="1"> Yes</option>
                               <option value="0"> No</option>
                           </select>
                       </div>
                       <label class="col-lg-2 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
                        </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
                    <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>
