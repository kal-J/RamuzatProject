<?php $enable_or_disable_editting = isset($org['edit_account_nos']) && $org['edit_account_nos'] ? null : "readonly"; ?>

<style type="text/css">
    .blueText{color: blue;font-size: 10px;}
</style>
<!--div class="modal inmodal fade" id="add_savings_account" tabindex="-1" role="dialog" aria-hidden="true" commented out to allow the search functionality of the select2-->
<div class="modal inmodal fade" id="add_savings_account" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url(); ?>Savings_account/Create" id="formSavings_account">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo "New Savings Account"; //
                        } else {
                            echo "New Savings Account";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                 <div class="modal-body">
                    <div class="">
                        <input type="hidden" name="id">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Account No.</label>
                            <div class="col-lg-10">
                                <input <?php echo $enable_or_disable_editting; ?> type="text" class="form-control"  id="account_no" name="account_no" data-bind="attr: {value:new_account_no}" required/>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Savings Product<span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <select id="select_savings_product" class="form-control" style="width: 100%" name="deposit_Product_id" data-bind="options: ProductOptions, optionsText: 'productname', optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: Product">
                                </select>
                            </div> 
                            <label class="col-lg-2 col-form-label">Account Holder<span class="text-danger">*</span></label>
                            <div class="col-lg-4">

                                <?php if (isset($selected_account) || isset($user) || isset($group)): ?>
                                    <input  class="form-control" value="<?php echo isset($selected_account) ? $selected_account['member_name'] : ((isset($user))? ($user['firstname'] . " " . $user['lastname'] . " " . $user['othernames']): ((isset($group))?$group['group_name']:'')); ?>" type="text" readonly>
                                    <input type="hidden" name="member_id" value="<?php echo isset($selected_account) ? $selected_account['member_id'] : ((isset($user))?($user['id']):((isset($group))?$group['id']:'')) ; ?>" />
                                    <input class="form-control" value="<?php echo isset($selected_account) ? $selected_account['client_type'] : (isset($user) ? 1 :(isset($group)?2:'')); ?>" name="client_type" id="client_type"  required type="hidden">
                                <?php else: ?>
                                    <select id="select_savings_account_client" class="form-control select2able" style="width: 100%" name="member_id" data-bind="options: filteredClients , 
                                    optionsText: function(data){ return ((data.client_no==0)?'':data.client_no+' - ') +data.client_name; }
                                    , optionsAfterRender: setOptionValue('id'), optionsCaption: '--select--', value: User">
                                    </select>
                                    <!-- ko with:User -->
                                    <input class="form-control" data-bind="value:client_type" name="client_type" id="client_type" required type="hidden">
                                    <!--/ko-->
                                <?php endif; ?>
                            </div> 

                        </div> 
                        <!-- ko with: Product -->
                        <input type="hidden" name="mandatory_saving" data-bind="value: mandatory_saving">
                        <div class="form-group row">                      
                            <!-- ko if: parseInt(producttype)==parseInt(2) -->
                            <label class="col-lg-2 col-form-label">Term length (Months)</label>
                            <div class="col-lg-4 form-group">
                                <input class="form-control" name="term_length" type="text" placeholder="Months "  data-bind='textInput:$parent.term_lenght, attr: {"data-rule-min":((parseFloat(mintermlength)>0)?mintermlength:null), "data-rule-max": ((parseFloat(maxtermlength)>0)?maxtermlength:null), "data-msg-min":"Interest rate is less than "+curr_format(parseInt(mintermlength)), "data-msg-max":"Interest rate is more than "+curr_format(parseInt(maxtermlength))}'>
                                <div class="blueText"> <p>
                                        <span data-bind="visible: (parseFloat(mintermlength)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(mintermlength)>0), text: curr_format(parseInt(mintermlength))"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(maxtermlength)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(maxtermlength)>0), text: curr_format(parseInt(maxtermlength))"></span>
                                    </p> </div>
                            </div>
                            <!-- /ko --> 
                            <!-- ko if: parseInt(interestpaid)===parseInt(1) -->
                            <label class="col-lg-2 col-form-label">Interest rate (%)</label>
                            <div class="col-lg-4 form-group">
                                <input placeholder="" class="form-control" name="interest_rate" type="text" data-bind='textInput: parseInt(producttype)==parseInt(2)?$root.compute_rate_amount(id,$root.term_lenght()):defaultinterestrate, attr: {"data-rule-min":parseInt(producttype)==parseInt(2)?$root.compute_rate_amount(id,$root.term_lenght()):defaultinterestrate, "data-rule-max": parseInt(producttype)==parseInt(2)?$root.compute_rate_amount(id,$root.term_lenght()):defaultinterestrate, "data-msg-min":"Interest rate must be equal to "+parseInt(producttype)==parseInt(2)?$root.compute_rate_amount(id,$root.term_lenght()):defaultinterestrate, "data-msg-max":"Interest rate must be equal to "+parseInt(producttype)==parseInt(2)?$root.compute_rate_amount(id,$root.term_lenght()):defaultinterestrate}' /> 
                               
                            </div>
                            <!--/ko-->
                        </div>  
                        <!-- /ko -->

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Date Opened<span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                                <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" >
                                    <input autocomplete="off" placeholder="DD-MM-YYYY" type="text" class="form-control" onkeydown="return false" name="date_opened"  required/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <!-- ko with: Product -->
                                <!-- ko if: parseInt(producttype)===parseInt(4) -->
                                <label class="col-lg-2 col-form-label">Select a child<span class="text-danger">*</span></label>
                                <div class="col-lg-4">
                                    
                                    <select id="select_child" name="child_id" class="form-control" style="width: 100%" data-bind="options: $root.children , 
                                    optionsText: function(data){ return data.firstname + ' ' + data.lastname + ' ' + data.othernames; }
                                    , optionsAfterRender: setOptionValue('id'), optionsCaption: '--Select Child--', value: $root.child_id">
                                    </select>
                                </div> 
                                <!-- /ko -->
                            <!-- /ko -->
                           
                        </div>                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
