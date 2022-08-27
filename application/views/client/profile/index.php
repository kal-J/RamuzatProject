<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-5 col-lg-3">
    <!-- ========LOAD  MEMBER NAV BAR HERE =============== -->
    <?php echo $profile_nav; ?>
    <!-- ========MEMBER NAV BAR =============== -->
    </div>
    <div class="col-xs-12 col-sm-6 col-md-7 col-lg-9" >
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                <div class="tab-content" style="min-height:500px;">
                    <!-- ================== START YOUR TAB CONTENT HERE =============== -->
                    <?php $this->load->view('client/profile/children/children_tab'); ?>
                    <?php $this->load->view('client/profile/personalinfo/personal_info'); ?>
                    <?php $this->load->view('client/profile/business/business_view_tab'); ?>
                    <?php $this->load->view('client/profile/contact/contact_view_tab'); ?>
                    <?php $this->load->view('client/profile/address/address_view_tab'); ?>
                    <?php $this->load->view('client/profile/nextofkin/nextofkin_view_tab'); ?>
                    <?php $this->load->view('client/profile/employment/employment_view_tab'); ?>
                    <?php $this->load->view('client/profile/password/password_view_tab'); ?>
                    <?php $this->load->view('client/profile/document/document_view_tab'); ?>
                    <!-- ================== END YOUR  TAB CONTENT HERE =============== -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    var dTable = {};
    var TableManageButtons = {};
    var userDetailModel = {};
    $(document).ready(function () {
        
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var loan_product_length='';
        <?php $this->load->view('user/profile_pic_js.php'); ?>
        <?php $this->load->view('user/signature/signature_pic_js.php'); ?>
        $("#village_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#subcounty_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#parish_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#district_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });

        var MemberFee = function () {
            var self = this;
            self.selected_fee = ko.observable();
        };

        //**************************************************************************************************************//
        var UserDetailModel = function () {
            var self = this;

            self.user = ko.observable(<?php echo json_encode($user); ?>);
            self.signature = ko.observable(<?php echo json_encode($user_signature); ?>);
            self.initialize_edit = function () {
                edit_data(self.user(), "form<?php echo ucfirst($type); ?>");
            };
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };


            // end memeber income and expenses

            self.districtsList = ko.observableArray(<?php echo json_encode($districts); ?>);
            self.subcountiesList = ko.observableArray();
            self.parishesList = ko.observableArray();
            self.villagesList = ko.observableArray();

            self.district = ko.observable();
            self.subcounty = ko.observable();
            self.parish = ko.observable();
            self.village = ko.observable();
            self.marital_status_id = ko.observable();
            self.date_of_birth = ko.observable();
            self.end_date=  ko.observable();
            self.checkbox = ko.observable();
            self.oncheck= function() {
                    return true;
            };

            self.district.subscribe(function (new_district) {
                if (typeof new_district !== 'undefined') {
                    get_filtered_admin_units(1, {district_id: new_district.id}, "<?php echo site_url("subcounty/jsonList"); ?>");
                }
                //clear the the other fields because we are starting the selection afresh
                self.subcountiesList(null);
                self.parishesList(null);
                self.villagesList(null);

                $('#subcounty_id').val(null).trigger('change');
                $('#parish_id').val(null).trigger('change');
                $('#village_id').val(null).trigger('change');
            });
            self.subcounty.subscribe(function (new_subcounty) {
                if (typeof new_subcounty !== 'undefined') {
                    get_filtered_admin_units(2, {subcounty_id: new_subcounty.id}, "<?php echo site_url("parish/jsonList"); ?>");
                }
                //clear the the parish and village fields because we are starting the selection afresh
                self.parishesList(null);
                self.villagesList(null);
                $('#parish_id').val(null).trigger('change');
                $('#village_id').val(null).trigger('change');
            });
            self.parish.subscribe(function (new_parish) {
                if (typeof new_parish !== 'undefined') {
                    get_filtered_admin_units(3, {parish_id: new_parish.id}, "<?php echo site_url("village/jsonList"); ?>");
                }
                //clear the the village field because we are starting the selection afresh
                self.villagesList(null);
                $('#village_id').val(null).trigger('change');
            });
        };
        userDetailModel = new UserDetailModel();
        ko.applyBindings(userDetailModel, $("#tab-biodata")[0]);
   
        //.......................end of address.................
        $('#form<?php echo ucfirst($type); ?>').validate({submitHandler: saveData2});
        $('#formNextOfKin').validator().on('submit', saveData);
        // for the address modal
        $('#formAddress').validator().on('submit', saveData);
        $('#formBusiness').validator().on('submit', saveData);
        $('#formContact').validator().on('submit', saveData);
        $('#formDocument').validator().on('submit', saveData);
        $('#formEmployment').validator().on('submit', saveData);
        $('#formUser_role').validator().on('submit', saveData);
        $('#formPassword').validator().on('submit', saveData);
        $('#formChildren').validator().on('submit', saveData);
        
        //contact javascript 
        var handleDataTableButtons = function (tabClicked) {

            <?php $this->load->view('client/profile/contact/contact_js'); ?>
                        // document ajax
            <?php $this->load->view('client/profile/document/document_js'); ?>
                        // nextofkin ajax
            <?php $this->load->view('client/profile/nextofkin/nextofkin_js'); ?>
                        //  employment javascript 
            <?php $this->view('client/profile/employment/employment_js'); ?>
            <?php $this->load->view('client/profile/address/address_js.php'); ?>
                        //  business javascript 
            <?php $this->load->view('client/profile/business/business_js.php'); ?>

            <?php $this->view('client/profile/children/children_js'); ?>
     
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();
        TableManageButtons.init("tab-active");

        
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "form<?php echo ucfirst($type); ?>":
                userDetailModel.user(response.user);
                break;
            
            default:
                //nothing really to do here
                break;
        }
    }
    function get_filtered_admin_units(admin_unit_type, data, url) {
        $.post(
                url,
                data,
                function (response) {
                    //if (response.success) {
                    switch (admin_unit_type) {
                        case 1: //subcounties
                            userDetailModel.subcountiesList(response.subcounties);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.subcounty(ko.utils.arrayFirst(userDetailModel.subcountiesList(), function (currentSubCounty) {
                                    return (parseInt(data.subcounty_id) === parseInt(currentSubCounty.id));
                                }));
                                $('#subcounty_id').val(data.subcounty_id).trigger('change');
                            }
                            //userDetailModel.subcountiesList().valueHasMutated();
                            break;
                        case 2: //parishes
                            userDetailModel.parishesList(response.parishes);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.parish(ko.utils.arrayFirst(userDetailModel.parishesList(), function (currentParish) {
                                    return (parseInt(data.parish_id) === parseInt(currentParish.id));
                                }));
                                $('#parish_id').val(data.parish_id).trigger('change');
                            }
                            break;
                        case 3: //villages
                            userDetailModel.villagesList(response.villages);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.village(ko.utils.arrayFirst(userDetailModel.villagesList(), function (currentVillage) {
                                    return (data.village_id === currentVillage.id);
                                }));
                                $('#village_id').val(data.village_id).trigger('change');
                            }
                            break;
                        default: //do nothing if wrong call
                            break;
                    }
                    //}
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }
    function set_selects(data) {
        edit_data(data, 'formAddress');
        //we need to set the district object  accordingly
        userDetailModel.district(ko.utils.arrayFirst(userDetailModel.districtsList(), function (currentDistrict) {
            return (parseInt(data.district_id) === parseInt(currentDistrict.id));
        }));
        $('#district_id').val(data.district_id).trigger('change');
        //as well as the subcounty object
        get_filtered_admin_units(1, data, "<?php echo site_url("subcounty/jsonList"); ?>");
        //we need to set the parish object accordingly
        get_filtered_admin_units(2, data, "<?php echo site_url("parish/jsonList"); ?>");
        //then finally thevillage object
        get_filtered_admin_units(3, data, "<?php echo site_url("village/jsonList"); ?>");
    }

    //for dateranger picker
    function handleDateRangePicker(startDate, endDate) {
                
        if(typeof displayed_tab !== 'undefined'){
                start_date = startDate;
                end_date = endDate;
                TableManageButtons.init(displayed_tab);
            }
    }
     function printDiv(divName) {
         var printContents = document.getElementById(divName).innerHTML;
         var originalContents = document.body.innerHTML;
         
         document.body.innerHTML = printContents;

         window.print();

         document.body.innerHTML = originalContents;
    }
</script>
