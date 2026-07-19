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
('dessert', 'Beza z kremem mascarpone', 'Chrupiąca beza, lekki krem mascarpone i owoce sezonowe.', 1),
('dessert', 'Mini serniczki', 'Klasyczny, o owocach leśnych i cytrynowy — w trzech smakach.', 2),
('dessert', 'Panna cotta waniliowa', 'Aksamitna panna cotta z sosem malinowym.', 3),
('dessert', 'Babeczki dekorowane', 'Dopasowane kolorystycznie do Waszego przyjęcia.', 4),
('dessert', 'Makaroniki', 'Pastelowe odcienie, delikatne nadzienia.', 5),
('dessert', 'Tartaletki owocowe', 'Kruche spody z kremem pistacjowym i owocami.', 6),
('dessert', 'Ciasteczka lukrowane', 'Z personalizowanym motywem przyjęcia.', 7),
('dessert', 'Mini eklerki', 'Z kremem karmelowym i chrupiącą polewą.', 8),
('cake', 'Torty weselne piętrowe', 'Wielopoziomowe kompozycje, eleganckie zdobienia, kwiaty lub cukrowe detale.', 1),
('cake', 'Torty okazjonalne', 'Na komunię, chrzest czy urodziny — motyw dopasowany do tematu przyjęcia.', 2),
('cake', 'Torty bezowe', 'Lekkie, chrupiące spody bezowe przełożone kremem i owocami.', 3),
('cake', 'Torty z owocami sezonowymi', 'Świeże owoce sezonowe podkreślające smak i wygląd tortu.', 4),
('cake', 'Torty urodzinowe', 'Kolorowe torty dla dzieci i dorosłych — z motywem, ulubionymi postaciami i smakami solenizanta.', 5),
('team', 'Właścicielka', 'Odkąd pamiętam, wierzę, że najlepsze chwile smakują wyjątkowo. Kuchciwróżka to moje domowe, rodzinne miejsce, w którym każdy tort i słodki stół powstaje ręcznie — z uważnością na detale i Waszą historię.', 1);

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
