<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Articulo;
use App\Entities\Author;
use App\Entities\Libro;
use PDO;

class ArticleRepository implements RepositoryInterface {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Obtener todos los artículos.
     *
     * @return Articulo[]
     */
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM article");
        $list = [];
        while ($row = $stmt->fetch()) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    /**
     * Buscar artículo por ID.
     *
     * @param int $id
     * @return Articulo|null
     */
    public function findById(int $id): ?object {
        $sql = "SELECT * FROM article WHERE publication_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Crear un artículo.
     *
     * @param object $entity
     * @return bool
     */
    public function create(object $entity): bool {
        if (!$entity instanceof Articulo) {
            throw new \InvalidArgumentException("Expected instance of Articulo.");
        }

        $author_id = $entity->getAuthor()->getId();
        $book_id = $entity->getBook() ? $entity->getBook()->getId() : null;

        // Inserción del artículo sin especificar publication_id si es autoincremental
        $sql = "INSERT INTO article (title, doi, abstract, keywords, indexation, magazine, area, author_id, book_id)
                VALUES (:title, :doi, :abstract, :keywords, :indexation, :magazine, :area, :author_id, :book_id)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':title' => $entity->getTitle(),
            ':doi' => $entity->getDoi(),
            ':abstract' => $entity->getAbstract(),
            ':keywords' => $entity->getKeywords(),
            ':indexation' => $entity->getIndexation(),
            ':magazine' => $entity->getMagazine(),
            ':area' => $entity->getArea(),
            ':author_id' => $author_id,
            ':book_id' => $book_id
        ]);
    }

    /**
     * Actualizar un artículo.
     *
     * @param object $entity
     * @return bool
     */
    public function update(object $entity): bool {
        if (!$entity instanceof Articulo) {
            throw new \InvalidArgumentException("Expected instance of Articulo.");
        }

        $sql = "UPDATE article SET 
                title = :title,
                doi = :doi,
                abstract = :abstract,
                keywords = :keywords,
                indexation = :indexation,
                magazine = :magazine,
                area = :area,
                author_id = :author_id,
                book_id = :book_id
                WHERE publication_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $entity->getId(),
            ':title' => $entity->getTitle(),
            ':doi' => $entity->getDoi(),
            ':abstract' => $entity->getAbstract(),
            ':keywords' => $entity->getKeywords(),
            ':indexation' => $entity->getIndexation(),
            ':magazine' => $entity->getMagazine(),
            ':area' => $entity->getArea(),
            ':author_id' => $entity->getAuthor()->getId(),
            ':book_id' => $entity->getBook() ? $entity->getBook()->getId() : null
        ]);
    }

    /**
     * Eliminar un artículo.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM article WHERE publication_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Convertir el resultado de la consulta en un objeto Articulo.
     *
     * @param array $row
     * @return Articulo
     */
    private function hydrate(array $row): Articulo {
        // Verifica si la consulta SELECT está recuperando los campos necesarios
        $sql = "SELECT article.*, author.first_name, author.last_name, author.username, author.email, author.orcid, author.affiliation 
                FROM article
                JOIN author ON article.author_id = author.author_id
                WHERE article.publication_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $row['publication_id']]);
        $row = $stmt->fetch();

        // Verifica que los campos de autor estén en la respuesta
        if (!isset($row['first_name'])) {
            throw new \Exception("Missing author fields in the database.");
        }

        $author = new Author(
            (int)$row['author_id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal', // Password temporal solo para instanciar
            $row['orcid'],
            $row['affiliation']
        );

        // Crear el objeto Articulo y devolverlo
        return new Articulo(
            (int)$row['publication_id'],
            $row['title'],
            $row['iddescription'],
            new \DateTime($row['publication_date']),
            $author,
            $row['doi'],
            $row['abstract'],
            $row['keywords'],
            $row['indexation'],
            $row['magazine'],
            $row['area']
        );
    }
}
?>
