<div class="modal inmodal fade" id="add_interest_cal_method" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="post" class="formValidate" action="<?php echo base_url();?>InterestCalMethod/Create" id="formInterestCalMethod">
<div class="modal-header">
 <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
 <h5 class="modal-title">
    <?php
    if (isset($modalTitle)) {
        echo $modalTitle;
    }else{
        echo "Add Interest Calculation Method";
    }
 ?></h5>
 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
</div>

<div class="modal-body">
 
 <div class="">
    <input type="hidden" name="id">
    <div class="form-group row">
       <label class="col-lg-4 col-form-label">Calculation Method<span class="text-danger">*</span></label>
         <div class="col-lg-8">
          <input placeholder="" required class="form-control" name="interest_method" type="text">
         </div>
       
    </div>
    <div class="form-group row">
      
         <label class="col-lg-4 col-form-label">Description<span class="text-danger">*</span></label>
    <div class="col-lg-8">
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
