<div role="tabpanel" id="tab-loan-provision" class="tab-pane loan-provision">
    <div class="row d-flex flex-row-reverse mt-3 mr-4">
     
        <div class="">
            <div class="panel-title" style="color:#fff;">
                <?php if (in_array('1', $privileges)) { ?>
                    <a href="#loan_provision_setting-modal" data-toggle="modal" id="add_loan_provision_setting" class="btn btn-primary btn-lg"> <i class="fa fa-plus-circle" ></i> New Range Setting</a>
                <?php } ?>
            </div>
           
        </div>
      

    </div>

    <h3 class="text" style="text-align: center;border-bottom:1px dashed #ccc;padding:2px">Loan Provision Setting</h3>
    <br>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="Portfolio_aging" width="100%">
                <thead>
                    <tr>
                       <th>Start Range (Days)</th>
                        <th>End Range (Days)</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Provision %</th>
                        <th>Loan Loss Prov. A/C</th>
                        <th>Action</th>
                         
                    </tr>
                </thead>
                <tbody>
                </tbody>
                 
            </table>
        </div>
    </div>
</div>