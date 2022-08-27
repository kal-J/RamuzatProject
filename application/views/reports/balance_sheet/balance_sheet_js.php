
if ($(".tblParent_table").length && (tabClicked === "tab-bs_accounts" || tabClicked === "tab-balance_sheet")) {
  
    if (typeof (dTable['tblAssets']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-bs_accounts").addClass("active");
        $("#tab-balance_sheet").addClass("active");
        dTable['tblAssets'].ajax.reload(null, true);
    } else {
        dTable['tblAssets'] = $('#tblAssets').DataTable(get_table_options("<?php echo site_url('reports/bs_assets_json') ?>"));
    }
    if (typeof (dTable['tblEquityandLiability']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-bs_accounts").addClass("active");
        $("#tab-balance_sheet").addClass("active");
        dTable['tblEquityandLiability'].ajax.reload(null, true);
    } else {
        dTable['tblEquityandLiability'] = $('#tblEquityandLiability').DataTable(get_table_options("<?php echo site_url('reports/bs_lc_json') ?>"));
    }
}
        