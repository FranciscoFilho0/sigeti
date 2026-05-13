<?php

/*
|--------------------------------------------------------------------------
| Rotas do dashboard
|--------------------------------------------------------------------------
*/

$router->group("/admin");
$router->get("/dashboard", "Admin\\DashboardController@index");

/*
|--------------------------------------------------------------------------
| Rotas dos perfis
|--------------------------------------------------------------------------
*/

$router->get("/perfis", "Admin\\RoleController@index");
$router->get("/perfis/cadastrar", "Admin\\RoleController@create");
$router->post("/perfis/cadastrar", "Admin\\RoleController@store");
$router->get("/perfis/editar/{id}", "Admin\\RoleController@edit");
$router->put("/perfis/editar/{id}", "Admin\\RoleController@update");
$router->delete("/perfis/excluir/{id}", "Admin\\RoleController@destroy");

/*
|--------------------------------------------------------------------------
| Rotas das permições
|--------------------------------------------------------------------------
*/

$router->get("/perfis/{id}/permissoes", "Admin\\RolePermissionController@edit");
$router->post("/perfis/{id}/permissoes", "Admin\\RolePermissionController@update");

/*
|--------------------------------------------------------------------------
| Rotas do usuário
|--------------------------------------------------------------------------
*/
$router->get("/usuarios", "Admin\\UserController@index");
$router->get("/usuarios/cadastrar", "Admin\\UserController@create");
$router->post("/usuarios/cadastrar", "Admin\\UserController@store");
$router->get("/usuarios/editar/{id}", "Admin\\UserController@edit");
$router->put("/usuarios/editar/{id}", "Admin\\UserController@update");
$router->delete("/usuarios/excluir/{id}", "Admin\\UserController@destroy");

/*
|--------------------------------------------------------------------------
| Rotas do usuário
|--------------------------------------------------------------------------
*/

$router->get("/departamentos", "Admin\\DepartmentController@index");
$router->get("/departamentos/cadastrar", "Admin\\DepartmentController@create");
$router->post("/departamentos/cadastrar", "Admin\\DepartmentController@store");
$router->get("/departamentos/editar/{id}", "Admin\\DepartmentController@edit");
$router->put("/departamentos/editar/{id}", "Admin\\DepartmentController@update");
$router->delete("/departamentos/excluir/{id}", "Admin\\DepartmentController@destroy");

/*
|--------------------------------------------------------------------------
| Rotas das categorias
|--------------------------------------------------------------------------
*/

$router->get("/categorias", "Admin\\CategoryController@index");
$router->get("/categorias/cadastrar", "Admin\\CategoryController@create");
$router->post("/categorias/cadastrar", "Admin\\CategoryController@store");
$router->get("/categorias/editar/{id}", "Admin\\CategoryController@edit");
$router->put("/categorias/editar/{id}", "Admin\\CategoryController@update");
$router->delete("categorias/excluir/{id}", "Admin\\CategoryController@destroy");
/*
|--------------------------------------------------------------------------
| Rotas das escolas
|--------------------------------------------------------------------------
*/

$router->get("/escolas", "Admin\\SchoolController@index");
$router->get("/escolas/cadastrar", "Admin\\SchoolController@create");
$router->post("/escolas/cadastrar", "Admin\\SchoolController@store");
$router->get("/escolas/editar/{id}", "Admin\\SchoolController@edit");
$router->put("/escolas/editar/{id}", "Admin\\SchoolController@update");
$router->delete("escolas/excluir/{id}", "Admin\\SchoolController@destroy");
