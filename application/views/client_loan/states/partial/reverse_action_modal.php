<div class="modal inmodal fade" id="reverse_action-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="post" class="formValidate" action="<?php echo base_url();?>loan_state/reverse" id="formReverse">

<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
     <h4 class="modal-title">
        <?php
        if (isset($modalTitle1)) {
            echo $modalTitle;
        }else{
            echo "Reversing Action";
        }
     ?></h4>
     <small class="font-bold">Note: Loan Application will go through the Application process again</small><br>
     <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
    </div>
        
          <div class="modal-body"><!-- Start of the modal body -->
            <!-- ko with: loan_details -->
                <input type="hidden" name="client_loan_id" data-bind="value: id" id="client_loan_id">
                <input type="hidden" name="group_loan_id" data-bind="value: (typeof group_loan_id !='undefined')?group_loan_id:''" id="group_loan_id">
                <input type="hidden" name="state_id" data-bind="value: (parseInt(state_id)==12)?7:1">
              <div class="form-group row"><!-- start of Requested Amount row -->
                <div class="col-lg-3"></div>
                    <div class="col-lg-3">
                      <h3 class="btn btn-danger btn-sm " data-bind="text: (state_name)?((!(action_date=='0000-00-00'))?state_name+' on '+moment(action_date,'YYYY-MM-DD').format('DD-MMM-YYYY'):state_name):''">
                      </h3>
                    </div>
                    <!-- 
                        <label class="col-lg-3 col-form-label">Requested Amount is</label>
                        <div class="col-lg-6 form-group">
                        <span placeholder="" class="form-control" type="readonly" data-bind="text: 'UGX '+curr_format((requested_amount)*1)"></span>
                        </div> -->
                    </div>
            <!--/ko--> 
                    <div class="form-group row">
                      <label class="col-lg-4 col-form-label">Date of Reversing<span class="text-danger">*</span></label>
                      <div class="col-lg-8 form-group">
                          <div class="input-group date" data-date-start-date="<?php echo isset($active_month)?date('d-m-Y', strtotime($active_month['month_start'])):date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month)?((strtotime(date('d-m-Y'))<(strtotime($active_month['month_end'])))?date('d-m-Y'):date('d-m-Y', strtotime($active_month['month_end']))):((strtotime(date('d-m-Y'))<(strtotime($fiscal_active['end_date'])))?date('d-m-Y'):date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                            <input class="form-control"   onkeydown="return false" autocomplete="off" required value="<?php echo date('d-m-Y')?>" name="action_date" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                          </div>
                      </div>
                    </div>
                    <div class="form-group row"><!-- start of reject note row -->
                        <label class="col-lg-4 col-form-label">Reason<span class="text-danger">*</span></label>
                        
                        <div class="col-lg-8 form-group">
                            <textarea required class="form-control" rows="3" name="comment" id="comment"></textarea>                           
                          </div>
                    </div><!--/row -->

                </div><!-- End of the modal body -->
                    <div class="modal-footer"><!-- start of the modal footer -->
                            <button  id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i> 
                            <?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                }else{
                                    echo "Save";
                                }
                             ?>
                        </button>
                        <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                            <i class="fa fa-times"></i> Cancel</button>
                    </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
