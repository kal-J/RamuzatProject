<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="tabs-container" id="sale_page">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1"><i class="fa fa-list"></i> Details</a></li>
                        <li><a data-toggle="tab" href="#tab-3"><i class="fa fa-photo"></i> Attachments</a></li>
                        <li><a data-toggle="tab" href="#tab-2"><i class="fa fa-credit-card"></i> Payments</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="box box-solid" data-bind="with:sale">
                                <div class="box-header with-border hidden_print">
                                    <!-- If the admin/bond owner is logged in -->
                                    <div class="pull-right">
                                        <div class="btn-group">
                                            <?php $no_order_cols = $no_order_cols1 = "";
                                            if ($_SESSION['role'] < 3) { ?>
                                                <span data-toggle="modal" href="#addSaleModal" title="Update sale details" class="btn btn-sm" data-bind="click: $parent.edit_sale"><i class="fa fa-pencil"></i> Edit</span>
                                            <?php } ?>
                                            <?php if ($_SESSION['role'] > 3 && $sale['vhcl_cnt'] == 0): ?>
                                                <span class="btn btn-sm" title='Delete sale details' data-bind="click:function(data_item){delete_item('the details of the sale with ref# ('+data_item.sale_ref+')', data_item.id, '<?php echo site_url("sale/delete"); ?>');}"><i class="fa fa-trash text-danger"></i> Delete</span>
                                                    <?php endif; ?>
                                                <span class="btn btn-sm" title='Print out sale details' data-bind="click:function(){printPageSection('tab-1', '<?php echo base_url("assets/css/bootstrap.min.css"); ?>');}"><i class="fa fa-print"></i> Print</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="col-md-2">
                                        <label>Sale Ref#</label>
                                    </div>
                                    <div class="col-md-1">
                                        <a data-bind="text: sale_ref, attr: {href:'<?php echo site_url("sales/view"); ?>/'+id}"  title="View Details"></a>
                                    </div>
                                    <div class="col-md-1">
                                        <label>Date</label>
                                    </div>
                                    <div class="col-md-2">
                                        <span data-bind="text: moment(sale_date,'X').format('D-MMM-YYYY')"  title="View Details"></span>
                                    </div>
                                    <div class="col-md-1">
                                        <label>Customer</label>
                                    </div>
                                    <div class="col-md-3">
                                        <a data-bind="text: customer_names, attr: {href:'<?php echo site_url("customer/view"); ?>/'+customer_id}" title="Customer details"><?php echo $sale['customer_names']; ?></a>
                                    </div> 
                                    <div class="clearfix"></div>
                                    <hr/>
                                    <div class="col-md-1">
                                        <label>Note</label>
                                    </div>
                                    <div class="col-md-6">
                                        <span data-bind="text: sale_notes">Notes</span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <hr/>
                                    <div class="box-header with-border">
                                        <ul class="nav nav-tabs">
                                            <li><h5>Vehicle(s)</h5></li>
                                            <?php if ($_SESSION['role'] < 3) { ?>
                                            <li class="pull-right hidden_print">
                                                <a class="btn btn-sm btn-default" href="#addOrderlineModal" data-toggle="modal" title="Add another vehicle to this sale"><i class="fa fa-plus-square"></i> Add</a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-stripped" id="tblOrderLine">
                                                <thead>
                                                    <tr>
                                                        <th>Entry Date</th>
                                                        <th>Make(Model)</th>
                                                        <th>License No.</th>
                                                        <th>Chasis No.</th>
                                                        <th>Engine No.</th>
                                                        <th>Year of Manufacture</th>
                                                        <th>Note</th>
                                                        <th>Amount</th>
                                                        <!-- If the bond officer or staff is logged in -->
                                                        <?php if ($_SESSION['role'] < 3): ?>
                                                            <th>&nbsp;</th>
                                                            <?php $no_order_cols = "8";
                                                        endif;
                                                        ?>
                                                        <!-- If the manager or admin is logged in -->
                                                        <?php if ($_SESSION['role'] > 3): ?>
                                                            <th>&nbsp;</th>
                                                                <?php $no_order_cols = "8";endif; ?>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Total</th>
                                                        <th colspan="6">&nbsp;</th>
                                                        <th>0</th>
                                                        <!-- If the bond officer or staff is logged in -->
                                                        <?php if ($_SESSION['role'] < 3): ?>
                                                            <th>&nbsp;</th>
                                                        <?php endif; ?>
                                                        <!-- If the manager or admin is logged in -->
                                                            <?php if ($_SESSION['role'] > 3): ?>
                                                            <th>&nbsp;</th>
                                                                <?php endif; ?>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                </div><!-- /.box body-->
                            </div><!-- /.box -->
                        </div><!-- /.tab-1 -->
                        <div id="tab-2" class="tab-pane">
                            <div class="table-responsive">
                                <table class="table table-condensed table-stripped" id="tblPayment">
                                    <thead>
                                        <tr>
                                            <th>Payment Ref#</th>
                                            <th>Sale Ref</th>
                                            <th>Date Paid</th>
                                            <th>Amount Paid</th>
                                            <!-- If the bond officer or staff is logged in -->
                                            <?php if ($_SESSION['role'] < 3): ?>
                                                <th>&nbsp;</th>
                                                <?php $no_order_cols1 .= "3";
                                            endif;
                                            ?>
                                            <!-- If the manager or admin is logged in -->
                                                <?php if ($_SESSION['role'] > 3): ?>
                                                <th>&nbsp;</th>
                                                    <?php $no_order_cols1 .= "3"; endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th colspan="2">&nbsp;</th>
                                            <th>0</th>
                                            <!-- If the bond officer or staff is logged in -->
                                            <?php if ($_SESSION['role'] < 3): ?>
                                                <th>&nbsp;</th>
                                                    <?php endif; ?>
                                            <!-- If the manager or admin is logged in -->
                                                <?php if ($_SESSION['role'] > 3): ?>
                                                <th>&nbsp;</th>
                                                    <?php endif; ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><!-- /.tab-2 -->
                    </div><!-- /.tab-content -->
                </div><!-- /.tabs-container -->
            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
    </div><!-- /.col-lg-5 -->
    <?php if ($_SESSION['role'] < 3) { 
        echo $add_sales_modal; 
        echo $add_vehicle_modal; ?>
    <?php } ?>
</div>
<script type="text/javascript">
    var dTable = saleModel = {};
    $(document).ready(function(){
        var SaleModel = function () {
            var self = this;
            self.sale = ko.observable(<?php echo json_encode($sale); ?>);
            <?php if ($_SESSION['role'] < 3) { ?>
            self.orderline_id = ko.observable();
            self.vehicle = ko.observable();
            self.vehicles = ko.observableArray(<?php echo json_encode($vehicles); ?>);
            self.filteredVehicles = ko.computed(function(){
                return ko.utils.arrayFilter(self.vehicles(), function(vehicle){
                    //return only those vehicles which haven't been selected for other sales or for this particular sale
                    //console.log(self.orderline_id());
                    return ( !vehicle.sale_id||(typeof self.orderline_id() !== 'undefined' && vehicle.orderline_id == self.orderline_id()) );
                    //return vehicle.id;
                });
            });
            <?php } ?>
            //operations
            self.edit_sale = function () { 
                edit_data(self.sale(),"formSale"); 
            };
        };
        saleModel = new SaleModel();
        ko.applyBindings(saleModel);
        
        var handleDataTableButtons = function() {
            if ($("#tblOrderLine").length) {
                   dTable['tblOrderLine'] = $("#tblOrderLine").DataTable({
                   "dom": '<".col-md-7"l><".col-md-2"><".col-md-3"f>rt<".col-md-7"i><".col-md-5"p>',
                   order: [[1, 'desc']],
                   deferRender: true,
                   "ajax": {
                       "url":"<?php echo site_url('orderline/jsonList')?>",
                       "dataType": "JSON",
                       "type": "POST"
                   },
                    columnDefs: [ {
                    "targets": [<?php echo $no_order_cols; ?>],
                    "orderable": false,
                    "searchable": false
                    }],
                    "footerCallback": function (tfoot, data, start, end, display ) {
                        var total = this.api().column(7).data().sum();
                        $(this.api().column(7).footer()).html( curr_format(Math.round(total)) );
                    },
                   columns:[
                       { data: 'entry_date', render: function( data, type, full, meta){ return data?moment(data,'X').format('D-MMM-YYYY'):'';}},
                       { data: 'label', render: function(data, type, full, meta){return full.vehicleMake + " ("+data+")"}},
                       { data: 'license_no'},
                       { data: 'chasis_no'},
                       { data: 'engine_no'},
                       { data: 'year_om'},
                       { data: 'order_line_notes'},
                       { data: 'amount', render: function( data, type, full, meta ){ return data?curr_format(data):0;}}
                      // If bond officer or staff is logged in 
                       <?php if ($_SESSION['role']  < 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return '<a data-toggle="modal" href="#addOrderlineModal" title="Update sale details" class="edit_me" ><i class="fa fa-pencil"></i></a>';}}
                       <?php endif; ?>
                       // If manager or admin is logged in -->
                       <?php if ($_SESSION['role']  > 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return '<a href="#" title="Delete" class="delete_me"><span class="fa fa-trash text-danger"></span></a>';}}
                       <?php endif; ?>	
                       ]
                   //   ,buttons: [ 'copy', 'excel', 'print' ]//, 'pdf'
               });
            }
            if ($("#tblPayment").length) {
                   dTable['tblPayment'] = $("#tblPayment").DataTable({
                   "dom": '<".col-md-7"B><".col-md-2l"><".col-md-3"f>rt<".col-md-7"i><".col-md-5"p>',
                   order: [[1, 'desc']],
                   deferRender: true,
                   "ajax": {
                       "url":"<?php echo site_url('payment/jsonList')?>",
                       "dataType": "JSON",
                       "type": "POST",
                       "data": function(d){
                           d.sale_id = <?php echo $sale['id']; ?>;
                        }
                   },
                    columnDefs: [ {
                    "targets": [<?php echo $no_order_cols1; ?>],
                    "orderable": false,
                    "searchable": false
                    }],
                    "footerCallback": function (tfoot, data, start, end, display ) {
                        var total = this.api().column(3).data().sum();
                        $(this.api().column(3).footer()).html( curr_format(Math.round(total)) );
                    },
                   columns:[
                       { data: 'payment_ref'},
                       { data: 'sale_ref'},
                       { data: 'payment_date', render: function( data, type, full, meta){ return data?moment(data,'X').format('D-MMM-YYYY'):'';}},
                       { data: 'amount', render: function( data, type, full, meta ){ return data?curr_format(data):0;}}
                      // If bond officer or staff is logged in 
                       <?php if ($_SESSION['role']  < 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return '<a data-toggle="modal" href="#addPaymentModal" title="Update payment details" class="edit_me" ><i class="fa fa-pencil"></i></a>';}}
                       <?php endif; ?>
                       // If manager or admin is logged in -->
                       <?php if ($_SESSION['role']  > 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return '<a href="#" title="Delete payment" class="delete_me"><span class="fa fa-trash text-danger"></span></a>';}}
                       <?php endif; ?>	
                       ]
                     ,buttons: [ 'copy', 'excel', 'print' ]//, 'pdf'
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
        //clicking the update icon
        $('table tbody').on('click', '.edit_me', function () {            
            var row = $(this).closest("tr");
            var dt = dTable["tblOrderLine"];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            edit_data(data, 'formOrderLine');
        });
        $('table tbody').on('click', '.delete_me', function () {            
            var row = $(this).closest("tr");
            var dt = dTable["tblOrderLine"];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            delete_item("vehicle with chasis no "+data.chasis_no, data.id, "<?php echo site_url("orderline/delete");?>");
        });
            
         <?php if ($_SESSION['role'] < 3) { ?>   
        $('#formSale').validator().on('submit', saveData);
        $('#formOrderLine').validator().on('submit', saveData);
         <?php } ?>
    });
    /*
    * this is for loading table data after data has been submitted via the form
    * @var response is the response (if any, that we got from the server
    * @var formElement, the form from which the data submission was made
     */
    
    function reload_data(formElement, response){
        switch(formElement){
            case "formSale":
                if(typeof response.sale !== 'undefined'){
                    saleModel.sale(response.sale);
                }
                break;
            case "formOrderLine":
                dTable["tblOrderLine"].ajax.reload(null,true);
                break;
            default:
                break;
        }
    }
    
 </script>   