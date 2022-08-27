$('table tbody').on('click', 'tr .deposit', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
                savingsModel.selected_account(data);

                

                savingsModel.group_members( null );
               if(parseInt(data.client_type)==2) {
                var url = "<?php echo site_url('group_member'); ?>" + "/jsonList";
                $.ajax({
                url: url,
                data: {group_id: data.member_id},
                type: 'POST',
                dataType:'json',
                success:function(response){
                    savingsModel.group_members(response.data );         
                }
                });
                }
        });
$('table tbody').on('click', 'tr .withdraw', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
             savingsModel.accountw( data );
             savingsModel.group_members( null ); 
          
            if(parseInt(data.client_type)==2) {
            var url = "<?php echo site_url('group_member'); ?>" + "/jsonList";
            $.ajax({
            url: url,
            data: {group_id: data.member_id},
            type: 'POST',
            dataType:'json',
            success:function(response){
            savingsModel.group_members( response.data );         
            }
            });
            }
        });

        $('table tbody').on('click', 'tr .transfer', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
             savingsModel.account_trans( data );
             savingsModel.group_members( null ); 
          
            if(parseInt(data.client_type)==2) {
            var url = "<?php echo site_url('group_member'); ?>" + "/jsonList";
            $.ajax({
            url: url,
            data: {group_id: data.member_id},
            type: 'POST',
            dataType:'json',
            success:function(response){
            savingsModel.group_members( response.data );         
            }
            });
            }
        });


        $('table tbody').on('click', 'tr .set_action', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            //console.log(data);
            savingsModel.selected_account(data);
            savingsModel.action_msg("activate this account");
            savingsModel.account_state(7);

            /* $.ajax({
            url: "<?php // echo site_url("savings_account/approval_fees"); ?>",
            data: {new_product_id: data.deposit_Product_id},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //populate the observables
                savingsModel.fees_upon_approval(response.fees_upon_approval);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        }); */
        });