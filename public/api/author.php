<?php 

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\AuthorController;


// Crear una instancia de AuthorController para manejar las solicitudes relacionadas con los autores
(new AuthorController())->handle();

?>