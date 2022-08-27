<div class="modal inmodal fade" id="add_share_issuance-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>Share_issuance/Create" id="formShare_issuance">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h4 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Share Issuance";
    }
 ?></h4>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

<div class="modal-body">
 
 <div class="">
    <input type="hidden" name="id">
           <div class="form-group row">
             <label class="col-lg-2 col-form-label">Name / Category</label>
                <div class="col-lg-4 form-group">
                <input  required  class="form-control" name="issuance_name" type="text" id="issuance_name">
            </div>

              <label class="col-lg-2 col-form-label">Total Number of shares to Issue</label>
            <div class="col-lg-4 form-group">
                    <input class="form-control"  name="share_to_issue" min ="1" id="share_to_issue" type="number">
             </div>
               <label class="col-lg-2 col-form-label">Issuance Code</label>
            <div class="col-lg-4 form-group">
                    <input class="form-control"  name="issuance_code" min ="1" id="issuance_code" type="text">
             </div>
        </div> 

        <div class="form-group row">
          
             <label class="col-lg-2 col-form-label">Price Per Share</label>
                <div class="col-lg-4 form-group">
                <input placeholder="" required min ="1" class="form-control" name="price_per_share" type="number" id="price_per_share">
            </div>
             <label class="col-lg-2 col-form-label">Share Capital Account<span class="text-danger">*</span></label>
            <div class="col-lg-4">
                <select name="share_capital_account_id" class="form-control" data-bind='options: $root.select2accounts(11), optionsText: $root.formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required></select>
            </div>
        </div>                             
    <div class="hr-line-dashed"></div>
            <div class="form-group row">
           <label class="col-lg-2 col-form-label">Date of Issue</label>
            <div class="col-lg-4 form-group">
                <div class="input-group date" >
                    <input  type="text" autocomplete="off" class="form-control" name="date_of_issue"  required/><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <label class="col-lg-2 col-form-label">Closing Date</label>
            <div class="col-lg-4 form-group">
                   <div class="input-group date" >
                    <input  type="text" class="form-control" autocomplete="off" name="closing_date"  required/><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                   </div>
            </div>
            </div>                            
      <fieldset class="col-lg-12">     
                        <legend><small>Number of shares per application </small></legend>
                        <div class="form-group row">
                        <label class="col-lg-1 col-form-label">Default</label>
                        <div class="col-lg-3 form-group">
                        <input placeholder="" required class="form-control" min ="1" name="default_shares" type="number" id="default_shares">
                        </div>
                        <label class="col-lg-1 col-form-label">Min</label>
                        <div class="col-lg-3 form-group">
                                <input class="form-control" required  name="min_shares" min ="1" id="min_shares" type="number">
                        </div>
                    
                        <label class="col-lg-1 col-form-label">Max</label>
                        <div class="col-lg-3 form-group">
                        <input class="form-control" required name="max_shares"  min ="1" type="number">
                    </div>
                    </div>                            
      </fieldset>
    <div class="hr-line-dashed"></div>
		<fieldset class="col-lg-12">     
            <legend>Lock-In Period</legend>
            <div class="row">
            <label class="col-lg-1 col-form-label">Default</label>
            <div class="col-lg-2 form-group">
            <input placeholder=""  class="form-control" name="default_lock_in_period" type="number" min ="0" id="default_lock_in_period">
            </div>
            <label class="col-lg-1 col-form-label">Min</label>
            <div class="col-lg-2 form-group">
                    <input class="form-control"  min ="0"  name="min_lock_in_period" id="min_lock_in_period" type="number">
                </div>
        
            <label class="col-lg-1 col-form-label">Max</label>
            <div class="col-lg-2 form-group">
            <input class="form-control"   min ="0" name="max_lock_in_period" id="max_lock_in_period" type="number">
			</div>
			<label class="col-lg-1 col-form-label">Period<span class="text-danger">*</span></label>
			 <select class="form-control col-lg-2"  name="lock_in_period_id" id="lock_in_period_id" data-bind='options:repayment_made_every_options, optionsText:"made_every_name", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: active_lock_period' required data-msg-required="Deposit product type is required">
                </select>
            </div>                     
    </fieldset>
	<div class="hr-line-dashed"> </div>
	  <div class="form-group row">
       <label class="col-lg-4 col-form-label">Allow Inactive clients dividends?<span class="text-danger">*</span></label>
         <div class="col-lg-2">
          <select class="form-control" required name="allow_inactive_clients_dividends" id="allow_inactive_clients_dividends" >
          <option value="">--select--</option>
          <option value="1">YES</option>
          <option value="0">NO</option>
          </select>
         </div>
   <!--  <label class="col-lg-3 col-form-label">Refund Deadline</label>
    <div class="col-lg-3 form-group">
        <div class="input-group date" >
            <input  type="text" autocomplete="off" class="form-control" name="refund_deadline"  required/><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div> -->
     <label class="col-lg-3 col-form-label">Link to Savings Account<span class="text-danger">*</span></label>
         <div class="col-lg-3">
          <select class="form-control" required name="link_to_savings" id="link_to_savings" >
          <option value="">--select--</option>
          <option value="1">YES</option>
          <option value="0">NO</option>
          </select>
         </div>
  </div>   
  <div class="form-group row">
       <label class="col-lg-4 col-form-label">Notes<span class="text-danger">*</span></label>
         <div class="col-lg-8">
          <textarea class="form-control" name="share_notes"></textarea> 
         </div>
    </div>      
</div>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
        <?php if((in_array('1', $share_issuance_privilege))||(in_array('3', $share_issuance_privilege))){ ?>
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
