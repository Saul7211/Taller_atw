<?php

declare(strict_types=1);    

namespace App\Repositories;

use App\Config\Database;
use App\Entities\Camioneta;
use App\Interfaces\RepositoryInterface;
use PDO;

class CamionetaRepository implements RepositoryInterface {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create (object $entity): bool{

    }
    public function update(object $entity): bool{

    }

    public function findAll(): array{

        $stmt = $this->db->query("CALL sp_Camioneta_list()");
        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = $row; // Aquí deberías hidratar el objeto si tienes una entidad Camioneta
        }
        return $list;
    }

    private function hydrate(array $row): Camioneta {
        // Aquí deberías crear y devolver una instancia de Camioneta a partir de los datos de $row
        // Por ejemplo:
        // return new Camioneta($row['id'], $row['model'], ...);
        return new Camioneta(
            (int)$row['auto_id'],
            $row['marca'],
            $row['modelo'],
            (int)$row['color'],
            (float)$row['anio'],
            $row['cabina'],
            (float)$row['capacidad_carga']



        ); 
    }

    
    public function findByid(int $id): ?object {

        $stmt = $this->db->prepare("CALL sp_find_camioneta(:id)");
        $ok = $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();


        return $row ? $this->hydrate($row) : null;
    }

        public function delete(int $id): bool {
        $stmt = $this->db->prepare("CALL sp_delete_Camioneta(:id)");
        $ok = $stmt->execute([':id' => $id]);

        if ($ok) {
            $stmt->fetchAll();
        }

        $stmt->closeCursor();

        return $ok;
    }



}