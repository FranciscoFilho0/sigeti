<?php

use CoffeeCode\Router\Router;

$router = new Router(APP_URL, "@");
$router->namespace("app\Controllers");


/*
|--------------------------------------------------------------------------
| Rotas da Web
|--------------------------------------------------------------------------
*/
$router->get("/", "WebController@index");


/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
*/
require __DIR__ . "/auth.php";


/*
|--------------------------------------------------------------------------
| Rotas do Técnico
|--------------------------------------------------------------------------
*/
require __DIR__ . "/technician.php";


/*
|--------------------------------------------------------------------------
| Rotas do Professor
|--------------------------------------------------------------------------
*/
require __DIR__ . "/teacher.php";


/*
|--------------------------------------------------------------------------
| Rotas do Admin
|--------------------------------------------------------------------------
*/
require __DIR__ . "/admin.php";

/*
|--------------------------------------------------------------------------
| Rotas de Perfil
|--------------------------------------------------------------------------
*/
require __DIR__ . "/profile.php";

/*
|--------------------------------------------------------------------------
<<<<<<< HEAD
| Rotas do técnico
|--------------------------------------------------------------------------
*/

$router->group("/tecnico");
$router->get("/dashboard", "Technician\\DashboardController@index");



// rotas dos chamados

$router->get("/chamados", "Technician\\TicketController@index");
$router->get("/chamados/cadastrar", "Technician\\TicketController@create");
$router->post("/chamados/cadastrar", "Technician\\TicketController@store");
$router->get("/chamados/editar/{id}", "Technician\\TicketController@edit");
$router->put("/chamados/editar/{id}", "Technician\\TicketController@update");
$router->get("/chamados/{ticket_id}/comentarios", "Technician\\TicketCommentController@index");
$router->post("/chamados/{ticket_id}/comentarios", "Technician\\TicketCommentController@store");

/*
|--------------------------------------------------------------------------
| Rotas do professor
|--------------------------------------------------------------------------
*/
$router->group(null);
$router->group("/professor");
$router->get("/dashboard", "Teacher\\DashboardController@index");
$router->get("/chamados", "Teacher\\TicketController@index");
$router->get("/chamados/cadastrar", "Teacher\\TicketController@create");
$router->post("/chamados/cadastrar", "Teacher\\TicketController@store");
$router->get("/chamados/{ticket_id}/comentarios", "Teacher\\TicketCommentController@index");
$router->post("/chamados/{ticket_id}/comentarios", "Teacher\\TicketCommentController@store");







/*
|--------------------------------------------------------------------------
=======
>>>>>>> 1b41385530cd94abb98a9bdf586e5c10f4cd7a00
| Rotas de Erro
|--------------------------------------------------------------------------
*/
$router->group(null);
$router->get("/erro/{errorCode}", "ErrorController@index");


$router->dispatch();


if ($router->error()) {
    redirect("/erro/{$router->error()}");
}