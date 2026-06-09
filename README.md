# 🛠️ System Zgłoszeń Help Desk

Autorski, nowoczesny system zgłoszeń (Help Desk) stworzony w języku PHP z wykorzystaniem bazy danych MySQL/MariaDB. Aplikacja pozwala klientom (również niezalogowanym gościom) na przesyłanie zgłoszeń wsparcia, bezpieczne dodawanie załączników oraz dwustronną komunikację z działem obsługi. Administratorzy posiadają pełny panel zarządzania użytkownikami, kategoriami zgłoszeń oraz wgląd w statystyki systemu.

## 💻 Wymagania systemowe
* Wersja Apache: **2.4.x** * Wersja PHP: **8.2.12**
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

1. **Umieszczenie plików projektu:**
   Pobierz kod projektu i umieść go w katalogu głównym serwera WWW (np. `public_html`, `htdocs` lub główny folder domeny).

2. **Uprawnienia katalogów:**
   System wymaga uprawnień zapisu do przechowywania przesyłanych przez użytkowników załączników. Należy upewnić się, że proces serwera ma uprawnienia do zapisu w folderze `uploads/` (w środowiskach Linux/serwerowych należy nadać uprawnienie `chmod 777 uploads/`).

3. **Inicjalizacja bazy danych:**
   * Utwórz nową bazę danych w panelu hostingowym lub poprzez phpMyAdmin.
   * Przejdź do zakładki "Import" w phpMyAdmin i wgraj plik struktury bazy danych znajdujący się w projekcie: `database/helpdesk.sql`.

4. **Konfiguracja połączenia z bazą:**
   * Otwórz plik `config.php` (znajdujący się w głównym katalogu) i zaktualizuj dane dostępowe do nowo utworzonej bazy danych (zmienne: `$db_host`, `$db_user`, `$db_password`, `$db_name`).

5. **Weryfikacja środowiska:**
   * Projekt zawiera wbudowany skrypt weryfikujący (plik `index.php`), który uruchamia się domyślnie po wejściu na główny adres aplikacji (np. `http://10.6.253.19/~jonczyk/`). 
   * Pełni on funkcję testera połączenia z bazą – jego uruchomienie potwierdzi prawidłową konfigurację środowiska.

6. **Uruchomienie aplikacji:**
   * Jeśli skrypt weryfikujący wyświetla zielone komunikaty, aplikacja jest gotowa do działania. Przejdź pod adres domeny z dopiskiem `/login.php` (lub kliknij przycisk na ekranie weryfikatora), aby rozpocząć korzystanie z systemu.

## ✍️ Autorzy
* **Natalia Flaszka**
  * *nr albumu:* 420530
  * *login z manticore'y:* flaszkan
* **Zuzanna Jonczyk**
  * *nr albumu:* 420537
  * *login z manticore'y:* jonczyk

## 📚 Wykorzystane zewnętrzne biblioteki
* Bootstrap (wersja **5.3.3**)