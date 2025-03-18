<?php
session_start();
if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = false;
}
require('../vendor/autoload.php');
require "../bootstrap.php";

use App\Controllers\BlogsController;
use App\Controllers\UsersController;
use App\Models\Users;
use Aura\Router\RouterContainer;

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

// Crear un contenedor de enrutador
$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

// Rutas con Aura Router
$map->get('home', '/', ['controller' => BlogsController::class, 'action' => "indexAction", 'auth' => false]); 
$map->get('about', '/about', ['controller' => BlogsController::class, 'action' => 'aboutAction', 'auth' => false]);
$map->get('addblog', '/addblog', ['controller' => BlogsController::class, 'action' => 'addBlogAction', 'auth' => true]); 
$map->get('contact', '/contact', ['controller' => BlogsController::class, 'action' => 'contactAction', 'auth' => true]); 
$map->get('show', '/show', ['controller' => BlogsController::class, 'action' => 'showAction', 'auth' => false]);
//Router para registrar usuario 
$map->post('saveUser', '/adduser', ['controller' => UsersController::class, 'action' => 'addUserAction', 'auth' => true]);
$map->get('addUser', '/adduser', ['controller' => UsersController::class, 'action' => 'addUserAction', 'auth' => true]);
//Router para loguear usuario
$map->post('login', '/login', ['controller' => UsersController::class, 'action' => 'loginAction', 'auth' => false]);
$map->get('loginForm', '/login', ['controller' => UsersController::class, 'action' => 'loginAction', 'auth' => false]);

// Router para el panel de admin 
$map->get('admin', '/admin', ['controller' => UsersController::class, 'action' => 'adminAction', 'auth' => true]);
// Router para cerrar sesión
$map->get('logout', '/logout', ['controller' => UsersController::class, 'action' => 'logoutAction', 'auth' => true]);

// Router para el formulario de registro
$map->get('registerForm', '/register', ['controller' => UsersController::class, 'action' => 'registerFormAction', 'auth' => false]);
$map->post('register', '/register', ['controller' => UsersController::class, 'action' => 'registerAction', 'auth' => false]);

// Router para añadir un blog
$map->post('saveBlog', '/addblog', ['controller' => BlogsController::class, 'action' => 'addBlogAction' , 'auth' => true]);

$map->post("Agregar comentario", "/postComment", ['controller' => BlogsController::class, 'action' => 'addCommentAction', 'auth' => false]);


// Obtener el adaptador de Aura Router
$matcher = $routerContainer->getMatcher();

// Hacer coincidir la ruta
$route = $matcher->match($request);
if (!$route) {
    echo 'error de ruta';
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $auth = $handlerData['auth'];
    $needsAuth = $handlerData['auth'] ?? false;
    $sessionAuth = $_SESSION['auth'] ?? false;


    if ($needsAuth && !$sessionAuth) {
        header('location: /login');
        exit();
    }
        $controller = new $controllerName;
        $response = $controller->$actionName($request);
        // foreach ($response->getHeaders() as $name => $values) {
        //     foreach ($values as $value) {
        //         header(sprintf("%s: %s", $name, $value), false);
        //     }
        // }
        // echo $response->getBody();
    
    
}