-- Import przez phpMyAdmin do JUŻ ISTNIEJĄCEJ bazy (wybierz bazę przed importem).
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    name VARCHAR(64) PRIMARY KEY,
    value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('dessert','cake','team') NOT NULL,
    name VARCHAR(190) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(60) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote TEXT NOT NULL,
    author VARCHAR(190) NOT NULL,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT DEFAULT NULL,
    name VARCHAR(190) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    show_images TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (name, value) VALUES
('hero_headline', 'Bajkowe torty i słodkie stoły
na Wasze wyjątkowe chwile'),
('hero_subline', 'Wesela, komunie, chrzciny, urodziny — słodycze szyte na miarę Waszej uroczystości, z dbałością o każdy detal.'),
('counter_enabled', '0'),
('counter_value', '250'),
('counter_label', 'zadowolonych klientek i klientów'),
('instagram_url', 'https://www.instagram.com/torty.kuchciwrozki/'),
('instagram_handle', '@torty.kuchciwrozki'),
('messenger_url', 'https://m.me/torty.kuchciwrozki'),
('delivery_area', 'Warka i okolice'),
('delivery_reach', 'Radom, okolice Warszawy'),
('seo_title', 'Torty Kuchciwróżki — torty artystyczne i słodkie stoły | Warka, Radom, Warszawa'),
('seo_description', 'Ręcznie robione torty na wesela, komunie, chrzciny i urodziny oraz słodkie stoły. Warka i okolice, dojazd do Radomia i Warszawy. Wycena indywidualna.')
ON DUPLICATE KEY UPDATE value=VALUES(value);

INSERT INTO items (type, name, description, sort_order) VALUES
('team', 'Właścicielka', 'Odkąd pamiętam, wierzę, że najlepsze chwile smakują wyjątkowo. Kuchciwróżka to moje domowe, rodzinne miejsce, w którym każdy tort i słodki stół powstaje ręcznie — z uważnością na detale i Waszą historię.', 1);

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

INSERT INTO testimonials (quote, author, sort_order) VALUES
('Tort przeszedł nasze najśmielsze oczekiwania — piękny i pyszny zarazem. Goście pytali, gdzie go zamówiliśmy!', 'Ania, wesele w Warce', 1),
('Słodki stół zachwycił wszystkich, a bezy z mascarpone zniknęły jako pierwsze.', 'Kasia, komunia syna', 2),
('Profesjonalizm, terminowość i mnóstwo serca w każdym detalu. Polecam z całego serca.', 'Magda, urodziny córki', 3);

INSERT INTO faq (question, answer, sort_order) VALUES
('Z jakim wyprzedzeniem złożyć zamówienie?', 'Najlepiej 3–4 tygodnie wcześniej, a w sezonie ślubnym (maj–wrzesień) nawet 2–3 miesiące.', 1),
('Jaki jest obszar dostawy?', 'Dojeżdżamy do Warki i okolic, a także do Radomia i w rejon Warszawy — koszt dostawy ustalany indywidualnie.', 2),
('Czy uwzględniacie alergie i diety?', 'Tak, przygotowujemy warianty bezglutenowe, wegańskie oraz bez orzechów — zgłoś to przy zamówieniu.', 3),
('Jak wygląda wycena?', 'Każdy tort i słodki stół wyceniamy indywidualnie na podstawie liczby gości, smaków i zdobień.', 4),
('Czy mogę zamówić degustację?', 'Tak, degustację można umówić przed większymi zamówieniami weselnymi.', 5),
('Jak przechowywać i podawać tort?', 'Zalecamy przechowywanie w chłodnym miejscu i wyjęcie z lodówki ok. 30 minut przed podaniem.', 6);

INSERT INTO gallery (tag, image, sort_order) VALUES
('Wesele', NULL, 1),
('Wesele', NULL, 2),
('Wesele', NULL, 3),
('Komunia', NULL, 1),
('Komunia', NULL, 2),
('Komunia', NULL, 3),
('Chrzciny', NULL, 1),
('Chrzciny', NULL, 2),
('Chrzciny', NULL, 3),
('Urodziny', NULL, 1),
('Urodziny', NULL, 2),
('Urodziny', NULL, 3);
