<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_shortlinks', 'shortlink'); // Load the model
        if (! $this->user->check_login()) {
            redirect('admin/login'); // Redirect to login if not logged in
        }
    }

    // Display all short links
    public function index()
    {
        if (isset($_GET['news'])) {
            // Retrieve the parameter value
            $news = $_GET['news'];
            $link = $this->shortlink->getOriginalUrl($news);
        }
        if ($link) {
            $this->shortlink->incrementClickCount($news); // Increment click count
            redirect($link['original_url']);
        } else {
            show_404(); // Handle invalid short codes
        }

    }

}
