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


require __DIR__ . "/auth.php";
require __DIR__ . "/admin.php";
require __DIR__ . "/profile.php";

/*
|--------------------------------------------------------------------------
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
| Rotas de Erro
|--------------------------------------------------------------------------
*/
$router->group(null);
$router->get("/erro/{errorCode}" , "ErrorController@index");


$router->dispatch();

if ($router->error()) {
    redirect("/erro/{$router->error()}");
}