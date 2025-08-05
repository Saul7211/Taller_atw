<?php
declare(strict_types=1);

namespace App\Config; // el proyecto se llama App

use PDO;

class Database {

    private static ?\PDO $instance = null;
    
    public static function getConnection(): PDO {
        if(self::$instance === null){
            $host     = 'localhost';
            $dbName   = 'project_db';
            $username = 'root';
            $password = '';
            $charset  = 'utf8mb4';

            // Corregir el error tipográfico en el DSN
            $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}"; // Corregido: $charset dentro de llaves

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // para que lance excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // para que devuelva los resultados como un array asociativo
                PDO::ATTR_EMULATE_PREPARES   => false, // para que no emule las consultas preparadas
            ];

            self::$instance = new PDO($dsn, $username, $password, $options);
        }

        return self::$instance;
    }
}
?>
