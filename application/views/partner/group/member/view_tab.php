<div role="tabpanel" id="tab-membership" class="tab-pane">
    <div class="panel-body">
    <?php if(in_array('1', $group_privilege)){ ?>
        <div><a data-toggle="modal" href="#add_group_member-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> Add Member</a></div>
    <?php } ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hoversmall m-t-md" id="tblGroup_member" width='100%'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Contribution</th>
                        <th>Leader?</th>
                        <?php //	if(isset($_SESSION['accountant']) || isset($_SESSION['admin'])){   ?>
                        <th>Action</th>
                        <?php
                        //}
                        ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
        <?php $this->view("group/member/add_modal"); ?>
</div>