 if ($("#tblSavings_ending").length && tabClicked === "tab-savings_ending") {
         if (typeof (dTable['tblSavings_ending']) !== 'undefined') {
             $(".tab-pane").removeClass("active");
             $("#tab-savings_ending").addClass("active");
         } else {
             getJsonData(function(data) {
                var columns = [];
                data = data;
                columnNames = data.month_name;
                for (var i in columnNames) {
                  columns.push({data: data[i], 
                                title: columnNames[i]});
             }
             dTable['tblSavings_ending'] = $('#tblSavings_ending').DataTable({
                "lengthMenu": [[10, 25,50,100], [10, 25,50,100]],
                "order": [],
                "bInfo": true,
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Monthly Savings Report '), <?php } else { echo "[],"; } ?>
                "data": data.data,
                "columns": columns,
                "responsive": true
             });
         },start_date,end_date);
        }
       }
