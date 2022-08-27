if ($("#tblAccount_based").length && tabClicked === "tab-account_based") {
         if (typeof (dTable['tblAccount_based']) !== 'undefined') {
             $(".tab-pane").removeClass("active");
             $("#tab-account_based").addClass("active");
             $("#tab-monthly_savings").addClass("active");
         } else {
             getJsonDataAccountBased(function(data) {
                var columns = [];
                data = data;
                columnNames = data.month_name;
                for (var i in columnNames) {
                  columns.push({data: data[i], 
                                title: columnNames[i]});
             }
             dTable['tblAccount_based'] = $('#tblAccount_based').DataTable({
                "lengthMenu": [[10, 25,50,100], [10, 25,50,100]],
                "order": [],
                "bInfo": true,
                "dom": '<"html5buttons"B>lTfgitp',
                "buttons": <?php if(in_array('6', $report_privilege)){ ?> getBtnConfig('Monthly Savings Account Report '), <?php } else { echo "[],"; } ?>
                "data": data.data,
                "columns": columns,
                "responsive": true
             });
         },start_date,end_date);
        }
       }
