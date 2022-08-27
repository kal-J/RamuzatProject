if ($("#tblIncome").length && (tabClicked === "tab-pl_vertical" || tabClicked === "tab-profit_and_loss")){
            if (typeof (dTable['tblIncome']) !== 'undefined') {
                $(".tab-pane").removeClass("active");
                $("#tab-pl_vertical").addClass("active");
                $("#tab-profit_and_loss").addClass("active");
                dTable['tblIncome'].ajax.reload(null, true);
            } else {
                dTable['tblIncome'] = $('#tblIncome').DataTable(get_table_options("<?php echo site_url('reports/income_accs_json') ?>"));
            }
        }
        if ($("#tblExpense").length && tabClicked === "tab-profit_and_loss") {
            if (typeof (dTable['tblExpense']) !== 'undefined') {
                $(".tab-pane").removeClass("active");
                $("#tab-pl_vertical").addClass("active");
                $("#tab-profit_and_loss").addClass("active");
                dTable['tblExpense'].ajax.reload(null, true);
            } else {
                dTable['tblExpense'] = $('#tblExpense').DataTable(get_table_options("<?php echo site_url('reports/expense_accs_json') ?>"));
            }
        }