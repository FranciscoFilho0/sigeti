<?php

namespace App\Controllers;

use App\Core\Controller;

class WebController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

        echo $this->view->render("home",
        [
            "title" => "Home | ". APP_NAME,
        ]);
    }
}