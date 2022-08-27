<?php

/**
 * Description of Pesapal Controller
 *
 * @author Eric Kasakya
 */
class Pesapal extends CI_Controller {

    private $payment_statuses = [
        'INVALID' => ['code'=>9,"message"=>"Invalid request"], 
        'PENDING' => ['code'=>1,"message"=>"Payment is pending"], 
        'COMPLETED' => ['code'=>2,"message"=>"Payment completed and received. Thank you for paying."], 
        'FAILED' => ['code'=>3,"message"=>"Payment has failed"]
        ];

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        if(empty($this->session->userdata('id'))){
            redirect('welcome');
        } 
        $this->single_contact = "
                (SELECT `user_id`, `mobile_number` FROM `fms_contact`
                WHERE `id` in (
                    SELECT MAX(`id`) from `fms_contact` WHERE `contact_type_id`=1 GROUP BY `user_id` 
                )
            )";
        $this->load->model('savings_account_model');
        $this->load->model('transaction_model');
        $this->load->model('payment_engine_model');
        $this->data['payment_engine_requirements'] = $this->payment_engine_model->get_requirement();
        $this->consumer_key = $this->data['payment_engine_requirements']['consumer_key'];//'NBc2cN1deeRxI83I1LOmPifgDgoDc2WQ';
        $this->consumer_secret = $this->data['payment_engine_requirements']['consumer_secret'];//'YG2RSsXkvJuE0hY/c6OzLFNwg/4=';

    }

    public function acknledge() {
        $data = [
            'id' => $this->input->get("pesapal_merchant_reference"),
            'pesapalNotification' => $this->input->get("pesapal_notification_type"),
            'pesapal_ttid' => $this->input->get("pesapal_transaction_tracking_id")
        ];
        $data['title'] = $data['sub_title'] = "Payment acknowledgement";
        $data['message'] = "The payment process has not yet completed";
        if (isset($data['id']) && $data['id'] != '' && isset($data['pesapal_ttid']) && $data['pesapal_ttid'] != '') {
            $data['message'] = "Thank you for making your payment. It is currently being processed. We will notify you once it has completed.";
            $update_data = [
                'id' => $data['id'], 
                // 'pesapal_ttid' => $data['pesapal_ttid'], 
                'status' => 1
                    ];
            $this->transaction_model->update_transaction($update_data);
            redirect('u/savings');
        }
        $this->template->content->view("payment/pesapal_ack", $data);
        $this->template->publish();

        
    }

    public function make_payment($sent_id=false) {
        if ($sent_id == false) {
            $account_id= $this->input->post('account_no_id');
        }else{
           $account_id=$sent_id; 
        }
        $this->load->helper('oauth');
        $account_details = $this->savings_account_model->get($account_id);
        // print_r($account_details); die;
        if (!empty($account_details)) {
            $postdata=array(
                'transaction_type_id'=>2,
                'amount'=>$this->input->post('amount'),
                'narrative'=>$this->input->post('narrative'),
                'group_member_id'=>$this->input->post('group_member_id'),
                'account_no_id'=>$this->input->post('account_no_id'),
                'payment_id'=>3,//Mobile money payment id
                'status_id'=>3
            );
            if ($response=$this->transaction_model->mm_set($postdata,$charges=[])) {
                $reference_id=$response['transaction_id'];
            }else{
                $reference_id=100;
            }
            $year = date('Y');
            $amount = ($this->input->post('amount')!=NULL) ? $this->input->post('amount') : 10000;
            $amount = number_format($amount, 2); //format amount to 2 decimal places

            $desc = "Sacco Member Deposit";
            $type = "MERCHANT";
            $reference = $reference_id; 
            $first_name = $account_details['member_name'];
            $last_name = 'Sacco';
            $email = isset($account_details['email'])?$account_details['email']:'ict@gmtconsults.com'; 

            if (preg_match("/^[\+]+[0-9]{12,12}$/", $this->input->post('client_contact'))) {
                $phonenumber = $this->input->post('client_contact');
            } elseif (preg_match("/^[07]+[0-9]{9,10}$/", $this->input->post('client_contact'))) {
                $phonenumber = '+256' . substr($this->input->post('client_contact'), -9);
            } else {
                $phonenumber = '+256784240163';
            }

            $callback_url = site_url('pesapal/acknledge'); //redirect url, the page that will handle the response from pesapal.
            $post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
                    . "<PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\""
                    . $amount . "\" Description=\"" . $desc . "\" Type=\"" . $type . "\" Reference=\"" . $reference . "\" FirstName=\"" . $first_name . "\" LastName=\"" . $last_name
                    . "\" Email=\"" . $email . "\" PhoneNumber=\"" . $phonenumber . "\" xmlns=\"http://www.pesapal.com\" />";
            $this->do_iframe($callback_url, htmlentities($post_xml));

        } else {
            //let the customer know that they do not have any payment
            show_404();
        }
    }

    private function show_payment_status($the_data) {
        $data = ['title' => 'Payment status', 'sub_title' => 'Payment status invoice#'.$the_data['receipt']['invoice_no'], 'bond' => $the_data["bond"], 'invoice' => $the_data["receipt"]];
        //$data = ['title'=>'Make payment','sub_title'=>'Make payment','iframe_src' => ""];
        $new_data = array_merge($data,$the_data);
        $this->template->content->view("payment/invoice_status", $new_data);
        
    }

    private function do_iframe($callback_url, $post_xml) {
        //pesapal params
        $token = $params = NULL;
        
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        //$iframelink = 'https://demo.pesapal.com/api/PostPesapalDirectOrderV4'; //change to
        $iframelink = "https://www.pesapal.com/API/PostPesapalDirectOrderV4"; // when you are ready to go live!

        $consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);
        //post transaction to pesapal
        $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
        $iframe_src->set_parameter("oauth_callback", $callback_url);
        $iframe_src->set_parameter("pesapal_request_data", $post_xml);
        $iframe_src->sign_request($signature_method, $consumer, $token); /**/

        $data = ['title' => 'Make payment', 'sub_title' => 'Pay via pesapal', 'iframe_src' => $iframe_src];
        //$data = ['title'=>'Make payment','sub_title'=>'Make payment','iframe_src' => ""];
        // print_r($data);die;
        $this->template->title = 'Pesapal Deposit';

        $this->template->content->view("payment/pesapal_iframe", $data);
        $this->template->publish();
        
    }

    private function get_payment_status($bond_invoice) {

        // Parameters sent to you by PesaPal IPN
        $pesapalNotification = $bond_invoice['pesapal_notification_type'];
        $pesapalTrackingId = $bond_invoice['pesapal_transaction_tracking_id'];
        $pesapal_merchant_reference = $bond_invoice["pesapal_merchant_reference"];

        //default message
        //$data['message'] = "Thank you for making your payment. It is currently being processed. We will notify you once it has completed.";
        $status = $bond_invoice['status'];
        if (/*$pesapalNotification == "CHANGE" && */$pesapal_merchant_reference != "" && $pesapalTrackingId != '') {
            //pesapal params
            $statusrequestAPI = "https://www.pesapal.com/api/querypaymentstatus";
            
            $token = $params = NULL;
            $consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);
            $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

            //get transaction status
            $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
            $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
            $request_status->set_parameter("pesapal_transaction_tracking_id", $pesapalTrackingId);
            $request_status->sign_request($signature_method, $consumer, $token);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_status);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            if (defined('CURL_PROXY_REQUIRED')){
                if (CURL_PROXY_REQUIRED == 'True') {
                    $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
                    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                }
            }
            $response = curl_exec($ch);

            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            //$raw_header = substr($response, 0, $header_size - 4);
            //$headerArray = explode("\r\n\r\n", $raw_header);
            //$header = $headerArray[count($headerArray) - 1];

            //transaction status
            $elements = preg_split("/=/", substr($response, $header_size));
            $status1 = $elements[1];

            curl_close($ch);

            //UPDATE YOUR DB TABLE WITH NEW STATUS FOR TRANSACTION WITH pesapal_transaction_tracking_id $pesapalTrackingId
            if ($bond_invoice['status'] !=$this->payment_statuses[$status1]['code'] ) {
                $status = $this->payment_statuses[$status1]['code'];
                $update_data = [
                    'id' => $pesapal_merchant_reference, 
                    'pesapal_ttid' => $pesapalTrackingId, 
                    'status' => $status
                        ];
                $this->bond_invoicing_model->update($update_data);
                if($status == 0){
                    $this->session->set_userdata("invoice",[]);
                }
                //$resp = "pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
                //$data['message'] = $this->payment_statuses[$status]['message'];
            }
        }
        return $status;
        /*$data = ['title' => 'Payment status', 'sub_title' => "Tracking ID#$pesapalTrackingId"];
        
        $this->template->content->view("payment/pesapal_ack", $data);
        */
    }

}
