<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Data_import
 *
 * @author REAGAN
 */
class Member_import extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("session");
        if (empty($this->session->userdata('id'))) {
            redirect('/');
        }
        $this->load->model("contact_model");
        $this->load->model("user_model");
        $this->load->model("member_model");
        $this->load->model("nextOfKin_model");
        $this->load->model("address_model");
        $this->load->model("Data_import_model");

        $this->account_no = "SV0000";
        $this->share_account_no = "SH0000";
        $this->member_no = "CL0000";
    }

    public function index()
    {
        $folder = "data_extract" . DIRECTORY_SEPARATOR . "mceesacco" . DIRECTORY_SEPARATOR;
        $file_name = "NEW_LOANS.csv";
        $file_path = FCPATH . $folder . $file_name;
        $feedback = $this->run_updates($file_path);
        echo json_encode($feedback);
    }

    private function run_updates($file_path)
    {
        $handle = fopen($file_path, "r");
        $total_clients = $count = 0;
        $field_names = $data_array = [];
        $feedback = ["success" => false, "message" => "File Could not be opened"];
        if ($handle) {
            ini_set('memory_limit', '200M');
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');
            ini_set('max_input_time', 3600);
            ini_set('max_execution_time', 3600);
            while (($data = fgetcsv($handle, 30048576, ",")) !== FALSE) {
                $data1 = $this->security->xss_clean($data);

                if ($count == 0) { //the row with the field_names
                    $field_names = $data1;

                    if ($field_names[0] != "CLIENT_NO") {
                        $feedback['message'] = "Please ensure that the first cell (A1) contains the key Client_no";
                        fclose($handle);
                        return $feedback;
                    }
                } else {
                    $this->account_no = ++$this->account_no;
                    $this->share_account_no = ++$this->share_account_no;
                    $this->member_no = ++$this->member_no;
                    $total_clients = $total_clients + $this->insert_user_data($data1);
                }

                $count++;
            }
            fclose($handle);

            if (is_numeric($total_clients)) {
                $feedback["success"] = true;
                $feedback["message"] = "Update done\n $total_clients records updated";
            }
        }
        return $feedback;
    }

    private function insert_user_data($customer_data)
    {
        //echo json_encode($customer_data);die;
        /* if ($customer_data[4]) {
            $date_registered = $customer_data[4];
            $date_registered  = date('Y-m-d', strtotime($customer_data[4]));
        } else {
            $date_registered = "2022-01-01";
        } */
        $date_registered = "2022-07-09";

        if ($customer_data[1] != "" && $customer_data[1] != NULL) {
            $names = explode(" ", trim($customer_data[1]));
            $single_row = [

                "firstname" => $names[0],
                "lastname" => isset($names[1]) ? $names[1] : '',
                "othernames" => isset($names[2]) ? $names[2] : '',
                "gender" => $customer_data[5] == "M" ? 1 : 0,
                "email" => '',
                "marital_status_id" => 1,
                "date_of_birth" => $date_registered,
                "children_no" => 0,
                "dependants_no" => 0,
                "status" => 1,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $user_id = $this->user_model->add_user($single_row);
            $member_data = [
                "id" => $customer_data[0],
                "user_id" => $user_id,
                "client_no" => $this->member_no,
                "branch_id" => 2,
                "subscription_plan_id" => 1,
                "occupation" => 'N/L',
                "registered_by" => 1,
                "date_registered" => $date_registered,
                "date_created" => time(),
                "created_by" => 1,
                "modified_by" => 1
            ];
            $member_id = $this->member_model->add_member(false, false, $member_data);

            /* if ($customer_data[3]) {
                $this->do_insert_contacts($user_id, $customer_data[3]);
            } */
            /* if ($customer_data[6]) {
                $this->do_insert_contacts($user_id,$customer_data[6]);
            } */

            //$this->create_savings_accounts($member_id);
            //$this->pay_membership_fees($member_id,$customer_data[1]);
            //$this->pay_subscription($member_id,$customer_data[1]);
            return 1;
        }
        return 0;
    }

    private function do_insert_contacts($user_id, $phone_number)
    {
        //echo json_encode($phone_number);die();
        $data = [
            "user_id" => $user_id,
            "mobile_number" => $phone_number,
            "contact_type_id" => 1,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];
        return $this->contact_model->add_contact(false, $data);
    }

    public function create_savings_accounts($member_id)
    {

        $data = [
            "member_id" => $member_id,
            "status_id" => 7,
            "client_type" => 1,
            "deposit_Product_id" => 1,
            "account_no" => $this->account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];

        $this->load->model("Data_import_model");
        $savings_account = $this->Data_import_model->add_savings_account($data);
        $this->create_share_accounts($member_id, $savings_account);
    }

    private function create_share_accounts($member_id, $savings_account)
    {
        $data = [
            "member_id" => $member_id,
            "status_id" => 1,
            "share_issuance_id" => 1,
            "date_opened" => date('Y-m-d'),
            "default_savings_account_id" => $savings_account,
            "share_account_no" => $this->share_account_no,
            "date_created" => time(),
            "created_by" => 1,
            "modified_by" => 1,
        ];

        $this->load->model("Data_import_model");
        $this->Data_import_model->set_share_state($data);
    }

    public function pay_membership_fees($member_id, $member_name)
    {
        $trans_row = [
            "transaction_no" => date('yws') . mt_rand(100000, 999999),
            "member_id" => $member_id,
            "amount" => 20000,
            "member_fee_id" => 1,
            "payment_id" => 1,
            "requiredfee" => 1,
            "payment_date" => '2022-01-01',
            "narrative" => "Membership Fees [ " . $member_name . " ]",
            "status_id" => 1,
            "fee_paid" => 1,
            "date_created" => time(),
            "created_by" => 1
        ];
        $this->Data_import_model->add_membership_fees($trans_row);
    }

    public function pay_subscription($member_id, $member_name)
    {
        $trans_row = [
            "transaction_no" => date('yws') . mt_rand(10000, 99999),
            "client_id" => $member_id,
            "amount" => 10000,
            "transaction_channel_id" => 1,
            "payment_id" => 1,
            "subscription_date" => '2022-01-01',
            "payment_date" => '2021-01-01',
            "narrative" => "ANNUAL SUBSCRIPTION [ " . $member_name . " ]",
            "sub_fee_paid" => 1,
            "status_id" => 1,
            "date_created" => time(),
            "created_by" => 1
        ];

        $this->Data_import_model->add_subscription_fees($trans_row);
    }
}
