<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul>
            </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <div class="pull-right add-record-btn">
                        <?php if (in_array('1', $member_privilege)) { ?>
                            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_member-modal"><i class="fa fa-plus-circle"></i> New <?php echo $this->lang->line('cont_client_name'); ?> </button>
                        <?php } ?>
                        <?php $this->view('user/member/add_member_model'); ?>
                    </div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-active_clients"> Active</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-inactive_clients">Inactive</a></li>

                        <li id="member_referral" data-bind="visible:parseInt(member_referral_status())==parseInt(1)"><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-member_referral">Member Referral</a>
                        </li>

                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-member_requests"></i>Member Requests

                                <span class="badge bg-red" data-bind="text: 0">0</span>

                        </a></li>



                    </ul><br>

                    <div class="input-group row col-lg-8" style="padding-left: 200px;pt-5">
                        <div class="col-lg-4">

                            <select class="form-control" name="gender" id="gender">
                                <option>--Select Gender--</option>
                                <?php
                                $gender = array(1, 0);

                                foreach ($gender as $type) {
                                ?>
                                    <option value="<?php echo $type ?>"><?php echo $type == 1 ? "Male" : "Female" ?></option>
                                <?php
                                }
                                ?>

                            </select>
                        </div>
                        <span>
                            <button onclick="member_list_preview(event)" class="btn btn-primary" id="btn_preview_memberslist">preview</button>
                        </span>
                    </div>

                    <div class="tab-content">
                        <div role="tabpanel" id="tab-active_clients" class="tab-pane active">
                            <div class="hr-line-dashed"></div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="tblMember" width="100%">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('cont_client_name'); ?> No</th>
                                                <th>Full Name</th>
                                                <th>Gender</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Branch</th>
                                                <th>Date Registered</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" id="tab-inactive_clients" class="tab-pane">
                            <div class="hr-line-dashed"></div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="tblInactiveMember" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Client No</th>
                                                <th>Full Name</th>
                                                <th>Gender</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Branch</th>
                                                <th>Date Registered</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- member referral  -->
                        <div role="tabpanel" id="tab-member_referral" class="tab-pane">
                            <div class="hr-line-dashed"></div>
                            <div class="col-lg-12">

                                <div class="table-responsive">

                                    <table class="table display compact nowrap" width="100%">
                                        <tbody style="border: none;">
                                            <tr>
                                                <td>
                                                    <label><strong>Referrer's Name</strong></label>
                                                    <select name="introduced_by_id" id="member_introducing">
                                                        <option value="All">All Members</option>
                                                        <?php foreach ($member_referral_info as $member) { ?>
                                                            <option value="<?php echo htmlspecialchars($member['introduced_by_id']) ?>"><?php echo htmlspecialchars($member['member_name']); ?></option>
                                                        <?php } ?>

                                                    </select> &nbsp;&nbsp;
                                                    <span><button class="btn btn-primary btn-sm btn-flat" onclick="get_member_referrals(event)">Preview</button></span>
                                                    <div class="clear"></div>

                                                </td>

                                            </tr>
                                            <tr style="border-bottom:dashed 1px #eee;padding:5px;">

                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table table-striped table-bordered table-hover" id="tblMember_referrals" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">Member Name</th>
                                                <th style="width: 100px;">Savings</th>
                                                <th style="width: 100px;"># Shares bought</th>
                                                <th style="width: 50px;">Membership Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- </div> -->
                        <!-- member requests  -->
                        <div role="tabpanel" id="tab-member_requests" class="tab-pane">
                            <div class="hr-line-dashed"></div>
                            <div class="col-lg-12">

                                <div class="table-responsive">

                                   

                                    <table class="table table-striped table-bordered table-hover" id="tblMember_requests" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">Client No.</th>
                                                <th style="width: 100px;">Member Name</th>
                                                <th style="width: 100px;">Comment</th>
                                                <th style="width: 50px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- </div> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('user/member/staff_to_member_modal.php'); ?>

<script>
    var dTable = {};

    $(document).ready(function() {



        $('form#formMember').validate({
            submitHandler: saveData2
        });
        $('form#formStaff').validate({
            submitHandler: saveData2
        });

        $('#introduced_by_id').select2({
            dropDownParent: $('#add_member-modal')
        });

        $("#member_introducing").select2();

        //**************************** Page View KO Model *********************************************************//
        var ViewModel = function() {
            var self = this;
            self.marital_status_id = ko.observable();

            self.member_referral_status = ko.observable(<?php echo (isset($member_referral)) ? json_encode($member_referral) : ''; ?>);
            self.member = ko.observable();
            self.display_table = function(data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.client_no = ko.observable("<?php echo (isset($new_client_no)) ? $new_client_no : ''; ?>");
            self.members = ko.observableArray(<?php echo (isset($sorted_users)) ? json_encode($sorted_users) : ''; ?>);
            self.introduced_by_id = ko.observable();


        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);



        var handleDataTableButtons = function(tabClicked) {

            if ($('#tblMember').length && tabClicked === "tab-active_clients") {
                if (typeof(dTable['tblMember']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-active_clients").addClass("active");
                    dTable['tblMember'].ajax.reload(null, true);
                } else {
                    dTable['tblMember'] = $('#tblMember').DataTable({
                        "lengthMenu": [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, "All"]
                        ],
                        "processing": true,
                        "serverSide": true,
                        "language": {
                            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                        },
                        "deferRender": true,
                        responsive: true,
                        dom: '<"html5buttons"B>lTfgirtp',
                        buttons: <?php if (in_array('6', $member_privilege)) { ?> getBtnConfig('<?php echo $title; ?>'),
                    <?php } else {
                                        echo "[],";
                                    } ?>
                    ajax: {
                        url: "<?php echo site_url("member/jsonList2"); ?>",
                        dataType: 'JSON',
                        type: 'POST',
                        data: function(d) {
                            d.status_id = 1;
                            d.gender = $('#gender').val();
                            <?php if ($_SESSION['role_id'] == 4) { ?>
                                d.created_by = <?php echo $_SESSION['id'] ?>;
                            <?php } ?>
                        }
                    },
                    /* "columnDefs": [{
                                                    "targets": [6],
                                                    "orderable": false,
                                                    "searchable": false
                                                }],*/
                    "order": [
                        [7, "desc"]
                    ],
                    columns: [{
                            "data": "client_no"
                        },
                        {
                            "data": "firstname",
                            render: function(data, type, full, meta) {
                                return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "'>" + data + "  " + full.lastname + "  " + full.othernames + "</a>";
                            }
                        },
                        {
                            "data": "gender",
                            render: function(data, type, full, meta) {
                                if (full.gender == 1) {
                                    return "M";
                                }
                                return "F";
                            }
                        },
                        {
                            "data": 'mobile_number'
                        },
                        {
                            "data": "email"
                        },
                        {
                            "data": "branch_name"
                        },
                        {
                            "data": "date_registered",
                            render: function(data, type, full, meta) {
                                if (type === 'sort') {
                                    return data ? moment(data, 'YYYY-MM-DD').format('X') : '';
                                }
                                return data ? moment(data, 'YYYY-MM-DD').format("D-MMM-YYYY") : ''
                            }
                        },
                        {
                            data: 'id',
                            render: function(data, type, full, meta) {
                                var ret_txt = "<div class='btn-group'><a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "' class='btn btn-sm ' title='Edit'><i class='fa fa-edit'></i></a>";
                                <?php if (in_array('4', $member_privilege)) { ?>
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm  change_status" title="Deactivate Member"><i class="fa fa-ban text-danger"></i></a></div>';
                                    if (!full.staff_no) {
                                        ret_txt += "<a href='#add_staff-modal' data-toggle='modal' class='btn btn-sm make_member'><i class='text-primary fa fa-plus-circle' title='Add Staff'></i></a>";
                                    }
                                <?php } ?>
                                return ret_txt;
                            }
                        }
                    ]
                    });
                }
            }
            if ($('#tblInactiveMember').length && tabClicked === "tab-inactive_clients") {
                if (typeof(dTable['tblInactiveMember']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-inactive_clients").addClass("active");
                    dTable['tblInactiveMember'].ajax.reload(null, true);
                } else {
                    dTable['tblInactiveMember'] = $('#tblInactiveMember').DataTable({
                        //pageLength: 10,
                        "processing": true,
                        "serverSide": true,
                        "deferRender": true,
                        responsive: true,
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: <?php if (in_array('6', $member_privilege)) { ?> getBtnConfig('<?php echo $title; ?> - Inactive Members'),
                    <?php } else {
                                        echo "[],";
                                    } ?>
                    ajax: {
                        url: "<?php echo site_url("member/jsonList2"); ?>",
                        dataType: 'JSON',
                        type: 'POST',
                        data: function(d) {
                            d.status_id = 2;
                            d.gender = $('#gender').val();
                        }
                    },
                    // "columnDefs": [{
                    //         "targets": [7],
                    //         "orderable": false,
                    //         "searchable": false
                    //     }],
                    "order": [
                        [7, "desc"]
                    ],
                    columns: [{
                            "data": "client_no"
                        },
                        {
                            "data": "firstname",
                            render: function(data, type, full, meta) {
                                return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "'>" + data + "  " + full.lastname + "  " + full.othernames + "</a>";
                            }
                        },
                        {
                            "data": "gender",
                            render: function(data, type, full, meta) {
                                if (full.gender == 1) {
                                    return "M";
                                }
                                return "F";
                            }
                        },
                        {
                            "data": 'mobile_number'
                        },
                        {
                            "data": "email"
                        },
                        {
                            "data": "branch_name"
                        },
                        {
                            "data": "date_registered",
                            render: function(data, type, full, meta) {
                                return data ? moment(data, 'YYYY-MM-DD').format("D-MMM-YYYY") : ''
                            }
                        },
                        {
                            data: 'id',
                            render: function(data, type, full, meta) {
                                var ret_txt = "<div class='btn-group'><a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "' class='btn btn-sm '><i class='fa fa-edit'></i></a>";
                                <?php if (in_array('4', $member_privilege)) { ?>
                                    ret_txt += '<a href="#" data-href="<?php echo base_url(); ?>member/change_status" data-toggle="modal" class="btn btn-sm  change_status"><i class="fa fa-undo text-danger"></i></a></div>';
                                <?php } ?>
                                return ret_txt;
                            }
                        }
                    ]
                    });
                }
            }
            // member referral 
            if ($("#tblMember_referrals").length && tabClicked === "tab-member_referral") {
                if (typeof(dTable['tblMember_referrals']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-member_referral").addClass("active");
                    dTable['tblMember_referrals'].ajax.reload(null, true);
                    // get_member_referrals(this);
                } else {
                    dTable['tblMember_referrals'] = $('#tblMember_referrals').DataTable({
                        "pageLength": 25,
                        "responsive": true,
                        "dom": '<"html5buttons"B>lTfgitp',
                        buttons: <?php if (in_array('6', $till_privilege)) { ?> getBtnConfig('<?php echo $title; ?>- Billing'),
                    <?php } else {
                                        echo "[],";
                                    } ?> "ajax": {
                        "url": "<?php echo site_url('member/memberReferrals'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d) {
                            d.status_id = 1,
                                d.introduced_by_id = $('#member_introducing').val();
                        }
                    },
                    "footerCallback": function(tfoot, data, start, end, display) {
                        /*

                        var api = this.api();
                        var amount_page = api.column(2, {page: 'current'}).data().sum();
                        var amount_overall = api.column(2).data().sum();
                        $(api.column(2).footer()).html(curr_format(amount_page) );

                        */
                    },

                    "columns": [{
                            "data": "member_name"
                        },
                        {
                            data: 'saving_account_balance',
                            render: function(data, type, full, meta) {
                                return curr_format(data * 1);
                            }
                        },
                        {
                            data: 'shares_bought',
                            render: function(data, type, full, meta) {
                                return curr_format(data * 1);
                            }
                        },

                        {
                            "data": "fees_paid",
                            render: function(data, type, full, meta) {
                                const fees_status = full.fees_paid;
                                return parseInt(fees_status) != 0 ? "<i class='fa fa-check-circle' style='color:green;'></i> Paid" : "<i class='fa fa-cancel-circle' style='color:red;'></i>Not Paid";
                            }
                        }


                    ]
                    });
                }
            }
            // member requests 
            if ($("#tblMember_requests").length && tabClicked === "tab-member_requests") {
                if (typeof(dTable['tblMember_requests']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-member_requests").addClass("active");
                    dTable['tblMember_requests'].ajax.reload(null, true);
                } else {
                    dTable['tblMember_requests'] = $('#tblMember_requests').DataTable({
                        "pageLength": 25,
                        "responsive": true,
                        

                        ajax: {
                        url: "<?php echo site_url("member/jsonList2"); ?>",
                        dataType: 'JSON',
                        type: 'POST',
                        data: function(d) {
                            d.status_id = 4;
                            <?php if ($_SESSION['role_id'] == 4) { ?>
                                d.created_by = <?php echo $_SESSION['id'] ?>;
                            <?php } ?>
                        }
                    },
                        "footerCallback": function(tfoot, data, start, end, display) {

                        },

                        "columns": [
                            {
                                "data": "client_no"
                            },
                            {
                            "data": "firstname",
                            render: function(data, type, full, meta) {
                                return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "'>" + data + "  " + full.lastname + "  " + full.othernames + "</a>";
                            }
                        },
                            {
                                data: 'saving_account_balance',
                                render: function(data, type, full, meta) {
                                    return "Update profile";
                                }
                            },
                           

                            
                            {
                            data: 'id',
                            render: function(data, type, full, meta) {
                                var ret_txt = "<div class='btn-group'><a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.id + "' class='btn btn-sm ' title='Update Data'><i class='fa fa-edit'></i></a>";
                                <?php if (in_array('4', $member_privilege)) { ?>
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm  change_status" title="Reject Member Request"><i class="fa fa-ban text-danger"></i></a></div>';
                                    // if (!full.staff_no) {
                                    //     ret_txt += "<a href='#add_staff-modal' data-toggle='modal' class='btn btn-sm make_member'><i class='text-primary fa fa-plus-circle' title='Add Staff'></i></a>";
                                    // }
                                <?php } ?>
                                return ret_txt;
                            }
                        }


                        ]
                    });
                }
            }
        };
        $('table tbody').on('click', 'tr .make_member', function(e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof(data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var formId = tbl_id.replace("tblMember", "formStaff");
            edit_data(data, formId);

        });


        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-active_clients");

        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-member_referral");
        <?php //$this->load->view('member_referral/index') 
        ?>

    });

    function reload_data(formId, reponse_data) {

        switch (formId) {
            case "formStaff":
                dTable['tblMember'].ajax.reload(null, false);
                break;
            case "formMember":
                if (typeof reponse_data.user !== 'undefined') {
                    window.location = "<?php echo site_url('member/member_personal_info/'); ?>" + reponse_data.user;
                }
                break;
            default:
                //nothing really to do here
                break;
        }

    }

    function member_list_preview() {
        dTable['tblMember'].ajax.reload(null, false);
    }

    function get_member_referrals() {
        dTable['tblMember_referrals'].ajax.reload(null, false);
    }

    $("#member_referral").click(function() {
        $('#gender').hide();
        $("#btn_preview_memberslist").hide();

    });
</script>