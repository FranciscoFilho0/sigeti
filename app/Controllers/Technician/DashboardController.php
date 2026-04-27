<?php

namespace App\Controllers\Technician;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct("App");

        Auth::requireRole(User::TECHNICIAN);
    }

    public function index(): void
    {
       $tickets = (new Ticket())->ticketsOrderedByStatusPriorityAndOpeningDate();
        $quantityTicketsByMonth = (new Ticket())->countTicketsByMonth();

        $quantityTicketsByCategory = (new Ticket())->countTicketsByCategory();
        $quantityTicketsByStatus = (new Ticket())->countTicketsByStatus();
        $avgResolutionDays = (new Ticket())->avgResolutionDaysByMonthCurrentYear();
        $ticketsByPriorityAndStatus = (new Ticket())->countByPriorityAndStatusCurrentYear();


        echo $this->view->render("technician/dashboard",
            [
                "title" => "Dashboard | Técnico" . APP_NAME,
                "tickets" => $tickets,
                "quantityTicketsByMonth" => $quantityTicketsByMonth,
                "quantityTicketsByCategory" => $quantityTicketsByCategory,
                "quantityTicketsByStatus" => $quantityTicketsByStatus,
                "avgResolutionDays" => $avgResolutionDays,
                "ticketsByPriorityAndStatus" => $ticketsByPriorityAndStatus,
            ]);
    }

}