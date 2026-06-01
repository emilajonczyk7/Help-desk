-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 01, 2026 at 05:14 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `helpdesk`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Awaria sprzętu'),
(2, 'Problem z siecią / Internetem'),
(3, 'Awaria poczty e-mail'),
(4, 'Instalacja oprogramowania'),
(5, 'Uszkodzenie mechaniczne sprzętu');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `ticket_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 2, 'przyjęte zgłoszenie', '2026-05-25 22:51:21'),
(2, 2, 2, 'Dzień dobry, przyjąłem zgłoszenie. Czy posiada Pani zatwierdzony wniosek od kierownika na zakup tej licencji?', '2026-05-25 22:53:24'),
(3, 2, 3, 'Tak, wniosek został podpisany wczoraj. Powinien być w systemie obiegu dokumentów pod numerem WN/2026/12.', '2026-05-25 22:53:24'),
(4, 58, 1, 'doeduywebuid', '2026-06-01 13:22:35');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('nowe','w trakcie','zakończone') DEFAULT 'nowe',
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `title`, `description`, `status`, `category_id`, `created_by`, `assigned_to`, `created_at`, `updated_at`, `attachment`) VALUES
(1, 'Niedziałająca drukarka', 'No nie działa', 'w trakcie', 1, 1, 2, '2026-05-24 21:25:34', '2026-05-25 22:49:43', NULL),
(2, 'Nie działa Wi-Fi w biurze 204', 'Od rana router w sali 204 mruga na czerwono. Brak połączenia z internetem na wszystkich laptopach.', 'nowe', 1, NULL, NULL, '2026-05-25 22:53:24', '2026-05-25 22:53:24', NULL),
(3, 'Prośba o instalację pakietu Adobe', 'Dzień dobry, potrzebuję pilnie licencji i instalacji programu Photoshop do obróbki grafik na stronę.', 'w trakcie', 3, 3, 2, '2026-05-25 22:53:24', '2026-05-25 22:53:24', NULL),
(4, 'Drukarka brudzi kartki', 'Podczas drukowania raportów na drukarce sieciowej HP, na każdej stronie pojawia się czarny pasek.', 'nowe', 4, 4, NULL, '2026-05-25 22:53:24', '2026-05-25 22:53:24', NULL),
(5, 'Zablokowane konto w systemie CRM', 'Po trzykrotnym wpisaniu błędnego hasła moje konto pracownika zostało zablokowane. Proszę o odblokowanie.', 'zakończone', 2, 3, 1, '2026-05-25 22:53:24', '2026-05-25 22:53:24', NULL),
(7, 'Brak dostępu do VPN', 'Nie mogę połączyć się z siecią firmową z domu. Wyskakuje błąd 809.', 'nowe', 1, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(8, 'Monitor mruga', 'Prawy monitor na moim biurku zaczyna migać po 10 minutach pracy.', 'w trakcie', 4, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(9, 'Zablokowane konto ERP', 'Wpisałem 3 razy złe hasło i system mnie wyrzucił. Proszę o reset.', 'zakończone', 3, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(10, 'Brak tonera', 'Drukarka w dziale kadr (pokój 112) zgłasza brak czarnego tonera.', 'nowe', 4, NULL, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(11, 'Outlook nie pobiera poczty', 'Od wczoraj nie przychodzą do mnie żadne maile, na dole jest komunikat \"Odłączono\".', 'w trakcie', 2, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(12, 'Wolne działanie komputera', 'Komputer włącza się ponad 15 minut, praca jest niemożliwa.', 'nowe', 4, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(13, 'Licencja na AutoCAD', 'Moja licencja wygasła. Wyskakuje okienko o konieczności odnowienia.', 'zakończone', 3, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(14, 'Nie działa Wi-Fi w sali konferencyjnej', 'Goście nie mogą się połączyć z siecią \"Guest_WiFi\", hasło nie wchodzi.', 'w trakcie', 1, NULL, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(15, 'Myszka przerywa', 'Kursor na ekranie skacze. Wymiana baterii na nowe nie pomogła.', 'nowe', 4, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(16, 'Błąd podczas drukowania PDF', 'Gdy próbuję wydrukować duży plik PDF, drukarka wypluwa puste kartki.', 'zakończone', 4, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(17, 'Brak dostępu do dysku sieciowego', 'Zniknął mi dysk Z: z mojego komputera. Potrzebuję tam dostępu.', 'w trakcie', 1, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(18, 'Podejrzany mail', 'Dostałam dziwnego maila z linkiem do rzekomej faktury. Nie klikałam.', 'nowe', 2, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(19, 'Instalacja nowego programu', 'Proszę o instalację programu 7-zip do pakowania plików.', 'zakończone', 3, NULL, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(20, 'Błąd na stronie firmowej', 'Zakładka \"Kontakt\" na naszej stronie ładuje się w nieskończoność.', 'w trakcie', 1, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(21, 'Skrzynka pocztowa jest pełna', 'Dostaję komunikaty, że przekroczyłem limit 5GB na poczcie.', 'nowe', 2, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(22, 'Klawiatura zalana kawą', 'Niestety wylałam rano kawę na klawiaturę, niektóre klawisze się lepią.', 'w trakcie', 4, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(23, 'Aktualizacja Windowsa utknęła', 'Od 2 godzin na ekranie kręci się kółko \"Trwa aktualizowanie 30%\".', 'nowe', 3, NULL, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(24, 'Brak dźwięku w słuchawkach', 'Kupiłem nowe słuchawki, ale na Teamsach nikt mnie nie słyszy.', 'zakończone', 4, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(25, 'Odrzucane maile', 'Kiedy wysyłam ofertę do klienta, wraca do mnie błąd \"Mail Delivery Subsystem\".', 'w trakcie', 2, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(26, 'Potrzebny kabel HDMI', 'Czy macie na stanie dłuższy kabel HDMI? Ten obecny jest za krótki.', 'nowe', 4, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(27, 'Problem z logowaniem do domeny', 'Przy starcie systemu wyskakuje komunikat o braku serwera logowania.', 'zakończone', 1, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(28, 'Pęknięty ekran w laptopie', 'Podczas podróży służbowej ekran uległ mechanicznemu uszkodzeniu.', 'nowe', 4, NULL, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(29, 'Brak stopki w mailu', 'Zniknęła moja stopka korporacyjna w Outlooku, jak ją przywrócić?', 'w trakcie', 2, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(30, 'Nowy pracownik - sprzęt', 'W poniedziałek zaczyna nowa osoba w dziale HR, potrzebny laptop i myszka.', 'zakończone', 4, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(31, 'Błąd Excela - makra', 'Po otwarciu raportu wyskakuje błąd \"Visual Basic Runtime Error\".', 'nowe', 3, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(32, 'Router piszczy', 'Urządzenie sieciowe w szafie RACK na 2 piętrze wydaje dziwne dźwięki.', 'w trakcie', 1, NULL, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(33, 'Zmiana nazwiska - aktualizacja poczty', 'Wyszłam za mąż, proszę o zmianę mojego adresu email i nazwy wyświetlanej.', 'nowe', 2, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(34, 'Brak miejsca na dysku C', 'Zostało mi tylko 100 MB wolnego miejsca, system bardzo zwolnił.', 'zakończone', 4, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(35, 'Instalacja czcionek', 'Dział marketingu podesłał nowe czcionki, nie mam uprawnień, żeby je wgrać.', 'w trakcie', 3, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(36, 'Kabel od internetu przecięty', 'Ktoś najechał krzesłem na kabel sieciowy i go przerwał.', 'nowe', 1, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(37, 'Alias pocztowy', 'Potrzebujemy nowego adresu kontakt@firma.pl podpiętego pod moją skrzynkę.', 'zakończone', 2, NULL, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(38, 'Kamera internetowa nie działa', 'Obraz z mojej kamery jest całkowicie czarny, dioda się nie świeci.', 'w trakcie', 4, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(39, 'Reset hasła do SAP', 'Zapomniałem hasła do systemu księgowego.', 'nowe', 3, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(40, 'Aplikacja ciągle się zamyka', 'Program do faktur crashuje się przy próbie zapisu pliku.', 'zakończone', 3, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(41, 'Nie działa port USB', 'Porty z lewej strony laptopa przestały reagować na pendrive.', 'w trakcie', 4, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(42, 'Podłączenie rzutnika', 'Proszę o pomoc w podłączeniu rzutnika w Sali A przed spotkaniem zarządu.', 'nowe', 4, NULL, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(43, 'Spam ze wschodu', 'Dostaję dziennie po 50 maili z ofertami z dziwnych adresów.', 'zakończone', 2, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(44, 'Zła godzina w komputerze', 'Zegar w Windowsie pokazuje godzinę o 2 do tyłu, nie mogę zmienić ręcznie.', 'w trakcie', 3, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(45, 'Nie mogę wejść na stronę banku', 'Przeglądarka wyświetla komunikat o niebezpiecznym połączeniu.', 'nowe', 1, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(46, 'Przegrzewający się laptop', 'Laptop parzy w ręce i buczy jak odkurzacz.', 'zakończone', 4, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(47, 'Pomyłka w wysłanym mailu', 'Czy da się cofnąć maila wysłanego 5 minut temu do całej firmy?', 'w trakcie', 2, NULL, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(48, 'Brak widoczności drukarki', 'Moja drukarka zniknęła z listy urządzeń w systemie.', 'nowe', 4, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(49, 'Blue Screen', 'Podczas pracy wyświetlił się niebieski ekran z błędem MEMORY_MANAGEMENT.', 'zakończone', 3, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(50, 'Zawiesza się Teams', 'Podczas udostępniania ekranu Teams całkowicie się zamraża.', 'w trakcie', 3, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(51, 'Płyta CD utknęła', 'Stara płyta z archiwum utknęła w zewnętrznym napędzie DVD.', 'nowe', 4, NULL, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(52, 'Nowy certyfikat', 'Przeglądarka krzyczy, że nasz certyfikat intranetowy wygasł.', 'zakończone', 1, 4, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(53, 'Dodanie do grupy mailowej', 'Proszę o dopisanie mnie do listy mailingowej \"Wszyscy_Pracownicy\".', 'w trakcie', 2, 3, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(54, 'Uszkodzona stacja dokująca', 'Laptopy nie ładują się po podpięciu do stacji na biurku nr 12.', 'nowe', 4, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(55, 'Program prosi o klucz administratora', 'Chcę zaktualizować program Adobe, ale wyskakuje okienko UAC.', 'zakończone', 3, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(56, 'Słaby zasięg Wi-Fi', 'W kuchni na 3 piętrze zrywa połączenie z siecią w telefonach.', 'w trakcie', 1, NULL, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(57, 'Wirus z pendrive?', 'Włożyłem obcego pendrive\'a i antywirus zaczął krzyczeć na czerwono.', 'nowe', 3, 4, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(58, 'Myszka leworęczna', 'Czy mógłbym poprosić o zamianę standardowej myszki na symetryczną?', 'zakończone', 4, 3, 1, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(59, 'Konto do testów', 'Potrzebuję tymczasowego konta mailowego do testowania aplikacji.', 'w trakcie', 2, 4, 2, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(60, 'Błąd 404 w systemie wewnętrznym', 'Klikając w link do instrukcji dostaję błąd \"Not Found\".', 'nowe', 1, 3, NULL, '2026-05-25 23:21:13', '2026-05-25 23:21:13', NULL),
(61, 'Pęknięty plastik przy zawiasie', 'Zauważyłem pęknięcie w moim służbowym laptopie.', 'zakończone', 4, NULL, 4, '2026-05-25 23:21:13', '2026-05-25 23:23:37', NULL),
(62, 'test', 'testt', 'w trakcie', 1, 6, NULL, '2026-05-25 23:31:08', '2026-06-01 15:54:40', 'uploads/1779744668_co_jeszcze1.jpg'),
(63, 'awaria', 'awaria', 'w trakcie', 3, NULL, 2, '2026-06-01 16:00:42', '2026-06-01 16:01:19', 'uploads/1780322442_Zrzut ekranu 2024-10-22 204652.png'),
(64, 'Awaria drukarki', 'Drukarka przestała działać podczas drukowania.', 'nowe', 1, NULL, NULL, '2026-06-01 16:19:52', '2026-06-01 16:19:52', 'uploads/1780323592_drukarka.png');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user','guest') NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `active`, `force_password_change`) VALUES
(1, 'admin', '$2y$10$iSnemE5wEcBqwFltkwtCT.3Xa8uPoO7Rk8yNQRye3nCPmxOTYE1ym', 'admin@helpdesk.local', 'admin', 1, 0),
(2, 'zuzia_j', '$2y$10$e23AKy.VHvEJOBv8LOUNf.ciOxsOjtdWb94ekGPSNfJsEPFpU3FUm', 'zuzia_j@wp.pl', 'user', 1, 0),
(3, 'natka_flatka', '$2y$10$YzmmMBP2IWPNt25XPyJUOeECjtH8SJlFTPXaoS94f5e2Kste5aLOW', 'natalka12@wp.pl', 'guest', 1, 0),
(4, 'marek_it', '$2y$10$mC7p6R9iHlA7XwG5T9k3OedR0W6sJ8YvM2fN1zKq9pB3cY6uD7b2.', 'marek@helpdesk.pl', 'user', 1, 0),
(5, 'gosia_serwis', '$2y$10$OdjPCUHdTPeLtTUaT0q6PelIHAWMWuFDZHNyOyXj5PeP.BQvuPPnG', 'gosia@helpdesk.pl', 'user', 1, 0),
(6, 'jan_kowalski', '$2y$10$Ys2Cp8Ew7HSQj6.26z7PPuVHxYa2zgFnTfh6N3mJ4I/PrW7euugvq', 'jan.kowalski@wp.pl', 'guest', 1, 0),
(7, 'anna_nowak', '$2y$10$mC7p6R9iHlA7XwG5T9k3OedR0W6sJ8YvM2fN1zKq9pB3cY6uD7b2.', 'anna.nowak@o2.pl', 'guest', 1, 0),
(8, 'jkowalskii', '$2y$10$QyVAKfKNIassdvnOcy0/ie6nCxvelKgL545xldhVSltcLmD5SN55e', 'jan@wp.pl', 'guest', 1, 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
