<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $sub_title; ?></h3>
                        <?php $no_order_cols = ""; if ($_SESSION['role'] < 3): ?>
                            <div class="pull-right">
                                <a data-toggle="modal" href="#addPaymentModal" class="btn btn-default" title="Add Payment"><i class="fa fa-plus-square"></i> New</a>
                            </div>
                                <?php endif; ?>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover" id="tblPayment">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Payment Ref#</th>
                                        <th>Payment Date</th>
                                        <th>Sale Ref#</th>
                                        <th>Sale Date</th>
                                        <th>Sale Amount</th>
                                        <th>Paid Amount</th>
                                        <!-- If the bond officer or staff is logged in -->
                                        <?php if ($_SESSION['role'] < 3): ?>
                                            <th>&nbsp;</th>
                                            <?php $no_order_cols .= "7"; endif; ?>
                                        <!-- If the manager or admin is logged in -->
                                        <?php if ($_SESSION['role'] > 3): ?>
                                            <th>&nbsp;</th>
                                                <?php $no_order_cols .= "7"; endif; ?>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th colspan="3">&nbsp;</th>
                                        <th>0</th>
                                        <th>0</th>
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
                        </div><!-- /.table-responsive -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.panel-body -->
        </div><!-- /.panel -->
        <?php if ($_SESSION['role']  < 3): ?>
<?php echo $add_payment_modal; ?>
        <?php endif; ?>
    </div><!-- /.col-lg-12 -->
</div><!-- /.row -->
<script type="text/javascript">
    var dTable = {};
    $(document).ready(function(){
        
        var handleDataTableButtons = function() {
            if ($("#tblPayment").length) {
                   dTable['tblPayment'] = $("#tblPayment").DataTable({
                   "dom": '<".col-md-7"B><".col-md-2"l><".col-md-3"f>rt<".col-md-7"i><".col-md-5"p>',
                   order: [[0, 'desc']],
                   deferRender: true,
                   "ajax": {
                       "url":"<?php echo site_url('payment/jsonList')?>",
                       "dataType": "JSON",
                       "type": "POST"
                   },
                    columnDefs: [ {
                    "targets": [<?php echo $no_order_cols; ?>],
                    "orderable": false,
                    "searchable": false
                    }],
                    "footerCallback": function (tfoot, data, start, end, display ) {
                        var total = this.api().column(6).data().sum();
                        $(this.api().column(6).footer()).html( curr_format(Math.round(total)) );
                },
                   columns:[
                       { data: 'customer_names', render: function( data, type, full, meta ){ return '<a href="#addCustomersModal-'+full.customer_id+'" data-toggle="modal" title="View Customer Info">'+data+'</a>';}},
                       { data: 'payment_ref', render: function(data, type, full, meta){ return '<a href="#" title="View Payment details">'+data+'</a>';}},
                       { data: 'payment_date', render: function( data, type, full, meta){ return data?moment(data,'X').format('D-MMM-YYYY'):'';}},
                       { data: 'sale_ref', render: function(data, type, full, meta){ return '<a href="<?php echo site_url('sales/view')?>/'+full.sale_id+'" title="View Sale details">'+data+'</a>';}},
                       { data: 'sale_date', render: function( data, type, full, meta){ return data?moment(data,'X').format('D-MMM-YYYY'):'';}},
                       { data: 'vhcl_amount', render: function( data, type, full, meta ){ return data?curr_format(data):'';}},
                       { data: 'amount', render: function( data, type, full, meta ){ return data?curr_format(data):0;}}
                       // If bond officer or staff is logged in 
                       <?php if ($_SESSION['role']  < 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return '<a data-toggle="modal" href="<?php echo site_url('payments/view')?>/'+full.id+'" title="Update payment details" ><i class="fa fa-pencil"></i></a>';}}
                       //{ data: 'id', render: function ( data, type, full, meta ) {return '<a data-toggle="modal" href="#addPaymentModal" title="Update payment details" class="edit_me" ><i class="fa fa-pencil"></i></a>';}}
                       <?php endif; ?>
                       // If manager or admin is logged in -->
                       <?php if ($_SESSION['role']  > 3): ?>,
                       { data: 'id', render: function ( data, type, full, meta ) {return (full.vhcl_cnt==0||!full.vhcl_cnt)?'<a href="#" title="Delete" class="delete_me"><span class="fa fa-trash text-danger"></span></a>':'';}}
                       <?php endif; ?>	
                       ],
                   buttons: [ 'copy', 'excel', 'print' ]//, 'pdf'
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
        <?php if ($_SESSION['role']  < 3): ?>
        $('table tbody').on('click', '.edit_me', function () {            
            var row = $(this).closest("tr");
            var dt = dTable["tblPayment"];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            edit_data(data, 'formPayment');
        });
        $('#formPayment').validator().on('submit', saveData);
        $('#formCustomer').validator().on('submit', saveData);
        <?php endif; ?>
        <?php if ($_SESSION['role']  > 3): ?>
        $('table tbody').on('click', '.delete_me', function () {            
            var row = $(this).closest("tr");
            var dt = dTable["tblPayment"];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            delete_item(data.payment_ref, data.id, "<?php echo site_url("payment/delete");?>");
        });
         <?php endif; ?>
    });
    /*
    * this is for loading table data after data has been submitted via the form
    * @var response is the response (if any, that we got from the server
    * @var formElement, the form from which the data submission was made
     */
    
    function reload_data(formElement, response){
        switch(formElement){
            case "formPayment":
                dTable["tblPayment"].ajax.reload(null,true);
                break;
            default:
                break;
        }
    }
    
 </script>   