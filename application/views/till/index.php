 <?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = $fiscal_year['end_date'] <= date('Y-m-d') ? date('d-m-Y', strtotime($fiscal_year['end_date'])) : date('d-m-Y');
?>
    <div class="row">
          <div class="col-lg-12">
          <div class="ibox ">
                <div class="ibox-title">
                     <ul class="breadcrumb">
                        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                    </ul> 
                </div>
                <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-cash_register">Cash Register</a></li>
                        <!-- <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-inactive_staffs">Inactive</a></li> -->
                    </ul>

                    <div class="tab-content">
                  

                          <?php $this->load->view('till/tab_view'); ?>


                        <div role="tabpanel" id="tab-inactive_staffs" class="tab-pane">
                          <div class="hr-line-dashed"></div>
                          <div class="col-lg-12">
                            <div class="table-responsive">
                             
                              </div>
                            </div>
                        </div>

                      </div>

                  </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
var dTable = {};
$(document).ready( function () {
      
        var ViewModel = function () {
            var self = this;
            self.marital_status_id = ko.observable();
            self.initialize_edit = function () {
                edit_data(self.formatOptions(), "form");
            };
            self.tchannel=ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.balance_bf=ko.observable(0);
            self.channel=ko.observable();
            self.closing_b=ko.observable(0);
            self.end_date=ko.observable();
            self.start_date=ko.observable();

            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        
    var handleDataTableButtons = function(tabClicked) {
        <?php $this->load->view('till/table_js'); ?>
 
    };
    TableManageButtons = function(){
    "use strict";
    return {
    init: function(tblClicked) {
      handleDataTableButtons(tblClicked);
    }
    };
    }();
    TableManageButtons.init("tab-cash_register");
    get_cash_register();

  } );

  function get_cash_register(that) {
        $('#gif').css('visibility', 'visible');
         var start_date = $('#start_date').val();
         var end_date = $('#end_date').val();
         var account_id = $('#account_id').val();
         var created_by = $('#created_by').val();
         var all = $('#all').val();
        $.ajax({
            url: "<?php echo site_url('till/cashRegister') ?>",
            data: {
                print: 0,
                status_id: 1,
                start_date: start_date,
                end_date: end_date,
                account_id:account_id,
                created_by:created_by,
                all:all
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                viewModel.start_date(start_date);
                viewModel.end_date(end_date);
                viewModel.balance_bf(response.balance);
                viewModel.closing_b(response.closing);
                dTable['tblJournal_transaction_line'].ajax.reload(null,true);
                $('#gif').css('visibility', 'hidden');
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
function reload_data(formId, reponse_data)
    {
          switch (formId) {
            case "formMember":
                dTable['tblStaff'].ajax.reload(null, false);
                break;
            case "formStaff":
               if (typeof reponse_data.user !== 'undefined' ) {
                 window.location = "<?php  echo site_url('staff/staff_data/');?>"+reponse_data.user;
               }
                break;
            default:
                //nothing really to do here
                break;
        }
     
      
 }
</script>
