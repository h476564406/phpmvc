<?php

class BackPlatformController extends Controller
{
    public function __construct()
    {
        $this->init_session();
        $this->check_login();
    }

    public function init_session()
    {
        session_start();
    }

    public function check_login()
    {
        if (ACTION === 'manage') {
            if (isset($_SESSION['user_login']) && $_SESSION['user_login'] == 'yes') {
            } else {
                $this->jump('index.php?c=Admin&a=login', 'Please login.', 2);
            }
        }
    }
}
