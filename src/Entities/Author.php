<?php
declare(strict_types=1);

namespace App\Entities;

class Author {
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $username;
    private string $email;
    private string $password;
    private string $orcid;
    private string $affiliation;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $password,
        string $orcid,
        string $affiliation
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->orcid = $orcid;
        $this->affiliation = $affiliation;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getOrcid(): string {
        return $this->orcid;
    }

    public function getAffiliation(): string {
        return $this->affiliation;
    }

    // Setters
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $plain): void {
        //$this->password = $password;
        $this->password = password_hash($plain, PASSWORD_BCRYPT);
    }

    public function setOrcid(string $orcid): void {
        $this->orcid = $orcid;
    }

    public function setAfiliation(string $affiliation): void {
        $this->affiliation= $affiliation;
    }
}

?>
