<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Controllers\BookController;

(new BookController())->handle();



// php -S localhost:8000 -t  public    con esto le decimos al servidor que solo muestre los archivos que hay 
//en public                                                               




?>