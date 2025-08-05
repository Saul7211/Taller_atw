<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Publicacion;         // para el type‐hint en toArray()
use App\Entities\PublicacionSimple;   // la clase concreta instanciable
use App\Entities\Author;
use App\Repositories\PublicationRepository;
use App\Repositories\AuthorRepository;

class PublicationController {
    private PublicationRepository $repo;
    private AuthorRepository      $authorRepo;

    public function __construct() {
        $this->repo       = new PublicationRepository();
        $this->authorRepo = new AuthorRepository();
    }

    public function handle(): void {
        header('Content-Type: application/json');
        $m  = $_SERVER['REQUEST_METHOD'];
        $in = json_decode(file_get_contents('php://input'), true);

        if ($m === 'GET') {
            if (isset($_GET['id'])) {
                $p = $this->repo->findById((int)$_GET['id']);
                echo json_encode($p ? $this->toArray($p) : null);
            } else {
                $all = array_map([$this, 'toArray'], $this->repo->findAll());
                echo json_encode($all);
            }
            return;
        }

        if ($m === 'POST') {
            $auth = $this->authorRepo->findById((int)($in['authorId'] ?? 0));
            if (!$auth) {
                http_response_code(400);
                echo json_encode(['error' => 'Author inválido']);
                return;
            }

            // Instanciamos la subclase concreta en lugar de la abstracta
            $p = new PublicacionSimple(
                0,
                $in['title']         ?? '',
                $in['iddescription'] ?? '',
                new \DateTime($in['publicationDate'] ?? 'now'),
                $auth
            );

            $ok = $this->repo->create($p);
            echo json_encode(['created' => $ok]);
            return;
        }

        if ($m === 'PUT') {
            $id = (int)($in['id'] ?? 0);
            $ex = $this->repo->findById($id);
            if (!$ex) {
                http_response_code(404);
                echo json_encode(['error' => 'Publicación no encontrada']);
                return;
            }
            if (isset($in['title'])) {
                $ex->setTitle($in['title']);
            }
            if (isset($in['iddescription'])) {
                $ex->setIddescription($in['iddescription']);
            }
            if (isset($in['publicationDate'])) {
                $ex->setPublicationDate(new \DateTime($in['publicationDate']));
            }
            if (isset($in['authorId'])) {
                $a = $this->authorRepo->findById((int)$in['authorId']);
                if (!$a) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Author inválido']);
                    return;
                }
                $ex->setAuthor($a);
            }

            $ok = $this->repo->update($ex);
            echo json_encode(['updated' => $ok]);
            return;
        }

        if ($m === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $ok = $this->repo->delete($id);
            echo json_encode(['deleted' => $ok]);
            return;
        }
    }

    private function toArray(Publicacion $p): array {
        return [
            'id'              => $p->getId(),
            'title'           => $p->getTitle(),
            'iddescription'   => $p->getIddescription(),
            'publicationDate' => $p->getPublicationDate()->format('Y-m-d'),
            'author'          => [
                'id'        => $p->getAuthor()->getId(),
                'firstName' => $p->getAuthor()->getFirstName(),
                'lastName'  => $p->getAuthor()->getLastName(),
            ],
        ];
    }
}
