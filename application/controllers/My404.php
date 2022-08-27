<?php

/**
 * Description of My404
 * This overrides the default 404 handling controller
 *
 * @author allan
 */
class My404 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("session");
        $this->load->library("helpers");
    }

    public function index() {
        $this->output->set_status_header('404');
       // $this->load->library("helpers");
        $this->template->title = "404 Page Not Found";
        $this->template->content->view('includes/404');
        // Publish the template
        $this->template->publish();
    }

}
