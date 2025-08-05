<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Libro;
use App\Repositories\BookRepository;
use App\Entities\Author;
use App\Repositories\AuthorRepository;

class BookController {
    private BookRepository $bookRepository;
    private AuthorRepository $authorRepository;

    public function __construct() {
        // Asignar la instancia de BookRepository a la propiedad bookRepository
        $this->bookRepository = new BookRepository();
    }

    public function handle(): void {
        header('Content-Type: application/json');
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            if(isset($_GET['id'])){
                $book =$this->bookRepository->findByid((int)$_GET['id']);
                echo json_encode($book?$this->bookToArray($book):null);
            }else{
                 // Llamamos al repositorio y mapeamos los resultados a un array
            $list = array_map([$this, 'bookToArray'], 
                $this->bookRepository->findAll()); // Ahora la propiedad está correctamente inicializada
                echo json_encode($list);

            }
                return;
        }

        $payload = json_decode(file_get_contents('php://input'), true);

        if($method === 'POST'){
            $author = $this->authorRepository->findById((int)$payload['author id'] ?? 0);
            if(!$author) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid Author']);
                return;
            }

            if ($method === 'PUT') {
            $id = (int)($payload['id'] ?? 0);
            $existing= $this->bookRepository->findByid($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Book not found']);
                return;
            }
            if($payload['authorId']){
                $author = $this->authorRepository->findById((int)$payload['authorId']);
                if (!$author) $existing->setAuthor($author);


            }
            if(isset($payload['title'])) {
                $existing->setTitle($payload['title']);

            }
            if(isset($payload['iddescription'])) {
                $existing->setTitle($payload['iddescription']);

            }
            if(isset($payload['publicationDate'])) {
                $existing->setTitle($payload['publicationDate']);

            }
            if($payload['isbn']){
                $existing->setTitle($payload['isbn']);

            }

            if($payload['genre']){
                $existing->setTitle($payload['genre']);

            }

            echo json_encode(['success' => $this->bookRepository->update($existing)]);
            return;
            
        }

        if ($method === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $existing = $this->bookRepository->findById($id);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['error' => 'Book not found']);
                return;
            }
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

            $book = new Libro(
                0, // ID será asignado por la base de datos
                $payload['title'] ?? '',
                $payload['iddescription'] ?? '',
                new \DateTime($payload['publicationDate'] ?? 'now'),
                $author,
                $payload['isbn'] ?? '',
                $payload['genre'] ?? '',
                (int)($payload['edition'] ?? 1)
            );

            echo json_encode(['created' => $this->bookRepository->create($book)]);
            

    }

    
    }


    public function bookToArray(Libro $book): array {
        // Convertir el objeto Libro en un array para respuesta JSON
        return [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'iddescription' => $book->getIdDescription(),
            'publicationDate' => $book->getPublicationDate()->format('Y-m-d'),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'firstName' => $book->getAuthor()->getFirstName(),
                'lastName' => $book->getAuthor()->getLastName(),
            ],
            'isbn' => $book->getIsbn(),
            'genre' => $book->getGenre(),
            'edition' => $book->getEdition()
        ];
    }
}
?>
