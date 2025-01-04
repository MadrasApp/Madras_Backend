<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shortlinks extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_shortlinks'); // Load the model
        if (! $this->user->check_login()) {
            redirect('admin/login'); // Redirect to login if not logged in
        }
    }

    // Display all short links
    public function index()
    {
        $data = $this->settings->data;
        $data['_title'] = 'Short Links';

        // Fetch all short links from the database
        $data['short_links'] = $this->M_shortlinks->getAllShortLinks();

        $this->_view('v_short_links', $data);
    }

    public function redirect($shortCode)
    {
        $link = $this->ShortLinkModel->getOriginalUrl($shortCode);
        if ($link) {
            $this->ShortLinkModel->incrementClickCount($shortCode); // Increment click count
            redirect($link['original_url']);
        } else {
            show_404(); // Handle invalid short codes
        }
    }

    // Load views with consistent layout
    public function _view($view, $data)
    {
        $this->load->view('admin/v_header', $data);
        $this->load->view('admin/v_sidebar');
        $this->load->view('admin/' . $view, $data);
        $this->load->view('admin/v_footer');
    }
}
