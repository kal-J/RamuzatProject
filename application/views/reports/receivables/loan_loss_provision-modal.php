<!-- bootstrap modal -->
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url('Portfolio_aging/loan_loss_provision') ?>" id="formLoanLoss_provision2">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">X</span></button>
                <h3 class="modal-title">Loan Loss Provision</h3>
                 <small class="font-bold">Note: Required fields are marked with<span class="text-danger">*</span></small>
            </div>
            <div class="modal-body">
              <div class="form-group row">
                    <label class="col-lg-3 col-form-label">Amount<span class="text-danger">*</span></label>
                    <div class="col-lg-9 form-group">
                    <div>
                     <input name="required_provision_amount" id="required_provision_amount" value="<?php echo $required_provision_amount; ?>" class="form-control"readonly/>
                     <input name="provision_loan_loss_account_id" id="provision_loan_loss_account_id"  type="hidden" value="<?php echo $provision_loan_loss_account_id; ?>" class="form-control"readonly/>
                     <input name="asset_account_id" id="asset_account_id" type="hidden" value="<?php echo $asset_account_id; ?>" class="form-control"readonly/>
                     
                       </div>
                    </div>
                    <label class="col-lg-4 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group date" id="datepicker">
                                <input type="text"  name="transaction_date" id="transaction_date" placeholder="Date" value="<?php echo date('d-m-Y')?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div> 
                        <label class="col-lg-4 col-form-label">Narrative<span class="text-danger">*</span></label><br>
                        <div class="col-lg-8"><br>
                            <textarea  class="form-control" rowspan="3" colspan="4" name="narrative" id="narrative"></textarea>
                        </div>
                </div>

                </div>
                <div class="modal-footer">
                <?php // if(in_array('10', $privileges)){ ?>
                     <button id="btn-submit" type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check"></i> Submit
                    </button>
                    <?php // } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    </div>
        </form>

        <script>
          $(document).ready(function (){
              
            $('.date').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                autoclose: true,
                format: "dd-mm-yyyy"
            }).on('hide', function (e) {
                e.stopPropagation();
            }).on('changeDate', function (e) {
                $(e.target).trigger('change');
            });
            $("#formLoanLoss_provision2").validate({
            rules : {
                narrative : {
                    minlength : 6,
                    required: true
                },
               
            },
            submitHandler: function (form) {
                $.ajax({
                    type: $(form).attr('method'),
                    url: $(form).attr('action'),
                    data: $(form).serialize(),
                    dataType : 'json'
                })
                .done(function (response) {
                    if (response.success == true) {    
                        toastr.success(response.message, "Success"); 
                        $("#loan_loss_provision-modal").modal('hide');          
                    } else {
                        toastr.warning(response.message, "Failure!");
                    }
                });
                return false; 
            }
        });
    });
                
        </script>