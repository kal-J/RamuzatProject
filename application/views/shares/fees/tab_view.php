<div role="tabpanel" id="tab-apply_share_fee" class="tab-pane ">
    <div class="panel-body">
    <?php $shareHolderName = $get_share_by_id[ 'salutation' ] . ' ' .$get_share_by_id[ 'firstname' ] . ' ' . $get_share_by_id[ 'lastname' ]; ?>
    
        <div><strong><?php echo $shareHolderName; ?>'s Share fees</strong> <?php if(in_array('1', $share_privilege)){ ?> 
        <a data-toggle="modal" href="#add_share_application-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Pay fee(s) </a> 
         <?php } ?></div>
        <div class="table-responsive">
                <table id="tblApplied_share_fee" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                    <thead>
                        <tr>
                            <th>Transaction no</th>
                            <th>Share Fee name</th>
                            <th>Amount Calculated as</th>
                            <th>Amount</th>
                            <th>Required Fee</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div><!-- /.table-responsive--> 
    </div>
</div><!--End of Fees section-->