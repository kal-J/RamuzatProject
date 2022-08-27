<div class="modal inmodal fade" id="myModalAddress" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="formValidate" action="<?php echo base_url(); ?>Address/Create" id="formAddress" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">New Address</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">

                    <div class="">
                        <style>

                        </style>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Address 1<span class="text-danger">*</span></label>
                            <div class="col-lg-4">						
                                <textarea class="form-control" name="address1" rows="2">
                                </textarea>
                            </div>
                            <label class="col-lg-2 col-form-label">Address 2<span class="text-danger"></span></label>

                            <div class="col-lg-4">
                                <textarea class="form-control" name="address2" rows="2">
                                </textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Address Type<span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <select class="form-control m-b" name="address_type">
                                    <option value="Residential">Residential</option>
                                    <option value="Home">Home </option>
                                </select>
                            </div>

                            <label class="col-lg-2 col-form-label">District<span class="text-danger">*</span></label>
                            <div class="col-lg-4">						
                                <select class="form-control m-b select2able " id="district_id" name="district" data-bind='options: districtsList, optionsText: "district", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:district' data-msg-required="district must be selected" style="width: 100%">
                                    <option  value="23">--select--</option>
                                </select>
                            </div>

                        </div>


                        <div class="form-group row">

                            <label class="col-lg-2 col-form-label">Sub County<span class="text-danger">*</span></label>
                            <div class="col-lg-4">						
                                <select class="form-control m-b select2able" id="subcounty" name="subcounty" data-bind='options: subcountiesList, optionsText: "subcounty", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:subcounty' data-msg-required="district must be selected" style="width: 100%">
                                    <option  value="23">--select--</option>
                                </select>
                            </div>

                            <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Parish</label>
                            <div class="col-lg-4">
                                <select class=" form-control select2able" id="parish_id" name="parish" data-bind='options: parishesList, optionsText: "parish", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:parish' data-msg-required="Parish must be selected" style="width: 100%">
                                    <option value="">--select--</option>
                                </select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Village<span class="text-danger">*</span></label>
                            <div class="col-lg-4">						
                                <select class="form-control m-b select2able" id="village_id" name="village"  data-bind='options: villagesList, optionsText: "village", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:village' data-msg-required="Parish must be selected" style="width: 100%">
                                    <option  value="">--select--</option>								
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary btn-flat" value="Save Member"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var dTable = null;
    var userDetailModel = {};
    $(document).ready(function () {

        //**************************************************************************************************************//
        var UserDetailModel = function () {
            var self = this;
            // self.papList = ko.observableArray();

            self.districtsList = ko.observableArray(<?php echo json_encode($districts); ?>);
            self.subcountiesList = ko.observableArray();
            self.parishesList = ko.observableArray();
            self.villagesList = ko.observableArray();

            self.district = ko.observable();
            self.subcounty = ko.observable();
            self.parish = ko.observable();
            self.village = ko.observable();

            self.district.subscribe(function (new_district) {
                get_filtered_admin_units(1, {district_id: new_district.id}, "<?php echo site_url("subcounty/jsonList"); ?>");
            });
            self.subcounty.subscribe(function (new_subcounty) {
                get_filtered_admin_units(2, {district_id: new_subcounty.id}, "<?php echo site_url("parish/jsonList"); ?>");
            });
            self.parish.subscribe(function (new_parish) {
                get_filtered_admin_units(3, {parish_id: new_parish.id}, "<?php echo site_url("village/jsonList"); ?>");
            });
        };
        function get_filtered_admin_units(admin_unit_type, data, url) {
            $.post(
                    url,
                    data,
                    function (response) {
                        if (response.success) {
                            switch (admin_unit_type) {
                                case 1: //subcounties
                                    self.subcountiesList(response.subcounties);
                                    if(typeof data.id !== 'undefined' && !isNaN(data.id)){
                                        self.subcounty(ko.utils.arrayFirst(self.subcountiesList(), function(currentSubCounty){
                                            return (parseInt(data.subcounty_id) === parseInt(currentSubCounty.id));
                                        }));
                                        $('#subcounty_id').val(data.subcounty_id).trigger('change');
                                    }
                                    self.subcountiesList().valueHasMutated();
                                    break;
                                case 2: //parishes
                                    self.parishesList(response.parishes);
                                    if(typeof data.id !== 'undefined' && !isNaN(data.id)){
                                        self.parish(ko.utils.arrayFirst(self.parishesList(), function(currentParish){
                                            return (parseInt(data.parish_id) === parseInt(currentParish.id));
                                        }));
                                        $('#parish_id').val(data.parish_id).trigger('change');
                                    }
                                    break;
                                case 3: //villages
                                    self.villagesList(response.villages);
                                    if(typeof data.id !== 'undefined' && !isNaN(data.id)){
                                        self.village(ko.utils.arrayFirst(self.villagesList(), function(currentVillage){
                                            return (data.village_id === currentVillage.id);
                                        }));
                                        $('#village_id').val(data.village_id).trigger('change');
                                    }
                                    break;
                                default: //do nothing if wrong call
                                    break;
                            }
                        }
                    },
                    'json').fail(function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            });
        }
        userDetailModel = new UserDetailModel();
        ko.applyBindings(userDetailModel);

        $('.table#tblStaff tbody').on('click', '.edit_me', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            //var row = $(this).parent().parent();
            var data = dt.row(row).data();
            if (typeof (data) == 'undefined') {
                data = dt.row($(row).prev()).data();
            }
            // Display the pap details update form
            set_selects(data);
        });
        function set_selects(data)
        {
        edit_data(data,'formAddress');
        //we need to set the district object  accordingly
        userDetailModel.district(ko.utils.arrayFirst(userDetailModel.districtsList(), function(currentDistrict){
        return (parseInt(data.district_id) === parseInt(currentDistrict.id));
        }));
        $('#district_id').val(data.district_id).trigger('change');
        //as well as the subcounty object
        get_filtered_admin_units(3, data, "<?php echo site_url("subcounty/jsonList"); ?>");
        //we need to set the parish object accordingly
        get_filtered_admin_units(2, data, "<?php echo site_url("parish/jsonList"); ?>");
        //then finally thevillage object
        get_filtered_admin_units(3, data, "<?php echo site_url("village/jsonList"); ?>");
            }
        });
	
	
</script>
