<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link " data-toggle="tab" href="#tab-details"> Branch Details</a></li>
                <li><a class="nav-link active" data-toggle="tab" href="#tab-depts">Departments</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-details" class="tab-pane ">
                    <div class="panel-body">
                        <table class="table table-stripped small m-t-md">
                            <tbody data-bind="with: branch">
                                <tr>
                                    <td class="no-borders">
                                        <i class="fa fa-houzz text-navy"></i> Branch Name
                                    </td>
                                    <td data-bind="text: branch_name" class="no-borders">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-hashtag text-navy"></i> Branch Code
                                    </td>
                                    <td data-bind="text: branch_number">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-mobile-phone text-navy"></i> Phone Contact
                                    </td>
                                    <td>
                                        <a data-bind="attr: {href:'tel:'+office_phone}, text: office_phone" title="Click to call"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-at text-navy"></i> Email Contact
                                    </td>
                                    <td>
                                        <a data-bind="attr: {href:'mailto:'+email_address}, text: email_address" title="Click to send email"></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-depts" class="tab-pane active">
                    <div class="panel-body">
                    <div><strong>Branch Departments</strong>  <?php if(in_array('1', $privileges)){ ?><a data-toggle="modal" href="#add_department-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus"></i> Add Department</a><?php } ?></div>
                        <div class="table-responsive">
                                <table id="tblDepartment" class="table table-striped table-bordered table-hover small m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>&nbsp;</th>
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
    <?php echo $add_branch_modal; ?>
    <?php echo $add_dept_modal; ?>
<script>
    var dTable = {};
    var branchDetailModel = {};
    $(document).ready(function() {
        $('form#formBranch').validator().on('submit', saveData);
        $('form#formDepartment').validator().on('submit', saveData);
    /*********************************** Page Data Model (Knockout implementation) *****************************************/
    var BranchDetailModel = function() {
        var self = this;
        self.branch = ko.observable(<?php echo json_encode($branch);?>);
        self.initialize_edit = function(){
            edit_data(self.branch(),"formBranch");
    };
    };
    branchDetailModel = new BranchDetailModel();
    ko.applyBindings(branchDetailModel);

        var handleDataTableButtons = function() {
            if ($("#tblDepartment").length) {
                dTable['tblDepartment'] = $('#tblDepartment').DataTable({
                    "dom": '<"html5buttons"B>lTfgitp',
                    order: [[1, 'asc']],
                    deferRender: true,
                    ajax:  {
                                        "url":"<?php echo site_url('department/jsonList')?>",
                                        "dataType": "JSON",
                                        "type": "POST",
                                        "data": function(d){
                                            d.branch_id = <?php echo $branch['id']; ?>;
                                        }
                                    },
                    "columnDefs": [ {
                            "targets": [2],
                            "orderable": false,
                            "searchable": false
                        }],
                    columns:[
                        { data: 'department_number' , render: function(data, type, full,meta){
                                if(type==="sort" || type==="filter"){
                                    return data;
                                }
                                return "<a href='<?php echo site_url('department/view'); ?>/"+full.id+"' title='View department details'>"+data+"</a>";
                        }
                       },
                        { data: 'department_name'  , render: function(data, type, full,meta){
                                if(type==="sort" || type==="filter"){
                                    return data;
                                }
                                return "<a href='<?php echo site_url('department/view'); ?>/"+full.id+"' title='View department details'>"+data+"</a>";
                        }
                       },
                       
                        { data: 'id', render: function(data, type, full, meta) {
                            var display_btn = "<div class='btn-grp'>";
                                 <?php if(in_array('3', $privileges)){ ?>
                                display_btn += "<a href='#add_department-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update department details'><i class='fa fa-edit'></i></a>";
                                <?php } if(in_array('4', $privileges)){ ?>
                                display_btn += '<a href="#" title="Delete department record"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                <?php } ?>
                                 display_btn += "</div>";
                                return display_btn; 
                            }
                        }
                    ],
                    <?php if(in_array('6', $privileges)){ ?>
                    buttons: [
                        { extend: 'copy'},
                        {extend: 'csv'},
                        {extend: 'excel', title: '<?php echo $title;?> Departments'},
                        {extend: 'pdf', title: '<?php echo $title;?> Departments'},
                        {extend: 'print',
                         customize: function (win){
                                $(win.document.body).addClass('white-bg');
                                $(win.document.body).css('font-size', '10px');

                                $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                        }
                        }
                    ],
                <?php } else { ?>
                buttons:[],
                <?php }  ?>
                responsive: true
                });
            }
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function() {
                    handleDataTableButtons();
                }
            };
        }();
        TableManageButtons.init();
    });
function reload_data(form_id, response){
    switch(form_id){
        case "formBranch":
            branchDetailModel.branch(response.branch);
            break;
        default:
            //nothing really to do here
            break;
    }
}
</script>