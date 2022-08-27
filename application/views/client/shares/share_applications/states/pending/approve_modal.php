<!-- bootstrap modal -->
 <style type="text/css">
    .greenText{color: green;font-size: 12px;}
 </style>

<div class="modal inmodal fade" id="approve-modal" tabindex="-1" privilege="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>shares/share_state" id="formShares_state">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h4 class="modal-title" data-bind="text:'Approve share application'"></h4>
                <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <!--ko with: application_details-->
                <input type="hidden" name="id" id="id" data-bind="value:id">
                <input type="hidden" name="status_id" value="1">
                <input type="text" name="share_issuance_id" id="share_issuance_id" data-bind="value:share_issuance_id">
                <!-- /ko -->
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">Applicant Name<span class="text-danger">*</span></label>
                    <div class="col-lg-4 form-group">
                   
                    <!--ko with: application_details-->
                    <input type="text" class="form-control" data-bind="value: salutation+' '+firstname+' '+lastname+' '+othernames" disabled>
                    <input type="hidden" name="member_id" data-bind="value:member_id" >
                    <!-- /ko -->
                    </div>
                    <label class="col-lg-2 col-form-label">Shares Requested<span class="text-danger">*</span></label>
                    <div class="col-lg-4 form-group">
                      <!--ko with: application_details-->
                         <input type="text"  class="form-control" data-bind="value:shares_requested" disabled >
                     <!-- /ko -->
                    </div>
                  </div>
                    <div class="form-group row">

                    <label class="col-lg-2 col-form-label">Approval Date <span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                          <div class="input-group date">
                            <input class="form-control" required name="approval_date" type="text"  ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                      </div> 
                    <!--ko with: application_details-->
                      <label class="col-lg-2 col-form-label">Shares approved <span class="text-danger">*</span></label>
                          <div class="col-lg-3 form-group" >
                              <input class="form-control" name="approved_shares"data-bind="textInput:approved_shares,valueUpdate:'afterkeydown', 
                                attr: {'data-rule-max':shares_requested,'data-msg-max':'You can not approve more than '+curr_format(shares_requested)+' ( maximum number of shares requested )','data-rule-min':1,'data-msg-min':'You can not approve less than '+curr_format(1)+' ( minimum number of shares per application )'}"  type="number" required >
                          </div> 
                          <input id='share_price' class="form-control" type="number" name="share_price" data-bind="value:share_price" hidden  required >
                     <!-- /ko -->

                    </div>
                   
                    <hr/> 
                      <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>
                            <div class="col-lg-10 form-group" >
                                <textarea required class="form-control" name="narrative" id="narrative"></textarea> 
                            </div>
                      </div>                      
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Approve</button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
