<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use App\Interfaces\RepositoryInterface;
use App\Entities\Author;
use App\Entities\Libro;
use PDO;

class BookRepository implements RepositoryInterface {

    private PDO $db;
    private AuthorRepository $authorRepo;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->authorRepo = new AuthorRepository();
    }

    public function findAll(): array {
        // Llamar al procedimiento almacenado
        $stmt = $this->db->query("CALL sp_book_list();");
        $rows = $stmt->fetchAll();
        $stmt->closeCursor();

        $list = [];
        foreach ($rows as $row) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Libro) {
            throw new \InvalidArgumentException("Book expected");
        }

        $stmt = $this->db->prepare("CALL sp_create_book(:t,:d,:dt,:aid,:i,:g,:ed)");
        $ok = $stmt->execute(
            [
                ':t'     => $entity->getTitle(),
                ':d'     => $entity->getIdDescription(),
                ':dt'    => $entity->getPublicationDate()->format('Y-m-d'),
                ':aid'   => $entity->getAuthor()->getId(),
                ':i'     => $entity->getIsbn(),
                ':g'     => $entity->getGenre(),
                ':ed'    => $entity->getEdition()
            ]
            );

            if ($ok) {
                $stmt->fetch();
            }
            $stmt->closeCursor();
            return $ok;
        

    }


    /**
     * Actualizar un libro (aún no implementado)
     * 
     * @param object $entity
     * @return bool
     */
    public function update(object $entity):bool {
        if (!$entity instanceof Libro) {
            throw new \InvalidArgumentException("Book expected");
        }
        $stmt = $this->db->prepare("CALL sp_update_book(:id,:t,:d,:dt,:aid,;i,:g,:ed)");
        $ok = $stmt->execute([
            ':id' => $entity->getId(),
            ':t' => $entity->getTitle(),
            ':d' => $entity->getIdDescription(),
            ':dt' => $entity->getPublicationDate()->format('Y-m-d'),
            ':aid' => $entity->getAuthor()->getId(),
            ':i' => $entity->getIsbn(),
            ':g' => $entity->getGenre(),
            ':ed' => $entity->getEdition()
        ]);

        if ($ok) {
            $stmt->fetchAll();
        }
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("CALL sp_delete_book(:id)");
        $ok = $stmt->execute([':id' => $id]);

        if ($ok) {
            $stmt->fetchAll();
        }

        $stmt->closeCursor();

        return $ok;
    }

    
    public function findByid(int $id): ?object {

        $stmt = $this->db->prepare("CALL sp_find_book(:id)");
        $ok = $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        $stmt->closeCursor();


        return $row ? $this->hydrate($row) : null;
    }

    private function hydrate(array $row): Libro {
        // Verificación de si los índices existen en el array con valores predeterminados
        $authorId = isset($row['id']) ? (int)$row['id'] : 0;
        $firstName = isset($row['first_name']) ? $row['first_name'] : '';
        $lastName = isset($row['last_name']) ? $row['last_name'] : '';
        $username = isset($row['username']) ? $row['username'] : ''; // Valor predeterminado ''
        $email = isset($row['email']) ? $row['email'] : '';
        $password = isset($row['password']) ? $row['password'] : 'temporal'; // Valor predeterminado 'temporal'
        $orcid = isset($row['orcid']) ? $row['orcid'] : '';
        $affiliation = isset($row['affiliation']) ? $row['affiliation'] : '';

        // Crear el objeto Author
        $author = new Author(
            $authorId,
            $firstName,
            $lastName,
            $username,
            $email,
            $password,  // Usando 'temporal' si no se encuentra
            $orcid,
            $affiliation
        );

        // Reemplazar el hash original del password sin regenerarlo
        $ref = new \ReflectionClass($author);
        $prop = $ref->getProperty('password');
        $prop->setAccessible(true);
        $prop->setValue($author, $password);  // Asignamos el password real

        // Verificación de si los índices del libro están presentes
        if (!isset($row['publication_id'], $row['title'], $row['description'], $row['publication_date'], $row['isbn'], $row['genre'], $row['edition'])) {
            throw new \InvalidArgumentException("Faltan datos necesarios para crear el libro.");
        }

        // Crear y retornar el objeto Libro
        return new Libro(
            (int)$row['publication_id'],  // id
            $row['title'],                 // title
            $row['description'],           // iddescription
            new \DateTime($row['publication_date']), // publicationDate
            $author,                       // author (objeto Author)
            $row['isbn'],                  // isbn
            $row['genre'],                 // genre
            (int)$row['edition']           // edition
        );
    }
}
