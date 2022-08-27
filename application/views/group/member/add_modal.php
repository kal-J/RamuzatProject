<div id="add_group_member-modal" class="modal inmodal fade" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formGroup_member" name="formGroup_member" action="<?php echo site_url("group_member/create"); ?>" class="wizard-big formValidate">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">New Member(s)</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12">
                        <h1>Group Members</h1>
                        <div class="table-responsive">
                            <table  class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <!-- ko if:  !group_loanModel.group_leader_present() -->
                                        <th>Group Leader?</th>
                                        <!--/ko -->
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.added_group_members'>
                                    <tr>
                                        <td>
                                            <!--select data-bind='attr:{name:"group_member["+$index()+"][member_id]"}, options: $root.available_group_members, optionsText: function(data_item){return data_item.firstname+" " + data_item.lastname + " " +(data_item.othernames?data_item.othernames:"") + " - " +data_item.client_no;}, optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"), select2:{dropdownParent:$("#add_group_member-modal")}' class="form-control"  style="width: 250px"> </select-->
                                                <select data-bind='attr:{name:"group_member["+$index()+"][member_id]"}, select2:select2options()' class="form-control"  style="width: 250px" required> </select>
                                        </td>
                                        <!-- ko if: !group_loanModel.group_leader_present() -->
                                        <td>
                                            <input type="radio" value="1" class="form-control" data-bind='attr:{name:"group_member["+$index()+"][group_leader]"}'/>
                                        </td>
                                        <!--/ko -->
                                        <td>
                                            <input type="hidden" data-bind='attr:{name:"group_member["+$index()+"][group_id]"}' value="<?php echo $group['id']; ?>"/>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeGroupMember'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bind='click: $root.addGroupMember' class="btn-white btn-sm"><i class="fa fa-plus"></i> Add another member</button>
                    <?php if((in_array('1', $group_privilege))||(in_array('3', $group_privilege))){ ?>
                    <button class="btn btn-primary btn-flat" data-bind="enable:$root.added_group_members().length > 0" type="submit">Submit</button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

