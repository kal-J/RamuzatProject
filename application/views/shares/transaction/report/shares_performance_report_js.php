 
if(tabClicked=='tab-shares_performance_report'){
  $(".tab-pane").removeClass("active");
  $("#tab-shares_performance_report").addClass("active");
    get_shares_performace_data(start_date,end_date,transaction_status);

}
if(tabClicked=='tab-shares_report'){
  set_active_select_value(start_date,end_date,issuance_id,gender,num_limit,less_more_equal,transaction_status);

}
                      

        