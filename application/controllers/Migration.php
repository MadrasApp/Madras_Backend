<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge(); // Load database forge library
    }

    public function create_short_links_table()
    {
        // Define fields for the 'short_links' table
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'short_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => TRUE
            ],
            'original_url' => [
                'type' => 'TEXT',
                'null' => FALSE
            ],
            'click_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
        ];

        // Add fields and primary key
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        // Create the table
        if ($this->dbforge->create_table('short_links', TRUE)) {
            echo "Table 'short_links' created successfully!";
        } else {
            echo "Failed to create table.";
        }
    }
}
