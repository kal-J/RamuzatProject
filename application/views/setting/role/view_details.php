<script language="JavaScript">
    function toggle(source) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i] != source)
                checkboxes[i].checked = source.checked;
        }
    }
</script>
<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("setting"); ?>">Settings</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title  back-change">
                <h3 class="text-uppercase text-center"><?php echo $role['role']; ?> </h3>
                <div  class="text-center"><small>Assign Privileges to this role (<strong> <?php echo $role['role']; ?> </strong>)</small></div>
            </div>
            <div class="ibox-content">
                <?php echo form_open_multipart("rolePrivilege/create", array('id' => 'formRolePrivilege', 'class' => 'formValidate', 'method' => 'post', 'name' => 'formRolePrivilege', 'data-toggle' => 'validator', 'role' => 'form')); ?>
                <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                <div class="row">
                    <div class="col-lg-3" style="font-size:15px;">
                    <input type="checkbox"  onClick="toggle(this)" /> &nbsp; Select All<br />
                    </div>
                </div>
                <div class="row">
                    <?php
                    foreach ($modules as $key => $module) {
                        $module_id = $module['id'];
                        ?>
                        <div class="col-lg-3">
                            <h4 ><?php echo $module['module_name']; ?></h4>
                            <div data-bind="foreach: module_privileges(<?php echo $module['id']; ?>)">
                                <input type="checkbox" data-bind="value:privilege_id, attr:{checked:parseInt(yesno)==1, name:'role_privilege['+$index()+'_<?php echo $key; ?>][privilege_id]'}" > <span data-bind="text:description"> </span>
                                <br>
                                <input type="hidden" data-bind="value:id, attr:{ name:'role_privilege['+$index()+'_<?php echo $key; ?>][id]'}"/>
                            </div>
                        </div>
                <?php } ?>
                </div>
                <div class="pull-right add-record-btn">
                <?php if((in_array('31', $rolemodule_privilege))||(in_array('30', $rolemodule_privilege))){ ?>
                    <button id="btn-submit" type="submit" class="btn btn-primary btn-sm save_data">
                        <i class="fa fa-check"></i> Save </button>
                <?php } ?>
                </div>
                </form>
                <br>
                <br>
            </div>
        </div>
    </div>
</div>
<script>
var viewModel = {};
$(document).ready(function() {
    $('form#formRolePrivilege').validator().on('submit', saveData);
    var ViewModel = function() {
        var self = this;
        self.all_privileges= ko.observableArray(<?php echo json_encode($all_privileges); ?>);
        self.module_privileges= function(module_id){
            return ko.computed(function () {
                return ko.utils.arrayFilter(self.all_privileges(), function(privilege) {
                    return (parseInt(module_id)==parseInt(privilege.module_id));
                });
        });
     };
    };
    viewModel = new ViewModel();
    ko.applyBindings(viewModel);
    });

function reload_data(form_id, response){
    switch(form_id){
        case "formRolePrivilege":
            viewModel.all_privileges(response.all_privileges);
            break;
        default:
            //nothing really to do here
            break;
    }
}
</script>