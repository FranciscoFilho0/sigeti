<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Permission;
use App\Models\Departments\Department;
use App\Models\Role\Role;
use App\Models\Ticket\Ticket;
use App\Models\User;

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct("App");

        Auth::requirePermission(Permission::VIEW_MANAGER_DASHBOARD);
    }

    public function index(): void
    {
        Auth::requirePermission(Permission::VIEW_MANAGER_DASHBOARD);

        $totalUsers = (new User())->totalUsers();
        $totalRoles = (new Role())->totalRoles();
        $totalDepartments = (new Department())->totalDepartments();
        $totalOpenTickets = (new Ticket())->totalOpenTickets();


            echo $this->view->render("admin/dashboard",
                [
                    "title" => "Dashboard | Admin" . APP_NAME,
                    "totalUsers" => $totalUsers,
                    "totalRoles" => $totalRoles,
                    "totalDepartments" => $totalDepartments,
                    "totalOpenTickets" => $totalOpenTickets,

                ]);
        }


}