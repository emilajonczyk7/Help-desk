# 🛠️ System Zgłoszeń Help Desk

Autorski, nowoczesny system zgłoszeń (Help Desk) stworzony w języku PHP z wykorzystaniem bazy danych MySQL/MariaDB. Aplikacja pozwala klientom (również niezalogowanym gościom) na przesyłanie zgłoszeń wsparcia, bezpieczne dodawanie załączników oraz dwustronną komunikację z działem obsługi. Administratorzy posiadają pełny panel zarządzania użytkownikami, kategoriami zgłoszeń oraz wgląd w statystyki systemu.

## 💻 Wymagania systemowe
* Wersja Apache: **2.4.58** * Wersja PHP: **8.2.12**
* Wersja MySQL: **10.4.32-MariaDB**

## ✨ Funkcjonalności systemu
* **Zarządzanie zgłoszeniami:** Tworzenie, przeglądanie, przypisywanie pracowników oraz zmiana statusów (Nowe -> W trakcie -> Zakończone).
* **Obsługa załączników:** Możliwość wgrywania plików (JPG, PNG, PDF, TXT, ZIP) do 5MB z automatyczną walidacją rozszerzeń i rozmiaru po stronie serwera.
* **System komentarzy:** Dynamiczna historia korespondencji wewnątrz każdego zgłoszenia pomiędzy klientem a obsługą techniczną.
* **Bezpieczeństwo i architektura:**
  * Haszowanie haseł za pomocą bezpiecznego algorytmu `PASSWORD_BCRYPT`.
  * Zastosowanie wzorca PRG (Post-Redirect-Get) wraz z komunikatami Flash, co zapobiega ponownemu przesyłaniu formularzy przy odświeżaniu strony.
  * Pełna walidacja i oczyszczanie danych wejściowych (`trim`, `filter_var`).
  * Ochrona podstron panelu oparta na sesjach (brak dostępu dla osób niezalogowanych lub bez odpowiednich uprawnień).
* **Paginacja i filtry:** Stronicowanie listy zgłoszeń (limit 15 na stronę) ułatwiające zarządzanie dużą ilością danych.

## 👥 Role użytkowników
1. **Administrator (`admin`):** Pełny dostęp do systemu. Zarządza kontami użytkowników (dodawanie, edycja danych, blokowanie, wymuszenie zmiany hasła, reset hasła do "Start123!"), tworzy i usuwa kategorie zgłoszeń oraz przegląda raporty i statystyki obciążenia systemu.
2. **Pracownik / Wsparcie (`user`):** Posiada wgląd do wszystkich ticketów w systemie. Może przypisać zgłoszenie do siebie lub innego pracownika, aktualizować statusy oraz odpowiadać klientom.
3. **Klient / Gość (`guest` / niezalogowany):** Może zarejestrować konto, tworzyć nowe zgłoszenia z załącznikami oraz śledzić status i korespondencję swojego zgłoszenia bez logowania (za pomocą unikalnego numeru ID zgłoszenia).

## 🚀 Instalacja i wdrożenie na serwerze

System został wyposażony w automatyczny instalator środowiska, co eliminuje potrzebę ręcznego importowania bazy danych przez phpMyAdmin.

1. **Umieszczenie plików projektu:**
   Prześlij zawartość projektu do katalogu głównego serwera WWW (np. `public_html`).

2. **Uprawnienia katalogów:**
   Upewnij się, że proces serwera posiada uprawnienia do zapisu w folderze `uploads/` (w środowiskach Linux zalecane `chmod 755` lub `777` w zależności od konfiguracji serwera).

3. **Konfiguracja połączenia z bazą:**
   Otwórz plik `config.php` i uzupełnij zmienne `$db_user`, `$db_password` oraz `$db_name` zgodnie z danymi dostępowymi do Twojej bazy danych MySQL na serwerze.

4. **Automatyczna inicjalizacja:**
   Wejdź na adres główny swojej aplikacji (np. `http://10.6.253.19/~jonczyk/`).
   * System automatycznie uruchomi instalator, który nawiąże połączenie z bazą danych.
   * Instalator samodzielnie wgra strukturę tabel oraz dane testowe z pliku `database/helpdesk.sql`.
   * Po wyświetleniu zielonych komunikatów o sukcesie, kliknij przycisk "Przejdź do strony logowania", aby rozpocząć korzystanie z systemu.

## ✍️ Autorzy
* **Natalia Flaszka**
  * *nr albumu:* 420530
  * *login z manticore'y:* flaszkan
* **Zuzanna Jonczyk**
  * *nr albumu:* 420537
  * *login z manticore'y:* jonczyk

## 📚 Wykorzystane zewnętrzne biblioteki
* Bootstrap (wersja **5.3.3**)