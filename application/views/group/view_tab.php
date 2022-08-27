<div role="tabpanel" id="tab-details" class="tab-pane active">
    <div class="panel-body">
        <div><strong>Group Details</strong>
            <?php if(in_array('3', $group_privilege)){ ?>
            <!-- ko if: parseInt('<?php echo $group['group_client_type'] ?>') === 2 -->
            <a data-toggle="modal" href="#add_group-modal" data-bind="click: initialize_edit"
                class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a>
            <!-- /ko -->
            <!-- ko if: parseInt('<?php echo $group['group_client_type'] ?>') === 3 -->
            <a data-toggle="modal" href="#add_company-modal" data-bind="click: initialize_edit"
                class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a>
            <!-- /ko -->
            <?php } ?>
        </div>
        <table class="table table-stripped  m-t-md">
            <tbody data-bind="with: group">
                <!-- ko if: parseInt('<?php echo $group['group_client_type'] ?>') === 2 -->
                <tr>
                    <td class="no-borders">
                        <i class="fa fa-houzz text-navy"></i> Group Number
                    </td>
                    <td data-bind="text: group_no" class="text-muted no-borders">
                    </td>
                </tr>
                <!-- /ko -->

                <tr>
                    <td>
                        <i class="fa fa-houzz text-navy"></i> <?php echo $group['group_client_type'] == 3 ? 'Company Name' : 'Group Name' ?>
                    </td>
                    <td data-bind="text: group_name" class="text-muted">
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fa fa-hashtag text-navy"></i> Description
                    </td>
                    <td class="text-muted" data-bind="text: description">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php $this->view("group/add_group_modal"); ?>
    <?php $this->view("group/add_company_modal"); ?>
</div>