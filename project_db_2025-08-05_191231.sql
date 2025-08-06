-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: project_db
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article` (
  `publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `doi` varchar(20) NOT NULL,
  `abstract` varchar(300) NOT NULL,
  `keywords` varchar(100) NOT NULL,
  `indexation` varchar(20) NOT NULL,
  `magazine` varchar(50) NOT NULL,
  `area` varchar(50) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`publication_id`),
  KEY `FK_book_id` (`book_id`),
  KEY `FK_author_id` (`author_id`),
  CONSTRAINT `FK_author_id` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`),
  CONSTRAINT `FK_book_id` FOREIGN KEY (`book_id`) REFERENCES `book` (`publication_id`) ON DELETE CASCADE,
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `orcid` varchar(200) NOT NULL,
  `affiliation` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book` (
  `publication_id` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `genre` varchar(20) NOT NULL,
  `edition` int(11) NOT NULL,
  PRIMARY KEY (`publication_id`),
  CONSTRAINT `book_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publication`
--

DROP TABLE IF EXISTS `publication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `publication_date` date NOT NULL,
  `author_id` int(11) NOT NULL,
  `type` enum('book','article') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `publication_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'project_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `sp_book_list` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_book_list`()
BEGIN
  SELECT 
    b.isbn,
    b.genre,
    b.edition,
    b.publication_id,
    p.id AS publication_id,
    p.description,
    p.publication_date,
    p.title,
    p.author_id,
    a.id AS author_id,
    a.first_name,
    a.last_name
  FROM book b
  JOIN publication p ON b.publication_id = p.id
  JOIN author a ON p.author_id = a.id
  ORDER BY p.publication_date DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_create_book` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_book`(
    IN p_title VARCHAR(255),
    IN p_description TEXT,
    IN p_publication_date DATE,
    IN p_author_id INT,
    IN p_isbn VARCHAR(20),
    IN p_genre VARCHAR(50),
    IN p_edition INT
)
BEGIN
    -- Manejo de errores: rollback en caso de fallo
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Iniciar transacción
    START TRANSACTION;

    -- Insertar en la tabla publication
    INSERT INTO publication (title, description, publication_date, author_id, type)
    VALUES (p_title, p_description, p_publication_date, p_author_id, 'book');

    -- Obtener el ID de la nueva publicación
    SET @new_pub_id = LAST_INSERT_ID();

    -- Insertar en la tabla book
    INSERT INTO book (publication_id, isbn, genre, edition)
    VALUES (@new_pub_id, p_isbn, p_genre, p_edition);

    -- Confirmar la transacción
    COMMIT;

    -- Devolver el ID de la nueva publicación
    SELECT @new_pub_id AS new_publication_id;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_find_book` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_find_book`(IN p_id INT)
BEGIN
  SELECT 
    b.isbn,
    b.genre,
    b.edition,
    b.publication_id,
    p.id AS publication_id,
    p.description,
    p.publication_date,
    p.title,
    p.author_id,
    a.id AS author_id,
    a.first_name,
    a.last_name
  FROM book b
  JOIN publication p ON b.publication_id = p.id
  JOIN author a ON p.author_id = a.id
  WHERE p.id = p_id
  ORDER BY p.publication_date DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_find_nook` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_find_nook`(IN p_id INT)
SELECT 
  b.isbn,
  b.genre,
  b.edition,
  b.publication_id,
  
  p.id AS publication_id,
  p.description,
  p.publication_date,
  p.title,
  p.author_id,
  
  a.id AS author_id,
  a.first_name,
  a.last_name
FROM book b
JOIN publication p ON b.publication_id = p.id
JOIN author a ON p.author_id = a.id
WHERE p.id = p_id
ORDER BY p.publication_date DESC ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_update_book` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_book`(
    IN p_id INT,
    IN p_title VARCHAR(255),
    IN p_description TEXT,
    IN p_publication_date DATE,
    IN p_isbn VARCHAR(20),
    IN p_genre VARCHAR(50),
    IN p_edition INT
)
BEGIN
    -- Declarar la variable para verificar si el libro existe
    DECLARE book_exists INT DEFAULT 0;

    -- Manejo de errores: rollback en caso de fallo
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    -- Verificar si el libro con el ID proporcionado existe
    SELECT COUNT(*) 
    INTO book_exists
    FROM publication p
    JOIN book b ON p.id = b.publication_id
    WHERE p.id = p_id;

    -- Si el libro no existe, lanzar una excepción
    IF book_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El libro con el ID proporcionado no existe';
    END IF;

    -- Iniciar transacción
    START TRANSACTION;

    -- Actualizar la tabla publication
    UPDATE publication
    SET title = p_title, description = p_description, publication_date = p_publication_date
    WHERE id = p_id;

    -- Actualizar la tabla book
    UPDATE book
    SET isbn = p_isbn, genre = p_genre, edition = p_edition
    WHERE publication_id = p_id;

    -- Confirmar la transacción
    COMMIT;

    -- Devolver mensaje de éxito
    SELECT 'Libro actualizado correctamente' AS message;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 19:12:44
