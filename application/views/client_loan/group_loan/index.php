<?php $this->load->view('client_loan/group_loan/group_loan_tab_data.php'); ?>
<?php echo $add_loan_modal;?>
<script type="text/javascript">
var dTable = {};
var group_loanModel = {};
$(document).ready( function () {
    var periods = ['days','weeks','months'];
    var loan_product_length='';
     <?php $this->load->view('client_loan/group_loan/group_loan_knockout.php'); ?>
        
var handleDataTableButtons = function (tabClicked) {
            <?php $this->load->view('client_loan/group_loan/types/pure/table_js'); ?>
            <?php $this->load->view('client_loan/group_loan/types/solidarity/table_js'); ?>
        };

    TableManageButtons = function () {
        "use strict";
        return {
            init: function (tblClicked) {
                handleDataTableButtons(tblClicked);
            }
        };
    }();

    TableManageButtons.init("tab-solidarity_loan");
   
} );


function reload_data(formId, reponse_data)
    {
      switch (formId) {
            case "formGroup_loan":
                if (reponse_data.loan_type_id == 1) {
                    TableManageButtons.init("tab-pure_loan");
                }else{
                    TableManageButtons.init("tab-solidarity_loan");
                }
                
                if(typeof reponse_data.new_group_loan_no !== 'undefined'){
                    group_loanModel.loan_ref_no(reponse_data.new_group_loan_no);
                }
                // dTable['tblGroup_loan'].ajax.reload(null, false);
                // dTable['tblPure_group_loan'].ajax.reload(null, false);
                break;       
            default:
                //nothing really to do here
                break;
      
 }
}
</script>
