<div class="modal inmodal fade" id="add_deposit_product-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>DepositProduct/Create" id="formDepositProduct">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Add New Savings Product";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

<div class="modal-body">
 
 <div class="">
    <input type="hidden" name="id">
    <div class="form-group row">
       <label class="col-lg-2 col-form-label">Product Name<span class="text-danger">*</span></label> 
         <div class="col-lg-4 form-group">
          <input placeholder="" required class="form-control" name="productname" type="text"> <br>
         </div>
         <label class="col-lg-2 col-form-label">Product Type<span class="text-danger">*</span></label>
          <div class="col-lg-4 form-group">
                <select class="form-control" id="producttype" name="producttype" data-bind='options: deposit_producttype, optionsText: "typeName", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: producttype' required data-msg-required="Deposit product type is required">
                </select>
                <div data-bind="with: producttype"><span class="help-block-none"><small data-bind="text: description">Product description goes here.</small></span></div>
          </div>
          <label class="form-label col-lg-2" for="saving_deposit_product">Product Code </label>
          <div class="form-group col-lg-4">
          <input type="text" class="form-control" name="product_code" id="product_code">

       </div>
       <label class="col-lg-2 col-form-label">Can be used for payments ?<span class="text-danger">*</span></label>
        <div class="col-lg-3 form-group">
               <br>
              <label><input  value="0" name="auto_payment" type="radio"   required> No</label>
              <label> <input value="1" name="auto_payment" type="radio"   required> Yes</label>
         </div>
       </div>
     <div class="form-group row">
       <label class="col-lg-3 col-form-label">Available To<span class="text-danger">*</span></label>
      <div class="col-lg-3 form-group">
        <select class="form-control" name="availableto" required>
        <option value="" >Select ....</option>
        <?php
       foreach($available_to as $category){
          echo "<option value='".$category['id']."'>".$category['name']."</option>";
       }
       ?>
        </select>
       </div>
        <label class="col-lg-2 col-form-label">Interest Applicable ?<span class="text-danger">*</span></label>
        <div class="col-lg-3 form-group">
               <br>
              <label><input  value="0" name="interestpaid" type="radio"  data-bind="checked: interestpaid" required> No</label>
              <label> <input value="1" name="interestpaid" type="radio"  data-bind="checked: interestpaid"  required> Yes</label>
         </div>
       
    </div>
   
         <div class="form-group row">
      <!-- ko if: parseInt(interestpaid())===1 -->
         <label class="col-lg-3 col-form-label">Interest Calculation Method<span class="text-danger">*</span></label>
          <div class="col-lg-3 form-group">
         <select class="form-control" name="interestcalmtd_id" required >
            <option value="" >Select .... </option>
                <?php
                foreach($cal_mthd as $method){
                    echo "<option value='".$method['id']."'>".$method['interest_method']."</option>";
                }
                ?>
          </select>
        </div>
        <!--/ko -->
        <!-- ko if: parseInt(interestpaid())===1 -->
        <!-- ko with: producttype  -->
        <!-- <label class="col-lg-2 col-form-label" data-bind="visible: parseInt(id)===1">Interest Rate <b>per annum</b></label>
        <div class="col-lg-3 form-group" data-bind="visible: parseInt(id)===1">
          <input min="0.5" required class="form-control" name="defaultinterestrate" type="number">
        </div> -->
              <label class="col-lg-2"  data-bind="visible: parseInt(id) !=2">Interest Rate <b>per annum</b><span class="text-danger">*</span></label>
              <div class="col-lg-2"  data-bind="visible: parseInt(id) !=2">
              Min: <input type="number" min="0"  max="99.9" name="mininterestrate" id="mininterestrate" class="form-control" required> 
              </div>
              <div class="col-lg-2"  data-bind="visible: parseInt(id) !=2">
              Max <input type="number" min="0"  max="100" name="maxinterestrate" id="maxinterestrate" class="form-control" required>
              </div>
        <!--/ko -->
        <!--/ko -->
         
    </div> 
      
     
           
    
    <!-- ko if: parseInt(interestpaid())===1 -->
    <div class="form-group row">
        <label class="col-lg-3 col-form-label">Interested Paid Account<span class="text-danger">*</span></label>
         <div class="col-lg-3">
         <select class="form-control" name="interest_paid_expense_account_id" id="interest_paid_expense_account_id"  data-bind='options: select2accounts(15), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
            </select>
         </div>
         <label class="col-lg-2 col-form-label">Interest Earned Payable Account<span class="text-danger">*</span></label>
         <div class="col-lg-4">
         <select class="form-control" name="interest_earned_payable_account_id" id="interest_earned_payable_account_id"  data-bind='options: select2accounts(8), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
            </select>
         </div>
    </div>  
      <div class="form-group row">
        <label class="col-lg-3 col-form-label">Days in Year<span class="text-danger">*</span></label>
         <div class="col-lg-3">
         <select class="form-control" name="daysinyear" required >
            <option value="" >Select .... </option>
                <?php
                foreach($daysinyear as $days){
                    echo "<option value='".$days['id']."'>".$days['name']."</option>";
                }
                ?>
            </select>
         </div>
          <label class="col-lg-3 col-form-label">Account Balance for Interest Calculation <span class="text-danger">*</span></label>
         <div class="col-lg-3">
         <select class="form-control" name="account_balance_for_interest_cal" required >
            <option value="" >Select .... </option>
                <?php
                foreach($account_balance_interest as $value){
                    echo "<option value='".$value['id']."'>".$value['name']."</option>";
                }
                ?>
            </select>
         </div>
  
    </div> 
    <div class="form-group row">
       <label class="col-lg-3 col-form-label" >Calculate Interest <b>every</b></label>
        <div class="col-lg-2 form-group" >
          <input min="1" max="28" required class="form-control" name="wheninterestispaid" type="number">
        </div>
         <label class="col-lg-3 col-form-label" >Of the <b>Month</b></label>
  
    </div>
     <!--/ko -->   
    <fieldset class="col-lg-12" data-bind="">     
      <legend>Amount</legend>
      <div class="form-group row">  
        <label class="col-lg-1 col-form-label">Min. Deposit</label>
        <div class="col-lg-3 form-group">
          <input min="0" class="form-control" name="mindepositamount" type="number"> 
        </div>
        <label class="col-lg-1 col-form-label">Max. Withdraw</label>    
        <div class="col-lg-4 form-group"> 
          <select class="form-control" id="withdraw_cal_method_id" name="withdraw_cal_method_id"data-bind="options: amountCalOptions, optionsText: 'amountcalculatedas', optionsAfterRender: setOptionValue('amountcalculatedas_id'), optionsCaption: '--select--'" >
          </select>     
        </div>
        <div class="col-lg-3 form-group">
          <input min="0" class="form-control" name="maxwithdrawalamount" type="number">
        </div> 
      </div> 
      <div class="form-group row">       
        <label class="col-lg-1 col-form-label">Minimum Balance<span class="text-danger">*</span></label>
        <br>
          <div class="col-lg-3 form-group"> 
            <select class="form-control" id="bal_cal_method_id" name="bal_cal_method_id" 
            data-bind="options: amountCalOptions, optionsText: 'amountcalculatedas', optionsAfterRender: setOptionValue('amountcalculatedas_id'), optionsCaption: '--select--'">
            </select>    
          </div>
        <div class="col-lg-3 form-group">
       <input  min="0" required class="form-control" name="min_balance" type="number"> <br>
      </div>   
    <!-- ko with: producttype  -->
        <label data-bind="visible:  parseInt(id) !==parseInt(3)" class="col-lg-2 col-form-label">Savings (Payable) Account<span class="text-danger">*</span></label>
        <label data-bind="visible:  parseInt(id) ===parseInt(3)" class="col-lg-2 col-form-label">Shares(Capital) Account<span class="text-danger">*</span></label>
        <div  class="col-lg-3 form-group">
         <select class="form-control" name="savings_liability_account_id" id="savings_liability_account_id"  data-bind='options: $parent.select2accounts([8,10,11]), optionsText: $parent.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
          </select>
        </div>
   
    <!--/ko -->
      </div>                           
    </fieldset>

    <!-- ko with: producttype  -->
    <fieldset class="col-lg-12" data-bind="visible: parseInt(id)===2">     
            <legend>Term Length (In Months)</legend>
            <span class="text-danger"><small>These are required fields for a Fixed Savings product</small></span>
            <div class="form-group row">  
    
    
         <label class="col-lg-3 col-form-label">Minimum <span class="text-danger">*</span></label>
         <div class="col-lg-3 form-group">
            <input class="form-control"  name="mintermlength" type="number" >
          </div>
           <label class="col-lg-3 col-form-label">Maximum<span class="text-danger">*</span></label>
         <div class="col-lg-3 form-group">
            <input class="form-control"  name="maxtermlength" type="number" >
          </div>
          </div>
             
    </fieldset>
    <!--/ko -->
    <div class="hr-line-dashed"></div>
   
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Mandatory Saving?<span class="text-danger">*</span></label>
      <div class="col-lg-2 form-group">
             <br>
            <label><input  value="0" name="mandatory_saving" type="radio"  data-bind="checked: mandatory_saving" required> No</label>
            <label> <input value="1" name="mandatory_saving" type="radio"  data-bind="checked: mandatory_saving"  required> Yes</label>
       </div>
       <!-- ko if: parseInt(mandatory_saving())===1 -->
       <label class="col-lg-2 col-form-label">Saving Made Every<span class="text-danger">*</span></label>                            
        <div class="col-lg-2 form-group">
            <input placeholder="" required class="form-control" name="saving_frequency" type="number">
        </div>
        <select class="col-lg-4 form-control" name="saving_made_every">
            <option value="" selected>--Select the frequency--</option>
            <?php
            foreach ($repayment_made_every as $value) {
                echo "<option value='" . $value['id'] . "'>" . $value['made_every_name'] . "</option>";
            }
            ?>
        </select>
        <!-- /ko -->
    </div>
    <!-- ko if: parseInt(mandatory_saving())===1 -->
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Min Saving Amount<span class="text-danger">*</span></label>
      <div class="col-lg-3 form-group">
          <div class="input-group">
             <input class="form-control"  required name="min_saving_amount" type="number" min="0">
          </div>
      </div>
      <label class="col-lg-2 col-form-label">Reminder<br/> <small>in days</small><span class="text-danger">*</span></label>
      <div class="col-lg-2 form-group">
        <input placeholder="" required class="form-control" name="reminder_frequency" type="number">
      </div>
      <select class="col-lg-3 form-control" name="reminder_made_every">
            <option value="" selected>--Select the frequency--</option>
            <option value="1">Once before & after</option>
            <option value="2">Once after</option>
            <option value="3">Daily before & after</option>
            <option value="4">Daily after</option>
            
      </select>
    </div>

    <div class="row form-group">
      <label class="col-lg-2 col-form-label">Penalty?<span class="text-danger">*</span></label>
      <div class="col-lg-2 form-group">
            <label><input  value="0" name="penalty" type="radio"  data-bind="checked: penalty" required> No</label>
            <label> <input value="1" name="penalty" type="radio"  data-bind="checked: penalty"  required> Yes</label>
       </div>
       <!-- ko if: parseInt(penalty())===1 -->
       <label class="col-lg-2 col-form-label" for="penalty_calculated_as">Calculated as<span class="text-danger">*</span></label>
        <div class="col-lg-3 form-group"> 
          <select data-bind="value: penalty_calculated_as" class="form-control" name="penalty_calculated_as">
            <option value="1">Percentages</option>
            <option value="2">Fixed Amount</option>
          </select>     
        </div>

        <div class="col-lg-3 form-group">
          <div class="input-group">
             <input data-bind="value: penalty_amount" class="form-control" placeholder="Amount"  required name="penalty_amount" type="number" min="0">
          </div>
        </div>
      <!-- /ko -->
    </div>
    <!-- ko if: parseInt(penalty())===1 -->
    <div class="row form-group">
            <label class="col-lg-3" for="penalty_income_account_id">Penalty Income Account <span class="text-danger">*</span></label>
            <div class="col-lg-4">
              <select class="form-control" name="penalty_income_account_id" id="penalty_income_account_id"
                data-bind='options: $root.select2accounts(12), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")'
                style="width: 100%" required data-msg-required="Select an option">
              </select>
            </div>
    </div>
    <!-- /ko -->


    <!-- /ko -->


    <div class="form-group row">
    <label class="col-lg-2 col-form-label">Description<span class="text-danger">*</span></label>
    <div class="col-lg-10 form-group">
         <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
    </div>
    </div>               
</div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
        <?php if((in_array('1', $deposit_product_privilege))||(in_array('3', $deposit_product_privilege))){ ?>
        <button type="submit" class="btn btn-primary"><?php
    if (isset($saveButton)) {
        echo $saveButton;
    }else{
        echo "Save";
    }
 ?></button>
   <?php } ?>
    </div>
</form>
</div>
</div>
</div>
