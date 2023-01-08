<?php

/**
 * Description of Client_loan_model
 *
 * @author Eric
 */
class Client_loan_model extends CI_Model
{

    private $columns = [];
    private $more_columns = [
        "a.id",  "a.loan_no", "a.branch_id", "a.member_id", "concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name",
        "a.credit_officer_id", "concat(d.firstname, ' ', d.lastname, ' ', d.othernames) credit_officer_name", "a.approved_installments", "a.approved_repayment_frequency",
        "a.approved_repayment_made_every", "a.group_loan_id", "a.status_id", "a.loan_product_id", "e.min_guarantor", "e.min_collateral", "e.product_name", "loan_product.product_name",
        "a.requested_amount", "a.application_date", "a.disbursement_date", "a.disbursement_note", "a.interest_rate", "a.offset_made_every", "a.offset_period",
        "f.made_every_name AS offset_every", "g.made_every_name AS approved_made_every_name", "a.grace_period", "a.repayment_frequency",
        "repayment_made_every.made_every_name", "a.repayment_made_every", "a.installments", "a.penalty_calculation_method_id", "a.penalty_tolerance_period",
        "a.penalty_rate_charged_per", "a.penalty_rate", "a.link_to_deposit_account", "a.loan_purpose", "loan_state.comment", "loan_state.action_date",
        "a.amount_approved", "a.approval_date", "ac.account_name as fund_source_account", "e.fund_source_account_id", "e.interest_income_account_id", "a.approved_by", "a.suggested_disbursement_date", "a.approval_note", "a.created_by", "a.modified_by", "a.date_created", "a.date_modified", "rsdl.paid_amount", "rsdl.paid_interest", "rsdl.paid_principal",
        "state_id", "state.state_name", "approvals", "ifnull(approvals,0) _approvals", "product_type_id", "disburse_data.expected_interest", "disburse_data.expected_principal", "next_pay_date", "last_pay_date", "a.preferred_payment_id", "payment_mode", "ac_name", "ac_number", "bank_branch", "bank_name", "phone_number", "a.topup_application", "a.linked_loan_id", "ifnull(b.disbursed_amount,0) disbursed_amount", "ifnull(rsdf.paid_principal,0) parent_paid_principal", "ifnull(rsdf.paid_interest,0) parent_paid_interest", "ifnull(c.expected_interest,0) parent_expected_interest", "ifnull(c.expected_principal,0) parent_expected_principal", "ifnull(amount_in_demand,0) amount_in_demand", "ifnull(days_in_demand,0) days_in_demand", "ifnull(principal_in_demand,0) principal_in_demand"
    ]; //,"(principal_in_demand*(days_in_demand-a.penalty_tolerance_period)*(a.penalty_rate/100)) penalty_demanded"


    private $alias_only_pattern = '/(\s+(as[\s]+)?)((`)?[a-zA-Z0-9_]+(`)?)$/';

    public function __construct()
    {
        $this->load->database();
        $this->load->library("loans_col_setup");
        $this->table = 'client_loan';
        $this->max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";

        $this->active_state = "(SELECT client_loan_id,comment,action_date AS loan_active_date FROM fms_loan_state
                WHERE id in ( SELECT MIN(id) from fms_loan_state WHERE state_id=7 GROUP BY client_loan_id ) )";

        $this->paid_amount = "(SELECT client_loan_id,SUM(paid_interest+paid_principal) AS paid_amount,SUM(paid_principal) AS paid_principal,SUM(paid_interest) AS paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 GROUP BY client_loan_id)";
        $this->pay_day = "(SELECT MIN(`repayment_date`) next_pay_date,MAX(`repayment_date`) last_pay_date,`client_loan_id` FROM fms_repayment_schedule WHERE `status_id`=1 AND `payment_status` IN (4,2) GROUP BY client_loan_id)";
        $this->approvals = "(SELECT client_loan_id,COUNT(client_loan_id) AS approvals FROM fms_loan_approval WHERE status_id=1 GROUP BY client_loan_id)";
        #######
        $this->paid_installment_amount = "(SELECT client_loan_id,repayment_schedule_id,SUM(paid_interest+paid_principal) AS paid_amount,SUM(paid_principal) AS paid_principal,SUM(paid_interest) AS paid_interest FROM fms_loan_installment_payment WHERE fms_loan_installment_payment.status_id=1 GROUP BY repayment_schedule_id)";
        $this->disburse_data = "(SELECT client_loan_id, SUM(interest_amount) expected_interest, SUM(principal_amount) expected_principal FROM `fms_repayment_schedule` WHERE status_id <>2 GROUP BY client_loan_id)";

        $this->linked_loan_to_topup = "(SELECT id, amount_approved, disbursed_amount FROM fms_client_loan WHERE status_id=1)";

        $this->columns = $this->loans_col_setup->get_fields();

        $this->no_of_installments = "(SELECT COUNT(id) as installments_no, client_loan_id FROM `fms_repayment_schedule` WHERE status_id=1 GROUP BY client_loan_id)";

        $this->paid_installments = "(SELECT COUNT(id) as paid_installments_no, client_loan_id FROM `fms_repayment_schedule` WHERE status_id=1 AND payment_status IN(1,3) GROUP BY client_loan_id)";

        // $this->columns =array_merge($this->first_columns, $this->more_columns);
        //for the analysis part and mainly for active loans
        $where = "";
        if ($this->input->post('date_to') != '') {
            $where = " `fms_repayment_schedule`.`repayment_date`<='" . $this->input->post('date_to') . "'";
        }
        if ($this->input->post('date_from') != '') {
            if (!empty($where)) {
                $where .= " AND `fms_repayment_schedule`.`repayment_date`>='" . $this->input->post('date_from') . "'";
            } else {
                $where = " `fms_repayment_schedule`.`repayment_date`>='" . $this->input->post('date_from') . "'";
            }
        }

        if (empty($where)) {
            $where = " `fms_repayment_schedule`.`repayment_date`<= NOW()";
        }

        $this->unpaid_installments_data = "(
                SELECT client_loan_id,SUM(interest_amount+principal_amount) AS total_payment, COUNT(installment_status) AS unpaid_installments 
                FROM(
                    SELECT client_loan_id,repayment_date,interest_amount,principal_amount, 
                        CASE 
                            WHEN $where AND `status_id`=1 THEN '1'
                            ELSE '0'
                        END AS installment_status FROM `fms_repayment_schedule` WHERE fms_repayment_schedule.payment_status <> 1 AND fms_repayment_schedule.payment_status <> 3
                    ) AS repayment_schedule WHERE repayment_schedule.installment_status=1 GROUP BY repayment_schedule.client_loan_id

            )";

        //
        $un_cleared_installments = "(SELECT SUM(lp.paid_principal) total_paid_principal, SUM(lp.paid_interest) total_paid_interest, rs.id AS schedule_id FROM fms_loan_installment_payment lp LEFT JOIN fms_repayment_schedule rs ON rs.id=lp.repayment_schedule_id WHERE lp.status_id=1 AND rs.payment_status<>1 AND rs.payment_status<>3 AND rs.repayment_date <= CURDATE() GROUP BY rs.id)";

        $this->amount_in_demand = "(SELECT client_loan_id, (SUM(interest_amount+principal_amount) - SUM(ifnull(uci.total_paid_principal,0) + ifnull(uci.total_paid_interest, 0)) ) amount_in_demand, (SUM(principal_amount) - ifnull(uci.total_paid_principal, 0)) principal_in_demand, (SUM(interest_amount) - ifnull(uci.total_paid_interest, 0) ) interest_in_demand  FROM `fms_repayment_schedule` 
        LEFT JOIN $un_cleared_installments uci ON uci.schedule_id=fms_repayment_schedule.id
        WHERE payment_status IN(2,4) AND status_id=1 AND repayment_date <= CURDATE() GROUP BY client_loan_id)";

        $this->days_in_demand = "(SELECT *, DATEDIFF(CURDATE(),repayment_date) days_in_demand FROM fms_repayment_schedule
                WHERE id in ( SELECT MIN(id) from fms_repayment_schedule WHERE repayment_date <= CURDATE() AND payment_status <> 1 AND payment_status <> 3 AND status_id=1 GROUP BY client_loan_id ))";

        $this->secure_unsecured_loans = "(
                    SELECT client_loan_id, SUM(`locked_amount`) security_amount from (SELECT `client_loan_id`,SUM(`amount_locked`) locked_amount FROM `fms_loan_guarantor` GROUP BY `client_loan_id`
                        union all
                    SELECT `client_loan_id`, SUM(`item_value`) locked_amount FROM `fms_loan_collateral` GROUP BY `client_loan_id`) secured_table GROUP BY client_loan_id)";
        //End of the construction
    }

    public function state_totals($filter = false, $state_based = true)
    {
        $this->db->select("COUNT(a.id) AS number, state_id");
        $this->db->from("client_loan a");
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=a.id");
        if ($filter != false) {
            $this->db->where($filter);
        } else {
            // $this->db->where("a.group_loan_id IS NULL");
        }
        if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Credit Officer' || $_SESSION['role_id'] == 4)) {
            $this->db->where('a.credit_officer_id', $_SESSION['staff_id']);
        }
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') !== '') {
            $this->db->where("a.application_date <='" . $this->input->post('date_to') . "'");
        }
        if ($this->input->post('date_from') != NULL && $this->input->post('date_from') !== '') {
            $this->db->where("a.application_date >='" . $this->input->post('date_from') . "'");
        }
        if ($state_based == true) {
            $this->db->group_by("state_id");
        }
        $query = $this->db->get();
        // print_r($this->db->last_query());die;
        return $query->result_array();
    }

    public function get_id()
    {
        $this->db->select(" (case when count(id) = 0 then 1 else max(id) + 1 end) id");
        $q = $this->db->get($this->table);
        return $q->row_array();
    }

    public function set($loan_ref_no = false, $group_loan_id = false)
    {
        $data = $this->input->post(NULL, TRUE);
        unset($data['existing_collaterals']);
        //Application date
        $action_date = date('Y-m-d');
        if ($this->input->post('application_date') != NULL && $this->input->post('application_date') != '') {
            $action_date = $this->helpers->yr_transformer($this->input->post('application_date'));
        } elseif ($this->input->post('action_date') != NULL && $this->input->post('action_date') != '') {
            $action_date = $this->helpers->yr_transformer($this->input->post('action_date'));
        }
        if ($group_loan_id != false) {
            $data['group_loan_id'] = $group_loan_id;
        } elseif ($data['group_loan_id'] == null || empty($data['group_loan_id'])) {
            unset($data['group_loan_id']);
        }
        $data['application_date'] = $action_date;
        unset($data['id'], $data['state_id'], $data['action_date'], $data['loan_type_id'], $data['group_id'], $data['loan_docs'], $data['collaterals'], $data['shareAccs'], $data['share_guarantors'], $data['member_guarantors'], $data['add_guarantor'], $data['guarantors'], $data['loanFees'], $data['savingAccs'], $data['incomes'], $data['expenses'], $data['loan_app_stage'], $data['source_fund_account_id'], $data['repayment_schedule'], $data['principal_value'], $data['interest_value'], $data['amount_approved'], $data['fund_source_account'], $data['ac_name'], $data['ac_number'], $data['bank_branch'], $data['bank_name'], $data['phone_number'], $data['complete_application'], $data['use_savings_as_security'], $data['use_share_as_security'], $data['comment'], $data['unpaid_interest'], $data['unpaid_principal'], $data['ko_unique_1']);
        $data['status_id'] = '1';
        $data['date_created'] = time();
        $data['loan_no'] = $loan_ref_no;
        $data['branch_id'] = $_SESSION['branch_id'];
        $data['created_by'] = $_SESSION['id'];

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function set2($sent_data)
    {
        unset($sent_data['existing_collaterals']);
        $query = $this->db->insert($this->table, $sent_data);
        return $this->db->insert_id();
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = $this->input->post(NULL, TRUE);
        $sent_date = explode('-', $data['action_date'], 3);
        $data['application_date'] = count($sent_date) === 3 ? ($sent_date[2] . "-" . $sent_date[1] . "-" . $sent_date[0]) : null;
        if ($data['group_loan_id'] == '') {
            unset($data['group_loan_id']);
        }
        unset($data['id'], $data['state_id'], $data['amount_approved'], $data['fund_source_account'], $data['action_date'], $data['loan_type_id'], $data['group_id'], $data['complete_application'], $data['comment'], $data['ac_name'], $data['ac_number'], $data['bank_branch'], $data['bank_name'], $data['phone_number'], $data['complete_application'], $data['comment']);
        $data['modified_by'] = $_SESSION['id'];
        $data['status_id'] = '1';
        $this->db->where('id', $this->input->post('id'));
        return $this->db->update($this->table, $data);
    }

    /**
     * This method deletes client_loan data from the database
     */
    public function delete_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->db->where('id', $id);
            $query = $this->db->delete($this->table);
            if ($query) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_payment_data($ref_no = false, $installment_number = false, $call_type = FALSE)
    {
        ####
        $this->db->select('client_loan.id,client_loan.loan_no,repayment_date,interest_amount,principal_amount, installment_number,repayment_schedule.id AS repayment_schedule_id,(principal_amount-ifnull(paid_principal,0)) AS remaining_principal,(interest_amount-( ifnull(paid_interest,0) )) AS remaining_interest');
        $this->db->from('client_loan ');
        $this->db->join('repayment_schedule', 'repayment_schedule.client_loan_id= client_loan.id ', 'left');
        $this->db->join("$this->paid_installment_amount rsdl", 'rsdl.repayment_schedule_id=repayment_schedule.id', 'left');

        if ($ref_no != '') {
            $this->db->where('client_loan.loan_no=', $ref_no);
        }
        if ($installment_number != '' && is_numeric($installment_number)) {
            $this->db->where('repayment_schedule.installment_number=', $installment_number);
        }
        if ($installment_number != '' && is_array($installment_number)) {
            $this->db->where_in('repayment_schedule.installment_number', $installment_number);
        }
        $this->db->where('repayment_schedule.payment_status <>1 AND repayment_schedule.status_id=1')->limit(1, 0);

        if ($call_type === FALSE) {
            $this->db->select("client_loan.member_id,concat(fms_user.firstname, ' ', fms_user.lastname, ' ', fms_user.othernames) client_name,member.client_no");
            $this->db->join('member', 'member.id =client_loan.member_id ');
            $this->db->join('user', 'user.id= member.user_id');
        } else {
            $this->db->select("group.group_name AS client_name");
            $this->db->join('group_loan', 'group_loan.id =client_loan.group_loan_id');
            $this->db->join('group', 'group.id =group_loan.group_id');
        }
        $query = $this->db->get();
        return $result = $query->row_array();
    }

    public function get_payment_data_2($ref_no = false, $installment_number = false, $call_type = FALSE)
    { //For multiple installments
        $this->db->select('client_loan.id,client_loan.loan_no,installment_number,repayment_date,sum(interest_amount) interest_amount,sum(principal_amount) principal_amount,(sum(principal_amount)-ifnull(paid_principal,0)) AS remaining_principal,(sum(interest_amount)-( ifnull(paid_interest,0) + ifnull(forgiven_interest,0) )) AS remaining_interest');
        $this->db->from('client_loan ');
        $this->db->join('repayment_schedule', 'repayment_schedule.client_loan_id= client_loan.id ', 'left');
        $this->db->join("$this->paid_installment_amount rsdl", 'rsdl.repayment_schedule_id=repayment_schedule.id', 'left');

        if ($ref_no != '') {
            $this->db->where('client_loan.loan_no=', $ref_no);
        }
        if ($installment_number != '' && is_array($installment_number)) {
            $this->db->where_in('repayment_schedule.installment_number', $installment_number);
        }
        if ($installment_number != '' && is_numeric($installment_number)) {
            $this->db->where('repayment_schedule.installment_number=', $installment_number);
        }
        $this->db->where('repayment_schedule.payment_status <>1 AND repayment_schedule.status_id=1')->limit(1, 0);

        if ($call_type === FALSE) {
            $this->db->select("client_loan.member_id,concat(fms_user.firstname, ' ', fms_user.lastname, ' ', fms_user.othernames) client_name,member.client_no");
            $this->db->join('member', 'member.id =client_loan.member_id ');
            $this->db->join('user', 'user.id= member.user_id');
        } else {
            $this->db->select("group.group_name AS client_name");
            $this->db->join('group_loan', 'group_loan.id =client_loan.group_loan_id');
            $this->db->join('group', 'group.id =group_loan.group_id');
        }
        $query = $this->db->get();
        return $result = $query->row_array();
    }

    private function get_select()
    {
        $this->db->from('client_loan a');
        $this->db->select('fg.group_name,mm.payment_status as mm_payment_status,checkout_request_id,status_description AS mm_status_description,mm.message AS mm_massage, installments_no, paid_installments_no, a.interest_amount_bf,e.product_name,interest_in_demand, active_state.loan_active_date');
        $this->db->join("$this->no_of_installments ins_no", 'ins_no.client_loan_id=a.id', 'left');
        $this->db->join("$this->paid_installments paid_ins_no", 'paid_ins_no.client_loan_id=a.id', 'left');
        $this->db->join('group_loan gl', 'gl.id=a.group_loan_id', 'left');
        $this->db->join('group fg', 'fg.id=gl.group_id', 'left');
        $this->db->join('staff s', 'a.credit_officer_id = s.id', 'left');
        $this->db->join('user d', 's.user_id = d.id', 'left');
        $this->db->join('loan_product e', 'e.id=a.loan_product_id')
            ->join('repayment_made_every', 'repayment_made_every.id=a.repayment_made_every', "left")
            ->join('repayment_made_every g', 'g.id=a.approved_repayment_made_every', "left");
        $this->db->join('repayment_made_every f', 'f.id=a.offset_made_every', "left");
        $this->db->join('accounts_chart ac', 'ac.id=e.fund_source_account_id', "left");
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', "left");
        $this->db->join("$this->active_state active_state", 'active_state.client_loan_id=a.id', "left");
        $this->db->join("state", 'state.id=loan_state.state_id', 'left');
        $this->db->join("$this->paid_amount rsdl", 'rsdl.client_loan_id=a.id', 'left');
        $this->db->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left');
        $this->db->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left');
        $this->db->join("$this->disburse_data c", 'c.client_loan_id=b.id', 'left');
        $this->db->join("$this->approvals fla", 'fla.client_loan_id=a.id', 'left');
        $this->db->join("$this->pay_day pay_day", 'pay_day.client_loan_id=a.id', 'left');
        $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');
        $this->db->join("$this->amount_in_demand amount_in_demand", 'amount_in_demand.client_loan_id=a.id', 'left');
        $this->db->join("$this->days_in_demand days_in_demand", 'days_in_demand.client_loan_id=a.id', 'left');
        $this->db->join('payment_details', 'a.id =payment_details.client_loan_id AND payment_details.status_id =1', 'left');
        $this->db->join('payment_mode', 'a.preferred_payment_id =payment_mode.id ', 'left');

        if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Credit Officer' || $_SESSION['role_id'] == 4)) {
            $this->db->where('a.credit_officer_id', $_SESSION['staff_id']);
        }

        if ($this->input->post('client_id') != "" && !empty($this->input->post('client_id'))) {
            $this->db->where('a.member_id', $this->input->post('client_id'));
        }
        //
        $this->db->select("group.group_name");
        if ($this->input->post('group_id') != "" && !empty($this->input->post('group_id'))) {
            $this->db->where('a.group_loan_id', $this->input->post('group_id'));
        }
        $this->db->join('member m', 'a.member_id = m.id', 'left');
        //$this->db->join('user_address ua',' m.user_id=ua.user_id', 'left');
        $this->db->join('user c', 'm.user_id = c.id', 'left');
        $this->db->join('group_loan', 'group_loan.id =a.group_loan_id', 'left');
        $this->db->join('group_loan_type', 'group_loan_type.id =group_loan.loan_type_id', 'left');
        $this->db->join('group', 'group.id =group_loan.group_id', 'left');
        // for mobile_payments only
        $this->db->join('mobile_money_transactions mm', 'a.id =mm.client_loan_id', 'left');
        // } else {
        ####
        //$this->db->where("a.group_loan_id IS NULL");
        //$this->db->join('member m', 'a.member_id = m.id');
        //$this->db->join('user_address ua',' m.user_id=ua.user_id', 'left');
        //$this->db->join('user c', 'm.user_id = c.id');
        //}
    }

    private function set_filters($all_columns)
    {
        if ($this->input->post("search") !== NULL) {
            $search = $this->input->post("search");
            if (isset($search['value']) && $search['value'] != "") {
                $this->db->group_start();
                for ($i = 0; $i < count($this->columns); $i++) {

                    if (isset($all_columns[$i]['searchable']) && $all_columns[$i]['searchable'] == "true") {
                        $column = preg_replace($this->alias_only_pattern, '', $this->columns[$i]);
                        $this->db->or_like($column, $search['value']);
                    }
                    // work around
                    $this->db->or_like('e.product_name', $search['value']);
                }

                // Individual column filtering
                foreach ($this->columns as $key) {
                    if (isset($all_columns[$key]['searchable']) && $all_columns[$key]['searchable'] == "true" && $all_columns[$key]['search']['value'] != '') {
                        $this->db->or_like(preg_replace($this->alias_only_pattern, '', $this->columns[$key]), $all_columns[$key]["search"]["value"]);
                    }
                }
                $this->db->group_end();
            }
        }
        /* if (isset($_SESSION) && isset($_SESSION['branch_id']) && is_numeric($_SESSION['branch_id'])) {
          $this->db->where("branch_id =", (int) $_SESSION['branch_id']);
          } */
    }

    //counting the number of returned rows
    public function get_found_rows()
    {
        $this->db->select("FOUND_ROWS()", FALSE);
        $q = $this->db->get();
        return $q->row_array();
    }

    //getting all member loans
    public function get_all_client_loans($filter = false)
    {

        $this->db->select("a.id, d.crb_card_no,loan_no,a.branch_id,loan_product_type.type_name,  member_id,b.branch_name,o.`name`,d.date_of_birth,ms.marital_status_name,
                    d.dependants_no,d.children_no,
                    a.credit_officer_id,concat(d.firstname, ' ', d.lastname) credit_officer_name, f.method_description,
                    group_loan_id, a.status_id ,approved_installments ,approved_repayment_frequency,approved_repayment_made_every,  a.loan_product_id,  e.min_guarantor,e.min_collateral, e.product_name, a.requested_amount, application_date,
                    disbursement_date, disbursement_note, a.interest_rate, a.offset_made_every,offset_period,g.made_every_name AS offset_every, grace_period, a.repayment_frequency,repayment_made_every.made_every_name, a.repayment_made_every,
                    installments, a.penalty_calculation_method_id, a.penalty_tolerance_period, penalty_rate_charged_per, penalty_rate, link_to_deposit_account,
                    a.loan_purpose,physical_address,postal_address,office_phone,email_address, loan_state.comment,loan_state.action_date,a.amount_approved, approval_date, approved_by,suggested_disbursement_date, approval_note, a.created_by, a.modified_by, a.date_created, a.date_modified,paid_amount,state_id,state.state_name,i.made_every_name AS penalty_charged_every,j.made_every_name AS approved_made_every_name,approvals,ifnull(approvals,0) _approvals,e.fund_source_account_id,ac.account_name as fund_source_account,preferred_payment_id,ac_name,ac_number,bank_branch,bank_name,payment_details.phone_number,payment_mode,linked_loan_id,topup_application");
        $this->db->from('client_loan a');
        $this->db->join('staff s', 's.id = a.credit_officer_id', 'left');
        $this->db->join('user d', 'd.id=s.user_id', 'left');
        $this->db->join('loan_product e', 'e.id=a.loan_product_id')
            ->join('repayment_made_every', 'repayment_made_every.id=a.repayment_made_every', 'left')
            ->join('repayment_made_every g', 'g.id=a.offset_made_every', 'left')
            ->join('repayment_made_every i', 'i.id=a.penalty_rate_charged_per', 'left')
            ->join('loan_product_type', 'loan_product_type.id=e.product_type_id')
            ->join('repayment_made_every j', 'j.id=a.approved_repayment_made_every', "left");
        $this->db->join('accounts_chart ac', 'ac.id=e.fund_source_account_id', "left");
        $this->db->join('penalty_calculation_method f', 'f.id=a.penalty_calculation_method_id', 'left');
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', 'left');
        $this->db->join("state", 'state.id=loan_state.state_id', 'left');
        $this->db->join("$this->paid_amount rsdl", 'rsdl.client_loan_id=a.id', 'left');
        $this->db->join("$this->approvals fla", 'fla.client_loan_id=a.id', 'left');
        $this->db->join('loan_approval la', 'la.client_loan_id =a.id', 'left');
        $this->db->join('marital_status ms', 'ms.id =d.marital_status_id', 'left');
        $this->db->join('payment_details', 'a.id =payment_details.client_loan_id AND payment_details.status_id =1', 'left');
        $this->db->join('payment_mode', 'a.preferred_payment_id =payment_mode.id ', 'left');

        //$this->db->where('member_id=67');

        if ($filter == false) {
            $this->db->select("concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,m.user_id,client_no,address1,address2,mobile_number");
            $this->db->join('member m', 'm.id =a.member_id ');
            $this->db->join('contact uc', ' m.user_id=uc.user_id', 'left');
            $this->db->join('user c', 'c.id= m.user_id');
            $this->db->join('user_address ua', ' ua.user_id=c.id', 'left');
            $this->db->join('branch b', 'b.id=m.branch_id');
            $this->db->join('organisation o', 'o.id=b.organisation_id');
            //$this->db->where('a.id=', $this->input->post('id'));
        } else {
            if (is_numeric($filter)) {
                $this->db->select("concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,m.user_id,client_no,address1,address2,mobile_number");
                $this->db->join('member m', 'm.id =a.member_id ');
                $this->db->join('contact uc', ' m.user_id=uc.user_id', 'left');
                $this->db->join('user c', 'c.id= m.user_id');
                $this->db->join('user_address ua', ' ua.user_id=c.id', 'left');
                $this->db->join('branch b', 'b.id=m.branch_id');
                $this->db->join('organisation o', 'o.id=b.organisation_id');
                $this->db->where('a.id=', $filter);
            } else {
                $this->db->select("group.group_name,group.id AS group_id,loan_type_id,group_loan_type.type_name");
                $this->db->join('group_loan', 'group_loan.id =a.group_loan_id');
                $this->db->join('group', 'group.id =group_loan.group_id');
                $this->db->join('group_loan_type', 'group_loan_type.id =group_loan.loan_type_id');
                $this->db->join('branch b', 'b.id=group.branch_id');
                $this->db->join('organisation o', 'o.id=b.organisation_id');
                $this->db->where($filter);
            }
        }

        $this->db->where('member_id=' . $this->input->post('member_id'));
        $this->db->order_by('id', 'desc');

        $this->db->limit($this->input->post('limit'));
        $query = $this->db->get();
        return $query->result_array();
    }

    //getting a single client loan
    public function get_client_loan($filter = false)
    {

        $this->db->select("a.id, d.crb_card_no,loan_no, loan_product_type.type_name,  member_id,b.branch_name,a.branch_id,o.`name`,d.date_of_birth,ms.marital_status_name,
                    d.dependants_no,d.children_no,
                    a.credit_officer_id,concat(d.firstname, ' ', d.lastname) credit_officer_name, f.method_description,
                    group_loan_id, a.status_id ,approved_installments ,approved_repayment_frequency,approved_repayment_made_every,  a.loan_product_id,  e.min_guarantor,e.min_collateral, e.product_name, a.requested_amount, application_date,
                    disbursement_date, disbursement_note, a.interest_rate, a.offset_made_every,offset_period,g.made_every_name AS offset_every, grace_period, a.repayment_frequency,repayment_made_every.made_every_name, a.repayment_made_every,
                    installments, ifnull(a.penalty_calculation_method_id, e.penalty_calculation_method_id) penalty_calculation_method_id, ifnull(a.penalty_tolerance_period, e.penalty_tolerance_period) penalty_tolerance_period, ifnull(a.penalty_rate_charged_per, e.penalty_rate_chargedPer) penalty_rate_charged_per, penalty_rate, link_to_deposit_account,
                    a.loan_purpose,physical_address,postal_address,office_phone,email_address, loan_state.comment,loan_state.action_date,a.amount_approved, approval_date, approved_by,suggested_disbursement_date, approval_note, a.created_by, a.modified_by, a.date_created, a.date_modified,paid_amount,state_id,state.state_name,i.made_every_name AS penalty_charged_every,j.made_every_name AS approved_made_every_name,approvals,ifnull(approvals,0) _approvals,e.fund_source_account_id,ac.account_name as fund_source_account,preferred_payment_id,ac_name,ac_number,bank_branch,bank_name,payment_details.phone_number,payment_mode,linked_loan_id,topup_application,ifnull(paid_principal,0) AS paid_principal,ifnull(paid_interest,0) AS paid_interest,ifnull(paid_amount,0) AS paid_amount,ifnull(linked_loan.disbursed_amount, ifnull(a.disbursed_amount,0) ) disbursed_amount,ifnull(expected_interest,0) AS expected_interest,ifnull(expected_principal,0) AS expected_principal, e.penalty_applicable_after_due_date, IF(a.penalty_calculation_method_id=2, a.penalty_rate , e.fixed_penalty_amount) fixed_penalty_amount, pay_day.last_pay_date, pay_day.next_pay_date,c.photograph");
        $this->db->from('client_loan a');
        $this->db->join('staff s', 's.id = a.credit_officer_id', 'left');
        $this->db->join('user d', 'd.id=s.user_id', 'left');
        $this->db->join('loan_product e', 'e.id=a.loan_product_id')
            ->join("$this->linked_loan_to_topup linked_loan", 'linked_loan.id=a.linked_loan_id AND a.topup_application =1', 'left')
            ->join('repayment_made_every', 'repayment_made_every.id=a.repayment_made_every', 'left')
            ->join('repayment_made_every g', 'g.id=a.offset_made_every', 'left')
            ->join('repayment_made_every i', 'i.id=a.penalty_rate_charged_per', 'left')
            ->join('loan_product_type', 'loan_product_type.id=e.product_type_id')
            ->join('repayment_made_every j', 'j.id=a.approved_repayment_made_every', "left");
        $this->db->join('accounts_chart ac', 'ac.id=e.fund_source_account_id', "left");
        $this->db->join('penalty_calculation_method f', 'f.id=ifnull(a.penalty_calculation_method_id, e.penalty_calculation_method_id)', 'left');
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', 'left');
        $this->db->join("state", 'state.id=loan_state.state_id', 'left');
        $this->db->join("$this->paid_amount rsdl", 'rsdl.client_loan_id=a.id', 'left');
        $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');
        $this->db->join("$this->approvals fla", 'fla.client_loan_id=a.id', 'left');
        $this->db->join('loan_approval la', 'la.client_loan_id =a.id', 'left');
        $this->db->join('marital_status ms', 'ms.id =d.marital_status_id', 'left');
        $this->db->join('payment_details', 'a.id =payment_details.client_loan_id AND payment_details.status_id =1', 'left');
        $this->db->join('payment_mode', 'a.preferred_payment_id =payment_mode.id ', 'left');
        $this->db->join("$this->pay_day pay_day", 'pay_day.client_loan_id=a.id', 'left');

        if ($filter == false) {
            $this->db->select("concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,m.user_id,client_no,address1,address2,mobile_number");
            $this->db->join('member m', 'm.id =a.member_id ');
            $this->db->join('contact uc', ' m.user_id=uc.user_id', 'left');
            $this->db->join('user c', 'c.id= m.user_id');
            $this->db->join('user_address ua', ' ua.user_id=c.id', 'left');
            $this->db->join('branch b', 'b.id=m.branch_id');
            $this->db->join('organisation o', 'o.id=b.organisation_id');
            $this->db->where('a.id=', $this->input->post('id'));
        } else {
            if (is_numeric($filter)) {
                $this->db->select("concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,m.user_id,client_no,address1,address2,mobile_number,group.group_name,");
                $this->db->join('member m', 'm.id =a.member_id ', 'left');
                $this->db->join('contact uc', ' m.user_id=uc.user_id', 'left');
                $this->db->join('user c', 'c.id= m.user_id', 'left');
                $this->db->join('group_loan', 'group_loan.id =a.group_loan_id', 'left');
                $this->db->join('group', 'group.id =group_loan.group_id', 'left');
                $this->db->join('user_address ua', 'ua.user_id=c.id', 'left');
                $this->db->join('branch b', 'b.id=m.branch_id', 'left');
                $this->db->join('organisation o', 'o.id=b.organisation_id', 'left');
                $this->db->where('a.id=', $filter);
            } else {
                $this->db->select("group.group_name,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,group.id AS group_id,loan_type_id,group_loan_type.type_name,group_loan_no");
                $this->db->join('group_loan', 'group_loan.id =a.group_loan_id');
                $this->db->join('group', 'group.id =group_loan.group_id');
                $this->db->join('group_loan_type', 'group_loan_type.id =group_loan.loan_type_id');
                $this->db->join('member m', 'm.id =a.member_id ', 'left');
                $this->db->join('user c', 'c.id= m.user_id', 'left');
                $this->db->join('branch b', 'b.id=group.branch_id');
                $this->db->join('organisation o', 'o.id=b.organisation_id');
                $this->db->where($filter);
            }
        }

        $query = $this->db->get();
        return $result = $query->row_array();
    }

    /*
      SELECT a.id, loan_no,b.product_name, member_id,  group_loan_id, a.status_id, loan_product_id, requested_amount, comment, amount_approved,approved_repayment_frequency,
      approved_installments,approved_repayment_made_every
      FROM sacco_fms.fms_client_loan a
      join fms_loan_product b on a.loan_product_id = b.id

      where member_id = 1 order by id desc limit  1 OFFSET 1;
     */

    //getting a previous loan
    public function get_prev_client_loan($filter = false)
    {
        $this->db->select("a.id, loan_no,b.product_name, member_id,  group_loan_id, a.status_id, loan_product_id, requested_amount, a.loan_purpose, amount_approved,approved_repayment_frequency,
    approved_installments,approved_repayment_made_every");
        $offset = 1;
        $limit = 1;
        $this->db->from('client_loan a', $offset, $limit);
        $this->db->join('loan_product b', 'a.loan_product_id = b.id');
        if ($filter == false) {
            $this->db->where('member_id=', $this->input->post('id'));
        } else {
            if (is_numeric($filter)) {
                $this->db->where('member_id', $filter);
                $this->db->order_by("id");
            } else {
                $this->db->select("group.group_name,group.id AS group_id,loan_type_id,group_loan_type.type_name");
                $this->db->join('group_loan', 'group_loan.id =a.group_loan_id');
                $this->db->where("member_id", $filter);
            }
        }

        $query = $this->db->get();
        return $result = $query->row_array();
    }

    private function conditional_selects()
    {
        if ($this->input->post('date_to') == NULL && $this->input->post('date_to') == '' && $this->input->post('state_id') == 7) { //for active tab we need a LEFT JOIN
            $this->db->select("total_payment,unpaid_installments");
        }
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') !== '' && $this->input->post('state_id') == 7) { //for defaulters and risky tabs we need a FULL JOIN
            $this->db->select("total_payment,unpaid_installments");
        }

        if ($this->input->post('state_id') == 13 || $this->input->post('report') == 'true') {
            $this->db->select("repayment_schedule1.repayment_date,days_in_arrears");
        }
        if ($this->input->post('report') == 'true') {
            $this->db->select("security_amount");
        }
    }

    private function conditional_filters()
    {
        if ($this->input->post('date_to') == NULL && $this->input->post('date_to') == '' && $this->input->post('state_id') == 7) { //for active tab we need a LEFT JOIN
            $this->db->join("$this->unpaid_installments_data repayment_schedule", 'repayment_schedule.client_loan_id=a.id', 'left');
            //$this->db->where("loan_state.action_date <='". $this->input->post('date_to')."'");
            //$this->db->where("loan_state.action_date >='". $this->input->post('date_from')."'");
        }
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') !== '' && $this->input->post('state_id') == 7) { //for defaulters and risky tabs we need a FULL JOIN
            $this->db->join("$this->unpaid_installments_data repayment_schedule", 'repayment_schedule.client_loan_id=a.id');
        }

        if ($this->input->post('state_id') == 13 || $this->input->post('report') == 'true') {
            $loans_with_last_repayment_date = "(SELECT *,DATEDIFF(CURDATE(),repayment_date) days_in_arrears FROM fms_repayment_schedule
                WHERE id in ( SELECT MIN(id) from fms_repayment_schedule WHERE status_id=1 AND payment_status=2 OR payment_status=4 AND repayment_date < CURDATE()  GROUP BY client_loan_id ))";
            $this->db->join("$loans_with_last_repayment_date repayment_schedule1", 'repayment_schedule1.client_loan_id=a.id', 'left');
        }

        if ($this->input->post('date_to') != NULL && $this->input->post('date_from') !== '' && $this->input->post('state_id') == 1) {
            $this->db->where("a.application_date <='" . $this->input->post('date_to') . "'");
            $this->db->where("a.application_date >='" . $this->input->post('date_from') . "'");
        }
        //End here
        if ($this->input->post('date_to') != NULL && $this->input->post('date_from') !== '' && $this->input->post('state_id') > 1 && $this->input->post('state_id') != 7) {
            $this->db->where("loan_state.action_date <='" . $this->input->post('date_to') . "'");
            $this->db->where("loan_state.action_date >='" . $this->input->post('date_from') . "'");
        }
        //disbursed loan report filters 
        if ($this->input->post('start_date_at') != NULL && $this->input->post('end_date_at') !== '' && $this->input->post('state_id') > 1 &&      $this->input->post('state_id') != 7) {
            $this->db->where("a.disbursement_date <='" . $this->input->post('start_date_at') . "'");
            $this->db->where("a.disbursement_date >='" . $this->input->post('end_date_at') . "'");
        }
        if ($this->input->post('start_date_at') != NULL && $this->input->post('end_date_at') !== '' && $this->input->post('state_id') > 1 &&      $this->input->post('state_id') != 7) {
            $this->db->where("loan_state.action_date <='" . $this->input->post('start_date_at') . "'");
            $this->db->where("loan_state.action_date >='" . $this->input->post('end_date_at') . "'");
        }
        if ($this->input->post('start_date_at') != NULL && $this->input->post('end_date_at') !== '' && $this->input->post('state_id') == 1) {
            $this->db->where("a.application_date <='" . $this->input->post('start_date_at') . "'");
            $this->db->where("a.application_date >='" . $this->input->post('end_date_at') . "'");
        }



        if ($this->input->post('date_to_filter') != NULL) {
            $this->db->where("loan_active_date <='" . $this->input->post('date_to_filter') . "'");
        }

        if ($this->input->post('date_from_filter') != NULL) {
            $this->db->where("loan_active_date >='" . $this->input->post('date_from_filter') . "'");
        }

        if ($this->input->post('repayment_expected_end_date') != NULL) {
            $this->db->where("pay_day.next_pay_date <='" . $this->input->post('repayment_expected_end_date') . "'");
        }

        if ($this->input->post('repayment_expected_start_date') != NULL) {
            $this->db->where("pay_day.next_pay_date >='" . $this->input->post('repayment_expected_start_date') . "'");
        }

        // end disbursed loan filters .

        if ($this->input->post('state_id') !== NULL && is_numeric($this->input->post('state_id'))) {
            $this->db->where("loan_state.state_id=", $this->input->post('state_id'));
        }
        //Used mainly in loan Reports
        if ($this->input->post('state_ids') !== NULL && is_array($this->input->post('state_ids'))) {
            $this->db->where_in("loan_state.state_id", $this->input->post('state_ids'));
        }
        if ($this->input->post('report') == 'true') {
            $this->db->join("$this->secure_unsecured_loans secure_unsecured_loans", 'secure_unsecured_loans.client_loan_id=a.id', 'left');
        }
        if ($this->input->post('min_amount') != 'All' && $this->input->post('min_amount') != '') {
            $this->db->where("a.requested_amount >=", $this->input->post('min_amount'));
        }
        if ($this->input->post('max_amount') != 'All' && $this->input->post('max_amount') != '') {
            $this->db->where("a.requested_amount <=", $this->input->post('max_amount'));
        }
        if ($this->input->post('min_days_in_arrears') != 'All' && $this->input->post('min_days_in_arrears') != '') {
            $this->db->where("days_in_arrears >=", $this->input->post('min_days_in_arrears'));
        }
        if ($this->input->post('max_days_in_arrears') != 'All' && $this->input->post('max_days_in_arrears') != '') {
            $this->db->where("days_in_arrears <=", $this->input->post('max_days_in_arrears'));
        }
        if ($this->input->post('product_id') != 'All' && $this->input->post('product_id') != '') {
            $this->db->where("a.loan_product_id =", $this->input->post('product_id'));
        }
        if ($this->input->post('loan_type') != 'All' && $this->input->post('loan_type') == '1') {
            $this->db->where("security_amount > 0 ");
        }
        if ($this->input->post('loan_type') != 'All' && $this->input->post('loan_type') == '0') {
            $this->db->where("security_amount IS NULL");
        }
        if ($this->input->post('due_days') != 'All' && $this->input->post('condition') == '1') {
            $this->db->where("days_in_demand >=", $this->input->post('due_days'));
        }
        if ($this->input->post('due_days') != 'All' && $this->input->post('condition') == '2') {
            $this->db->where("days_in_demand <=", $this->input->post('due_days'));
        }
        if ($this->input->post('credit_officer_id') != 0 && $this->input->post('credit_officer_id') != '') {
            $this->db->where("a.credit_officer_id", $this->input->post('credit_officer_id'));
        }
        if ($this->input->post('next_due_month') != 'All' && $this->input->post('next_due_month') != '') {
            $this->db->where("MONTH(next_pay_date)", $this->input->post('next_due_month'));
        }
        if ($this->input->post('next_due_year') != 'All' && $this->input->post('next_due_year') != '') {
            $this->db->where("YEAR(next_pay_date)", $this->input->post('next_due_year'));
        }
    }

    public function get2($filter = FALSE)
    {
        $this->db->select('count(a.id) no_rows');
        $this->get_select();
        $this->conditional_filters();
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['no_rows']) ? $result['no_rows'] : 0;
    }

    public function get($filter = FALSE)
    {
        $this->db->select(implode(", ", $this->columns), FALSE);
        $this->get_select();
        $this->conditional_filters();

        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $this->db->where("m.id=", $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_dTable()
    {
        $all_columns = $this->input->post('columns');

        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);

        $this->conditional_selects();
        $this->get_select();
        $this->set_filters($all_columns);
        $this->conditional_filters();

        if ($this->input->post('order') !== NULL && $this->input->post('order') !== '') {
            $order_columns = $this->input->post('order');
            $regex = "/^[a-zA-Z]+\(([.`,'\"a-zA-Z0-9_\s]|[a-zA-Z]+\([.`,'\"a-zA-Z0-9_\s]+\))+\)\s+/";

            foreach ($order_columns as $order_column) {
                if (isset($order_column['column']) && $all_columns[$order_column['column']]['orderable'] == "true") {
                    $replaced = preg_replace($regex, '', $this->columns[$order_column['column']]);
                    $this->db->order_by($replaced, $order_column['dir']);
                }
            }
        }

        if ($this->input->post('start') !== NULL && is_numeric($this->input->post('start')) && $this->input->post('length') != '-1') {
            $this->db->limit($this->input->post('length'), $this->input->post('start'));
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_excel_data($state)
    {
        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);

        $this->conditional_selects();
        $this->get_select();
        $this->set_filters($all_columns = false);
        $this->conditional_filters();

        $this->db->where('state_id=', $state);
        $query = $this->db->get();

        return $query->result_array();
    }

    private function get_loan_details_by_id($loan_id)
    {
        if (!$loan_id) {
            return false;
        }
        $this->db->select("id,credit_officer_id, loan_no");
        $this->db->where('id', $loan_id);
        $this->db->from($this->table);
        $query = $this->db->row_array();
        if ($query) {
            return $query['credit_officer_id'];
        } else {
            return false;
        }
    }

    public function get_credit_officer_details_by_id($staff_id)
    {
        $this->db->select('staff.id,firstname,lastname,othernames,salutation,staff_no, email');
        $this->db->from('staff');
        $this->db->join('user', 'user.id=staff.user_id');
        $this->db->join('approving_staff ap', 'ap.staff_id=staff.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function sendEmailToLoanOfficer($loan_details, $officer_detials)
    {
        $loan_number = $loan_details['loan_number'];
        $id = $loan_details['id'];
        $staff_email = $officer_detials['email'];
        $staff_first_name = $officer_detials['first_name'];
        $message = "Hello " . ucfirst($staff_first_name) . ", a loan with numbered" . $loan_number . " has transitioned to a next stage, you can visit <a href='" . base_url('client_loan/view/' . $id) . "'>this link</a> to view the loan in its new state";

        $subject = "Status update for loan " . $loan_number;

        $this->helpers->send_email_to_credit_officer($staff_email, $message, $subject);
    }

    public function change_status_by_id($id = false)
    {

        if ($id === false) {
            $id = $this->input->post('id');
            $data = array('status_id' => '2');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                // send the email
                $loan_details = $this->get_loan_details_by_id($id);
                $credit_officer_id = $loan_details['credit_officer_id'];

                $credit_officer_details = $this->get_credit_officer_details_by_id($credit_officer_id);
                $this->sendEmailToLoanOfficer($loan_details, $credit_officer_details);
                return true;
            } else {
                return false;
            }
        } else {
            $data = array('status_id' => '2');
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                // send the email
                $loan_details = $this->get_loan_details_by_id($id);
                $credit_officer_id = $loan_details['credit_officer_id'];

                $credit_officer_details = $this->get_credit_officer_details_by_id($credit_officer_id);
                $this->sendEmailToLoanOfficer($loan_details, $credit_officer_details);
                return true;
            } else {
                return false;
            }
        }
    }

    //Approving loan application
    public function approve($id = false, $unique_id = false)
    {
        if ($id === false) {
            $id = $this->input->post('client_loan_id');
            $data = $this->input->post(NULL, TRUE);
            $data['approval_note'] = $data['comment'];
            //approval date conversion
            $approval_date = explode('-', $data['action_date'], 3);
            $data['approval_date'] = count($approval_date) === 3 ? ($approval_date[2] . "-" . $approval_date[1] . "-" . $approval_date[0]) : null;
            //suggested disbursement date conversation
            $suggested_disbursement_date = explode('-', $data['suggested_disbursement_date'], 3);
            $data['suggested_disbursement_date'] = count($suggested_disbursement_date) === 3 ? ($suggested_disbursement_date[2] . "-" . $suggested_disbursement_date[1] . "-" . $approval_date[0]) : null;

            unset($data['id'], $data['requested_amount'], $data['action_date'], $data['state_id'], $data['client_loan_id'], $data['group_loan_id'], $data['comment'], $data['rank'], $data['client_id']);

            $data['approved_by'] = $_SESSION['id'];
            $data['unique_id'] = $unique_id;
            $this->db->where('id', $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $action_date = ($this->input->post('application_date') != NULL) ? $this->input->post('application_date') : (($this->input->post('action_date') != NULL) ? $this->input->post('action_date') : date('Y-m-d'));
            $data['approval_note'] = $this->input->post('comment');
            $data['approved_installments'] = $this->input->post('installments');
            $data['approved_repayment_frequency'] = $this->input->post('repayment_frequency');
            $data['approved_repayment_made_every'] = $this->input->post('repayment_made_every');
            $data['amount_approved'] = $this->input->post('requested_amount');
            $data['suggested_disbursement_date'] = $this->helpers->yr_transformer($action_date);
            $data['approval_date'] = $this->helpers->yr_transformer($action_date);
            $data['approved_by'] = $_SESSION['id'];
            $data['unique_id'] = $unique_id;
            $this->db->where('id=' . $id);
            $query = $this->db->update($this->table, $data);
            if ($query) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    // get loan disbursement_date
    public function get_loan_disbursement_date($loan_id)
    {
        $this->db->select("disbursement_date");
        $this->db->from("client_loan");
        $this->db->where("id", $loan_id);
        $this->db->order_by('disbursement_date', 'desc');
        $query = $this->db->get();

        return $query->row_array();
    }

    //get loan data
    public function get_client_data($loan_id = '')
    {
        $this->db->select(' a.offset_period, ifnull(g.group_name , concat( concat(salutation,".")," ",firstname," ", lastname," ", othernames)) AS member_name , a.offset_made_every, a.amount_approved, product_type_id, a.interest_rate, a.approved_installments, a.approved_repayment_made_every, a.approved_repayment_frequency, a.loan_product_id, a.loan_no, a.member_id, a.topup_application, a.linked_loan_id, ifnull(b.disbursed_amount,0) AS disbursed_amount, ifnull(rsdf.paid_principal,0) parent_paid_principal, ifnull(rsdf.paid_interest,0) parent_paid_interest')
            ->from('client_loan a')
            ->join('loan_product', 'a.loan_product_id=loan_product.id', 'left')
            ->join("client_loan b", 'b.id=a.linked_loan_id AND a.topup_application =1', 'left')
            ->join("group_loan gl", 'gl.id=a.group_loan_id', 'left')
            ->join("group g", 'g.id=gl.group_id', 'left')
            ->join("member", "member.id = a.member_id", 'left')
            ->join("user", "member.user_id = user.id", 'left')
            ->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=b.id', 'left')
            ->where('a.id', $loan_id)
            ->limit(1, 0);
        $query = $this->db->get();
        //print_r($this->db->last_query());   die();
        return $query->row_array();
    }

    public function reschedule($unique_id = false)
    {
        $id = $this->input->post('client_loan_id');
        $data = array(
            'approved_installments' => $this->input->post('installments'),
            'interest_rate' => $this->input->post('interest_rate'),
            'approved_repayment_made_every' => $this->input->post('repayment_made_every'),
            'approved_repayment_frequency' => $this->input->post('repayment_frequency'),
            'status_id' => '3',
            'modified_by' => $_SESSION['id'],
            'unique_id' => $unique_id
        );
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $this->db->update($this->table, $data);
            return true;
        } else {
            return false;
        }
    }

    public function update_source_fund($id = false, $unique_id = false)
    {

        if ($id === false) {
            $id = $this->input->post('client_loan_id');
        }
        $data['source_fund_account_id'] = $this->input->post('source_fund_account_id');
        $data['disbursed_amount'] = $this->input->post('principal_value');
        $data['unique_id'] = $unique_id;
        $this->db->where('id', $id);
        $query = $this->db->update($this->table, $data);
        if ($query) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function get_active_arrears($filter = FALSE, $end_date = FALSE)
    {

        $where = "WHERE state_id IN(7,13)";
        if ($end_date != false) {
            $where .= " AND action_date <='" . $end_date . "'";
        }
        $this->max_state_id1 = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select("a.id,loan_no,loan_state.action_date,a.amount_approved,interest_income_account_id, approval_date, a.disbursement_date,SUM(interest_amount) expected_interest,state_id")
            ->from('client_loan a');

        $this->db->join("fms_loan_product p", 'p.id=a.loan_product_id', 'left');
        $this->db->join("fms_repayment_schedule rp", 'rp.client_loan_id=a.id', 'left');
        $this->db->join("$this->max_state_id1 loan_state", 'loan_state.client_loan_id=a.id', 'left');
        if ($filter === FALSE) {
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter)) {
                $query = $this->db->get();
                return $query->row_array();
            } else {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            }
        }
    }

    public function get_loans($filter = FALSE, $call_type = FALSE)
    {
        $this->db->select("a.id,a.loan_no,interest_rate,a.loan_product_id,loan_state.state_id,ifnull(paid_principal,0) paid_principal, ifnull(paid_interest,0) paid_interest, ifnull(expected_interest,0) expected_interest, ifnull(expected_principal,0) expected_principal,group.group_name AS group_name,group_loan_no")
            ->from('client_loan a');
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', 'left');
        $this->db->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=a.id', 'left');
        $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');
        $this->db->join('group_loan', 'group_loan.id =a.group_loan_id', 'left');
        $this->db->join('group', 'group.id =group_loan.group_id', 'left');

        if ($this->input->post('state_id') !== NULL && is_numeric($this->input->post('state_id'))) {
            $this->db->where("loan_state.state_id=", $this->input->post('state_id'));
        }
        if ($filter === FALSE) {
            $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
            $this->db->join('member m', 'm.id =a.member_id ');
            $this->db->join('user c', 'c.id= m.user_id');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter) && $call_type === FALSE) {
                $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
                $this->db->join('member m', 'm.id =a.member_id ', 'left');
                $this->db->join('user c', 'c.id= m.user_id', 'left');
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else if ($filter !== FALSE && $call_type !== FALSE) {
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
                $this->db->join('member m', 'm.id =a.member_id ', 'left');
                $this->db->join('user c', 'c.id= m.user_id', 'left');
                $this->db->where($filter);
                $query = $this->db->get();

                // print_r($this->db->last_query());die();
                return $query->result_array();
            }
        }
    }


    public function get_loan_interest($filter = false)
    {
        $this->db->select("id,loan_no,interest_rate,disbursement_date");
        if ($filter != false) {
            $query = $this->db->get_where('client_loan', 'client_loan.id=' . $filter);
            return $query->row_array();
        } else {
            $query = $this->db->get('client_loan');
            return $query->result_array();
        }
    }

    //chart querys
    public function combined_state_totals($filter)
    { //Used for the pie chart
        $where = "WHERE 1 ";
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') != '') {
            $where .= " AND action_date <='" . $this->input->post('date_to') . "'";
        }
        if ($this->input->post('date_from') != NULL && $this->input->post('date_from') != '') {
            $where .= " AND action_date >='" . $this->input->post('date_from') . "'";
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";


        $this->db->select("a.id");
        $this->db->from('client_loan a');
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', 'left');
        $this->db->where_in("loan_state.state_id", $filter);
        $query = $this->db->get();
        // print_r($this->db->last_query()); die;
        return $query->num_rows();
    }


    public function get_sum_count($filter = 1)
    {
        $where = "WHERE 1 ";
        if ($this->input->post('end_date') != NULL && $this->input->post('end_date') != '' && $this->input->post('end_date') != 'Invalid date') {
            $where .= " AND action_date <='" . $this->input->post('end_date') . "'";
        }
        if ($this->input->post('start_date') != NULL && $this->input->post('start_date') != '' && $this->input->post('start_date') != 'Invalid date') {
            $where .= " AND action_date >='" . $this->input->post('start_date') . "'";
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select(" ifnull(sum(`amount_approved`),0) `amount_approved`, ifnull(sum(`requested_amount`),0) `requested_amount`, count(`id`) `loan_count`");
        $this->db->from('client_loan');
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=client_loan.id", "left");
        $this->db->where($filter);
        if ($this->input->post('staff_id')) {
            $this->db->where("$this->table.created_by", $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') !== NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id =', $this->input->post('credit_officer_id'));
        }

        $q = $this->db->get();
        // print_r($this->db->last_query()); die;
        return $q->row_array();
    }
    public function get_sum_count_before($filter = 1, $start_date = false, $end_date = false)
    {
        $where = "WHERE 1 ";
        if ($end_date != false) {
            $where .= " AND action_date <='" . $end_date . "'";
        }
        if ($start_date != false) {
            $where .= " AND action_date >='" . $start_date . "'";
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state $where GROUP BY client_loan_id ) )";

        $this->db->select(" sum(`amount_approved`) `amount_approved`, sum(`requested_amount`) `requested_amount`, count(`id`) `loan_count`");
        $this->db->from('client_loan');
        $this->db->join("$this->max_state_id loan_state", "loan_state.client_loan_id=client_loan.id", "left");
        $this->db->where($filter);
        if ($this->input->post('staff_id')) {
            $this->db->where("$this->table.created_by", $this->input->post('staff_id'));
        }
        if ($this->input->post('credit_officer_id') !== NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $this->db->where('client_loan.credit_officer_id =', $this->input->post('credit_officer_id'));
        }

        $q = $this->db->get();
        // print_r($this->db->last_query()); die;
        return $q->row_array();
    }

    public function product_combined_state_totals($filter)
    { //Used for the bar graph
        $where = "WHERE 1 ";
        if ($this->input->post('date_to') != NULL && $this->input->post('date_to') != '') {
            $where .= " AND fms_loan_state.action_date <='" . $this->input->post('date_to') . "'";
        }
        if ($this->input->post('date_from') != NULL && $this->input->post('date_from') != '') {
            $where .= " AND fms_loan_state.action_date >='" . $this->input->post('date_from') . "'";
        }
        if ($this->input->post('end_date') != NULL && $this->input->post('end_date') != '') {
            $where .= " AND fms_loan_state.action_date <='" . $this->input->post('end_date') . "'";
        }
        if ($this->input->post('start_date') != NULL && $this->input->post('start_date') != '') {
            $where .= " AND fms_loan_state.action_date >='" . $this->input->post('start_date') . "'";
        }
        if ($this->input->post('credit_officer_id') != NULL && is_numeric($this->input->post('credit_officer_id'))) {
            $where .= " AND fms_client_loan.credit_officer_id =" . $this->input->post('credit_officer_id');
        }
        $this->max_state_id = "(SELECT client_loan_id,state_id,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(fms_loan_state.id) from fms_loan_state JOIN fms_client_loan ON fms_loan_state.client_loan_id=fms_client_loan.id  $where GROUP BY client_loan_id ) )";

        $this->db->select('a.id AS product_id, product_name, ifnull(count(c.state_id),0) total ', FALSE);
        $this->db->from('loan_product a')->join('client_loan b', 'b.loan_product_id=a.id', 'left');
        $this->db->join("$this->max_state_id c", 'c.client_loan_id=b.id AND ' . $filter, 'left');
        $this->db->group_by("a.id");
        $this->db->order_by('a.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    //find the members savings account
    public function get_member_account($filter = 1)
    {
        $this->db->select('c.id');
        $this->db->from('client_loan a');
        $this->db->join('member b', 'b.id=a.member_id');
        $this->db->join('savings_account c', 'c.member_id=b.id');
        $this->db->join('savings_product d', 'c.deposit_product_id=d.id');
        $this->db->where($filter);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }


    public function get_member_details($filter = FALSE, $call_type = FALSE)
    {
        $this->db->select("a.id, a.member_id, a.loan_no,interest_rate, a.loan_product_id, loan_state.state_id, ifnull(paid_principal,0) paid_principal, ifnull(paid_interest,0) paid_interest, ifnull(expected_interest,0) expected_interest, ifnull(expected_principal,0) expected_principal")
            ->from('client_loan a');
        $this->db->join("$this->max_state_id loan_state", 'loan_state.client_loan_id=a.id', 'left');
        $this->db->join("$this->paid_amount rsdf", 'rsdf.client_loan_id=a.id', 'left');
        $this->db->join("$this->disburse_data disburse_data", 'disburse_data.client_loan_id=a.id', 'left');

        if ($this->input->post('state_id') !== NULL && is_numeric($this->input->post('state_id'))) {
            $this->db->where("loan_state.state_id=", $this->input->post('state_id'));
        }
        if ($filter === FALSE) {
            $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
            $this->db->join('member m', 'm.id =a.member_id ');
            $this->db->join('user c', 'c.id= m.user_id');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            if (is_numeric($filter) && $call_type === FALSE) {
                $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
                $this->db->join('member m', 'm.id =a.member_id ', 'left');
                $this->db->join('user c', 'c.id= m.user_id', 'left');
                $this->db->where('a.id=' . $filter);
                $query = $this->db->get();
                return $query->row_array();
            } else if ($filter !== FALSE && $call_type !== FALSE) {
                $this->db->select("group.group_name AS member_name");
                $this->db->join('group_loan', 'group_loan.id =a.group_loan_id');
                $this->db->join('group', 'group.id =group_loan.group_id');
                $this->db->where($filter);
                $query = $this->db->get();
                return $query->result_array();
            } else {
                $this->db->select("a.member_id,concat(c.firstname, ' ', c.lastname, ' ', c.othernames) member_name,client_no");
                $this->db->join('member m', 'm.id =a.member_id ');
                $this->db->join('user c', 'c.id= m.user_id');
                $this->db->where($filter);
                $query = $this->db->get();

                // print_r($this->db->last_query());die();
                return $query->result_array();
            }
        }
    }

    # Get fields that are updated when doing a disbursement
    public function get_disbursement_updated_fields($loan_id)
    {
        $this->db->select("approval_note, approved_installments, approved_repayment_frequency, approved_repayment_made_every, amount_approved, suggested_disbursement_date, approval_date, approved_by, source_fund_account_id, disbursed_amount");
        $this->db->from("client_loan");
        $this->db->where("id", $loan_id);
        $query = $this->db->get();

        return $query->row_array();
    }

    public function count_loans_in_state($filter)
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) from fms_loan_state GROUP BY client_loan_id ) )";

        if ($start_date) {
            $max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) FROM fms_loan_state WHERE DATE(action_date) >='{$start_date}' GROUP BY client_loan_id ) )";
        }
        if ($end_date) {
            $max_state_id = "(SELECT client_loan_id,state_id,comment,action_date FROM fms_loan_state
                WHERE id in ( SELECT MAX(id) FROM fms_loan_state WHERE DATE(action_date) <='{$end_date}' GROUP BY client_loan_id ) )";
        }

        $this->db->select('COUNT(a.id) as no_of_loans');
        $this->db->where($filter);
        $this->db->from("client_loan a");
        $this->db->join("$max_state_id mls", 'mls.client_loan_id=a.id', 'left');

        $query = $this->db->get();

        return $query->row_array();
    }

    public function update_loan_installment_payment_date($new_payment_date, $installment_payment_id)
    {
        $this->db->trans_start();
        $this->db->where('id', $installment_payment_id);
        $this->db->update('fms_loan_installment_payment', ['payment_date' => $new_payment_date]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->select('id');
        $this->db->from('fms_journal_transaction');
        $this->db->where('ref_id', $installment_payment_id);
        $query = $this->db->get();
        $updated_journal_id = $query->result_array()[0]['id'];
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('ref_id', $installment_payment_id);
        $this->db->update('fms_journal_transaction', ['transaction_date' => $new_payment_date]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('journal_transaction_id', $updated_journal_id);
        $this->db->update('fms_journal_transaction_line', ['transaction_date' => $new_payment_date]);
        $this->db->trans_complete();
    }

    public function wipe_out_loan($client_loan_id)
    {
        $state_id = 18; // DELETED
        $status_id = 18; // DELETED
        $this->db->trans_start();
        $this->db->select('loan_no');
        $this->db->from('fms_client_loan');
        $this->db->where('id', $client_loan_id);
        $query = $this->db->get();
        $loan_no = $query->row_array()['loan_no'];
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('ref_no', $loan_no);
        $this->db->update('fms_journal_transaction', ['status_id' => $status_id]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('reference_no', $loan_no);
        $this->db->update('fms_journal_transaction_line', ['status_id' => $status_id]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('client_loan_id', $client_loan_id);
        $this->db->update('fms_applied_loan_fee', ['status_id' => $status_id]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $loan_state_data = [
            'client_loan_id' => $client_loan_id,
            'state_id' => $state_id,
            'comment' => 'Wiped out of the system',
            'action_date' => date('Y-m-d'),
            'created_by' => isset($_SESSION['id']) ? $_SESSION['id'] : 1,
        ];
        $this->db->insert('fms_loan_state', $loan_state_data);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('client_loan_id', $client_loan_id);
        $this->db->update('fms_repayment_schedule', ['status_id' => $status_id]);
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->where('client_loan_id', $client_loan_id);
        $this->db->update('fms_loan_installment_payment', ['status_id' => $status_id]);
        $this->db->trans_complete();

        print_r($loan_no . ' has been wiped out.');
        print_r("\r\n");
    }

    public function adjust_penalty($post_data, $current_penalty)
    {
        $penalty_offset = $post_data['new_penalty_amount'] - $current_penalty;
        $this->db->trans_start();
        $this->db->where('client_loan_id', $post_data['client_loan_id']);
        $this->db->where('payment_status IN (2,4)');
        $this->db->where('status_id', 1);
        $this->db->from('fms_repayment_schedule');
        $this->db->select('id, demanded_penalty')->order_by('id', 'desc')->limit(1);
        $query = $this->db->get();
        $repayment_schedule = $query->row_array();
        $repayment_schedule_id = isset($repayment_schedule['id']) ? $repayment_schedule['id'] : null;
        $this->db->trans_complete();

        $this->db->trans_start();
        $new_penalty_offset = $penalty_offset + $repayment_schedule['demanded_penalty'];
        $this->db->where('id', $repayment_schedule_id);
        $result = $this->db->update('fms_repayment_schedule', ['demanded_penalty' => $new_penalty_offset]);
        $this->db->trans_complete();

        return $result;
    }

    public function countAllLoans($filter = false)
    {
        $this->db->select('id')
            ->from('fms_client_loan');
        if ($filter === false) {
            $query = $this->db->get();
            return $query->num_rows();
        } else {
            $this->db->where($filter);
            $query = $this->db->get();
            return $query->num_rows();
        }
    }

    public function get_interest_brought($id)
    {
        $this->db->select('interest_amount_bf');
        $this->db->from('fms_client_loan');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        return isset($result['interest_amount_bf']) ? $result['interest_amount_bf'] : 0;
    }

    public function get_loan_payable_today()
    {
        $all_columns = $this->input->post('columns');

        $this->db->select("SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $this->columns)), FALSE);

        $this->conditional_selects();
        $this->get_select();
        $this->set_filters($all_columns);
        $this->conditional_filters();


        $query = $this->db->get();
        return $query !== FALSE && $query->num_rows() > 0 ? $query->result_array() : [];
    }

    public function get_loan_amount_due($client_loan_id)
    {
        $this->db->select('amount_in_demand');
        $this->db->from('fms_client_loan a');
        $this->db->join("$this->amount_in_demand amount_in_demand", 'amount_in_demand.client_loan_id=a.id', 'left');
        $this->db->where("a.id", $client_loan_id);
        $query = $this->db->get();

        return $query->row_array()['amount_in_demand'];
    }
}
