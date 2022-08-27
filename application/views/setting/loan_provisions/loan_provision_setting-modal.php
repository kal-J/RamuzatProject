<!-- bootstrap modal -->
<div class="modal inmodal fade" id="loan_provision_setting-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url();?>portfolio_aging/create" id="formLoan_provision_setting">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">New Loan Provision Setting</h3>
                 <small class="font-bold">Note: Required fields are marked with<span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Range (Days)<span class="text-danger">*</span></label>
                    <div class="col-lg-3 form-group">
                    <div>
                     <input type="number" min="0" name ="start_range_in_days" id="start_range_in_days" class="form-control sm-3" placeholder="Start Range"required/>
                   </div>
                    </div>
                    <div class="col-lg-3">
                    <input type="number" min="0" name ="end_range_in_days" id="end_range_in_days" class="form-control sm-3" placeholder="End Range"required/>
                        <input type="hidden" name="alert_id" value="<?php //echo  $row['id']?>">
                    </div>
                </div>
                    
                    <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Name</label></label>
                        <div class="col-lg-6">
                        <input type="text" name="name" id="name" min="1" class="form-control">
                        </div>
                    </div> 
                    <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Description</label>
                    <div class="col-lg-9 form-group">
                    <textarea name="description" id="description" cols="3" rows="3" class="form-control" style="justify-content: left;"></textarea>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Provision Percentage<span class="text-danger">*</span></label>
                    <div class="col-lg-2 form-group">
                    <div>
                     <input type="number" min="0" name ="provision_percentage" id="provision_percentage" class="form-control sm-1"placeholder="0"required/>
                   </div>
                    </div>
                    <label class="col-lg-3 col-form-label">Loan Loss Provision A/c<span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                    <select class="form-control" name="provision_loan_loss_account_id" id="provision_loan_loss_account_id"required>
                     <?php foreach($account_from_chart as $accounts){
                        
                         ?>
                        <option value="<?php echo $accounts['id'] ?>"><?php  echo $accounts['account_code']." ".$accounts['account_name'] ?></option>
                        <?php }?>
                    </select>
                  
                    <input type="hidden" name="id" value="<?php //echo  $row['id']?>">
                    </div>
                </div>
                <div class="form-group row"> 
                    <label class="col-lg-3 col-form-label">Asset A/c<span class="text-danger">*</span></label>
                    <div class="col-lg-6">
                    <select class="form-control" name="asset_account_id" id="asset_account_id"required>
                     <?php foreach($account_from_chart2 as $accounts){
                         ?>
                        <option value="<?php echo $accounts['id'] ?>"><?php  echo $accounts['account_code']." ".$accounts['account_name'] ?></option>
                        <?php }?>
                    </select>
                    <input type="hidden" name="id" value="<?php //echo  $row['id']?>">
                    </div>
                </div>  
                    <div class="form-group row"> 
                    <label class="col-lg-3 col-form-label">Provision Method<span class="text-danger">*</span></label>
                    <div class="col-lg-6">
                     <select name="provision_method_id"id="provision_method_id" class="form-control">
                         <?php  $provision_method_id =array("Manual"=>0,"Automatic"=>1);
                         foreach($provision_method_id as $key=>$value){ 
                         ?>
                         <option value="<?php echo $value ?>"><?php echo $key ?></option>
                         <?php } ?>
                         </select>
                       </div>
                       </div>
                          
                </div>
                <div class="modal-footer">
                <?php if(in_array('3', $privileges)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data" onclick="update_alert_setting()">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
