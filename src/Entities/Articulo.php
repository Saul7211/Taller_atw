<?php
namespace App\Entities;
class Articulo extends Publicacion {
    private string $doi;
    private string $abstract;
    private string $keywords;
    private string $indexation;
    private string $magazine;
    private string $area;

    private ?Libro $book = null;  // Relación con libro
    protected Author $author;  // Relación con autor

    public function __construct(
        int $id, 
        string $title, 
        string $iddescription, 
        \DateTime $publicationDate, 
        Author $author,
        ?Libro $book = null,  // Relación con Libro
        string $doi = '', 
        string $abstract = '', 
        string $keywords = '', 
        string $indexation = '', 
        string $magazine = '', 
        string $area = ''
    ) {
        parent::__construct($id, $title, $iddescription, $publicationDate, $author);
        $this->book = $book;
        $this->doi = $doi;
        $this->abstract = $abstract;
        $this->keywords = $keywords;
        $this->indexation = $indexation;
        $this->magazine = $magazine;
        $this->area = $area;
    }

    // Getters
    public function getDoi(): string {
        return $this->doi;
    }

    public function getAbstract(): string {
        return $this->abstract;
    }

    public function getKeywords(): string {
        return $this->keywords;
    }

    public function getIndexation(): string {
        return $this->indexation;
    }

    public function getMagazine(): string {
        return $this->magazine;
    }

    public function getArea(): string {
        return $this->area;
    }

    public function getBook(): ?Libro {
        return $this->book;
    }

    // Setters
    public function setDoi(string $doi): void {
        if (empty($doi)) {
            throw new \InvalidArgumentException("DOI cannot be empty.");
        }
        $this->doi = $doi;
    }

    public function setAbstract(string $abstract): void {
        $this->abstract = $abstract;
    }

    public function setKeywords(string $keywords): void {
        $this->keywords = $keywords;
    }

    public function setIndexation(string $indexation): void {
        $this->indexation = $indexation;
    }

    public function setMagazine(string $magazine): void {
        $this->magazine = $magazine;
    }

    public function setArea(string $area): void {
        $this->area = $area;
    }

    public function setBook(?Libro $book): void {
        $this->book = $book;
    }
}
?>