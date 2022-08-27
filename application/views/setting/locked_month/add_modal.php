<!-- bootstrap modal -->
<div class="modal inmodal fade" id="add_month-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <form method="post" class="formValidate" action="<?php echo base_url();?>Fiscal_month/create" id="formFiscal_month">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">New Month</h3>
                 <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="id">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Month <span class="text-danger">*</span></label>
                    <div class="col-lg-8 form-group">
                        <select class="form-control m-b" name="month_id"  required>
                            <option value="">--Select Month--</option>
                            <?php
                            foreach ($months as $month) {
                                echo "<option value='" . $month['id'] . "'>" . $month['month_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Year<span class="text-danger">*</span></label>
                        <div class="col-lg-8 form-group">
                           <select class="form-control m-b" name="year"  required>
                            <option value="<?php echo date('Y');?>"><?php echo date('Y');?></option>
                            <?php
                            for ($year =date('Y')+1;$year>=date('Y')-9; $year--) {
                                echo "<option value='" . $year . "'>" . $year . "</option>";
                            }
                            ?>
                        </select>
                        </div>                                                  
                    </div>                        
                </div>
                <div class="modal-footer">
                <?php if((in_array('1', $fiscal_privilege))||(in_array('3', $fiscal_privilege))){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> <?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            }else{
                                echo "Save";
                            }
                        ?>
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>
        </div>
    </div>
</div>
