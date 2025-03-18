<?php

namespace App\Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\User;
use App\Models\Comment;
use App\Models\Blog;

class UsersController extends BaseController
{
    private $twig;

    public function __construct()
    {
        // Configurar Twig
        $loader = new FilesystemLoader('../app/Views');
        $this->twig = new Environment($loader);
    }
    // Función getData para obtener los blogs, comentarios y tags
    public function getData()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        $comments = Comment::orderBy('created_at', 'desc')->take(5)->get(); // Obtener los últimos 5 comentarios
        $tags = Blog::distinct()->pluck('tags'); // Obtener todos los tags únicos

        $user = null;
        if (isset($_SESSION['userId'])) {
            $user = User::find($_SESSION['userId']);
        }
        // Guardar los datos en un array
        $data = [
            'blogs' => $blogs,
            'comments' => $comments,
            'tags' => $tags,
            'auth' => $_SESSION['auth'] ?? false,
            'user' => $user
        ];
        return $data;
    }

    // Función addUserAction para añadir un usuario
    public function addUserAction()
    {
        if (isset($_POST['submit']) && $_SESSION['auth'] == true) {
            $user = User::create([
                'nombre' => $_POST['nombre'],
                'user' => $_POST['user'],
                'email' => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'perfil' => $_POST['perfil'] ?? 'user' // Asignar 'user' por defecto si no se proporciona
            ]);
            $_SESSION['userId'] = $user->id;
            $_SESSION['auth'] = true;
            header('Location: /');
            exit();
        }
        $data = $this->getData();
        echo $this->twig->render('add_user_view.twig',  [
            'data' => $data,
        ]);
    }

    // Función loginAction para iniciar sesión
    public function loginAction()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth'] == false) {
            if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['password'])) {
            $user = User::where('email', $_POST['email'])->first();
            if ($user && password_verify($_POST['password'], $user->password)) {
                $_SESSION['auth'] = true;
                $_SESSION['userId'] = $user->id;
                header('Location: /');
                exit();
            } else {
                echo 'Usuario o contraseña incorrectos';
            }
            }
            $data = $this->getData();
            echo $this->twig->render('login_view.twig',  [
            'data' => $data,
            ]);
        } else {
            header('Location: /');
        }
    }

    // Función logoutAction para cerrar sesión
    public function logoutAction()
    {
        session_unset();
        session_destroy();
        header('Location: /');
    }

    // Función adminAction para mostrar la vista de administrador
    public function adminAction()

    {
        $data = $this->getData();
        if ($_SESSION['auth'] == true) {
            echo $this->twig->render('admin_view.twig',  [
                'data' => $data,
            ]);
        } else {
            header('Location: /');
        }
    }

    // Función registerFormAction para mostrar el formulario de registro
    public function registerFormAction()
    {
        $data = $this->getData();
        echo $this->twig->render('add_user_view.twig', [
            'data' => $data,
        ]);
    }

    // Función registerAction para registrar un usuario
    public function registerAction()
    {
        // Comprobar si se ha enviado el formulario
        if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['user']) && !empty($_POST['nombre'])) {
            $user = User::where('email', $_POST['email'])->first();
            if (!$user) {
                $user = User::create([
                    'nombre' => $_POST['nombre'],
                    'user' => $_POST['user'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'perfil' => $_POST['perfil'] ?? 'user' 
                ]);
                // Iniciar sesión
                if ($_SESSION['auth'] == true) {
                    header('Location: /admin');
                } else {
                    header('Location: /login');
                }
                exit();
            } else {
                echo 'El correo electrónico ya está registrado.';
            }
        }
        $data = $this->getData();
        echo $this->twig->render('add_user_view.twig', [
            'data' => $data,
        ]);
    }
}