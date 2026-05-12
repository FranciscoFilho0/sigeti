<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Message;
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

    public function create(): void {

        Auth::requirePermission(Permission::CREATE_ROLE);
        echo $this->view->render("admin/role/create");
        clear_old();

    }


    public function edit(?array $data): void {

        Auth::requirePermission(Permission::EDIT_ROLE);

        $role = Role::find($data['id']);

        if (!$role){
            Message::error("perfil não encontrado.");
            redirect("/admin/perfis/cadastrar");
            return;
        }

        echo $this->view->render("admin/role/edit",[
            "role" => $role
        ]);

        clear_old();

    }

}