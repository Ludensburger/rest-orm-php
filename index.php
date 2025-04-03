<?php
require_once 'init.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$host = 'localhost';
$user = 'root';
$password = 'root';
$dbname = 'rest_api';
$secretKey = '8f42a73d5c4b2a1e9f6g7h8j9k0l1m2n3o4p5q6r7s8t9u0v'; // Your generated secure key

// Initialize the user repository with connection details
$userRepository = new UserRepository($host, $user, $password, $dbname);

// Initialize the database connection (if still needed elsewhere)
$db = new Database($host, $user, $password, $dbname);

// Initialize the request object
$request = new Request();

// Initialize auth components
$authController = new AuthController($userRepository, $request, $secretKey);
$authMiddleware = new AuthMiddleware($userRepository, $secretKey);


// Initialize the user controller with dependencies
$controller = new UserController($userRepository, $request);

// Load routes
$routes = include __DIR__ . '/routes.php';

// Initialize the router
$router = new Router($request, new RouteMatcher());

// Register routes
foreach ($routes as $route) {
    $middleware = isset($route['middleware']) ? $route['middleware'] : null;
    $router->addRoute($route['method'], $route['path'], $route['handler'], $middleware);
}


// optional: try-catch block to handle exceptions
// try {

    // $userRepository->table('users')->insert([
    //     'name' => 'Donz',
    //     'email' => 'Donz@example.com'
    // ]);
    // echo "User inserted successfully.<br>";

    // Get all users
    $users = $userRepository->table('users')->getAll();
    header('Content-Type: application/json');
    echo json_encode($users, JSON_PRETTY_PRINT);

    // Get a user by ID
    // $user = $userRepository->table('users')->select()->where('id', 3)->get();
    // echo "User by ID:" . print_r($user, true);

    // Update a user
    // $userRepository->table('users')->where('id', 4)->update([
    //     'email' => 'Ludensberg@example.com'
    // ]);
    // echo "User updated successfully.<br>";

    //  Delete a user
    // $userRepository->table('users')->where('id', 2)->delete();
    // echo "User deleted successfully.<br>";



// optional: try-catch block to handle exceptions
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

// Dispatch the request
$response = $router->dispatch();

// Send the response
http_response_code($response->getStatusCode());
header('Content-Type: application/json');

// show the response body no formatting
echo $response->getBody();