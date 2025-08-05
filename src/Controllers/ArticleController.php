<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Articulo;
use App\Entities\Author;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;

class ArticleController {
    private ArticleRepository $artRepo;
    private AuthorRepository  $authorRepo;

    public function __construct() {
        $this->artRepo    = new ArticleRepository();
        $this->authorRepo = new AuthorRepository();
    }

    public function handle(): void {
        header('Content-Type: application/json');
        $m = $_SERVER['REQUEST_METHOD'];

        if ($m === 'GET') {
            if (isset($_GET['id'])) {
                $a = $this->artRepo->findById((int)$_GET['id']);
                echo json_encode($a ? $this->toArray($a) : null);
            } else {
                $all = array_map([$this, 'toArray'], $this->artRepo->findAll());
                echo json_encode($all);
            }
            return;
        }

        $p = json_decode(file_get_contents('php://input'), true);

        if ($m === 'POST') {
            $auth = $this->authorRepo->findById((int)($p['authorId'] ?? 0));
            if (!$auth) {
                http_response_code(400);
                echo json_encode(['error' => 'Author inválido']);
                return;
            }

            $art = new Articulo(
                0,
                $p['title']           ?? '',
                $p['iddescription']   ?? '',
                new \DateTime($p['publicationDate'] ?? 'now'),
                $auth,
                $p['doi']             ?? '',
                $p['abstract']        ?? '',
                $p['keywords']        ?? '',
                $p['indexation']      ?? '',
                $p['magazine']        ?? '',
                $p['area']            ?? ''
            );

            $ok = $this->artRepo->create($art);
            echo json_encode(['created' => $ok]);
            return;
        }

        if ($m === 'PUT') {
            $id = (int)($p['id'] ?? 0);
            $ex = $this->artRepo->findById($id);
            if (!$ex) {
                http_response_code(404);
                echo json_encode(['error' => 'Artículo no existe']);
                return;
            }

            // Sólo sobreescribimos los campos que vengan en el payload
            if (isset($p['title']))           $ex->setTitle($p['title']);
            if (isset($p['iddescription']))   $ex->setIddescription($p['iddescription']);
            if (isset($p['publicationDate'])) {
                $ex->setPublicationDate(new \DateTime($p['publicationDate']));
            }
            if (isset($p['authorId'])) {
                $a = $this->authorRepo->findById((int)$p['authorId']);
                if (!$a) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Author inválido']);
                    return;
                }
                $ex->setAuthor($a);
            }
            if (isset($p['doi']))         $ex->setDoi($p['doi']);
            if (isset($p['abstract']))    $ex->setAbstract($p['abstract']);
            if (isset($p['keywords']))    $ex->setKeywords($p['keywords']);
            if (isset($p['indexation']))  $ex->setIndexation($p['indexation']);
            if (isset($p['magazine']))    $ex->setMagazine($p['magazine']);
            if (isset($p['area']))        $ex->setArea($p['area']);

            $ok = $this->artRepo->update($ex);
            echo json_encode(['updated' => $ok]);
            return;
        }

        if ($m === 'DELETE') {
            $id = (int)($_GET['id'] ?? 0);
            $ok = $this->artRepo->delete($id);
            echo json_encode(['deleted' => $ok]);
            return;
        }
    }

    private function toArray(Articulo $a): array {
        return [
            'id'              => $a->getId(),
            'title'           => $a->getTitle(),
            'iddescription'   => $a->getIddescription(),
            'publicationDate' => $a->getPublicationDate()->format('Y-m-d'),
            'author'          => [
                'id'        => $a->getAuthor()->getId(),
                'firstName' => $a->getAuthor()->getFirstName(),
                'lastName'  => $a->getAuthor()->getLastName(),
            ],
            'doi'          => $a->getDoi(),
            'abstract'     => $a->getAbstract(),
            'keywords'     => $a->getKeywords(),
            'indexation'   => $a->getIndexation(),
            'magazine'     => $a->getMagazine(),
            'area'         => $a->getArea(),
        ];
    }
}
