<?php

namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct("App");
    }

    public function index()
    {
        echo "<h1>Lista de usuarios</h1>";
    }

}