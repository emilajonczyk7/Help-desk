# 🛠️ System Zgłoszeń Help Desk

Autorski, nowoczesny system zgłoszeń (Help Desk) stworzony w języku PHP z wykorzystaniem bazy danych MySQL/MariaDB. Aplikacja pozwala klientom (również niezalogowanym gościom) na przesyłanie zgłoszeń wsparcia, bezpieczne dodawanie załączników oraz dwustronną komunikację z działem obsługi. Administratorzy posiadają pełny panel zarządzania użytkownikami, kategoriami zgłoszeń oraz wgląd w statystyki systemu.

## 💻 Wymagania systemowe
* wersja apache'a: **2.4.x** 
* wersja PHP'a: **8.2.12**
* wersja MySQL: **10.4.32-MariaDB**

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

## 🚀 Instalacja
Projekt jest w pełni przystosowany do uruchomienia w lokalnym środowisku programistycznym (np. XAMPP).

1. **Umieszczenie plików projektu:**
   Pobierz kod projektu i umieść go w katalogu głównym serwera Apache (np. `C:\xampp\htdocs\Help-desk`).

2. **Uprawnienia katalogów:**
   System wymaga uprawnień zapisu do przechowywania przesyłanych przez użytkowników załączników.
   * **Katalog docelowy:** `uploads/`
   * **Wymagane uprawnienia:** Należy upewnić się, że proces serwera WWW ma uprawnienia do zapisu w tym folderze (w środowiskach Linux/serwerowych należy nadać uprawnienie `chmod 777 uploads/`).

3. **Konfiguracja połączenia z bazą:**
   * Aplikacja domyślnie korzysta ze standardowych danych dostępowych XAMPP (użytkownik: `root`, hasło: *brak*). Plik `config.php` jest już dołączony do repozytorium i gotowy do działania. W razie konieczności zmiany danych serwerowych, zaktualizuj odpowiednie zmienne w plikach `config.php` oraz `install.php`.

4. **Automatyczna instalacja (Instalator PHP):**
   * Otwórz przeglądarkę internetową i przejdź pod adres instalatora: `http://localhost/Help-desk/install.php`
   * Skrypt instalacyjny automatycznie połączy się z serwerem, utworzy bazę danych `helpdesk`, zaimportuje strukturę tabel oraz doda konta testowe.

5. **Uruchomienie aplikacji:**
   * Po pojawieniu się komunikatów o pomyślnej instalacji, kliknij przycisk na dole strony lub przejdź bezpośrednio pod adres: `http://localhost/Help-desk/login.php`

## ✍️ Autorzy
* **Natalia Flaszka**
  * *nr albumu:* 420530
  * *login z manticore'y:* flaszkan
* **Zuzanna Jonczyk**
  * *nr albumu:* 420537
  * *login z manticore'y:* jonczyk

## 📚 Wykorzystane zewnętrzne biblioteki
* Bootstrap (wersja **5.3.3**)
