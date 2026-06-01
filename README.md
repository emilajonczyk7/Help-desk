# 🛠️ Help Desk Ticketing System

A robust, custom-built Help Desk ticketing system developed in PHP and MySQL. This application allows clients to submit support tickets, attach files, and communicate with the support staff, while providing administrators with full control over users and categories.

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

1. **Administrator (`admin`)**
   * Full access to the system.
   * Can manage users (add, edit, block, force password resets).
   * Can create and manage ticket categories.
   * Can permanently delete tickets.
   * Access to system reports and statistics.

2. **Employee / Support Staff (`user`)**
   * Can view all tickets in the system.
   * Can assign tickets to themselves or others.
   * Can change ticket statuses (New -> In Progress -> Resolved).
   * Can reply to clients via the comment system.

3. **Client (`guest`)**
   * Can register a new account.
   * Can create new support tickets and upload attachments.

## 🚀 How to Run the Project (Local Setup)

This project is built to run on a standard LAMP/WAMP/XAMPP stack.

1. **Clone the repository:**
   Download the project and place it inside your local server's document root (e.g., `C:\xampp\htdocs\helpdesk`).

2. **Set up the Database:**
   * Open **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
   * Create a new, empty database named `helpdesk`.
   * Navigate to the `Import` tab.
   * Choose the `helpdesk.sql` file located in the `database/` folder of this repository.
   * Click **Import** to generate the tables and dummy data.

3. **Configure the connection (if needed):**
   * By default, the system assumes a standard XAMPP configuration (user: `root`, password: *empty*).
   * If your local database uses a different user or password, update the credentials in the `config.php` file.

4. **Launch the Application:**
   * Open your web browser and go to: `http://localhost/helpdesk/login.php`
