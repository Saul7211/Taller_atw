<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Author;
use App\Repositories\AuthorRepository;

class AuthorController {
    private AuthorRepository $repo;

    public function __construct() {
        $this->repo = new AuthorRepository();
    }

    public function handle(): void {
        header('Content-Type: application/json');
        $m = $_SERVER['REQUEST_METHOD'];
        $in = json_decode(file_get_contents('php://input'), true);

        if ($m === 'GET') {
            if (isset($_GET['id'])) {
                $a = $this->repo->findById((int)$_GET['id']);
                echo json_encode($a ? $this->toArray($a) : null);
            } else {
                $all = array_map([$this, 'toArray'], $this->repo->findAll());
                echo json_encode($all);
            }
            return;
        }

        if ($m === 'POST') {
            $a = new Author(
                0,
                $in['firstName']   ?? '',
                $in['lastName']    ?? '',
                $in['username']    ?? '',
                $in['email']       ?? '',
                '', // el password lo seteamos a continuación
                $in['orcid']       ?? '',
                $in['affiliation'] ?? ''
            );
            $a->setPassword($in['password'] ?? '');
            $ok = $this->repo->create($a);
            echo json_encode(['created' => $ok]);
            return;
        }

        if ($m === 'PUT') {
            $id = (int)($in['id'] ?? 0);
            $ex = $this->repo->findById($id);
            if (!$ex) {
                http_response_code(404);
                echo json_encode(['error'=>'Author no encontrado']);
                return;
            }
            // actualizamos sólo campos presentes
            if (isset($in['firstName']))   $ex->setFirstName($in['firstName']);
            if (isset($in['lastName']))    $ex->setLastName($in['lastName']);
            if (isset($in['username']))    $ex->setUsername($in['username']);
            if (isset($in['email']))       $ex->setEmail($in['email']);
            if (isset($in['password']))    $ex->setPassword($in['password']);
            if (isset($in['orcid']))       $ex->setOrcid($in['orcid']);
            if (isset($in['affiliation'])) $ex->setAfiliation($in['affiliation']);

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

    private function toArray(Author $a): array {
        return [
            'id'          => $a->getId(),
            'firstName'   => $a->getFirstName(),
            'lastName'    => $a->getLastName(),
            'username'    => $a->getUsername(),
            'email'       => $a->getEmail(),
            'orcid'       => $a->getOrcid(),
            'affiliation' => $a->getAffiliation(),
        ];
    }
}
