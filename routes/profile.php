<?php

/*
|--------------------------------------------------------------------------
| Rotas ddo perfil
|--------------------------------------------------------------------------
*/

$router->get("/perfil", "ProfileController@index");
$router->post("/perfil", "ProfileController@update");

/*
|--------------------------------------------------------------------------
| Rotas de segurança
|--------------------------------------------------------------------------
*/

$router->get("/seguranca", "ProfileController@security");
$router->post("/seguranca", "ProfileController@updatePassword");