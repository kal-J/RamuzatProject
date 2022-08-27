<div id="tab-social_fund" class="tab-pane">
    <div class="pull-right add-record-btn">
    <?php if(in_array('1', $subscription_privilege)){ ?>
        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_subscription_plan-modal"><i class="fa fa-plus-circle"></i> Add <?php echo $this->lang->line('cont_subscription');  ?></button>
    <?php } ?>
    </div>
    <h3><center><?php echo $this->lang->line('cont_subscription');  ?></center></h3>
    <div class="hr-line-dashed"></div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblSubscription_plan" width="100%" >
            <thead>
                <tr>
                    <th>Plan name</th>
                    <th>Amount payable</th>
                    <th>Paid Every</th>
                    <th>First payment made upon</th>
                    <th>Note</th>  
                    <th>Linked Income A/C</th>
                    <th>Linked Income Receivable A/C</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
     <?php $this->view('setting/subscription_plan/add_modal'); ?>
</div>

