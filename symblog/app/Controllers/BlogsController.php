<?php

namespace App\Controllers;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Models\Blog;
use App\Models\Comment;
use Respect\Validation\Validator as v;



class BlogsController extends BaseController
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader('../app/Views');
        $this->twig = new Environment($loader);
    }

    // Función getData para obtener los blogs, comentarios y tags
    public function getData()
    {
        
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        // Cojo los 5 primeros comentarios
        $comments = Comment::orderBy('created_at', 'desc')->take(5)->get(); 
        $tags = Blog::distinct()->pluck('tags'); 
        // Guardo los datos en un array
        $data = [
            'blogs' => $blogs,
            'comments' => $comments,
            'tags' => $tags,
            'auth' => $_SESSION['auth'] ?? false
        ];
        // Devuevo los datos
        return $data; 
    }
    

    // Función indexAction que redirige a la vista principal
    public function indexAction()
    {
        $data = $this->getData();
        echo $this->twig->render('index_view.twig',  [
            'data' => $data,
        ]);
    }

    // Función aboutAction que redirige a la vista de about
    public function aboutAction()
    {
        $data = $this->getData();

        echo $this->twig->render('about_view.twig',  [
            'data' => $data,
        ]);
    }

    // Función addBlogAction que se encarga de insertar los blogs
    public function addBlogAction()
    {
        // Comprobamos el envío del formulario
        if (isset($_POST['submit'])) { 
            // Valicación de campos
            $validation = v::key('title', v::notEmpty())
                ->key('tags', v::notEmpty())
                ->key('author', v::notEmpty())
                ->key('description', v::notEmpty());
            try {
                $validation->assert($_POST);
                $image = $_FILES['image'];
                if (empty($image['name'])) {
                    $image['name'] = 'beach.jpg';
                }
                // Crear el blog con el método de eloquent
                $blog = Blog::create([
                    'title' => $_POST['title'],
                    'author' => $_POST['author'],
                    'blog' => $_POST['description'],
                    'image' => $image['name'],
                    'tags' => $_POST['tags'],
                ]);

                // Subir la imagen
                if ($image['error'] === UPLOAD_ERR_OK) {
                    $imageFileName = uniqid() . '_' . $image['name'];
                    move_uploaded_file($image['tmp_name'], "../public/img/$imageFileName");
                    $blog->image = $imageFileName;
                    $blog->save();
                }

  
                header("Location: /");
                exit();
            } catch (\Exception $e) {
                // Capturar errores y renderizar la vista con el mensaje de error
                $errors = $e->getMessage();
                $data = [
                    'errors' => $errors,
                ];
                echo $this->twig->render('addBlog_view.twig',  [
                    'data' => $data,
                ]);
            }
        }
        // Si no se envía el formulario, renderizar la vista
        $data = $this->getData();
        echo $this->twig->render('addBlog_view.twig',  [
            'data' => $data,
        ]);
    }



    // Función contactAction que redirige a la vista de contacto
    public function contactAction()
    {
        $data = $this->getData();
        echo $this->twig->render('contact_view.twig',  [
            'data' => $data,
        ]);
    }
    // Función showAction que redirige a la vista de show
    public function showAction()
    {
        $data = $this->getData();
        $data['blog'] = Blog::find($_GET['id']);
        echo $this->twig->render('show_view.twig',  [
            'data' => $data,
        ]);
    }

    // Función addCommentAction que se encarga de insertar los comentarios
    public function addCommentAction($request)
    {
        $validador = v::key('user', v::stringType()->notEmpty())
            ->key('comment', v::stringType()->notEmpty())
            ->key('blog_id', v::intVal()->positive());

        try {
            // Validar los datos del formulario
            $validador->assert($request->getParsedBody());
            echo 'Validación pasada';
            // Crear el comentario usando Mass Assignment
            Comment::create([
                'user' => $request->getParsedBody()['user'],
                'comment' => $request->getParsedBody()['comment'],
                'blog_id' => $request->getParsedBody()['blog_id']
            ]);

            // Redirigir a la vista del blog
            header("Location: /show?id=" . $request->getParsedBody()['blog_id']);
            exit; 
        } catch (\Exception $e) {
            // Capturar errores y renderizar la vista con el mensaje de error
            $error = "Error: " . $e->getMessage();
            $data = $this->getData();
            $data['blog'] = Blog::find($request->getParsedBody()['blog_id']);
            $data['error'] = $error;
            echo $this->twig->render('show_view.twig', ['data' => $data]);
        }
    }


    
}