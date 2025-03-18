<?php
require "../vendor/autoload.php";
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

// Configuración de la base de datos con Eloquent ORM
$capsule = new Capsule();
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'symblog', // Reemplaza con el nombre real de tu BD
    'username'  => 'root', // Usuario de la BD
    'password'  => '', // Contraseña de la BD
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Modelo para la tabla 'blogs'
class Blog extends Model {
    protected $table = 'blog';
    protected $fillable = ['title', 'author', 'blog', 'image', 'tags'];
    public $timestamps = false; // Desactiva el uso de created_at y updated_at
}


// Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $author = $_POST['author'] ?? null;
    $blog = $_POST['blog'] ?? null;
    $tags = $_POST['tags'] ?? null;
    $imagePath = null;

    // Manejo de subida de imágenes
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePath = $uploadDir . basename($_FILES["image"]["name"]);
        
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            die("❌ Error al subir la imagen.");
        }
    }

    // Guardado en la base de datos
    try {
        $post = Blog::create([
            'title' => $title,
            'author' => $author,
            'blog' => $blog,
            'image' => $imagePath,
            'tags' => $tags
        ]);

        if ($post) {
            echo "<p style='color: green;'>✅ Post guardado correctamente.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error al guardar el post: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Irish+Grover' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=La+Belle+Aurore' rel='stylesheet' type='text/css'>
    <link href="css/screen.css" type="text/css" rel="stylesheet" />
    <link href="css/sidebar.css" type="text/css" rel="stylesheet" />
    <link href="css/blog.css" type="text/css" rel="stylesheet" />
    <link href="css/web.css" type="text/css" rel="stylesheet" />
    <link rel="shortcut icon" href="img/favicon.ico" />
</head>

<body>
    <section id="wrapper">
        <header id="header">
            <div class="top">
                <nav>
                    <ul class="navigation">
                        <li><a href="index_sb.php">Home</a></li>
                        <li><a href="about_sb.php">About</a></li>
                        <li><a href="contact_sb.php">Contact</a></li>
                    </ul>
                </nav>
            </div>
            <hgroup>
                <h2><a href="index_sb.php/">symblog</a></h2>
                <h3><a href="index_sb.php/">creating a blog in Symfony2</a></h3>
            </hgroup>
        </header>
        
        <section class="main-col">
            <h2>Add a New Blog Post</h2>
            <form action="addBlog.php" method="post" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required><br><br>

                <label for="blog">Content:</label>
                <textarea id="blog" name="blog" rows="5" required></textarea><br><br>

                <label for="tags">Tags (comma separated):</label>
                <input type="text" id="tags" name="tags"><br><br>

                <label for="author">Author:</label>
                <input type="text" id="author" name="author" required><br><br>

                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept="image/*"><br><br>

                <button type="submit">Submit</button>
            </form>
        </section>
        
        <aside class="sidebar">
            <section class="section">
                <header>
                    <h3>Tag Cloud</h3>
                </header>
                <p class="tags">
                    <span class="weight-1">magic</span>
                    <span class="weight-5">symblog</span>
                    <span class="weight-4">movie</span>
                </p>
            </section>
            <section class="section">
                <header>
                    <h3>Latest Comments</h3>
                </header>
                <article class="comment">
                    <header>
                        <p class="small"><span class="highlight">Carlos Aguilera</span> commented on
                            <a href="#">A day with Symfony2</a>
                        </p>
                    </header>
                    <p>Comentario Usuario</p>
                </article>
            </section>
        </aside>
        
        <div id="footer">
        symblog - creado por <a href="#">Daniel Fernández Balsera</a>
        </div>
    </section>
</body>

</html>
