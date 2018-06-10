<?php

class Controller
{
    /*
     * This method will guide user to other router.
     */
    protected function jump($url, $message = '', $time = '2')
    {
        // If no specific message, redirect directly.
        if ($message === '') {
            header('Location: ' . $url);
        } else {
            // If has specific message, use default html to show message, then redirect.
            if (file_exists(VIEW_DIR . 'jump.html')) {
                require VIEW_DIR . 'jump.html';
            }
        }
        die;
    }
}
