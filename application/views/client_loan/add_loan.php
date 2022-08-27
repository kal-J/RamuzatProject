
<style type="text/css">
    section{
    overflow-y : auto;
}
</style>

<div class="ibox-content">
    <h2>
        Validation Wizard Form
    </h2>
    <p>
        This example show how to use Steps with jQuery Validation plugin.
    </p>

    <form method="post" class="formValidate wizard-big" id="formClient_loan1" action="<?php echo base_url('client_loan/Create2')?>" enctype="multipart/form-data">

        <h1>Loan Product Details</h1> <!-- Step one -->
        <section>
            <?php $this->load->view('client_loan/loan_steps_files/loan_application_details.php'); ?>
        </section>


        <h1>Income & Expenses</h1> <!-- Step two -->
        <section class="section">
            <?php $this->load->view('client_loan/loan_steps_files/income_and_expense.php'); ?>
        </section>


        <h1>Security & Fees</h1><!-- Step three -->
        <section class="section">
            <?php $this->load->view('client_loan/loan_steps_files/security_fees.php'); ?>
        </section>

        <h1>Loan Docs</h1><!-- Step three -->
        <section class="section">
            <?php $this->load->view('client_loan/loan_steps_files/loan_docs_file.php'); ?>
        </section>


        <h1>Finish</h1>
        <section>
            <div class="form-group row">
            <label class="col-lg-3 col-form-label">Save to ?</label>
                <div class="col-lg-9 form-group">
                    <select name="loan_app_stage"  class="form-control required">
                    <option value=''>--Select-- </option>  
                    <option value='0'> Application => Pending Approval </option>    
                    <option value='1'> Application => Approved </option>
                    <option value='2' > Application => Disburse </option>
                    </select>
                </div>      
            </div>

            <div class="form-group row">
                <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>
                <div class="col-lg-10 form-group">
                    <textarea class="form-control" rows="3"  required  name="comment" ></textarea>
                </div>
            </div><!--/row -->
        </section>
    </form>
</div>

<script>
        var add_loanModel = {};
        $(document).ready(function(){
                   


        <?php $this->load->view('client_loan/client_loan_knockout.php'); ?>
        
       });
    </script>