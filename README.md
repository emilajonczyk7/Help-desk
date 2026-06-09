# 🛠️ Help Desk Ticketing System

A robust, custom-built Help Desk ticketing system developed in PHP and MySQL. This application allows clients to submit support tickets, attach files, and communicate with the support staff, while providing administrators with full control over users and categories.

## 💻 System Requirements
* **Apache:** version [2.4+]
* **PHP:** version [8.0+]
* **MySQL/MariaDB:** version [10+]

## ✨ Features
* **Ticket Management:** Create, view, update, and resolve support tickets.
* **File Attachments:** Safely upload files (images, PDFs, text files) to tickets with automatic size and extension validation.
* **Role-Based Access Control (RBAC):** Three distinct user levels with specific permissions.
* **Communication History:** Built-in commenting system for each ticket.
* **Robust Security:**
  * Password hashing (`PASSWORD_BCRYPT`).
  * PRG (Post-Redirect-Get) pattern with Flash Messages to prevent form resubmission.
  * Server-side data validation (`trim`, `empty`, `filter_var`).
  * Session-based route protection for all panel files.
* **Pagination:** Easily navigate through large amounts of tickets.

## 👥 User Roles
The system uses three distinct roles to manage permissions:
1. **Administrator (`admin`)** - Full access, manages users, categories, and system reports.
2. **Employee / Support Staff (`user`)** - Views, assigns, and resolves tickets, replies to clients.
3. **Client (`guest`)** - Registers accounts, creates tickets, uploads attachments.

## 🚀 Installation & Configuration (Local Setup)
This project is built to run on a standard LAMP/WAMP/XAMPP stack.

1. **Clone the repository:**
   Download the project and place it inside your local server's document root (e.g., `C:\xampp\htdocs\helpdesk`).

2. **Directory Permissions:**
   Ensure that the directory responsible for storing file attachments has write permissions.
   * Directory: `[podaj ścieżkę do folderu, np. /uploads/attachments/]`
   * Permissions: Set `chmod 777` (or ensure the web server user has write access).

3. **Set up the Database:**
   * Open **phpMyAdmin**.
   * Create a new, empty database named `helpdesk`.
   * Import the `helpdesk.sql` file located in the `database/` folder.

4. **Configure the connection:**
   * By default, the system assumes a standard XAMPP configuration (user: `root`, password: *empty*).
   * Update credentials in `config.php` if necessary.

5. **Launch the Application:**
   * Go to: `http://localhost/helpdesk/login.php`

## 📚 External Libraries Used
* Bootstrap (version [5.3.0])
* jQuery (version [3.7.0])

## ✍️ Author
* **Natalia Flaszka & Zuzanna Jonczyk**
* *Student ID (nr albumu): 420530 & 420537*
* *Manticore Login: flaszkan & jonczyk*
