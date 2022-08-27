<div id="add_loan_installment_rate-modal" class="modal fade" role="dialog" aria-labelledby="modal_loan_installment_rate" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h3>Add  a Loan installment rate </h3>
                        <div class="ibox-tools">
                            <a data-dismiss="modal" class="close">&times;</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <?php echo form_open_multipart("loan_installment_rate/create", array('id' => 'formLoan_installment_rate', 'class' => 'formValidate', 'name' => 'formLoan_installment_rate', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                        <input type="hidden" name="id" id="id" >
                        <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Loan&nbsp;installment rate<span class="text-danger">*</span></label>
                         <div class="col-lg-4 form-group">
                          <input placeholder="" required class="form-control" name="loan_installment_rate" id="loan_installment_rate" type="text">
                         </div>
                            <label class="col-lg-2 col-form-label">Unit<span class="text-danger">*</span></label>
                          <div class="col-lg-4 form-group">
                            <select id='loan_installment_unit' class="form-control required" name="loan_installment_unit" >
                               
                               <?php
                                 foreach($units as $unit){
                                   echo "<option value='".$unit['id']."'>".$unit['unit']."</option>";
                                 }
                               ?>
                            </select>
                          </div>       
                    </div><!--/row -->

                                        <select id='loan_installment_unit' class="form-control required" name="loan_installment_unit" >

                                            <?php
                                            foreach ($units as $unit) {
                                                echo "<option value='" . $unit['id'] . "'>" . $unit['amountcalculatedas'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                            <?php if((in_array('1', $privileges))||(in_array('3', $privileges))){ ?>
                            <button type="submit" class="btn btn-primary"><?php
                                if (isset($saveButton)) {
                                    echo $saveButton;
                                } else {
                                    echo "Save";
                                }
                           ?></button>
                            <?php } ?>
                        </div>

                        </form>
                    </div><!--/.ibox-content -->
                </div><!--/ibox -->
            </div><!--/modal-body -->
        </div><!--/modal-content -->
    </div><!--/col-lg-6 -->
</div><!--/add_branch-modal -->
