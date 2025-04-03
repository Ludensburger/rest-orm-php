<?php
// Define authentication middleware function
$authenticate = function($next) use ($authMiddleware) {
    return $authMiddleware->authenticate($next);
};

return [
    // Auth routes (public)
    ['method' => 'POST', 'path' => '/auth/register', 'handler' => function () use ($authController) {
        return $authController->register();
    }],
    ['method' => 'POST', 'path' => '/auth/login', 'handler' => function () use ($authController) {
        return $authController->login();
    }],
    
    // Protected User routes
    ['method' => 'GET', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->getAllUsers();
    }, 'middleware' => $authenticate],
    
    ['method' => 'GET', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->getUserById($id);
    }, 'middleware' => $authenticate],
    
    ['method' => 'POST', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->createUser();
    }, 'middleware' => $authenticate],
    
    ['method' => 'PUT', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->updateUser($id);
    }, 'middleware' => $authenticate],
    
    ['method' => 'DELETE', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->deleteUser($id);
    }, 'middleware' => $authenticate]
];