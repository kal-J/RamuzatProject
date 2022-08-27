<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                       
                    <li><a class="nav-link active" data-toggle="tab" href="#tab-organisation" data-bind="click: display_table"><i class="fa fa-modx"></i> Organisations </a></li>
                       
                        <li><a class="nav-link" data-toggle="tab" href="#tab-modulePrivilege" data-bind="click: display_table"><i class="fa fa-sitemap"></i>System Modules</a></li>
                       <!--  <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Organisation structure"><i class="fa fa-modx"></i> Structure </a>
                            <ul class="dropdown-menu">
                               
                                <li><a class="nav-link" data-toggle="tab" href="#tab-branch" data-bind="click: display_table"><i class="fa fa-modx"></i> Branches</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#tab-department" data-bind="click: display_table"><i class="fa fa-sitemap"></i> Departments</a></li>
                                
                                <li><a class="nav-link" data-toggle="tab" href="#tab-position" data-bind="click: display_table">Position</a></li>
                            </ul>
                        </li> -->
                      
                        
                    </ul>
                    <div class="tab-content">
                        <?php $this->view('organisation/tab_view'); ?>
                        <?php $this->view('setting/privilege/module_privilege/modulePrivilege_tab'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->view('organisation/add_modal'); ?>
<script>
    var dTable = {};
    var TableManageButtons = {};
    var settingsModel = {};
    $(document).ready(function () {
    
        $('form#formOrganisation').validate({submitHandler: saveData2});  
        $('form#formModulePrivilege').validator().on('submit', saveData);
      
        /*********************************** Page Data Model (Knockout implementation) *****************************************/
        var SettingsModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.chargeTriggerOptions = ko.observableArray(<?php echo json_encode($chargeTrigger); ?>);
            

        };
        settingsModel = new SettingsModel();
        ko.applyBindings(settingsModel);

        var handleDataTableButtons = function (tabClicked) {
        <?php $this->view('organisation/table_js'); ?>
        <?php $this->view('setting/privilege/module_privilege/moduleprivilege_js'); ?>
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-organisation");

    });

    function reload_data(formId, reponse_data) {
        if (typeof reponse_data.loan !== 'undefined') {

            window.location = "<?php echo site_url('loan_product/view/'); ?>" + reponse_data.loan;

        } 
        //settingsModel.groupList(reponse_data.parent_accounts);
    
    }
</script>
