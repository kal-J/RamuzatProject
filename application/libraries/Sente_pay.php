<?php
if (!defined('BASEPATH'))
  exit("No direct script access allowed");
/**
 * @Author Reagan
 */

use HTTP\Request2; // Only when installed with PEAR

class Sente_pay
{
  protected $mm_channel_data;
  protected $CI;
  public function __construct()
  {
    // Assign the CodeIgniter super-object
    $this->CI = &get_instance();
    $this->CI->load->model('payment_engine_model', '', TRUE);
    $this->mm_channel_data = $this->CI->payment_engine_model->get_requirement(1);
  }

  //responsible for formatting contacts into international format
  public function format_contact($contact)
  {

    if (preg_match("/^[\+]+[0-9]{12,12}$/", $contact)) {
      $mobile_number = $contact;
    } elseif (preg_match("/^[07]+[0-9]{9,10}$/", $contact)) {
      $mobile_number = '+256' . substr($contact, -9);
    }
    return $mobile_number;
  }

  //responsible for creating a collection request
  public function new_collection($instruction_data)
  {
    $phonenumber = $this->format_contact($instruction_data['phone_number']);

    $request = new HTTP_Request2();
    $request->setUrl('https://sentepay.com/app/api/transact/collect');
    $request->setMethod(HTTP_Request2::METHOD_POST);
    $request->setConfig(array(
      'follow_redirects' => TRUE
    ));

    $headers = array(
      'X-Authorization' => $this->mm_channel_data['api_key'],
    );
    $request->setHeader($headers);
    $parameter_array = array(
      'currency' => 'UGX',
      'provider' => 'MTN_UG',
      'amount' => $instruction_data['amount'],
      'msisdn' => $phonenumber,
      'narrative' => $instruction_data['narrative'],
      'ext_ref' => $instruction_data['external_ref'],
      'callback_url' => 'http://callback.url/ordercompleter',
      'customer_names' => $instruction_data['names']
    );
    $request->addPostParameter($parameter_array);
    try {
      $response = $request->send();
      if ($response->getStatus() == 200) {
        $parameter_array['status'] = $response->getStatus();
        return $parameter_array;
        //echo $response->getBody();
      } else {
        $parameter_array['status'] = $response->getStatus();
        return $parameter_array;
        //echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
        //$response->getReasonPhrase();
      }
    } catch (HTTP_Request2_Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  public function new_payment($instruction_data)
  {
    $phonenumber = $this->format_contact($instruction_data['phone_number']);
    $request = new HTTP_Request2();
    $request->setUrl('https://api.sentepay.com/api/transact/pay');
    $request->setMethod(HTTP_Request2::METHOD_POST);
    $request->setConfig(array(
      'follow_redirects' => TRUE
    ));
    $headers = array(
      'X-Authorization' => $this->mm_channel_data['api_key'],
    );
    $request->setHeader($headers);
    $request->addPostParameter(array(
      'currency' => 'UGX',
      'amount' => round($instruction_data['amount'], 0),
      'msisdn' => $phonenumber,
      'narrative' => "DISBURSE LOAN", //,
      'ext_ref' => $instruction_data['ext_ref'],
      'customer_names' => $instruction_data['names'],
      'customer_email' => ''
    ));
    //print_r($request);die;
    try {
      $response = $request->send();
      if ($response->getStatus() == 200) {
        return (array) $response->getBody();
      } else {
        return 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
          $response->getBody();
      }
    } catch (HTTP_Request2_Exception $e) {
      return 'Error: ' . $e->getMessage();
    }
  }

  public function check_payment_status($data)
  {
    //print_r('https://api.sentepay.com/api/transact/pay/status/'. $data['refNo'] . '/' . $data['ext_ref']); die;
    $request = new HTTP_Request2();
    $request->setUrl('https://api.sentepay.com/api/transact/pay/status/'. $data['refNo'] . '/' . $data['ext_ref']);
    $request->setMethod(HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
      'follow_redirects' => TRUE
    ));
    $headers = array(
      'X-Authorization' => $this->mm_channel_data['api_key'],
    );
    $request->setHeader($headers);

    try {
      $response = $request->send();
      if ($response->getStatus() == 200) {
        $result = json_decode($response->getBody(), true);
        $result['status_code'] = $response->getStatus();
        return $result;

      } else {
        return 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
          $response->getReasonPhrase();
      }
    } catch (HTTP_Request2_Exception $e) {
      return 'Error: ' . $e->getMessage();
    }
  }
}
