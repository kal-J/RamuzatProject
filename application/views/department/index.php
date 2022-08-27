
    <div class="row">
        <div class="col-xs-12 col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
        <?php if(in_array('1', $privileges)){ ?>
                <a href="#add_branch-modal" data-toggle="modal"  class="btn btn-default btn-sm">
                    <i class="fa fa-plus-circle"></i> Add</a>
        <?php } ?>
            </div>
        </div>
        <?php //} ?>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-lg-12">
                    <div class="table-responsive">
                        <table id="tblBranch" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Telephone</th>
                                    <th>Email</th>
                                    <th>Physical Address</th>
                                    <th>Postal Address</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive-->
                </div><!-- /.col-xs-12 col-lg-10-->
                <?php echo $add_modal; ?>
            </div><!-- /.row-->
        </div><!-- ./panel-body -->
    </div><!-- ./panel panel-default -->
</div><!-- /.col-lg-12-->
</div><!-- /.row-->
<script>
    var dTable = {};
    $(document).ready(function() {
        var handleDataTableButtons = function() {
            if ($("#tblBranch").length) {
                dTable['tblBranch'] = $('#tblBranch').DataTable({
                    "dom": '<"html5buttons"B>lTfgitp',
                    order: [[1, 'asc']],
                    deferRender: true,
                    ajax: "<?php echo site_url('branch/jsonList')?>",
                    "columnDefs": [ {
                            "targets": [6],
                            "orderable": false,
                            "searchable": false
                        }],
                    columns:[
                        { data: 'branch_number' , render: function(data, type, full,meta){
                                if(type==="sort" || type==="filter"){
                                    return data;
                                }
                                return "<a href='<?php echo site_url('branch/view'); ?>/"+full.id+"' title='View branch details'>"+data+"</a>";
                        }
                    },
                        { data: 'branch_name'  , render: function(data, type, full,meta){
                                if(type==="sort" || type==="filter"){
                                    return data;
                                }
                                return "<a href='<?php echo site_url('branch/view'); ?>/"+full.id+"' title='View branch details'>"+data+"</a>";
                        }
                    },
                        { data: 'office_phone', render:function ( data, type, full, meta ) {return "<a href='tel:"+data+"'>"+data+"</a>";} },
                        { data: 'email_address', render:function ( data, type, full, meta ) {return "<a href='mailto:"+data+"'>"+data+"</a>";} },
                        { data: 'physical_address' },
                        { data: 'postal_address' },
                        { data: 'id', render: function(data, type, full, meta) {
                            var display_btn ="";
                            display_btn += "<div class='btn-grp'>";
                                <?php if(in_array('3', $privileges)){ ?>
                                display_btn += "<a href='#add_branch-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update branch details'><i class='fa fa-edit'></i></a>";
                              <?php } if(in_array('4', $privileges)){ ?>
                                display_btn += '<a href="#" title="Delete branch record"><span class="fa fa-trash text-danger delete_me"></span></a>';
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
                        {extend: 'excel', title: 'Sacco Branches'},
                        {extend: 'pdf', title: 'Sacco Branches'},
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
                    buttons: [],
                 <?php } ?>
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
    dt.ajax.reload(null, false);
    if(typeof response.branch_id !== 'undefined' && !isNaN(response.branch_id)){
        window.location.href = "<?php echo site_url("branch/view"); ?>/"+response.branch_id;
    }
}
</script>