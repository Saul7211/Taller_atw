<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use PDO;

class AuthorRepository implements RepositoryInterface {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todos los autores.
     *
     * @return Author[]
     */
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM author");
        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    /**
     * Buscar autor por ID.
     *
     * @param int $id
     * @return Author|null
     */
    public function findById(int $id): ?object {
        $sql = "SELECT * FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Crear un autor.
     *
     * @param object $entity
     * @return bool
     */
    public function create(object $entity): bool {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException("Author expected");
        }

        $sql = "INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation) 
                VALUES (:fn, :lne, :usrn, :email, :paswd, :orcid, :aff)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':fn'     => $entity->getFirstName(),
            ':lne'    => $entity->getLastName(),
            ':usrn'   => $entity->getUsername(),
            ':email'  => $entity->getEmail(),
            ':paswd'  => $entity->getPassword(),
            ':orcid'  => $entity->getOrcid(),
            ':aff'    => $entity->getAffiliation()
        ]);
    }

    /**
     * Actualizar un autor.
     *
     * @param object $entity
     * @return bool
     */
    public function update(object $entity): bool {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException("Author expected");
        }

        $sql = "UPDATE author SET 
                    first_name = :fn, 
                    last_name = :lne, 
                    username = :usrn, 
                    email = :email,
                    password = :paswd,
                    orcid = :orcid,
                    affiliation = :aff 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'     => $entity->getId(),
            ':fn'     => $entity->getFirstName(),
            ':lne'    => $entity->getLastName(),
            ':usrn'   => $entity->getUsername(),
            ':email'  => $entity->getEmail(),
            ':paswd'  => $entity->getPassword(),
            ':orcid'  => $entity->getOrcid(),
            ':aff'    => $entity->getAffiliation()
        ]);
    }

    /**
     * Eliminar un autor.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM author WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Convertir el resultado de la consulta en un objeto Author.
     *
     * @param array $row
     * @return Author
     */
    private function hydrate(array $row): Author {
        $author = new Author(
            (int)$row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal', // Password temporal solo para instanciar
            $row['orcid'],
            $row['affiliation']
        );

        // Reemplazar el hash original sin regenerar
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']);

        return $author;
    }
}
?>
