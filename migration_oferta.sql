SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    name VARCHAR(190) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    show_images TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (id, parent_id, name, show_images, sort_order) VALUES
(1, NULL, 'Słodki stół', 1, 1),
(2, 1, 'Tarty', 0, 1),
(3, 1, 'Deserki w kubeczkach', 0, 2),
(4, 1, 'Słodkie przekąski', 0, 3),
(5, 1, 'Monoporcje', 0, 4),
(6, 1, 'Viralowe chrupiące owoce', 0, 5),
(7, 1, 'Desery na paterze', 0, 6),
(8, NULL, 'Torty', 0, 2),
(9, 8, 'Rodzaje biszkoptów', 0, 1),
(10, 8, 'Rodzaje kremów', 0, 2),
(11, 10, 'Kremy klasyczne', 0, 1),
(12, 10, 'Kremy owocowe', 0, 2),
(13, 2, 'Tarta malinowa', 0, 1),
(14, 2, 'Tarta cytrynowa', 0, 2),
(15, 2, 'Tarta karmelowa', 0, 3),
(16, 3, 'Panna cotta', 0, 1),
(17, 3, 'Snickers', 0, 2),
(18, 4, 'Rurki z kremem', 0, 1),
(19, 4, 'Muffinki', 0, 2),
(20, 4, 'Mini bezy', 0, 3),
(21, 4, 'Donuty', 0, 4),
(22, 5, 'Serce', 0, 1),
(23, 5, 'Chmurka', 0, 2),
(24, 6, 'Malinowe', 0, 1),
(25, 6, 'Mango', 0, 2),
(26, 7, 'Tort bezowy', 0, 1),
(27, 7, 'Sernik pistacjowy', 0, 2),
(28, 11, 'Śmietankowy', 0, 1),
(29, 11, 'Czekoladowy', 0, 2),
(30, 11, 'Biała czekolada', 0, 3),
(31, 11, 'Biały michałek', 0, 4),
(32, 11, 'Pistacjowy', 0, 5),
(33, 11, 'Rafaello', 0, 6),
(34, 11, 'Ferrero Rocher', 0, 7),
(35, 11, 'Karmel', 0, 8),
(36, 11, 'Słony karmel', 0, 9),
(37, 11, 'Oreo', 0, 10),
(38, 11, 'Kinder Bueno', 0, 11),
(39, 12, 'Truskawkowy', 0, 1),
(40, 12, 'Malinowy', 0, 2),
(41, 12, 'Jagodowy', 0, 3),
(42, 12, 'Owoce leśne', 0, 4),
(43, 12, 'Czarna porzeczka', 0, 5),
(44, 12, 'Cytryna', 0, 6);

-- Kasuje stare płaskie pozycje Oferty (Słodkie stoły / Torty), zastąpione drzewem kategorii.
-- Zdjęcia tych pozycji w uploads/ zostają osierocone na dysku — nieszkodliwe, można je później
-- ręcznie posprzątać przez Menedżer plików Hostingera (uploads/), nieobowiązkowe.
DELETE FROM items WHERE type IN ('dessert', 'cake');