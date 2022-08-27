    // loan history javascript 
    if (tabClicked === "tab-loan_history") {
        $(".loans").removeClass("active");
        $("#tab-loan_history").addClass("active");

        $('#loan_history_member_select').select2();
        $('#loan_limit').select2();
    }
