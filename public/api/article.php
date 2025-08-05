<?php 

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\ArticleController;
// Crear una instancia de ArticleController para manejar las solicitudes relacionadas con los artículos
(new ArticleController())->handle();


?>