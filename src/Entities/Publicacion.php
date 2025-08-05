<?php
declare(strict_types=1);

namespace App\Entities;

abstract class Publicacion {
    protected int $id;
    protected string $title;
    protected string $iddescription;
    protected \DateTime $publicationDate;
    protected Author $author;


    public function __construct(
        int $id,
        string $title,
        string $iddescription,
        \DateTime $publicationDate,
        Author $author
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->iddescription = $iddescription;
        $this->publicationDate = $publicationDate;
        $this->author = $author;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getIddescription(): string {
        return $this->iddescription;
    }

    public function getPublicationDate(): \DateTime {
        return $this->publicationDate;
    }

    public function getAuthor(): Author {
        return $this->author;
    }

    // Setters
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setIddescription(string $iddescription): void {
        $this->iddescription = $iddescription;
    }

    public function setPublicationDate(\DateTime $publicationDate): void {
        $this->publicationDate = $publicationDate;
    }

    public function setAuthor(Author $author): void {
        $this->author = $author;
    }
}
?>
