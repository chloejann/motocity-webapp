# motocity-webapp

# MotoCity 🏍️

**MotoCity** is a dynamic web application for renting motorbikes built using **Object-Oriented PHP** and **MySQL**. It allows users to browse, rent, and return motorbikes across the city, while providing administrators with tools to manage the fleet and rental transactions.

## 📋 Features

### User (Customer)
* **Authentication:** Secure User Registration and Login.
* **Browse Fleet:** View a list of all available motorbikes.
* **Search:** Filter motorbikes by location or model.
* **Rent a Bike:** Start a rental period with real-time timestamping.
* **Return a Bike:** End a rental and automatically calculate the total cost based on duration and hourly rate.
* **Rental History:** View past rental transactions and current active rentals.

### Administrator
* **Fleet Management:** Add new motorbikes to the system or edit existing details.
* **Rental Management:** Rent or return bikes on behalf of customers.
* **Dashboard Views:**
    * List all motorbikes.
    * List currently available bikes.
    * List currently rented bikes.

## 🛠️ Tech Stack

* **Backend:** PHP 8+ (Object-Oriented Programming style)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (AJAX for search filtering)
* **Server:** Apache (via XAMPP/WAMP/MAMP)

## ⚙️ Installation & Setup

1.  **Clone the repository**
    ```bash
    git clone [https://github.com/yourusername/motocity.git](https://github.com/yourusername/motocity.git)
    ```

2.  **Set up the Database**
    * Open **phpMyAdmin** (usually at `http://localhost/phpmyadmin`).
    * Create a new database named `motocity_db`.
    * Import the `database.sql` file located in the root directory of this project.

3.  **Configure the Application**
    * Open `includes/Database.php` (or your config file).
    * Ensure the database credentials match your local setup:
        ```php
        private $host = "localhost";
        private $user = "root";
        private $password = ""; // Default is empty for XAMPP
        private $db_name = "motocity_db";
        ```

4.  **Run the Application**
    * Move the project folder to your server's root directory (e.g., `htdocs` for XAMPP).
    * Open your browser and navigate to `http://localhost/motocity`.

