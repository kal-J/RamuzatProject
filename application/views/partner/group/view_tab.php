<div role="tabpanel" id="tab-details" class="tab-pane active">
    <div class="panel-body">
        <div><strong>Investiment Group Details</strong> 
        <?php if(in_array('3', $group_privilege)){ ?>
        <a data-toggle="modal" href="#add_group-modal"  data-bind="click: initialize_edit" class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Update</a>
        <?php } ?>
        </div>
        <table class="table table-stripped  m-t-md">
            <tbody data-bind="with: group">
                <tr>
                    <td class="no-borders">
                        <i class="fa fa-houzz text-navy"></i> Group Number
                    </td>
                    <td data-bind="text: group_no" class="text-muted no-borders">
                    </td>
                </tr>
                <tr>
                    <td>
                        <i class="fa fa-houzz text-navy"></i> Group Name
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
        <?php $this->view("partner/group/add_modal"); ?>
</div>