<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use App\Repositories\ArticleRepository;
use App\Entities\Author;
use App\Entities\Libro;
use App\Entities\Articulo;

// Crear autor (temporal)
$author = new Author(
    0,
    'Marta',
    'Jacome',
    'MJ',
    'MJ123@gmail.com',
    'sasasasas11',
    '0000-0001-2345-6789',
    'Universidad UDLA'
);

// Repositorios
$authorRepo = new AuthorRepository();
$bookRepo = new BookRepository();
$articleRepo = new ArticleRepository();

// Guardar autor
$authorRepo->create($author);

// Crear libro (temporal)
$libro = new Libro(
    3,
    'La ciudad y los perros233',
    'Novela sobre la vida militar23',
    new DateTime('2025-01-01'),
    $author,  // Relacionamos el libro con el autor
    '978-84-376-0494-7',
    'Horror',
    5
);

// Guardar libro
$bookRepo->create($libro);

// Crear artículo (temporal) relacionado con el libro
$articulo = new Articulo(
    2,
    'La guerra en la literatura',
    'Artículo que explora cómo la guerra ha sido representada en la literatura.',
    new DateTime('2025-02-01'),
    $author,  // Relacionamos el artículo con el autor
    $libro,   // Relacionamos el artículo con el libro (pasamos el objeto libro directamente)
    '10.1234/abcd.efgh',  // DOI
    'Un análisis de la representación de la guerra en obras literarias.',   
    'Guerra, Literatura, Análisis',
    'Indexado en revistas académicas',
    'Revista de Literatura',
    'Literatura Militar'
);

// Guardar artículo
$articleRepo->create($articulo);

// Mostrar todos los autores
echo "=== Lista de Autores ===\n";
foreach ($authorRepo->findAll() as $a) {
    echo "- {$a->getFirstName()} {$a->getLastName()} ({$a->getUsername()})\n";
}

// Mostrar todos los libros
echo "\n=== Lista de Libros ===\n";
foreach ($bookRepo->findAll() as $b) {
    echo "- {$b->getTitle()} ({$b->getIsbn()})\n";
}

// Mostrar todos los artículos
echo "\n=== Lista de Artículos ===\n";
foreach ($articleRepo->findAll() as $a) {
    // Acceder al ISBN desde el libro relacionado con el artículo
    $isbn = $a->getBook() ? $a->getBook()->getIsbn() : 'No ISBN'; // Verifica si existe el libro y su ISBN
    echo "- {$a->getTitle()} ({$isbn})\n";
}

?>
