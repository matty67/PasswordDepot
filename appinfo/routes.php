<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\PasswordDepot\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'password#index', 'url' => '/passwords', 'verb' => 'GET'],
        ['name' => 'password#create', 'url' => '/passwords', 'verb' => 'POST'],
        ['name' => 'password#update', 'url' => '/passwords/{id}', 'verb' => 'PUT'],
        ['name' => 'password#delete', 'url' => '/passwords/{id}', 'verb' => 'DELETE'],
        ['name' => 'password#show', 'url' => '/passwords/{id}', 'verb' => 'GET'],
        ['name' => 'password#share', 'url' => '/passwords/{id}/share', 'verb' => 'POST'],
        ['name' => 'password#unshare', 'url' => '/passwords/{id}/share/{shareId}', 'verb' => 'DELETE'],
    ]
];