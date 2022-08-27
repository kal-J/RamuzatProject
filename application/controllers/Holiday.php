<?php

/**
 * Description of holiday
 *
 * @author Eric
 */
class Holiday extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->model('holiday_model');
    }

    public function jsonList() {
        $holiday_data = $this->holiday_model->get();
        //setting default timezone
        date_default_timezone_set('Africa/Kampala');
        $data['data']=[];
        $holidays = array();
        foreach ($holiday_data as $key => $value) {
            $date=$value['holiday_date'];
                if ($value['every'] == 'Constant') {
                    $holidays[$key]['holiday_date'] =  date('Y-m-d', strtotime($date));
                } else if ($value['every'] == 'Good_Friday') {
                    $the_year = date('Y');
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[$key]['holiday_date'] = date("Y-m-d", strtotime("-2 day", strtotime($the_easter_sunday)));
                } else if ($value['every'] == 'Easter_Sunday') {
                    $the_year = date('Y');
                    $holidays[$key]['holiday_date'] = date('Y-m-d', easter_date($the_year));
                } else if ($value['every'] == 'Easter_Monday') {
                    $the_year = date('Y');
                    $the_easter_sunday = date('Y-m-d', easter_date($the_year));
                    $holidays[$key]['holiday_date'] = date("Y-m-d", strtotime("+1 day", strtotime($the_easter_sunday)));
                } else {
                    $year = date('Y');
                    $month = strtolower($value['month']);
                    $day = strtolower($value['day']);
                    $every = strtolower($value['every']);
                    $holidays[$key]['holiday_date'] = date('Y-m-d', strtotime("$every $day of $month $year"));
                }
                $holidays[$key]['id']=$value['id'];
                $holidays[$key]['frequency_every_id']=$value['frequency_every_id'];
                $holidays[$key]['frequency_day_id']=$value['frequency_day_id'];
                $holidays[$key]['frequency_of_id']=$value['frequency_of_id'];
                $holidays[$key]['holiday_name']=$value['holiday_name'];
            }
            $data['data']=$holidays;
        echo json_encode($data);
    }

    public function create() {
        $this->form_validation->set_rules('holiday_name', 'Holiday name', array('required', 'min_length[2]', 'max_length[100]'), array('required' => '%s must be entered'));

        $feedback['success'] = false;
        if ($this->form_validation->run() === FALSE) {
            $feedback['message'] = validation_errors();
        } else {
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                if ($this->holiday_model->update()) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Holiday details successfully updated";
                    $feedback['holiday'] = $this->holiday_model->get($_POST['id']);
                    //activity log 

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing holiday details ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                } else {
                    $feedback['message'] = "There was a problem updating the holiday details";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Editing holiday details ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
                }
            } else {
                $holiday_id = $this->holiday_model->set();
                if ($holiday_id) {
                    $feedback['success'] = true;
                    $feedback['message'] = "Holiday details successfully saved";

                      $this->helpers->activity_logs($_SESSION['id'],18,"Creating holiday details ",$data['message']." # ".$this->input->post('holiday_name'),NULL,"id #".$holiday_id);
                }
                 else {
                    $feedback['message'] = "There was a problem saving the holiday details";

                     $this->helpers->activity_logs($_SESSION['id'],18,"Creating holiday details ",$data['message']." # ".$this->input->post('holiday_name'),NULL,"id #".$holiday_id);
                }
            }
        }
        echo json_encode($feedback);
    }

    public function delete() {
        $response['message'] = "Holiday details could not be deleted, contact support.";
        $response['success'] = FALSE;
         $this->helpers->activity_logs($_SESSION['id'],18,"Deleting holiday details ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));

        if ($this->holiday_model->delete_by_id($this->input->post('id'))) {
            $response['success'] = TRUE;
            $response['message'] = "Holiday details successfully deleted.";
            
             $this->helpers->activity_logs($_SESSION['id'],18,"Deleting holiday details ",$data['message']." # ".$this->input->post('id'),NULL,"id #".$this->input->post('id'));
        }
        echo json_encode($response);
    }

}
