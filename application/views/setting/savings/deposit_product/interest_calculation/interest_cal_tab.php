<div id="tab-interest_cal_method" class="tab-pane">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $deposit_product_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_interest_cal_method"><i class="fa fa-plus-circle"></i> Add Interest Calculation Method </button>
    <?php } ?>
    </div>
    <h3><center>Interest calculation</center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblInterestCalMethod" width="100%" >
            <thead>
                <tr>
                    <th>Calculation Method</th>
                    <th>Description</th> 
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->load->view('setting/savings/deposit_product/interest_calculation/add_cal_method'); ?>
</div>

