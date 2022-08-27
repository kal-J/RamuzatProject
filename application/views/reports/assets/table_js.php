if ($('#tblAsset_reports').length && tabClicked === "tab-asset_reports") {
if(typeof dTable['tblAsset_reports'] !=='undefined'){
$("#tab-sms").addClass("active");
dTable['tblAsset_reports'].ajax.reload(null,true);
}else{
dTable['tblAsset_reports'] = $('#tblAsset_reports').DataTable({
"pageLength": 25,
"responsive": true,
"dom": '<"html5buttons"B>lTfgitp',
    buttons: <?php if (in_array('6', $report_privilege)) {?> getBtnConfig('<?php echo $title; ?>- Assets Report'),
    <?php } else {echo "[],";}?>
    "ajax": {
    "url": "<?php echo site_url('reports/assets_full_list'); ?>",
    "dataType": "json",
    "type": "POST",
    "data": function(d){}
    },
    "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var total_page = api.column(1,{page: 'current'}).data();

                    var total_page_2 = api.column(5,{page: 'current'}).data();

                    var total_page_3= api.column(3,{page: 'current'}).data();

                    var total_page_4= api.column(4,{page: 'current'}).data();
                    var total_page_6= api.column(6,{page: 'current'}).data();
                    var total_page_7= api.column(7,{page: 'current'}).data();
                     var total_page_8= api.column(8,{page: 'current'}).data();
 
                     var total_overall = api.column(1).data();

                     var total_overall_2 = api.column(5).data();
                     var total_overall_3 = api.column(3).data();
                     var total_overall_4 = api.column(4).data();
                    var total_overall_6 = api.column(6).data();
                    var total_overall_7 = api.column(7).data();
                       var total_overall_8 = api.column(8).data();
                   
                    var total_page_amount = 0;
                    var total_overall_amount = 0;
                
                    var total_page_amount_2 = 0;
                    var total_overall_amount_2 = 0;
                 
                    var total_page_amount_3 = 0;
                    var total_overall_amount_3 = 0;

                    var total_page_amount_4 = 0;
                    var total_overall_amount_4 = 0;
                    var total_page_amount_6 = 0;
                    var total_overall_amount_6 = 0;

                     var total_page_amount_7 = 0;
                     var total_overall_amount_7 = 0;

                    var total_page_amount_8 = 0;
                    var total_overall_amount_8 = 0;
 
                    $.each(total_page, function (key, val) {
                        total_page_amount += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall, function (key, val) {
                        total_overall_amount += (val) ? (parseFloat(val)) : 0;
                    });
                   
                     $.each(total_page_2, function (key, val) {
                        total_page_amount_2 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_2, function (key, val) {
                        total_overall_amount_2 += (val) ? (parseFloat(val)) : 0;
                    });

                     //
                     $.each(total_page_3, function (key, val) {
                        total_page_amount_3 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_3, function (key, val) {
                        total_overall_amount_3 += (val) ? (parseFloat(val)) : 0;
                    });

                    //
                     $.each(total_page_4, function (key, val) {
                        total_page_amount_4 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_4, function (key, val) {
                        total_overall_amount_4 += (val) ? (parseFloat(val)) : 0;
                    });

                    $.each(total_page_6, function (key, val) {
                    total_page_amount_6 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_6, function (key, val) {
                        total_overall_amount_6 += (val) ? (parseFloat(val)) : 0;
                    });

                     $.each(total_page_7, function (key, val) {
                    total_page_amount_7 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_8, function (key, val) {
                        total_overall_amount_8 += (val) ? (parseFloat(val)) : 0;
                    });
                       $.each(total_page_8, function (key, val) {
                    total_page_amount_8 += (val) ? (parseFloat(val)) : 0;

                    });
                    $.each(total_overall_8, function (key, val) {
                        total_overall_amount_8 += (val) ? (parseFloat(val)) : 0;
                    });

                    
                    $(api.column(1).footer()).html(curr_format(total_page_amount));

                    $(api.column(5).footer()).html(curr_format(total_page_amount_2));
                    $(api.column(3).footer()).html(curr_format(total_page_amount_3));
                    $(api.column(4).footer()).html(curr_format(total_page_amount_4));
                    $(api.column(6).footer()).html(curr_format(total_page_amount_6));
                    $(api.column(7).footer()).html(curr_format(total_page_amount_7));
                     $(api.column(8).footer()).html(curr_format(total_page_amount_8));
                      

                },
    "columns": [
    {data: 'asset_name', render: function (data, type, full, meta) { 
                    var level = (full.asset_name);
                      if(level !=''){
                        var padding=0;  
                        return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("inventory/asset");?>/"+full.id+"' title='Click to view full asset details'>"+full.asset_name+"</a>";
                }
                }
            },
    {"data": "purchase_cost",render:function(data, type, display, meta){
                return curr_format(round(data,0));
    }},
    {"data": 'dpr_apr_rate',render: function(data,type,full,meta){
                var decimal =(full.dpr_apr_rate);
                dm=decimal.toString();

              var check_decimal= dm.substring(dm.indexOf(".")+1,dm.length);
              dm_sliced=check_decimal.charAt(0);

              if(dm_sliced>=1){
              return curr_format(round(data,0))+'.'+dm_sliced+'%';
             }
             else{

              return curr_format(round(data,0))+'%';
             
         }
        }},
    {"data": "dpr_amount",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "apr_amount",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "asset_value",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "cash",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "gain",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "loss",render:function(data){
                return curr_format(round(data,0));
   }},
    {"data": "purchase_date"},
    {data: 'disposal_status', render: function (data, type, display, meta) { 

        //Capitalizing the first letter:

        var fullString =(display.disposal_status).slice(1);

        var dsFirstLetter=(display.disposal_status).toUpperCase().charAt(0);

        var label = (dsFirstLetter+fullString);

        if(label==='Disposed off'){
        return "<a href='<?php echo site_url("inventory/asset");?>/"+display.id+"'><span class='badge badge-danger'style='background-color: #dc3545'>"+label+"</span></a>";
        }
        else{
        return "<a href='<?php echo site_url("inventory/asset");?>/"+display.id+"'><span class='badge badge-success bg-green'>"+label+"</span></a>";
        }
        }
              
                }
    ]
    });
    }
    }

    $('table tbody').on('click', 'tr .edit_me4', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var tbl = row.parent().parent();
    var tbl_id = $(tbl).attr("id");
    var dt = dTable[tbl_id];
    var data = dt.row(row).data();
    if (typeof (data) === 'undefined') {
    data = dt.row($(row).prev()).data();
    if (typeof (data) === 'undefined') {
    data = dt.row($(row).prev().prev()).data();
    }
    }

    });