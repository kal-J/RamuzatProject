<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
              
            <div class="ibox-content">
            <?php if(in_array('1', $privileges)){ ?>
            <div><a data-toggle="modal" href="#assign_moduleprivilege-modal" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus-circle"></i>Assign Privilege</a></div>
            <?php } ?>
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-active_privilege"> <?php echo $module['module_name']; ?> Module</a></li>
                    </ul>
                    
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-active_privilege" class="tab-pane active">
                        <div class="hr-line-dashed"></div>
          
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="tblModulePrivilege" >
                                        <thead>
                                            <tr>
                                                <th>Privilege</th>
                                                <th>CODE</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->view('setting/privilege/module_privilege/assign_privilege_modal'); ?>
<script>
    var dTable = {};
    $(document).ready(function () {
        $('form#formModulePrivilege').validator().on('submit', saveData);
        //**************************** Page View KO Model *********************************************************//
        var ViewModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        var handleDataTableButtons = function (tabClicked) {
            if ($('#tblModulePrivilege').length && tabClicked === "tab-active_privilege") {
                if (typeof (dTable['tblModulePrivilege']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-active_privilege").addClass("active");
                    dTable['tblModulePrivilege'].ajax.reload(null, true);
                } else {
                    dTable['tblModulePrivilege'] = $('#tblModulePrivilege').DataTable({
                        pageLength: 10,
                        "responsive": true,
                        "dom": '<"html5buttons"B>lTfgitp',
                        "buttons": <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Print Privileges'), <?php } else { echo "[],"; } ?>
                        ajax: {
                            url: "<?php echo site_url("modulePrivilege/jsonList2"); ?>",
                            dataType: 'JSON',
                            type: 'POST',
                            data: function (d) {
                                d.module_id= <?php echo $module['id'] ?>;
                            }
                        }, "columnDefs": [{
                                "targets": [3],
                                "orderable": true,
                                "searchable": true
                            }],


                        columns: [
                            {"data": "description"},
                            {"data": "privilege_code"},
                            {"data": 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                            {"data": 'status_id', render: function (data, type, full, meta) {
                                var ret_txt ="";
                                <?php if(in_array('3', $privileges)){ ?>
                                ret_txt += "<a href='#assign_moduleprivilege-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' title='Update Module Privileges details'><i class='fa fa-edit '></i></a>";
                              <?php } if(in_array('7', $privileges)){ ?>
                                    var title_text = parseInt(data)===1?"De":"A";
                                    var fa_class = parseInt(data)===1?"ban":"undo";
                                    var icon_color = parseInt(data)===1?"warning":"default";
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status" title="'+title_text+'ctivate role"><i class="fa fa-'+fa_class+' text-'+icon_color+'"></i></a>';
                                <?php } if(in_array('4', $privileges)){ ?>
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                                <?php } ?>
                                    return ret_txt;
                                }}
                        ]
                    });
                }
            }
            
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-active_privilege");

   
    });
</script>