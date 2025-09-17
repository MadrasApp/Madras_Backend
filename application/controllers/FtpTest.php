<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib3\Net\SFTP;

class FtpTest extends CI_Controller {

    public function index()
    {
        $sftp = new SFTP('your-server.com', 30046);
        if (!$sftp->login('username', 'password')) {
            echo "❌ اتصال یا لاگین به SFTP ناموفق بود!";
            return;
        }

        echo "✅ اتصال موفق!";
    }
}
