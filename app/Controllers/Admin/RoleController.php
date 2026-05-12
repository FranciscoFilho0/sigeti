<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permission;
use App\Models\Role\Role;


class RoleController extends Controller
{

    public function __construct()
    {
        parent::__construct('App');
        Auth::requirePermission(Permission::VIEW_ROLES);
    }

    public function index(): void {

        $roles = Role::all();

        echo $this->view->render("admin/role/index", [
            "roles" => $roles
        ]);

        clear_old();
    }

    public function create(): void {}

}