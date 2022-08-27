<div class="modal fade" id="edit_transaction_date_modal" tabindex="-1" role="dialog" aria-labelledby="printLayoutTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <div class="modal-body">
                <form id="edit_loan_trans_date" action="<?php echo base_url('client_loan/update_loan_installment_payment_date'); ?>">
                <input type="hidden" name="id" />

                    <div class="form-group">

                        <div class="row">
                            <label class="col-lg-12">Update Transaction Date</label>
                            <input class="form-control" id="payment_date" name="payment_date" type="text">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>