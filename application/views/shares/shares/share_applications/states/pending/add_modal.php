<!-- bootstrap modal -->
 <style type="text/css">
    .greenText{color: green;font-size: 12px;}
 </style>

<div class="modal inmodal fade" id="add_share_application-modal" tabindex="-1" privilege="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>shares/create2" id="formShares_application">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">  <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "New Shares Application";
                        }?></h3>
                 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Share Account<span class="text-danger">*</span></label>
                    <div class="col-lg-4 form-group">
                   <?php if (!isset($modalTitle)) { ?>
                        <select id='share_account_id' class="form-control" name="share_account_id"  required data-msg-required="Share Account is required" style="width: 100%">
                        </select>
                   <?php }else{?>
                    <!--ko with: share_details-->
                    <input type="text" class="form-control" data-bind="value: salutation+' '+firstname+' '+lastname+' '+othernames" disabled>
                    <input type="text" name="share_account_id" data-bind="value:share_account_id" >
                    <input id='share_issuance_id' class="form-control" type="text" name="share_issuance_id"  required data-bind='textInput: id'>
                    <!-- /ko -->
                    <?php } ?>
                    </div>
                    <label class="col-lg-2 col-form-label">Application Date</label>
                        <div class="col-lg-4 form-group">
                          <div class="input-group date">
                            <input class="form-control" required name="application_date" type="text" value="<?php echo date('d-m-Y');?>" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                      </div> 


                  </div>
                    <!-- <div class="form-group row">
                   
                      <label class="col-lg-2 col-form-label">Dividends Start Date</label>
                        <div class="col-lg-4 form-group">
                          <div class="input-group date">
                            <input class="form-control" required name="divident_DATE" type="text" ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                      </div>                                                 
                    </div> -->
                    <fieldset class="col-lg-12">     
                        <legend>Share  Details and payment</legend>
                        <div class="form-group row">
                          <label class="col-lg-2 col-form-label">Share Category<span class="text-danger">*</span></label>
                           <div class="col-lg-4 form-group">
                            <select class="form-control" id="category_id" name="category_id" data-bind='options: share_categories, optionsText: "category", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: category_id' required data-msg-required="Share category  is required">
                            </select>
                            <div data-bind="with: category_id"><span class="help-block-none"><small data-bind="text: cat_description">Category description goes here.</small></span></div>
                        </div>
                        <!--ko with: category_id-->
                         <input type="hidden" name="share_issuance_id" id="id" data-bind="value:share_issuance_id">
                          <label class="col-lg-2 col-form-label">Price per share<span class="text-danger">*</span></label>
                          <div class="col-lg-4 form-group" >
                              <input id='share_price' class="form-control" type="number" name="share_price" readonly required data-bind='textInput: price_per_share'>
                          </div>
                          
                         <!--  <label class="col-lg-2 col-form-label">Lock Period<span class="text-danger">*</span></label>
                          <div class="col-lg-3 form-group" >
                            <input id='lock_in_period' class="form-control" name="lock_in_period" required type="number" data-bind='textInput: default_lock_in_period, attr: {"data-rule-min":((parseInt(min_lock_in_period)>0)?min_lock_in_period:null), "data-rule-max": ((parseInt(max_lock_in_period)>0)?max_lock_in_period:null), "data-msg-min":"Share account lock in period is less than "+parseInt(min_lock_in_period), "data-msg-max":"Share account lock in period is more than "+parseInt(max_lock_in_period)}'>
                          </div> -->
                           
                        <!--/ko-->
                          </div>
                          <div class="form-group row">
                        <!--ko with: category_id-->

                            <label class="col-lg-2 col-form-label">Number of Shares <span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group" >
                                 <input class="form-control" data-bind="textInput: $root.shares_puchaced,valueUpdate:'afterkeydown', attr: {'data-rule-max':max_shares,'data-msg-max':'Can not be more than '+max_shares+' ( maximum number of shares per application )','data-rule-min':min_shares,'data-msg-min':'Can not be less than '+min_shares+' ( minimum number of shares per application )'}" id='shares_requested' name="shares_requested"  type="number" required >
                            </div>
                        <!--/ko-->

                        <!--ko with: category_id-->
                             <label class="col-lg-3 col-form-label">First Payment Amount<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group" >
                              <input readonly class="form-control"  type="text" data-bind='value: curr_format(round((($root.shares_puchaced()*price_per_share)),2)*($root.share_issuance().first_call_percent/100))'> 
                             <span class="greenText">First payment is ( <b data-bind="text:$root.share_issuance().first_call_percent"></b> % ) of the total amount.</span>
                            <input id='amount' readonly class="form-control" name="amount" required type="hidden" data-bind='value: round((($root.shares_puchaced()*price_per_share)*($root.share_issuance().first_call_percent/100)),2)'>
                             
                            </div>
                           <!--/ko-->
                          </div>
                       
                    </fieldset><!--/col-lg-12 -->  
                    <hr/> 
                      <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment</label>
                            <div class="col-lg-10 form-group" >
                                <textarea  class="form-control"  name="narrative" id="narrative"></textarea> 
                            </div>
                      </div>                      
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> <?php
                                            if (isset($saveButton)) {
                                                echo $saveButton;
                                            }else{
                                                echo "Save";
                                            }
                                         ?></button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
