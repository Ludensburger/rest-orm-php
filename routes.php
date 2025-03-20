<?php
return [
    ['method' => 'GET', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->getAllUsers();
    }],
    ['method' => 'GET', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->getUserById($id);
    }],
    ['method' => 'POST', 'path' => '/users', 'handler' => function () use ($controller) {
        return $controller->createUser();
    }],
    ['method' => 'PUT', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->updateUser($id);
    }],
    ['method' => 'DELETE', 'path' => '/users/{id}', 'handler' => function ($id) use ($controller) {
        return $controller->deleteUser($id);
    }],
];