<?php

class IndexController extends BackPlatformController
{

    public function indexAction()
    {
        require VIEW_DIR . 'homepage.html';
    }
}
