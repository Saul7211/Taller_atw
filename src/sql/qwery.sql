CREATE TABLE author (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL,
  orcid VARCHAR(200) NOT NULL,
  affiliation VARCHAR(50) NOT NULL -- corregido nombre
);

CREATE TABLE publication (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  description VARCHAR(100) NOT NULL,
  publication_date DATE NOT NULL,
  author_id INT NOT NULL,
  type ENUM('book', 'article') NOT NULL,
  FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE
);

CREATE TABLE book (
  publication_id INT PRIMARY KEY, -- eliminado AUTO_INCREMENT
  isbn VARCHAR(20) NOT NULL,
  genre VARCHAR(20) NOT NULL,
  edition INT NOT NULL,
  FOREIGN KEY (publication_id) REFERENCES publication(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
);

CREATE TABLE article (
  publication_id INT PRIMARY KEY, 
  doi VARCHAR(20) NOT NULL,
  abstract VARCHAR(300) NOT NULL,
  keywords VARCHAR(100) NOT NULL,
  indexation VARCHAR(20) NOT NULL,
  magazine VARCHAR(50) NOT NULL,
  area VARCHAR(50) NOT NULL,
  FOREIGN KEY (publication_id) REFERENCES publication(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
);

CALL sp_book_list();

CALL sp_find_book(1)

CALL sp_update_book(2, '978-1234567891', 'Horror', 3);

CALL sp_delete_book(1);

SELECT * FROM author;
INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation)
VALUES (
  'Tim', 
  'Berners-Lee', 
  'tberners', 
  'timbl@w3.org', 
  'webinventor123', 
  '0000-0001-9999-0000', 
  'Massachusetts Institute of Technology'
);
INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation)
VALUES (
  'Margaret',
  'Hamilton',
  'mhamilton',
  'hamilton@nasa.gov',
  '$2y$10$EjemploDeHashDiferenteParaSeguridadABCDEF', 
  '0000-0002-8888-4444',
  'NASA'
);

-- Autor 3: Ada Lovelace
INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation)
VALUES (
  'Ada',
  'Lovelace',
  'alovelace',
  'ada@analyticalengine.com',
  '$2y$10$PasswordSeguraParaAda999999999999999999999', -- HASH
  '0000-0003-1234-5678',
  'Analytical Engine Society'
);

--Publicacion

INSERT INTO publication (title, description, publication_date, author_id, type
) VALUES ('Weaving the Web', 'Libro donde Tim Berners-Lee narra la invención de la World Wide Web y su impacto en la sociedad', '1999-10-01', 1, 'book');

-- Publicación 2: The Innovators
INSERT INTO publication (
  title, 
  description, 
  publication_date, 
  author_id, 
  type
) VALUES (
  'The Innovators', 
  'Libro sobre los pioneros que hicieron posible la revolución digital, desde Ada Lovelace hasta los creadores de Internet', 
  '2014-10-07', 
  2, 
  'book'
);

-- Publicación 3: Code: The Hidden Language
INSERT INTO publication (
  title, 
  description, 
  publication_date, 
  author_id, 
  type
) VALUES (
  'Code: The Hidden Language of Computer Hardware and Software', 
  'Explora los fundamentos de los sistemas digitales, desde el código Morse hasta la arquitectura moderna de computadoras', 
  '2000-01-01', 
  3, 
  'book'
);


--Libro 

-- Libro 2: The Innovators
INSERT INTO book (
  publication_id, 
  isbn, 
  genre, 
  edition
) VALUES (
  2, -- ID de la publicación "The Innovators"
  '9781476708690', 
  'Historia de la computación', 
  1
);

-- Libro 3: Code: The Hidden Language
INSERT INTO book (
  publication_id, 
  isbn, 
  genre, 
  edition
) VALUES (
  3, -- ID de la publicación "Code: The Hidden Language..."
  '9780735611313', 
  'Tecnología', 
  2
);

--CONSULTA CON JOIN

SELECT 
  b.publication_id,
  p.title,
  p.description,
  p.publication_date,
  b.isbn,
  b.genre,
  b.edition
FROM book b
JOIN publication p ON b.publication_id = p.id;

--CONSULTA MEDIANTE INNER JOIN
SELECT 
  b.publication_id,
  p.title,
  p.description,
  p.publication_date,
  b.isbn,
  b.genre,
  b.edition,
  a.first_name,
  a.last_name,
  a.email
FROM book b
INNER JOIN publication p ON b.publication_id = p.id
INNER JOIN author a ON p.author_id = a.id;

SELECT 
  ar.publication_id,
  p.title,
  p.description,
  p.publication_date,
  ar.doi,
  ar.abstract,
  ar.keywords,
  ar.indexation,
  ar.magazine,
  ar.area,
  a.first_name,
  a.last_name,
  a.email
FROM article ar
INNER JOIN publication p ON ar.publication_id = p.id
INNER JOIN author a ON p.author_id = a.id;

SELECT 
  b.publication_id,
  p.title,
  p.publication_date,
  b.isbn,
  b.genre,
  b.edition
FROM book b
INNER JOIN publication p ON b.publication_id = p.id;

INSERT INTO article (
  publication_id, doi, abstract, keywords, indexation, magazine, area
) VALUES
(1, '10.1000/abc001', 'Estudio del impacto de la inteligencia artificial en la educación superior.', 'IA, educación, aprendizaje', 'Scopus', 'Revista Tecnología Educativa', 'Educación'),
(2, '10.1000/abc002', 'Análisis de algoritmos de cifrado simétrico en sistemas modernos.', 'seguridad, cifrado, AES', 'Latindex', 'Revista Seguridad Informática', 'Informática');

SELECT * FROM book


CALL sp_book_list();

CALL sp_find_nook(1);

CALL sp_create_book(
    1,  
    '978-1234567890',  
    'Ficción',         
    1                  
);


SELECT * FROM article WHERE publication_id = 1;

ALTER TABLE article ADD COLUMN book_id INT;

ALTER TABLE article 
ADD COLUMN author_id INT; -- Relación con autor

DESCRIBE article;

ALTER TABLE article ADD COLUMN title VARCHAR(255);

ALTER TABLE article MODIFY COLUMN publication_id INT AUTO_INCREMENT;
DESCRIBE author;

SELECT * FROM publication WHERE id = 1;  -- Usar el id correspondiente

ALTER TABLE article MODIFY COLUMN publication_id INT;

ALTER TABLE article ADD CONSTRAINT FK_book_id FOREIGN KEY (book_id) REFERENCES book(publication_id) ON DELETE CASCADE;


ALTER TABLE article ADD CONSTRAINT FK_author_id FOREIGN KEY (author_id) REFERENCES author(id);

SELECT author_id FROM article WHERE author_id NOT IN (SELECT id FROM author);

DELETE FROM article WHERE author_id NOT IN (SELECT id FROM author);

SELECT * FROM publication WHERE id = :publication_id;



ALTER TABLE article ADD CONSTRAINT FK_author_id FOREIGN KEY (author_id) REFERENCES author(id);

SHOW CREATE TABLE article;

CONSTRAINT `FK_author_id` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`)

SELECT COUNT(*) FROM publication WHERE id = :id;


