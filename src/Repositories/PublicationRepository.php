<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Publicacion;
use App\Entities\PublicacionSimple;
use App\Entities\Author;
use PDO;

class PublicationRepository implements RepositoryInterface {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todas las publicaciones.
     *
     * @return Publicacion[]
     */
    public function findAll(): array {
        $sql = "
            SELECT p.*, 
                   a.id           AS author_id,
                   a.first_name   AS first_name,
                   a.last_name    AS last_name,
                   a.username     AS username,
                   a.email        AS email,
                   a.password     AS password,
                   a.orcid        AS orcid,
                   a.affiliation  AS affiliation
              FROM publication p
              JOIN author a ON p.author_id = a.id
        ";
        $stmt = $this->db->query($sql);
        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    /**
     * Buscar publicación por ID.
     *
     * @param int $id
     * @return Publicacion|null
     */
    public function findById(int $id): ?object {
        $sql = "
            SELECT p.*, 
                   a.id           AS author_id,
                   a.first_name   AS first_name,
                   a.last_name    AS last_name,
                   a.username     AS username,
                   a.email        AS email,
                   a.password     AS password,
                   a.orcid        AS orcid,
                   a.affiliation  AS affiliation
              FROM publication p
              JOIN author a ON p.author_id = a.id
             WHERE p.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Crear una publicación.
     *
     * @param object $entity
     * @return bool
     */
    public function create(object $entity): bool {
        if (!$entity instanceof Publicacion) {
            throw new \InvalidArgumentException("Expected instance of Publicacion.");
        }

        $sql = "
            INSERT INTO publication (title, iddescription, publication_date, author_id)
            VALUES (:title, :iddescription, :publication_date, :author_id)
        ";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':title'            => $entity->getTitle(),
            ':iddescription'    => $entity->getIddescription(),
            ':publication_date' => $entity->getPublicationDate()->format('Y-m-d'),
            ':author_id'        => $entity->getAuthor()->getId(),
        ]);
    }

    /**
     * Actualizar una publicación.
     *
     * @param object $entity
     * @return bool
     */
    public function update(object $entity): bool {
        if (!$entity instanceof Publicacion) {
            throw new \InvalidArgumentException("Expected instance of Publicacion.");
        }

        $sql = "
            UPDATE publication SET
                title            = :title,
                iddescription    = :iddescription,
                publication_date = :publication_date,
                author_id        = :author_id
             WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id'               => $entity->getId(),
            ':title'            => $entity->getTitle(),
            ':iddescription'    => $entity->getIddescription(),
            ':publication_date' => $entity->getPublicationDate()->format('Y-m-d'),
            ':author_id'        => $entity->getAuthor()->getId(),
        ]);
    }

    /**
     * Eliminar una publicación.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM publication WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Convertir el resultado de la consulta en un objeto Publicacion.
     *
     * @param array $row
     * @return Publicacion
     */
    private function hydrate(array $row): Publicacion {
        // Primero instanciamos el Author
        $author = new Author(
            (int)$row['author_id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporary',  // placeholder para password
            $row['orcid'],
            $row['affiliation']
        );
        // Restauramos el hash real de la base de datos
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $row['password']);

        // Luego la publicación
        return new PublicacionSimple(
            (int)$row['id'],
            $row['title'],
            $row['iddescription'],
            new \DateTime($row['publication_date']),
            $author
        );
    }
}
