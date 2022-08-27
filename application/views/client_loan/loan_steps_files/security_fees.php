<?php if ( (isset($modules) &&  (in_array('6', $modules)) && (in_array('5', $modules)))){?>
<!--ko if: $root.product_name() && (parseInt($root.product_name().use_savings_as_security) == 1 )-->
<div class="form-group row" >
            <label class="col-lg-3 col-form-label text-success" style="font-size: 1.1em; font-weight: bold;">Use Savings as Security?  <span class="text-danger">*</span></label>
            <div class="col-lg-1 form-group">
                <center>
                    <label> <input checked value="0" name="use_savings_as_security" type="radio" data-bind="checked: use_savings_as_security"> No </label>
                    <label> <input value="1" type="radio" name="use_savings_as_security" data-bind="checked: use_savings_as_security" > Yes</label>
                </center>
            </div>
</div>

 <!-- ko if: parseInt(use_savings_as_security())===1 -->
        
    <table  class="table table-striped table-condensed table-hover">
         <caption class="text-success" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Guarantor Savings Account</caption>
         <div data-bind="visible: $root.saving_guarantor_check() && ($root.saving_guarantor_check().valid == false)" class="row text-center">
            <span data-bind="visible: $root.saving_guarantor_check() && ($root.saving_guarantor_check().valid == false)" class="text-danger">
                <span data-bind="visible: $root.saving_guarantor_check() && ($root.saving_guarantor_check().valid == false), text: $root.saving_guarantor_check() ? $root.saving_guarantor_check().member_name : '' "></span> &nbsp; can not be added as a Guarantor to this loan. He is already a Guarantor on more than <span data-bind="text: $root.max_loans_to_guarantee()"></span> other loans.
            </span>
         </div>
         
        <thead>
            <tr>
                <th>Guarantor Savings account</th>
                <th>Amount to lock</th>
                <th>Relationship</th>
                <th><a data-bind="click: $root.addGuarantor" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead>         
        <tbody data-bind='foreach: $root.added_guarantor'>
            <tr>
                <td>
                    <select class="form-control form-control-sm loan_security_fees" id="savings_account_id"  data-bind='options: $root.guarantors, optionsText: function(data){return data.account_no + " | " + data.member_name;}, optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: selected_guarantor, attr:{name:"guarantors["+$index()+"][savings_account_id]"}' style="width: 100%" data-msg-required="A savings account is required">
                    </select>
                </td>
                <td data-bind="with: selected_guarantor">
                        <input placeholder="" min='0' type='number' class="form-control required form-control-sm" type="text" data-bind='textInput: 0, attr: {"data-rule-min":((parseFloat(cash_bal)<0)?cash_bal:null), "data-rule-max": ((parseFloat(cash_bal)>0)?cash_bal:null), "data-msg-min":"Minimum amount entered is below the 0 limit ", "data-msg-max":"Maximum amount should be "+curr_format(parseInt(cash_bal)),"name":"guarantors["+$index()+"][amount_locked]"}' required />
                        <div class="blueText"><p>
                                <span data-bind="visible: (parseFloat(0))">Min: </span>
                                <span data-bind="visible: (parseFloat(0)), text: curr_format(parseInt(0))"></span> &nbsp;
                                <span data-bind="visible: (parseFloat(cash_bal)>0)">Max: </span>
                                <span data-bind="visible: (parseFloat(cash_bal)>0), text: curr_format(parseInt(cash_bal))"></span></p>
                        </div>
               </td>
                <td data-bind="with: selected_guarantor">
                    <select class="form-control required form-control-sm" id="relationship_type_id" data-bind='options: $root.relationships, optionsText: "relationship_type", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"),attr:{name:"guarantors["+$index()+"][relationship_type_id]"}' style="width: 100%" data-msg-required="Relationship is required">
                    </select>
                </td>
                <td>
                    <span title="Remove Guarantor" class="btn text-danger" data-bind='click: $root.removeGuarantor'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- /ko -->

<!--/ko -->

<?php } ?>
<?php if ( (isset($modules) &&  (in_array('6', $modules)) && (in_array('5', $modules)))){?>
 
    <table  class="table table-striped table-condensed table-hover">
          <caption class="text-success" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Savings A/C <small>For auto payments</small></caption>
        <thead>
            <tr>
                <th>Account No(s).</th>
                <th>Amount to lock</th>
                <th>Amount available</th>
                <th> <a data-bind='click: $root.addSavingAcc' class="btn-success btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead> 
        <tbody data-bind='foreach: $root.attached_loan_saving_accounts'>
            <tr>
                <td>

                    <select class="form-control form-control-sm"  data-bind='
                    options: $root.filtered_savingac, 
                    optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),
                    value: selected_ac' style="width: 100%"> </select>
                </td>
                <td data-bind="with: selected_ac">
                        <input placeholder="" min='0' type='number' class="form-control required form-control-sm" type="text" data-bind='textInput: 0, attr: {"data-rule-min":((parseFloat(cash_bal)<0)?cash_bal:null), "data-rule-max": ((parseFloat(cash_bal)>0)?cash_bal:null), "data-msg-min":"Minimum amount entered is below the 0 limit ", "data-msg-max":"Maximum amount should be "+curr_format(parseInt(cash_bal)),"name":"savingAccs["+$index()+"][amount_locked]"}' required />
                        <div class="blueText"><p>
                                <span data-bind="visible: (parseFloat(0))">Min: </span>
                                <span data-bind="visible: (parseFloat(0)), text: curr_format(parseInt(0))"></span> &nbsp;
                                <span data-bind="visible: (parseFloat(cash_bal)>0)">Max: </span>
                                <span data-bind="visible: (parseFloat(cash_bal)>0), text: curr_format(parseInt(cash_bal))"></span></p>
                        </div>
               </td>
                <td data-bind="with: selected_ac">
                    <label data-bind="text: (cash_bal)?curr_format(parseFloat(cash_bal)):''"></label>
                    <input type="hidden" data-bind='attr:{name:"savingAccs["+$index()+"][saving_account_id]"}, value: id'/> 
                </td>
                <td>
                    <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeSavingAcc'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
<?php } ?>

<?php if ( (isset($modules) &&  (in_array('12', $modules)))){?>
<hr>
<!--ko if: $root.product_name() && (parseInt($root.product_name().use_shares_as_security) == 1) -->

<div class="form-group row">
    <label class="col-lg-3 col-form-label text-warning" style="font-size: 1.1em; font-weight: bold;">Use Shares as Security? <span class="text-danger">*</span></label>
            <div class="col-lg-1 form-group">
                <center>
                    <label> <input checked value="0" name="use_share_as_security" type="radio" data-bind="checked: use_share_as_security"> No   </label>
                    <label> <input value="1" type="radio" name="use_share_as_security" data-bind="checked: use_share_as_security" > Yes</label>
                </center>
            </div>
        </div>
 <!-- ko if: parseInt(use_share_as_security())===1 -->

    <table  class="table table-striped table-condensed table-hover">
         <caption class="text-warning" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Guarantor Share Account</caption>
        <thead>
            <tr>
                <th>Guarantor Share account</th>
                <th>Amount to lock</th>
                <th>Relationship</th>
                <th><a data-bind="click: $root.addShareGuarantor" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead>         
        <tbody data-bind='foreach: $root.added_share_guarantor'>
            <tr>
                <td>
                    <select class="form-control form-control-sm loan_security_fees" id="share_account_id"  data-bind='options: $root.share_guarantors, optionsText: function(data){return data.share_account_no + " | " + data.member_name;}, optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: selected_share_guarantor, attr:{name:"share_guarantors["+$index()+"][share_account_id]"}' style="width: 100%" data-msg-required="A share account is required">
                    </select>
                </td>
                <td data-bind="with: selected_share_guarantor">
                        <input placeholder="" min='0' type='number' class="form-control required form-control-sm" type="text" data-bind='textInput: 0, attr: {"data-rule-min":((parseFloat(total_amount)<0)?total_amount:null), "data-rule-max": ((parseFloat(total_amount)>0)?total_amount:null), "data-msg-min":"Minimum amount entered is below the 0 limit ", "data-msg-max":"Maximum amount should be "+curr_format(parseInt(total_amount)),"name":"share_guarantors["+$index()+"][amount_locked]"}' required />
                        <div class="blueText"><p>
                                <span data-bind="visible: (parseFloat(0))">Min: </span>
                                <span data-bind="visible: (parseFloat(0)), text: curr_format(parseInt(0))"></span> &nbsp;
                                <span data-bind="visible: (parseFloat(total_amount)>0)">Max: </span>
                                <span data-bind="visible: (parseFloat(total_amount)>0), text: curr_format(parseInt(total_amount))"></span></p>
                        </div>
               </td>
                <td data-bind="with: selected_share_guarantor">
                    <select class="form-control required form-control-sm" id="share_relationship_type_id" data-bind='options: $root.relationships, optionsText: "relationship_type", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"),attr:{name:"share_guarantors["+$index()+"][relationship_type_id]"}' style="width: 100%" data-msg-required="Relationship is required">
                    </select>
                </td>
                <td>
                    <span title="Remove Guarantor" class="btn text-danger" data-bind='click: $root.removeShareGuarantor'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
    <!--/ko-->


<?php } ?>
<?php if ( (isset($modules) &&  (in_array('12', $modules)))){?>
     
 <!-- ko if: parseInt(use_share_as_security())===1 -->
    <table  class="table table-striped table-condensed table-hover">
          <caption class="text-warning" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Share Account</caption>
        <thead>
            <tr>
                <th>Member Share Account No(s).</th>
                <th>Amount available</th>
                <th> <a data-bind='click: $root.addShareAcc' class="btn-warning btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead> 
        <tbody data-bind='foreach: $root.attached_loan_share_accounts'>
            <tr>
                <td>

                    <select class="form-control form-control-sm"  data-bind='
                    options: $root.filtered_shareac, 
                    optionsText: function(data_item){return data_item.share_account_no + " | " + data_item.member_name}, 
                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),
                    value: selected_share_ac' style="width: 100%"> </select>
                </td>
                <td data-bind="with: selected_share_ac">
                    <label data-bind="text: (total_amount)?curr_format(parseFloat(total_amount)):''"></label>
                    <input type="hidden" data-bind='attr:{name:"shareAccs["+$index()+"][share_account_id]"}, value: id'/> 
                </td>
                <td>
                    <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeShareAcc'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
    <!--/ko-->

<!--/ko -->

<?php } ?>

<?php if ( (isset($modules) &&  !(in_array('6', $modules)) && !(in_array('12', $modules)))){?>


<div class="form-group row" >
            <label class="col-lg-3 col-form-label text-success" style="font-size: 1.1em; font-weight: bold;">Add Guarantor?  <span class="text-danger">*</span></label>
            <div class="col-lg-1 form-group">
                <center>
                    <label> <input checked value="0" name="add_guarantor" type="radio" data-bind="checked: add_guarantor"> No </label>
                    <label> <input value="1" type="radio" nam="add_guarantor" data-bind="checked: add_guarantor" > Yes</label>
                </center>
            </div>
</div>

 <!-- ko if: parseInt(add_guarantor())===1 -->
        
    <table  class="table table-striped table-condensed table-hover">
         <caption class="text-success" style=" font-size: 1.2em; font-weight: bold; text-align: center; caption-side: top;">Attach Guarantor</caption>
        <thead>
            <tr>
                <th>Guarantor</th>
                <th>Relationship</th>
                <th><a data-bind="click: $root.addMemberGuarantor" class="btn btn-success btn-xs"><i class="fa fa-plus"></i></a></th>
            </tr>
        </thead>         
        <tbody data-bind='foreach: $root.added_member_guarantor'>
            <tr>
                <td>
                    <select class="loan_security_fees form-control-sm" id="member_guarantor_id3" data-bind='options: $root.filtered_member_names, optionsText: function(data){return data.member_name + " -" + data.client_no;}, optionsCaption: "-- select --", value: selected_member_guarantor, optionsAfterRender: setOptionValue("id"), attr:{name:"member_guarantors["+$index()+"][member_id]"}' style="width: 200px" data-msg-required="A Member is required" required="required"> 
                    </select>
                </td>
                <td data-bind="with: selected_member_guarantor">
                    <select class="form-control required form-control-sm" id="relationship_type_id" data-bind='options: $root.relationships, optionsText: "relationship_type", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"),attr:{name:"member_guarantors["+$index()+"][relationship_type_id]"}' style="width: 200px" data-msg-required="Relationship is required" required="required">
                    </select>
                </td>
                <td>
                    <span title="Remove Guarantor" class="btn text-danger" data-bind='click: $root.removeMemberGuarantor'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- /ko -->
<?php } ?>

<?php if (isset($case2) && $case2 !='My Loans') { ?>
        <hr>

<!-- <h1><center><span class="text-danger">Optional</span></center></h1> -->
    <table  class="table table-striped table-condensed table-hover m-t-md">
         <div class="text-danger" style=" font-size: 1.3em; font-weight: bold; text-align: center; caption-side: top;">Attach Collateral</div>
         <div class="row d-flex flex-row-reverse mr-2">
             <select id="existing-collateral" class="loan_security_fees form-control-sm" class="form-control">
                 <option value="0">-- Attach from Existing --</option>
                 <!-- ko foreach:$root.existing_collateral  -->

                 <option type='button'
                     data-bind="text:collateral_type_name, value: JSON.stringify($data)"></option>
                 <!-- /ko-->
             </select>
         </div>

        <thead>
            <tr>
                <th>Collateral</th>
                <th>Description</th>
                <th>Item value</th>
                <th>File name</th>
                <th> <a data-bind="click: $root.addCollateral_type" class="btn btn-danger btn-xs"><i class="fa fa-plus"></i></a>
                </th>
                
            </tr>
        </thead>
        <tbody data-bind='foreach: $root.added_collateral_type'>
            <tr>
                <td>
                   <select class="loan_security_fees form-control-sm" id="collateral_type_id1" data-bind='options: $root.collateral_list, optionsText: "collateral_type_name",  
                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),  value:selected_collateral, attr:{name:"collaterals["+$index()+"][collateral_type_id]"}' class="form-control"  style="width: 200px"> 
                    </select> 
                </td>
                <td data-bind="with: selected_collateral">
                    <textarea type="text" data-bind='attr:{name:"collaterals["+$index()+"][description]"}' class="form-control " required="required"></textarea>

                    <?php if(isset($member)) { ?> 
                        <input type="hidden" value="<?php echo $member['id']; ?>" data-bind='attr:{name:"collaterals["+$index()+"][member_id]"}'/>

                    <?php } else { ?> 
                        <input type="hidden" data-bind='attr:{name:"collaterals["+$index()+"][member_id]"}, value: $root.member_name() ? $root.member_name().id : ""'/> 
                    <?php } ?>

                </td>
                <td data-bind="with: selected_collateral">
                     <input type="number" data-bind='attr:{name:"collaterals["+$index()+"][item_value]"}' class="form-control form-control-sm" required="required">
                </td>
                <td data-bind="with: selected_collateral">
                    <input type="file" data-bind='attr:{name:"file_name[]"}'/>  
                    
                </td>
                <td>
                    <span title="Remove income" class="btn text-danger" data-bind='click: $root.removeCollateral_type'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
        <tbody data-bind='foreach: $root.added_existing_collateral'>
        <tr>
                <td>
                   <span data-bind='text:collateral_type_name'></span>
                    <input type="hidden" data-bind='attr:{name:"existing_collaterals["+$index()+"][member_collateral_id]"}, value: id'/> 
                </td>
                <td>
                    <span data-bind="text: description"></span>  
                    
                </td>
                <td>
                <span data-bind="text: item_value"></span>
                <input type="hidden" data-bind='attr:{name:"existing_collaterals["+$index()+"][item_value]"}, value: item_value'/> 
                     
                </td>
                <td>
                    <span data-bind="text: file_name"></span>
                    
                </td>
                <td>
                    <span data-bind="click: $root.remove_existing_collateral" title="Remove income" class="btn text-danger"><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>

    </table>
<!-- <fieldset class="col-lg-12">
    <legend>Attach Loan Fees</legend>
    <table  class="table table-striped table-condensed table-hover m-t-md">
        <thead>
            <tr>
                <th>Fee</th>
                <th>Amount</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <div class="col-sm-2 pull-right">
                <a data-bind='click: $root.addLoanFee' class="btn-info btn-sm"><i class="fa fa-plus"></i></a>
            </div>
        <tbody data-bind='foreach: $root.applied_loan_fee'>
            <tr>
                <td>
                    <select class="loan_security_fees" data-bind='
                    options: $root.filtered_loan_fees, 
                    optionsText: function(data_item){return data_item.feename}, 
                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), 
                    value: selected_fee' class="form-control"  style="width: 250px"> </select>
                </td>
                <td data-bind="with: selected_fee">
                    <label data-bind="text: (parseInt(amountcalculatedas_id)==3)?curr_format( $root.compute_fee_amount(loanfee_id,$root.app_amount())):( curr_format(parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):amount) ) "></label>
                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: (parseInt(amountcalculatedas_id)==3)?$root.compute_fee_amount(loanfee_id,$root.app_amount()):( parseInt(amountcalculatedas_id)==1?(parseFloat(amount)*$root.app_amount()/100):amount ) '/>
                    <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id'/>  
                    <input type="hidden" value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}'>
                </td>
                <td>
                    <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeLoanFee'><i class="fa fa-minus"></i></span>
                </td>
            </tr>
        </tbody>
    </table>
</fieldset> -->
<?php } ?>
<?php $this->load->view('client_loan/loan_steps_files/loan_docs_file.php'); ?>
